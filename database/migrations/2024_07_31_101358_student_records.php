<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_records', function (Blueprint $table) {
            $table->id();
            $table->string('student_ID');
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename');
            $table->string('birthdate');
            $table->string('sex');
            $table->string('address');
            $table->string('subject_ID')->nullable();
            $table->string('section_ID')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
