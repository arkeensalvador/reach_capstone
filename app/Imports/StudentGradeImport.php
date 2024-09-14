<?php

namespace App\Imports;

use App\Models\StudentRecords;
use App\Models\Grade;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class StudentGradeImport implements ToModel, WithHeadingRow
{
    /**
     * Define the logic to import each row into the StudentRecords and Grades tables.
     *
     * @param array $row
     * @return void
     */
    public function model(array $row)
    {
        // Create or update the student record
        $student = StudentRecords::updateOrCreate(
            ['firstname' => $row['firstname'], 'lastname' => $row['lastname']], // Match on name
            [
                'middlename' => $row['middlename'],
                'sex' => $row['sex'],
                'address' => $row['address'],
                'level_to_be_enrolled' => $row['level_to_be_enrolled'],
                'guardians_name' => $row['guardians_name'],
                'guardians_contact' => $row['guardians_contact'],
                'section' => $row['section'],
                'adviser' => $row['adviser'],
                'mother_name' => $row['mother_name'],
                'academic_year' => $row['academic_year'],
            ]
        );

        // Then, create or update the grades for that student
        Grade::updateOrCreate(
            [
                'studentID' => $student->id,
                'subject' => $row['subject'] // Ensure we are updating/adding based on subject
            ],
            [
                'grades' => $row['grades'] // Assign the grade to the corresponding subject
            ]
        );
    }
}
