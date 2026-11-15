<?php

namespace App\Http\Controllers\Ps;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $data['title'] = 'Project Setup - Dashboard';
        
        // Metrics calculations
        $data['totalProjects'] = Project::where('company_id', $companyId)->count();
        $data['activeProjects'] = Project::where('company_id', $companyId)->where('status', 1)->count();
        $data['inactiveProjects'] = Project::where('company_id', $companyId)->where('status', 0)->count();
        
        // Recent projects list
        $data['recentProjects'] = Project::where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get();

        return view('ps.dashboard', $data);
    }
}