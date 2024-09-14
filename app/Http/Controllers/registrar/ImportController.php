<?php

namespace App\Http\Controllers\registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\StudentGradeImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function import_form() {
        return view('registrar.import-form');
    }
    public function import(Request $request)
    {
        // Validate the file upload
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Import the uploaded file
        Excel::import(new StudentGradeImport, $request->file('file'));

        return back()->with('success', 'File imported successfully!');
    }
}