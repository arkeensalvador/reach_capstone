<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Grade;
use App\Models\StudentRecords;
use App\Models\Transaction;
use DB;
use Illuminate\Support\Facades\Auth;

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
    public function admin_index(Request $request)
    {
        // Statistics
        $totalTransaction = Transaction::count();
        $pendingTransaction = Transaction::where('status', 0)->count();
        $approvedTransaction = Transaction::where('status', 1)->count();
        $rejectedTransaction = Transaction::where('status', 2)->count();
        $totalStudents = StudentRecords::count();
        $totalRegistrar = User::where('userType', 'registrar')->count();

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

        // Count FORM 137 and GOOD MORAL requests
        $form137Count = Transaction::where('doc_requested', 1)->count();
        $goodMoralCount = Transaction::where('doc_requested', 2)->count();

        // // Fetch all student records with related grades for the selected academic year
        // $students = StudentRecords::with('grades')
        //     ->where('academic_year', $selectedYear)
        //     ->get();

        // // Filter students who have exactly 9 subjects
        // $filteredStudents = $students->filter(function ($student) {
        //     return $student->grades->count() === 9;
        // })->map(function ($student) {
        //     $subjectCount = $student->grades->count();
        //     $totalGrades = $student->grades->sum('grades');
        //     $averageGrade = $subjectCount > 0 ? $totalGrades / $subjectCount : 0;
        //     $status = $averageGrade >= 75 ? 'Pass' : 'Fail';

        //     return [
        //         'status' => $status,
        //         'academic_year' => $student->academic_year,
        //     ];
        // });

        // // Calculate total counts
        // $totalCount = $students->count();
        // $passedCount = $filteredStudents->where('status', 'Pass')->count();
        // $failedCount = $filteredStudents->where('status', 'Fail')->count();

        // // Prepare data for ApexCharts
        // $chartData = [
        //     [
        //         'name' => 'Passed',
        //         'data' => [$passedCount]
        //     ],
        //     [
        //         'name' => 'Failed',
        //         'data' => [$failedCount]
        //     ],
        //     [
        //         'name' => 'Total Students',
        //         'data' => [$totalCount]
        //     ]
        // ];
        // Fetch all student records with related grades
        $students = StudentRecords::with('grades')->get();

        // Filter students who have exactly 9 subjects for pass/fail calculations
        $filteredStudents = $students->filter(function ($student) {
            return $student->grades->count() === 9;
        })->map(function ($student) {
            // Calculate the number of subjects, total grades, and average grade
            $subjectCount = $student->grades->count();
            $totalGrades = $student->grades->sum('grades');
            $averageGrade = $subjectCount > 0 ? $totalGrades / $subjectCount : 0;
            $status = $averageGrade >= 75 ? 'Pass' : 'Fail';

            // Format the student's full name
            $studentName = $student->lastname . ', ' . $student->firstname . ' ' . $student->middlename;

            return [
                'name' => $studentName,
                'subject_count' => $subjectCount,
                'total_grades' => $totalGrades,
                'average_grade' => number_format($averageGrade, 2),
                'academic_year' => $student->academic_year,
                'status' => $status
            ];
        });

        // Get all unique academic years
        $allAcademicYears = StudentRecords::select('academic_year')->distinct()->pluck('academic_year');

        // Group all students by academic year
        $groupedByYear = $students->groupBy('academic_year')->map(function ($studentsByYear, $year) use ($filteredStudents) {
            $totalCount = $studentsByYear->count();
            $filteredStudentsByYear = $filteredStudents->where('academic_year', $year);

            // Calculate pass/fail counts only for students with exactly 9 subjects
            $passedCount = $filteredStudentsByYear->where('status', 'Pass')->count();
            $failedCount = $filteredStudentsByYear->where('status', 'Fail')->count();

            return [
                'year' => $year,
                'total_count' => $totalCount,
                'passed_count' => $passedCount,
                'failed_count' => $failedCount
            ];
        });

        // Prepare data for all academic years, including those with no students having grades
        $passFailStats = $allAcademicYears->map(function ($year) use ($groupedByYear) {
            $stats = $groupedByYear->get($year, ['total_count' => 0, 'passed_count' => 0, 'failed_count' => 0]);
            return array_merge(['year' => $year], $stats);
        });

        // Sort the data by academic year
        $passFailStats = $passFailStats->sortBy('year')->values();

        return view('admin.dashboard', compact(
            'totalTransaction',
            'approvedTransaction',
            'rejectedTransaction',
            'pendingTransaction',
            'transactionsPerMonth',
            'academicYears',
            'selectedYear',
            'sectionCounts',
            'totalStudents',
            'totalRegistrar',
            'form137Count',
            'goodMoralCount'
        ),  [
            'passFailStats' => $passFailStats,
        ]);
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

    public function updateAccountStatus(Request $request, $id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if ($user) {
            // Toggle account status between 1 and 0
            $user->acc_status = $user->acc_status == 1 ? 0 : 1;
            $user->save();

            // Redirect back with success message
            return redirect()->route('registrar.accounts')->with('success', 'Account status updated.');
        } else {
            // Redirect back with error message if user not found
            return redirect()->route('registrar.accounts')->with('error', 'Registrar account not found.');
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
        return view('admin.records.student_records', compact('students', 'academicYears', 'sections'));
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

        return redirect()->back()->with('success', 'Student record updated successfully');
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

        return view('admin.records.edit_student_records', compact('student', 'academic_years'));
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
                        <li><a class="dropdown-item view-student-data" href="#" data-bs-toggle="modal" data-bs-target="#viewStudentModal2" data-student-id="' . $student->id . '">View</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="edit-student/' . $student->id . '">Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item open-grades-modal" href="#" data-bs-toggle="modal" data-bs-target="#addGradesModal" data-student-id="' . $student->id . '">Add Grades</a></li>
                        <li><a class="dropdown-item" href="' . route('view_grades_admin', ['id' => $student->id]) . '">View Grades</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item delete-student-admin" href="#" data-student-id="' . $student->id . '">Delete</a></li>
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

    public function storeGradesAdmin(Request $request)
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

    public function viewGradesAdmin($id)
    {
        // Fetch the student record by ID and load the related grades
        $student = StudentRecords::with('grades')->findOrFail($id);

        // Format the student's full name
        $studentName = $student->lastname . ', ' . $student->firstname . ' ' . $student->middlename;

        // Pass the student, formatted name, and their grades to the view
        return view('admin.records.view_student_grades', [
            'student' => $student,
            'studentName' => $studentName,
        ]);
    }

    public function updateGradesAdmin(Request $request, $id)
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

    public function deleteGradesAdmin(Request $request, $id)
    {
        // Find the grade record by ID and delete it
        $grade = Grade::findOrFail($id);
        $grade->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Subject and Grades deleted successfully.');
    }
}
