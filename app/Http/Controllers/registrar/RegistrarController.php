<?php

namespace App\Http\Controllers\registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use App\Models\StudentRecords;

class RegistrarController extends Controller
{
    public function registrar_index()
    {
        return view('registrar.registrar_index');
    }

    public function year_select_index()
    {
        // Fetch distinct academic_year values from the database
        $academic_years = StudentRecords::select('academic_year')->distinct()->get();

        // Pass the data to the view
        return view('registrar.year_select', compact('academic_years'));
    }

    public function section_select_index($year)
    {
        // Fetch distinct sections
        $section = StudentRecords::select('section')->distinct()->get();

        // Pass the year and section data to the view
        return view('registrar.section_select', compact('section', 'year'));
    }


    public function records_data_index($year, $section)
    {
        // Fetch student records for the given academic year and section
        $students = StudentRecords::where('academic_year', $year)
            ->where('section', $section)
            ->get();

        // Pass the data to the view
        return view('registrar.student_records', compact('students', 'year', 'section'));
    }

    public function enroll_student()
    {
        return view('registrar.add_student_record.enroll_student');
    }

    
    public function add_student_data(Request $request)
    {
        // Validate the request data
        $request->validate([
            'lastname' => 'required',
            'firstname' => 'required',
            'middlename' => 'required',
            'sex' => 'required',
            'address' => 'required',
            'level_to_be_enrolled' => 'required',
            'guardians_name' => 'required',
            'guardians_contact' => 'required',
            'section' => 'required',
            'adviser' => 'required',
            'academic_year' => 'required',
        ], [
            'lastname.required' => 'Last name is required',
            'middlename.required' => 'Middle name is required',
            'firstname.required' => 'First name is required',
            'sex.required' => 'Sex is required',
            'address.required' => 'Complete address is required',
            'level_to_be_enrolled.required' => 'Level to be enrolled is required',
            'guardians_name.required' => 'Guardian\'s name is required',
            'guardians_contact.required' => 'Guardian\'s contact no. is required',
            'section.required' => 'Section is required',
            'adviser.required' => 'Adviser is required',
            'academic_year.required' => 'Academic year is required',
        ]);

        // Insert the student record into the database
        $insert = StudentRecords::create([
            'firstname' => $request->input('firstname'),
            'middlename' => $request->input('middlename'),
            'lastname' => $request->input('lastname'),
            'sex' => $request->input('sex'),
            'address' => $request->input('address'),
            'level_to_be_enrolled' => $request->input('level_to_be_enrolled'),
            'guardians_name' => $request->input('guardians_name'),
            'guardians_contact' => $request->input('guardians_contact'),
            'section' => $request->input('section'),
            'adviser' => $request->input('adviser'),
            'academic_year' => $request->input('academic_year'),
        ]);

        // Redirect back with a success message
        if ($insert) {
            return redirect()->route('enroll_student')->with('success', 'Student enrolled successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to enroll student.');
        }
    }


    // notifications in sidebar
    public function checkPendingRequestsRegistrar()
    {
        $pendingRequestsCountRegistrar = Transaction::where('status', 0)->count();
        return response()->json(['pendingRequestsCountRegistrar' => $pendingRequestsCountRegistrar]);
    }

    // show all requests
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

    // update status of request
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

    // fetch request logs
    public function fetchRequestLogs()
    {
        $requestLogs = Transaction::all(); // Adjust your query as needed

        return response()->json([
            'logs' => $requestLogs
        ]);
    }
}
