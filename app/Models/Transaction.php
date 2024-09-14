<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = "transactions"; // Ensure this matches your table name
    protected $primaryKey = 'transaction_ID';
    protected $fillable = [
        'user_id',
        'lname',
        'mname',
        'fname',
        'sex',
        'address',
        'doc_requested',
        'mother_name',
        'status'
    ];

     // Define the relationship with the Grades model
     public function documents()
     {
         return $this->hasMany(DocumentUpload::class, 'transaction_id');
     }
}
