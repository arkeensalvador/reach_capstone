<?php

namespace App\Http\Controllers\ML;

use App\Models\StudentRecords;
use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Fetch data from the Laravel database
        // Example data (you should fetch real data from your database)
        $data = [
            'average_grade' => 85,
            'level_to_be_enrolled' => 2
        ];

        // Send data to the Python API
        $response = Http::post('http://localhost:5000/predict', $data);
        $prediction = $response->json('prediction');

        // Pass prediction to the view
        return view('ml.analytics', ['prediction' => $prediction]);
    }
}
