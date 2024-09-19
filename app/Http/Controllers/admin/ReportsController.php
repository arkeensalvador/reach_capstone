<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentRecords;
use App\Models\Transaction;
use App\Models\Grade;
use DB;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;
use OpenAI\Client;
use App\Services\OpenAIService;

class ReportsController extends Controller
{
    public function passFailStats()
    {
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

        // Pass the data to the view
        return view('admin.records.test', [
            'passFailStats' => $passFailStats,
        ]);
    }




    public function index(Request $request)
    {
        // Existing statistics data
        $totalStudents = StudentRecords::count();
        $totalTransactions = Transaction::count();

        // Fetch students by academic year and section
        $studentsByYearAndSection = StudentRecords::select('academic_year', 'section', DB::raw('count(*) as total'))
            ->groupBy('academic_year', 'section')
            ->get();

        // Prepare the data for the table
        $studentsByYear = $studentsByYearAndSection->groupBy('academic_year')->map(function ($items) {
            return $items->keyBy('section');
        });

        // Extract section names
        $sections = $studentsByYearAndSection->pluck('section')->unique()->sort()->values();

        // Existing transactions data
        $transactionsByStatus = Transaction::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // New logic to calculate total passed and failed students by academic year
        // Fetch all student records with related grades
        $students = StudentRecords::with('grades')->get();

        // Filter students who have exactly 9 subjects
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

        // Group all students by academic year and calculate total, pass, and fail counts
        $groupedByYear = $students->groupBy('academic_year')->map(function ($studentsByYear, $year) use ($filteredStudents) {
            $totalCount = $studentsByYear->count();
            $filteredStudentsByYear = $filteredStudents->where('academic_year', $year);

            $passedCount = $filteredStudentsByYear->where('status', 'Pass')->count();
            $failedCount = $filteredStudentsByYear->where('status', 'Fail')->count();

            return [
                'year' => $year,
                'total_count' => $totalCount,
                'passed_count' => $passedCount,
                'failed_count' => $failedCount
            ];
        });

        // Prepare data for all academic years, including those with no students having exactly 9 subjects
        $passFailStats = $allAcademicYears->map(function ($year) use ($groupedByYear) {
            $stats = $groupedByYear->get($year, ['total_count' => 0, 'passed_count' => 0, 'failed_count' => 0]);
            return array_merge(['year' => $year], $stats);
        });

        // Sort the data by academic year
        $passFailStats = $passFailStats->sortBy('year')->values();

        return view('admin.reports.reports_index', compact(
            'totalStudents',
            'totalTransactions',
            'studentsByYear',
            'transactionsByStatus',
            'passFailStats',
            'sections'
        ));
    }


    public function exportCsvStudentTotal()
    {
        // Fetch students by academic year and section
        $studentsByYearAndSection = StudentRecords::select('academic_year', 'section', DB::raw('count(*) as total'))
            ->groupBy('academic_year', 'section')
            ->get();

        // Prepare data for CSV
        $csvData = [];
        $sections = $studentsByYearAndSection->pluck('section')->unique()->sort()->values();

        // Add header
        $csvData[] = array_merge(['Academic Year'], $sections->toArray(), ['Total Enrolled']);

        // Add data rows
        $studentsByYear = $studentsByYearAndSection->groupBy('academic_year')->map(function ($items) {
            return $items->keyBy('section');
        });

        foreach ($studentsByYear as $year => $sectionsData) {
            $row = [$year];
            $totalForYear = 0;
            foreach ($sections as $section) {
                $count = $sectionsData->get($section)->total ?? 0;
                $row[] = $count;
                $totalForYear += $count;
            }
            $row[] = $totalForYear;
            $csvData[] = $row;
        }

        // Add totals row
        $totalsRow = ['Total'];
        foreach ($sections as $section) {
            $totalForSection = $studentsByYear->reduce(function ($carry, $items) use ($section) {
                return $carry + ($items->get($section)->total ?? 0);
            }, 0);
            $totalsRow[] = $totalForSection;
        }
        $totalsRow[] = $studentsByYear->reduce(function ($carry, $items) use ($sections) {
            return $carry + $sections->reduce(function ($subCarry, $section) use ($items) {
                return $subCarry + ($items->get($section)->total ?? 0);
            }, 0);
        }, 0);
        $csvData[] = $totalsRow;

        // Convert data to CSV format
        $csvContent = implode("\n", array_map(function ($row) {
            return implode(',', $row);
        }, $csvData));

        // Return CSV file as response
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_by_academic_year.csv"',
        ]);
    }
    public function exportCsv(Request $request)
    {
        $passFailStats = StudentRecords::with('grades')
            ->select('academic_year')
            ->get()
            ->groupBy('academic_year')
            ->map(function ($students, $year) {
                $totalCount = $students->count();
                $passedCount = $students->filter(function ($student) {
                    $totalGrades = $student->grades->sum('grades');
                    $subjectsCount = $student->grades->count();
                    $averageGrade = $subjectsCount > 0 ? $totalGrades / $subjectsCount : 0;
                    return $averageGrade >= 75; // Adjusted to 75 as specified
                })->count();
                $failedCount = $totalCount - $passedCount;

                return [
                    'year' => $year,
                    'total_count' => $totalCount,
                    'passed' => $passedCount,
                    'failed' => $failedCount,
                ];
            })->values()
            ->sortBy('year'); // Sort by academic year

        $response = new StreamedResponse(function () use ($passFailStats) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Academic Year', 'Total Students', 'Total Passed', 'Total Failed']); // Header

            foreach ($passFailStats as $stats) {
                fputcsv($handle, [$stats['year'], $stats['total_count'], $stats['passed'], $stats['failed']]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="pass_fail_stats.csv"');

        return $response;
    }

    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    // // AI BASED ANALYSIS
    // public function analyzeGrades()
    // {
    //     // Fetch all student records with their grades
    //     $students = StudentRecords::with('grades')->get();

    //     // Initialize arrays for analysis
    //     $subjects = Grade::select('subject')->distinct()->pluck('subject')->toArray();
    //     $subjectGrades = array_fill_keys($subjects, []);

    //     // Aggregate grades by subject
    //     foreach ($students as $student) {
    //         foreach ($student->grades as $grade) {
    //             if (array_key_exists($grade->subject, $subjectGrades)) {
    //                 $subjectGrades[$grade->subject][] = $grade->grades;
    //             }
    //         }
    //     }

    //     // Analyze grades
    //     $analysis = [];
    //     foreach ($subjectGrades as $subject => $grades) {
    //         $min = min($grades);
    //         $max = max($grades);
    //         $mean = !empty($grades) ? array_sum($grades) / count($grades) : null;

    //         $analysis[$subject] = [
    //             'grades' => $grades,
    //             'min' => $min,
    //             'max' => $max,
    //             'mean' => $mean,
    //         ];
    //     }

    //     // Find subjects with lowest and highest mean grades
    //     $meanGrades = array_column($analysis, 'mean');
    //     $lowestMeanSubject = array_search(min($meanGrades), $meanGrades);
    //     $highestMeanSubject = array_search(max($meanGrades), $meanGrades);

    //     $conclusion = [
    //         'lowest_mean_subject' => $subjects[$lowestMeanSubject] ?? null,
    //         'highest_mean_subject' => $subjects[$highestMeanSubject] ?? null,
    //         'lowest_mean_grade' => min($meanGrades) ?? null,
    //         'highest_mean_grade' => max($meanGrades) ?? null,
    //     ];

    //     // Prepare data for charts
    //     // Assuming $analysis contains your analysis data
    //     $chartData = [
    //         'subjects' => [],
    //         'gradesBySubject' => [],
    //         'meanGradesBySubject' => [],
    //     ];

    //     foreach ($analysis as $subject => $stats) {
    //         $chartData['subjects'][] = $subject;
    //         $chartData['gradesBySubject'][] = $stats['grades'];
    //         $chartData['meanGradesBySubject'][] = $stats['mean'];
    //     }

    //     return response()->json([
    //         'analysis' => $analysis,
    //         'conclusion' => $conclusion,
    //         'chartData' => $chartData,
    //     ]);
    // }

    public function analyzeGrades()
    {
        // Fetch all student records with their grades
        $students = StudentRecords::with('grades')->get();

        // Initialize arrays for analysis
        $subjects = Grade::select('subject')->distinct()->pluck('subject')->toArray();
        $subjectGrades = array_fill_keys($subjects, []);
        $sections = []; // Array to store sections with data

        // Aggregate grades by subject
        foreach ($students as $student) {
            // Only add section if there are grades
            if ($student->grades->isNotEmpty()) {
                $sections[] = $student->section; // Collect sections
                foreach ($student->grades as $grade) {
                    if (array_key_exists($grade->subject, $subjectGrades)) {
                        $subjectGrades[$grade->subject][] = $grade->grades;
                    }
                }
            }
        }

        // Remove duplicates from sections and reset keys
        $sections = array_values(array_unique($sections));

        // Analyze grades
        $analysis = [];
        foreach ($subjectGrades as $subject => $grades) {
            $min = min($grades);
            $max = max($grades);
            $mean = !empty($grades) ? array_sum($grades) / count($grades) : null;

            $analysis[$subject] = [
                'grades' => $grades,
                'min' => $min,
                'max' => $max,
                'mean' => $mean,
            ];
        }

        // Find subjects with lowest and highest mean grades
        $meanGrades = array_column($analysis, 'mean');
        $lowestMeanSubject = array_search(min($meanGrades), $meanGrades);
        $highestMeanSubject = array_search(max($meanGrades), $meanGrades);

        $conclusion = [
            'lowest_mean_subject' => $subjects[$lowestMeanSubject] ?? null,
            'highest_mean_subject' => $subjects[$highestMeanSubject] ?? null,
            'lowest_mean_grade' => min($meanGrades) ?? null,
            'highest_mean_grade' => max($meanGrades) ?? null,
        ];

        // Prepare data for charts
        $chartData = [
            'subjects' => [],
            'gradesBySubject' => [],
            'meanGradesBySubject' => [],
            'sections' => array_values(array_unique($sections)), // Ensure sections are unique and correctly ordered
        ];

        foreach ($analysis as $subject => $stats) {
            $chartData['subjects'][] = $subject;
            $chartData['gradesBySubject'][] = $stats['grades'];
            $chartData['meanGradesBySubject'][] = $stats['mean'];
        }

        // Send the conclusion data to OpenAI for analysis
        $conclusionAnalysis = $this->openAIService->analyzeConclusion($conclusion);

        return response()->json([
            'analysis' => $analysis,
            'conclusion' => $conclusion,
            'conclusionAnalysis' => $conclusionAnalysis, // Add this for the frontend
            'chartData' => $chartData,
        ]);

        // return response()->json([
        //     'analysis' => $analysis,
        //     'conclusion' => $conclusion,
        //     'chartData' => $chartData,
        // ]);
    }
}
