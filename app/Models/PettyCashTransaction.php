<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashTransaction extends Model
{
    use HasFactory;

    protected $table = 'petty_cash_transactions';

    protected $fillable = [
        'company_id',
        'user_id',
        'amount',
        'transaction_type',
        'notes',
        'given_by',
        'transaction_date',
    ];

    // Relationship with Worker / User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with Admin who gave the cash
    public function admin()
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    // Relationship with Company
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
