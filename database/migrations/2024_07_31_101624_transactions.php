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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_ID');
            $table->string('user_ID');
            $table->string('lname');
            $table->string('mname');
            $table->string('fname');
            $table->string('sex');
            $table->string('address');
            $table->string('doc_requested');
            $table->string('date_released')->nullable();
            $table->string('mother_name');
            $table->text('rejection_reason')->nullable();
            $table->boolean('status')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
