<?php

namespace App\Http\Controllers\Ps;

use App\Http\Controllers\Controller;
use App\Models\ProjectType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ProjectTypeController extends Controller
{
    /**
     * Show Single View Page for Project Types
     */
    public function index(Request $request)
    {
        $data['title'] = 'PS - Project Types Management';
        return view('ps.view_project_type', $data);
    }

    /**
     * Datatable AJAX Endpoint
     */
    public function datatable(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $types = ProjectType::where('company_id', $companyId);

        return DataTables::of($types)
            ->addColumn('type_name_col', function ($row) {
                return '<div class="fw-bold text-dark">' . e($row->type_name) . '</div>';
            })
            ->addColumn('status_badge', function ($row) {
                $checked = (int)$row->status === 1 ? 'checked' : '';
                return '<div class="form-check form-switch d-flex justify-content-center align-items-center mb-0">
                    <input class="form-check-input status-toggle" type="checkbox" data-id="' . $row->id . '" ' . $checked . ' style="cursor:pointer; width:38px; height:20px;">
                </div>';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="d-flex align-items-center justify-content-center gap-1">';
                // Edit Button triggers modal
                $html .= '<button type="button" class="btn btn-sm btn-outline-primary rounded-circle editTypeBtn" data-id="' . $row->id . '" data-name="' . e($row->type_name) . '" title="Edit" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-pencil fs-6"></i></button>';
                // Delete Button triggers modal
                $html .= '<button type="button" class="btn btn-sm btn-outline-danger rounded-circle deleteTypeBtn" data-id="' . $row->id . '" title="Delete" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-trash fs-6"></i></button>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['type_name_col', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Store New Project Type via AJAX
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        $companyId = auth()->user()->company_id;

        try {
            ProjectType::create([
                'company_id' => $companyId,
                'type_name'  => trim($request->type_name),
                'status'     => 1,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Project type added successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to add project type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Project Type via AJAX
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        $companyId = auth()->user()->company_id;
        $projectType = ProjectType::where('company_id', $companyId)->findOrFail($id);

        try {
            $projectType->update([
                'type_name' => trim($request->type_name),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Project type updated successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update project type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Status Toggle via AJAX
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'id'     => 'required|integer|exists:project_types,id',
            'status' => 'required|in:0,1',
        ]);

        $companyId = auth()->user()->company_id;
        $projectType = ProjectType::where('company_id', $companyId)->findOrFail($request->id);

        $projectType->status = (int)$request->status;
        $projectType->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Status updated successfully.',
        ]);
    }

    /**
     * Delete Project Type via AJAX
     */
    public function destroy(int $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $projectType = ProjectType::where('company_id', $companyId)->findOrFail($id);

        try {
            $projectType->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Project type deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete project type: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export Project Types to PDF
     */
    public function exportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $projectTypes = ProjectType::where('company_id', $companyId)->get();

        $company = Company::find($companyId);

        $data = [
            'projectTypes' => $projectTypes,
            'company_name' => $company ? $company->company_name : 'OMSystem Portal',
            'company_logo' => $company ? $company->company_logo : 'assets/img/main-logo.png'
        ];

        $pdf = Pdf::loadView('ps.pdf.project_types_list', $data);
        return $pdf->download('Project_Types_' . date('Ymd_His') . '.pdf');
    }
}
