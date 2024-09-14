<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentRecordsSeeder extends Seeder
{
    public function run()
    {
        $academicYears = ['2021-2022', '2022-2023', '2023-2024', '2024-2025'];
        $sections = ['A', 'B', 'C', 'D', 'E'];

        foreach ($academicYears as $academicYear) {
            foreach ($sections as $section) {
                DB::table('student_records')->insert([
                    [
                        'lastname' => 'Doe',
                        'firstname' => 'John',
                        'middlename' => 'A',
                        'sex' => 'Male',
                        'address' => '123 Main St',
                        'level_to_be_enrolled' => 'Grade 10',
                        'guardians_name' => 'Jane Doe',
                        'guardians_contact' => '09123456789',
                        'adviser' => 'Mr. Smith',
                        'section' => "10-$section",
                        'mother_name' => 'Jane Doe',
                        'academic_year' => $academicYear,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'lastname' => 'Smith',
                        'firstname' => 'Alice',
                        'middlename' => 'B',
                        'sex' => 'Female',
                        'address' => '456 Elm St',
                        'level_to_be_enrolled' => 'Grade 11',
                        'guardians_name' => 'Robert Smith',
                        'guardians_contact' => '09876543210',
                        'adviser' => 'Mrs. Johnson',
                        'section' => "11-$section",
                        'mother_name' => 'Susan Smith',
                        'academic_year' => $academicYear,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'lastname' => 'Johnson',
                        'firstname' => 'Emily',
                        'middlename' => 'C',
                        'sex' => 'Female',
                        'address' => '789 Oak St',
                        'level_to_be_enrolled' => 'Grade 12',
                        'guardians_name' => 'Michael Johnson',
                        'guardians_contact' => '09198765432',
                        'adviser' => 'Ms. Davis',
                        'section' => "12-$section",
                        'mother_name' => 'Sarah Johnson',
                        'academic_year' => $academicYear,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'lastname' => 'Brown',
                        'firstname' => 'David',
                        'middlename' => 'D',
                        'sex' => 'Male',
                        'address' => '321 Pine St',
                        'level_to_be_enrolled' => 'Grade 10',
                        'guardians_name' => 'Laura Brown',
                        'guardians_contact' => '09234567890',
                        'adviser' => 'Mr. Wilson',
                        'section' => "10-$section",
                        'mother_name' => 'Laura Brown',
                        'academic_year' => $academicYear,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'lastname' => 'Wilson',
                        'firstname' => 'Sophia',
                        'middlename' => 'E',
                        'sex' => 'Female',
                        'address' => '654 Maple St',
                        'level_to_be_enrolled' => 'Grade 11',
                        'guardians_name' => 'Daniel Wilson',
                        'guardians_contact' => '09345678901',
                        'adviser' => 'Mrs. Thompson',
                        'section' => "11-$section",
                        'mother_name' => 'Emily Wilson',
                        'academic_year' => $academicYear,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'lastname' => 'Taylor',
                        'firstname' => 'Michael',
                        'middlename' => 'F',
                        'sex' => 'Male',
                        'address' => '987 Birch St',
                        'level_to_be_enrolled' => 'Grade 12',
                        'guardians_name' => 'Jessica Taylor',
                        'guardians_contact' => '09456789012',
                        'adviser' => 'Mr. Brown',
                        'section' => "12-$section",
                        'mother_name' => 'Jessica Taylor',
                        'academic_year' => $academicYear,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }
        }
    }
}
