<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRecords extends Model
{
    use HasFactory;
    protected $table = 'student_records';
    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'sex',
        'address',
        'level_to_be_enrolled',
        'guardians_name',
        'guardians_contact',
        'section',
        'adviser',
        'mother_name',
        'academic_year',
    ];

    // Define the relationship with the Grades model
    public function grades()
    {
        return $this->hasMany(Grade::class, 'studentID');
    }
}
