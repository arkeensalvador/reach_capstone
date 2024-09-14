<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Storage;

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

        // Fetch the request logs with related documents
        $requestLogs = $query->with('documents')->get();

        // Return the view with student request logs data
        return view('student.student_logs', compact('requestLogs'));
    }

     // Method to download a document
     public function downloadDocument($id)
     {
         $document = DocumentUpload::findOrFail($id);
         return Storage::download('public/' . $document->file_path);
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
                'user_mother_name' => 'required',
            ],
            [
                'user_lname.required' => 'Last name is required',
                'user_mname.required' => 'Middle name is required',
                'user_fname.required' => 'First name is required',
                'user_address.required' => 'Address is required',
                'user_sex.required' => 'Sex is required',
                'user_doc_requested.required' => 'Document requested is required',
                'user_mother_name.required' => 'Mother\'s maiden name is required',
            ],
        );

        $inserted = Transaction::create([
            'user_id' => $request->input('user_id'),
            'lname' => $request->input('user_lname'),
            'mname' => $request->input('user_mname'),
            'fname' => $request->input('user_fname'),
            'sex' => $request->input('user_sex'),
            'address' => $request->input('user_address'),
            'doc_requested' => $request->input('user_doc_requested'),
            'mother_name' => $request->input('user_mother_name'),
        ]);

        if ($inserted) {
            return redirect()->route('student.student_request')->with('success', 'Document requested successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to request document.');
        }
    }
}
