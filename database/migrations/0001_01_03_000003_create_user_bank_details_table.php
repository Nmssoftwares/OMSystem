<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_bank_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->string('account_holder_name');
            $table->string('bank_name');          
            $table->string('account_number');     
            $table->string('ifsc_code', 11);      
            $table->string('branch_name')->nullable();
            $table->string('upi_number', 15)->nullable();
            $table->string('upi_id')->nullable();
            $table->string('bank_proof')->nullable();
            $table->tinyInteger('is_verified')->default(0)->comment('0: Unverified, 1: Verified');
            $table->timestamps();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bank_details');
    }
};