<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import the Auth facade

class StudentController extends Controller
{
    public function student_index()
    {
        return view('student.student_index');
    }

    public function student_request_index()
    {
        return view('student.student_request');
    }

    public function checkPendingRequestsStudent()
    {
        $pendingRequestsCountStudent = Transaction::where('status', 0)->count();
        return response()->json(['pendingRequestsCountStudent' => $pendingRequestsCountStudent]);
    }

    public function show_request_logs(Request $request)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You need to log in first.');
        }

        // Get the status from the request query parameters
        $status = $request->input('status');

        // Fetch student request logs, optionally filtered by status
        $query = Transaction::where('user_id', Auth::user()->id);

        if (!is_null($status)) {
            $query->where('status', $status);
        }

        $requestLogs = $query->get();

        // Return the view with student request logs data
        return view('student.student_logs', compact('requestLogs'));
    }

    public function student_request_add(Request $request)
    {
        date_default_timezone_set('Asia/Hong_Kong');

        // Validate the request data
        $request->validate(
            [
                'user_lname' => 'required',
                'user_mname' => 'required',
                'user_fname' => 'required',
                'user_address' => 'required',
                'user_sex' => 'required',
                'user_doc_requested' => 'required',
                'user_signature' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048', // Validate file type and size
            ],
            [
                'user_lname.required' => 'Last name is required',
                'user_mname.required' => 'Middle name is required',
                'user_fname.required' => 'First name is required',
                'user_address.required' => 'Address is required',
                'user_sex.required' => 'Sex is required',
                'user_doc_requested.required' => 'Document requested is required',
                'user_signature.required' => 'E-Signature is required',
            ],
        );

        // Handle file upload
        $signaturePath = null;
        if ($request->hasFile('user_signature')) {
            $file = $request->file('user_signature');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Create a unique file name
            $file->move(public_path('signatures'), $fileName); // Move the file to the 'public/signatures' directory
            $signaturePath = 'signatures/' . $fileName; // Store the file path to be saved in the database
        }

        $inserted = Transaction::create([
            'user_id' => $request->input('user_id'),
            'lname' => $request->input('user_lname'),
            'mname' => $request->input('user_mname'),
            'fname' => $request->input('user_fname'),
            'sex' => $request->input('user_sex'),
            'address' => $request->input('user_address'),
            'doc_requested' => $request->input('user_doc_requested'),
            'e_signature' => $signaturePath, // Store the file path in the database
        ]);

        if ($inserted) {
            return redirect()->route('student.student_request')->with('success', 'Document requested successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to request document.');
        }
    }
}
