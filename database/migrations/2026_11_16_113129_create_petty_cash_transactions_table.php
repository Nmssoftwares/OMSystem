<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->decimal('amount', 10, 2);
            $table->tinyInteger('transaction_type')->default(1)->comment('1=Advance, 2=Daily Kharcha, 3=Other');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('given_by')->nullable();
            $table->date('transaction_date')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_transactions');
    }
};
