<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWageLog extends Model
{
    use HasFactory;

    protected $table = 'user_wage_logs';

    protected $fillable = [
        'user_id',
        'company_id',
        'old_wage',
        'new_wage',
        'changed_by',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Admin / Changer (optional)
    public function admin()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}