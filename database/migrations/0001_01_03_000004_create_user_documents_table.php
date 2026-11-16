<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('document_type')->comment('1: Aadhaar, 2: PAN, 3: Driving Licence, 4: Passport, 5: Voter ID, 6: Other');
            $table->string('document_number'); // e.g., PAN or Aadhaar card number
            $table->string('document_front_path')->nullable(); // Front image/PDF
            $table->string('document_back_path')->nullable();  // Back image/PDF (useful for Aadhaar/DL)
            $table->tinyInteger('is_verified')->default(0)->comment('0: Unverified, 1: Verified');
            $table->timestamps();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
