<?php

namespace App\Http\Controllers\Ps;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Show Projects Listing View
     */
    public function index(Request $request)
    {
        $data['title'] = 'PS - Projects Management';
        return view('ps.view_project', $data);
    }

    /**
     * Datatable AJAX Endpoint for Projects
     */
    public function datatable(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        $projects = Project::where('company_id', $companyId);

        return DataTables::of($projects)
            ->addColumn('project_info', function ($row) {
                $labelBadge = $row->project_label ? '<span class="badge bg-light text-primary border ms-1">' . e($row->project_label) . '</span>' : '';
                return '<div class="fw-bold text-dark">' . e($row->project_name) . $labelBadge . '</div>';
            })
            ->addColumn('owner_info', function ($row) {
                $ownerLabel = $row->owner_label ? '<br><small class="text-muted">Label: ' . e($row->owner_label) . '</small>' : '';
                return '<div>' . e($row->owner_name ?? 'NA') . $ownerLabel . '</div>';
            })
            ->addColumn('project_type_col', fn($row) => e($row->project_type ?? 'NA'))
            ->addColumn('address_col', fn($row) => e($row->project_address ?? 'NA'))
            ->addColumn('timeline', function ($row) {
                $start = $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d-m-Y') : '-';
                $end = $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('d-m-Y') : '-';
                return '<small class="text-muted">' . $start . ' <br>to ' . $end . '</small>';
            })
            ->addColumn('status_badge', function ($row) {
                return (int)$row->status === 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('share_action', function ($row) {
                return '<button type="button" class="btn btn-sm btn-link text-warning p-0" title="Share Project"><i class="bi bi-box-arrow-up-right fs-5"></i></button>';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="d-flex align-items-center justify-content-center gap-1">';
                // Edit Button
                $html .= '<a href="#" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-pencil fs-6"></i></a>';
                // Delete Button
                $html .= '<button type="button" class="btn btn-sm btn-outline-danger rounded-circle deleteProjectBtn" data-id="' . $row->id . '" title="Delete" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-trash fs-6"></i></button>';
                // Download / Report Button
                $html .= '<a href="#" class="btn btn-sm btn-outline-success rounded-circle" title="Download Report" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-download fs-6"></i></a>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['project_info', 'owner_info', 'timeline', 'status_badge', 'share_action', 'action'])
            ->make(true);
    }

    /**
     * Export Projects to PDF
     */
    public function exportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $projects = Project::where('company_id', $companyId)->get();

        $company = Company::find($companyId);

        $data = [
            'projects'     => $projects,
            'company_name' => $company ? $company->company_name : 'OMSystem Portal',
            'company_logo' => $company ? $company->company_logo : 'assets/img/main-logo.png'
        ];

        $pdf = Pdf::loadView('ps.pdf.projects_list', $data);
        return $pdf->download('Projects_List_' . date('Ymd_His') . '.pdf');
    }
}
