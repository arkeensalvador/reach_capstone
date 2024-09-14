<?php

namespace App\Http\Controllers\registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use App\Models\StudentRecords;
use Illuminate\Support\Facades\DB;
use App\Models\Grade;
use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Storage;

date_default_timezone_set('Asia/Hong_Kong');

class RegistrarController extends Controller
{

    public function registrar_index(Request $request)
    {
        // Statistics
        $totalTransaction = Transaction::count();
        $pendingTransaction = Transaction::where('status', 0)->count();
        $approvedTransaction = Transaction::where('status', 1)->count();
        $rejectedTransaction = Transaction::where('status', 2)->count();
        $totalStudents = StudentRecords::count();

        // Transactions per month
        $monthlyTransactions = Transaction::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->all();

        $transactionsPerMonth = array_fill(1, 12, 0);
        foreach ($monthlyTransactions as $month => $count) {
            $transactionsPerMonth[$month] = $count;
        }

        // Fetch academic years and student section counts
        $academicYears = StudentRecords::select('academic_year')->distinct()->orderBy('academic_year', 'asc')->pluck('academic_year');
        $selectedYear = $request->input('academic_year', $academicYears->first());

        $sectionCounts = StudentRecords::select('section', DB::raw('count(*) as total'))
            ->where('academic_year', $selectedYear)
            ->groupBy('section')
            ->pluck('total', 'section')
            ->all();

        return view('registrar.registrar_index', compact(
            'totalTransaction',
            'approvedTransaction',
            'rejectedTransaction',
            'pendingTransaction',
            'transactionsPerMonth',
            'academicYears',
            'selectedYear',
            'sectionCounts',
            'totalStudents'
        ));
    }

    public function year_select_index()
    {
        // Fetch distinct academic_year values and order them in ascending order
        $academic_years = StudentRecords::select('academic_year')
            ->distinct()
            ->orderByRaw("CAST(SUBSTRING_INDEX(academic_year, '-', 1) AS UNSIGNED) ASC")
            ->get();
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

    // public function records_data_index($year, $section)
    // {

    //     $students = StudentRecords::where('academic_year', $year)
    //         ->where('section', $section)
    //         ->get();


    //     return view('registrar.student_records', compact('students', 'year', 'section'));
    // }

    public function records_data_index(Request $request)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You need to log in first.');
        }

        // Get the selected academic year and section from the request query parameters
        $academicYear = $request->input('academic_year');
        $section = $request->input('section');

        // Initialize the query
        $query = StudentRecords::query();

        // Apply filters if specified
        if (!is_null($academicYear) || !is_null($section)) {
            if (!is_null($academicYear)) {
                $query->where('academic_year', $academicYear);
            }

            if (!is_null($section)) {
                $query->where('section', $section);
            }

            $students = $query->get();
        } else {
            $students = collect(); // Return an empty collection
        }

        // Fetch distinct academic years and sections for the dropdown filters
        $academicYears = StudentRecords::select('academic_year')->distinct()->orderBy('academic_year', 'asc')->pluck('academic_year');
        $sections = StudentRecords::select('section')->distinct()->orderBy('academic_year', 'asc')->pluck('section');

        // Return the view with student records and filter options
        return view('registrar.student_records', compact('students', 'academicYears', 'sections'));
    }
    public function getStudentRecords(Request $request)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = StudentRecords::query();

        // Apply filters if specified
        $academicYear = $request->input('academic_year');
        $section = $request->input('section');

        if (!is_null($academicYear) && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        if (!is_null($section) && $section !== '') {
            $query->where('section', $section);
        }

        $totalRecords = $query->count();
        $filteredRecords = $totalRecords; // Set this to the count after filtering

        $students = $query->get(['id', 'firstname', 'middlename', 'lastname', 'sex', 'address', 'guardians_name', 'guardians_contact', 'level_to_be_enrolled', 'adviser', 'section', 'mother_name', 'academic_year']);

        // Add a fullname field and an action column with buttons
        $students->transform(function ($student) {
            $student->fullname = trim($student->firstname . ' ' . $student->middlename . ' ' . $student->lastname);
            $student->action = '
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Action
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item view-student-data" href="#" data-bs-toggle="modal" data-bs-target="#viewStudentModal" data-student-id="' . $student->id . '">View</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="edit-student/' . $student->id . '">Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item open-grades-modal" href="#" data-bs-toggle="modal" data-bs-target="#addGradesModal" data-student-id="' . $student->id . '">Add Grades</a></li>
                        <li><a class="dropdown-item" href="' . route('view_grades', ['id' => $student->id]) . '">View Grades</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item delete-student" href="#" data-student-id="' . $student->id . '">Delete</a></li>
                    </ul>
                </div>';
            return $student;
        });


        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $students
        ]);
    }


    public function enroll_student()
    {
        // Fetch distinct academic_year values and order them in ascending order
        $academic_years = StudentRecords::select('academic_year')
            ->distinct()
            ->orderByRaw("CAST(SUBSTRING_INDEX(academic_year, '-', 1) AS UNSIGNED) ASC")
            ->get();
        return view('registrar.add_student_record.enroll_student', compact('academic_years'));
    }


    public function add_student_data(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
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
            'mother_name' => 'required',
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
            'mother_name.required' => 'Mother\'s maiden name is required',
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
            'mother_name' => $request->input('mother_name'),
            'academic_year' => $request->input('academic_year'),
            'created_at' => now(),
        ]);

        // Redirect back with a success message
        if ($insert) {
            return redirect()->route('enroll_student')->with('success', 'Student enrolled successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to enroll student.');
        }
    }

    public function edit_student($id)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors('Unauthorized');
        }

        // Fetch distinct academic_year values and order them in ascending order
        $academic_years = StudentRecords::select('academic_year')
            ->distinct()
            ->orderByRaw("CAST(SUBSTRING_INDEX(academic_year, '-', 1) AS UNSIGNED) ASC")
            ->get();
        $student = StudentRecords::find($id);

        return view('registrar.edit_student_records', compact('student', 'academic_years'));
    }


    public function update_student_record(Request $request)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors('Unauthorized');
        }

        $validated = $request->validate([
            'id' => 'required|exists:student_records,id',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'sex' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'guardians_name' => 'nullable|string|max:255',
            'guardians_contact' => 'nullable|string|max:20',
            'level_to_be_enrolled' => 'nullable|string|max:50',
            'adviser' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'academic_year' => 'nullable|string|max:10'
        ]);

        $student = StudentRecords::find($validated['id']);
        $student->update($validated);

        return redirect()->route('records.data')->with('success', 'Student record updated successfully');
    }
    // notifications in sidebar
    public function checkPendingRequestsRegistrar()
    {
        $pendingRequestsCountRegistrar = Transaction::where('status', 0)->count();
        return response()->json(['pendingRequestsCountRegistrar' => $pendingRequestsCountRegistrar]);
    }

    public function storeGrades(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'studentID' => 'required|string|max:255', // Validate studentID if it's part of the request
            'subjects.*.name' => 'required|string|max:255',
            'subjects.*.grade' => 'required|string|max:10',
        ]);

        // Iterate over each subject and grade from the validated data
        foreach ($validated['subjects'] as $subject) {
            // Save each subject and grade to the 'grades' table
            DB::table('grades')->insert([
                'studentID' => $validated['studentID'],
                'subject' => ucwords($subject['name']),
                'grades' => $subject['grade'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Grades added successfully!');
    }

    public function viewGrades($id)
    {
        // Fetch the student record by ID and load the related grades
        $student = StudentRecords::with('grades')->findOrFail($id);

        // Format the student's full name
        $studentName = $student->lastname . ', ' . $student->firstname . ' ' . $student->middlename;

        // Pass the student, formatted name, and their grades to the view
        return view('registrar.view_student_grades', [
            'student' => $student,
            'studentName' => $studentName,
        ]);
    }

    public function updateGrade(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100', // Validate the grade input
        ]);

        // Find and update the grade
        $grade = Grade::findOrFail($id);
        $grade->grades = $request->input('grade');
        $grade->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Grade updated successfully.');
    }

    public function deleteGrade(Request $request, $id)
    {
        // Find the grade record by ID and delete it
        $grade = Grade::findOrFail($id);
        $grade->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Subject and Grades deleted successfully.');
    }
    // show all requests
    public function show_request_logs(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You need to log in first.');
        }

        $status = $request->input('status');
        $query = Transaction::query();

        if (!is_null($status)) {
            $query->where('status', $status);
        }

        $requestLogs = $query->with('documents')->get();

        if ($request->ajax()) {
            return view('registrar.partials.request_logs_body', compact('requestLogs'))->render();
        }

        return view('registrar.registrar_logs', compact('requestLogs'));
    }


    // update status of request
    public function updateStatus(Request $request)
    {
        $request->validate([
            'transaction_ID' => 'required|integer',
            'status' => 'required|integer',
            'release_date' => 'nullable|date',
            'rejection_reason' => 'nullable|string',
        ]);

        $log = Transaction::find($request->transaction_ID);
        if ($log) {
            $log->status = $request->status;

            // Check if the status is either "Pending" (status = 0) or "Rejected" (status = 2)
            if ($request->status == 1) { // Assuming 1 is for "Approved" and should have a release date
                $log->date_released = $request->release_date;
                $log->rejection_reason = null;
            } else {
                $log->date_released = null;
            }

            if ($request->status == 2) { // Check if status is "Rejected"
                $log->rejection_reason = $request->rejection_reason; // Save the rejection reason
            } else {
                $log->rejection_reason = null; // Clear the rejection reason if not rejected
            }

            $log->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Log not found.']);
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

    // Upload scanned document
    // Method to handle the document upload
    public function upload(Request $request)
    {
        // Validate the input
        $request->validate([
            'file' => 'required|mimes:pdf,png,jpg|max:2048', // max 2MB file size
            'transaction_id' => 'required|exists:transactions,transaction_ID', // Ensure transaction exists
        ]);

        // Retrieve the transaction ID from the request
        $transactionId = $request->transaction_id;

        // Check if a document already exists for this transaction
        $existingDocument = DocumentUpload::where('transaction_id', $transactionId)->first();

        if ($existingDocument) {
            // If a document already exists, return an error message
            return back()->withErrors('A document has already been uploaded for this transaction.');
        }

        // Store the uploaded file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/documents', $fileName, 'public');

            // Save the file information and associate it with the transaction
            DocumentUpload::create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'transaction_id' => $transactionId, // Associate document with transaction
            ]);

            return back()->with('success', 'File uploaded successfully!');
        }

        return back()->withErrors('File upload failed. Please try again.');
    }
    // Method to download a document
    public function download($id)
    {
        $document = DocumentUpload::findOrFail($id);
        return Storage::download('public/' . $document->file_path);
    }

    // DELETE STUDENT RECORD
    public function delete_student_record($id)
    {
        // Begin a transaction to ensure data integrity
        DB::beginTransaction();
        try {
            // Find the student record
            $student = StudentRecords::findOrFail($id);

            // Delete associated grades
            $student->grades()->delete();

            // Delete the student record
            $student->delete();

            // Commit the transaction
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Error deleting record'], 500);
        }
    }
}
