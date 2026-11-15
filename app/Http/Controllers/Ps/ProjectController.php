<?php

namespace App\Http\Controllers\Ps;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Company;
use App\Models\ProjectType;
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
        $data['title'] = 'Project Setup - View Projects';
        return view('ps.view_project', $data);
    }

    /**
     * Datatable AJAX Endpoint for Projects
     */
    public function datatable(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        // Eager load projectType relation
        $projects = Project::with('projectType')->where('company_id', $companyId);

        return DataTables::of($projects)
            ->addColumn('project_info', function ($row) {
                $labelBadge = $row->project_label ? '<span class="badge bg-light text-primary border ms-1">' . e($row->project_label) . '</span>' : '';
                return '<div class="fw-bold text-dark">' . e($row->project_name) . $labelBadge . '</div>';
            })
            ->addColumn('owner_info', function ($row) {
                $ownerLabel = $row->owner_label ? '<br><small class="text-muted">Label: ' . e($row->owner_label) . '</small>' : '';
                return '<div>' . e($row->owner_name ?? 'NA') . $ownerLabel . '</div>';
            })
            ->addColumn('project_type_col', function ($row) {
                return '<div class="fw-semibold text-secondary">' . e($row->projectType->type_name ?? 'NA') . '</div>';
            })
            ->addColumn('address_col', fn($row) => e($row->project_address ?? 'NA'))
            ->addColumn('timeline', function ($row) {
                $start = $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d-m-Y') : '-';
                $end = $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('d-m-Y') : '-';
                return '<small class="text-muted">' . $start . ' <br>to ' . $end . '</small>';
            })
            ->addColumn('status_badge', function ($row) {
                $checked = (int)$row->status === 1 ? 'checked' : '';
                return '<div class="form-check form-switch d-flex justify-content-center align-items-center mb-0">
                    <input class="form-check-input status-toggle" type="checkbox" data-id="' . $row->id . '" ' . $checked . ' style="cursor:pointer; width:38px; height:20px;">
                </div>';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="d-flex align-items-center justify-content-center gap-1">';
                $html .= '<a href="' . route('ps.projects.edit', $row->id) . '" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-pencil fs-6"></i></a>';
                $html .= '<button type="button" class="btn btn-sm btn-outline-danger rounded-circle deleteProjectBtn" data-id="' . $row->id . '" title="Delete" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-trash fs-6"></i></button>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['project_info', 'owner_info', 'project_type_col', 'timeline', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show Add Project Form
     */
    public function create()
    {
        $companyId = auth()->user()->company_id;

        $data['title']        = 'PS - Add New Project';
        $data['projectTypes'] = ProjectType::where('company_id', $companyId)
            ->where('status', 1)
            ->orderBy('type_name', 'asc')
            ->get();

        return view('ps.add_project', $data);
    }

    /**
     * Store New Project via AJAX
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'project_name'    => 'required|string|max:255',
            'project_label'   => 'nullable|string|max:50',
            'owner_name'      => 'nullable|string|max:255',
            'owner_label'     => 'nullable|string|max:50',
            'project_type_id' => 'nullable|integer|exists:project_types,id',
            'project_address' => 'nullable|string|max:500',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'status'          => 'required|in:0,1',
        ], [
            'project_name.required'   => 'Project name is required.',
            'end_date.after_or_equal' => 'End date must be a date after or equal to start date.',
        ]);

        $companyId = auth()->user()->company_id;

        try {
            Project::create([
                'company_id'      => $companyId,
                'project_name'    => $request->project_name,
                'project_label'   => $request->project_label ? strtoupper($request->project_label) : null,
                'owner_name'      => $request->owner_name,
                'owner_label'     => $request->owner_label,
                'project_type_id' => $request->project_type_id,
                'project_address' => $request->project_address,
                'start_date'      => $request->start_date,
                'end_date'        => $request->end_date,
                'status'          => $request->status,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Project created successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Edit Project Form
     */
    public function edit(int $id)
    {
        $companyId = auth()->user()->company_id;

        $data['title']        = 'PS - Edit Project';
        $data['project']      = Project::where('company_id', $companyId)->findOrFail($id);
        $data['projectTypes'] = ProjectType::where('company_id', $companyId)
            ->where('status', 1)
            ->orderBy('type_name', 'asc')
            ->get();

        return view('ps.edit_project', $data);
    }

    /**
     * Update Project via AJAX
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $project = Project::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'project_name'    => 'required|string|max:255',
            'project_label'   => 'nullable|string|max:50',
            'owner_name'      => 'nullable|string|max:255',
            'owner_label'     => 'nullable|string|max:50',
            'project_type_id' => 'nullable|integer|exists:project_types,id',
            'project_address' => 'nullable|string|max:500',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'status'          => 'required|in:0,1',
        ], [
            'project_name.required'   => 'Project name is required.',
            'end_date.after_or_equal' => 'End date must be a date after or equal to start date.',
        ]);

        try {
            $project->update([
                'project_name'    => $request->project_name,
                'project_label'   => $request->project_label ? strtoupper($request->project_label) : null,
                'owner_name'      => $request->owner_name,
                'owner_label'     => $request->owner_label,
                'project_type_id' => $request->project_type_id,
                'project_address' => $request->project_address,
                'start_date'      => $request->start_date,
                'end_date'        => $request->end_date,
                'status'          => $request->status,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Project updated successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Project via AJAX
     */
    public function destroy(int $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $project = Project::where('company_id', $companyId)->findOrFail($id);

        try {
            $project->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Project deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Project Status via AJAX Toggle
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'id'     => 'required|integer|exists:projects,id',
            'status' => 'required|in:0,1',
        ]);

        $companyId = auth()->user()->company_id;
        $project = Project::where('company_id', $companyId)->findOrFail($request->id);

        $project->status = (int)$request->status;
        $project->save();

        return response()->json([
            'status'  => 'success',
            'message' => "Project status updated successfully.",
        ]);
    }

    /**
     * Export Projects to PDF
     */
    public function exportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $projects = Project::with('projectType')->where('company_id', $companyId)->get();

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
