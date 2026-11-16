<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_wage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedInteger('company_id')->index()->comment('Foreign reference to companies.id');
            $table->decimal('old_wage', 10, 2)->default(0.00);
            $table->decimal('new_wage', 10, 2)->default(0.00);
            $table->unsignedBigInteger('changed_by')->nullable()->comment('Admin user id who changed the wage');
            $table->timestamps();

            // Foreign key constraint for users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_wage_logs');
    }
};