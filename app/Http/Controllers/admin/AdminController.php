<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function admin_index()
    {
        return view('admin.dashboard');
    }

    // REGISTRAR CONTROLLER
    public function show_registrar_accounts()
    {
        // Fetch users with 'registrar' userType
        $registrarAccounts = User::where('userType', 'registrar')->get();

        // Return the view with registrar accounts data
        return view('admin.accounts.accounts_index', compact('registrarAccounts'));
    }
    public function add_registrar(Request $request)
    {
        date_default_timezone_set('Asia/Hong_Kong');

        // Validate the request data
        $request->validate(
            [
                'name' => 'required',
                'username' => 'required|min:4|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'userType' => 'required',
                'password' => 'required|confirmed|min:6',
            ],
            [
                'name.required' => 'Name is required!',
                'username.required' => 'Username is required!',
                'username.unique' => 'Username already exists!',
                'email.required' => 'Email is required!',
                'email.unique' => 'Email already exists!',
                'userType.required' => 'User Type is required!',
                'password.required' => 'Password is required!',
                'password.confirmed' => 'Password confirmation does not match!',
            ],
        );

        // Check for duplicate username or email
        $existingUser = User::where('username', $request->input('username'))
            ->orWhere('email', $request->input('email'))
            ->first();

        if ($existingUser) {
            return redirect()->back()->with('error', 'Username or Email already exists.');
        }

        $inserted = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'userType' => $request->input('userType'),
            'password' => Hash::make($request->input('password')),
        ]);

        if ($inserted) {
            return redirect()->route('registrar.accounts')->with('success', 'Registrar added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add registrar.');
        }
    }

    public function edit_registrar($id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if ($user) {
            return view('admin.accounts.edit_registrar', compact('user'));
        } else {
            return redirect()->route('registrar.accounts')->with('error', 'Registrar account not found.');
        }
    }

    public function update_registrar(Request $request, $id)
    {
        // Validate the request data
        $request->validate(
            [
                'name' => 'required',
                'username' => 'required|min:4|unique:users,username,' . $id,
                'email' => 'required|email|unique:users,email,' . $id,
                'userType' => 'required',
                'password' => 'nullable|confirmed|min:6',
            ],
            [
                'name.required' => 'Name is required!',
                'username.required' => 'Username is required!',
                'username.unique' => 'Username already exists!',
                'email.required' => 'Email is required!',
                'email.unique' => 'Email already exists!',
                'userType.required' => 'User Type is required!',
                'password.confirmed' => 'Password confirmation does not match!',
            ]
        );

        // Find the user by ID
        $user = User::find($id);

        if ($user) {
            // Update the user's details
            $user->name = $request->input('name');
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->userType = $request->input('userType');

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->save();

            return redirect()->route('registrar.accounts')->with('success', 'Registrar account updated successfully!');
        } else {
            return redirect()->route('registrar.accounts')->with('error', 'Registrar account not found.');
        }
    }

    public function delete_registrar($id)
    {
        // Find the user by ID and delete
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return redirect()->route('registrar.accounts')->with('success', 'Registrar account deleted successfully!');
        } else {
            return redirect()->route('registrar.accounts')->with('error', 'Registrar account not found.');
        }
    }
}
