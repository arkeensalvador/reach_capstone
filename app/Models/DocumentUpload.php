<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentUpload extends Model
{
    use HasFactory;

    protected $table = 'documents';
    protected $fillable = ['file_name', 'file_path', 'transaction_id'];

    // Define the inverse of the relationship
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id'); // Ensure 'transaction_id' is the correct foreign key
    }
}
