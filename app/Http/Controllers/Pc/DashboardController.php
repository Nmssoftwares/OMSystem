<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $data['title'] = 'Petty Cash - Dashboard';
        return view('pc.dashboard', $data);
    }
}
