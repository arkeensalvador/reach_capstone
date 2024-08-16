<?php

namespace App\Http\Controllers\registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth; // Import the Auth facade

class RegistrarController extends Controller
{
    public function registrar_index()
    {
        return view('registrar.registrar_index');
    }

    public function checkPendingRequestsRegistrar()
    {
        $pendingRequestsCountRegistrar = Transaction::where('status', 0)->count();
        return response()->json(['pendingRequestsCountRegistrar' => $pendingRequestsCountRegistrar]);
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
        $query = Transaction::query();

        if (!is_null($status)) {
            $query->where('status', $status);
        }

        $requestLogs = $query->get();

        // Return the view with student request logs data
        return view('registrar.registrar_logs', compact('requestLogs'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'transaction_ID' => 'required|integer',
            'status' => 'required|integer'
        ]);

        $log = Transaction::where('transaction_ID', $request->transaction_ID)->first();
        if ($log) {
            $log->status = $request->status;
            $log->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Log not found.'], 404);
        }
    }
    public function fetchRequestLogs()
    {
        $requestLogs = Transaction::all(); // Adjust your query as needed

        return response()->json([
            'logs' => $requestLogs
        ]);
    }
}
