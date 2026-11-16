<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Models\PettyCashTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $data['title'] = 'Petty Cash - Dashboard';
        
        // Metrics calculations (Aap apne table structure ke anusar inhe adjust kar sakte hain)
        $data['totalDisbursed'] = PettyCashTransaction::where('company_id', $companyId)->sum('amount') ?? 0;
        $data['activeWorkersCount'] = PettyCashTransaction::where('company_id', $companyId)->distinct('user_id')->count('user_id');
        $data['totalTransactions'] = PettyCashTransaction::where('company_id', $companyId)->count();
        
        // Recent Petty Cash / Labour Kharcha list
        $data['recentTransactions'] = PettyCashTransaction::with('user')
            ->where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get();

        return view('pc.dashboard', $data);
    }
}