<?php

namespace App\Http\Controllers\Um;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Company;

class UserDocumentController extends Controller
{
    /**
     * Show User Documents Listing View
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $data['title'] = 'User Management - Official Documents';
        $data['users'] = User::where('company_id', $companyId)
            ->orderBy('first_name', 'asc')
            ->get();
        $data['documentTypes'] = UserDocument::getDocumentTypes();
        return view('um.view_user_documents', $data);
    }

    /**
     * Datatable AJAX Endpoint
     */
    public function datatable(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $documents = UserDocument::with('user')->where('company_id', $companyId);

        return DataTables::of($documents)
            ->addColumn('user_info', function ($row) {
                $userName = trim(($row->user->first_name ?? '') . ' ' . ($row->user->last_name ?? '')) ?: ($row->user->name ?? 'NA');
                $phone = $row->user->phone ?? 'No Phone';
                return '<div class="fw-bold text-dark">' . e($userName) . '</div>
                        <small class="text-muted"><i class="bi bi-telephone me-1"></i>' . e($phone) . '</small>';
            })
            ->addColumn('doc_type_name', fn($row) => '<span class="badge bg-light text-dark border">' . e($row->document_type_name) . '</span>')
            ->addColumn('doc_number', fn($row) => '<span class="fw-semibold">' . e($row->document_number) . '</span>')
            ->addColumn('front_proof', function ($row) {
                if ($row->document_front_path) {
                    $url = asset('storage/' . $row->document_front_path);
                    return '<div class="d-flex justify-content-center align-items-center">
                                <a href="' . $url . '" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" title="View Front Proof" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-eye fs-6"></i></a>
                            </div>';
                }
                return '<div class="text-center"><span class="text-muted small">N/A</span></div>';
            })
            ->addColumn('back_proof', function ($row) {
                if ($row->document_back_path) {
                    $url = asset('storage/' . $row->document_back_path);
                    return '<div class="d-flex justify-content-center align-items-center">
                                <a href="' . $url . '" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" title="View Back Proof" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-eye fs-6"></i></a>
                            </div>';
                }
                return '<div class="text-center"><span class="text-muted small">N/A</span></div>';
            })
            ->addColumn('status_badge', function ($row) {
                $checked = (int)$row->is_verified === 1 ? 'checked' : '';
                return '<div class="form-check form-switch d-flex justify-content-center align-items-center mb-0">
                    <input class="form-check-input verify-toggle" type="checkbox" data-id="' . $row->id . '" ' . $checked . ' style="cursor:pointer; width:38px; height:20px;" title="Toggle Verification">
                </div>';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="d-flex align-items-center justify-content-center gap-1">';
                $html .= '<button type="button" class="btn btn-sm btn-outline-primary rounded-circle editDocBtn" 
                            data-id="' . $row->id . '" 
                            data-user_id="' . $row->user_id . '"
                            data-doctype="' . $row->document_type . '"
                            data-docnum="' . e($row->document_number) . '"
                            title="Edit" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-pencil fs-6"></i></button>';
                $html .= '<button type="button" class="btn btn-sm btn-outline-danger rounded-circle deleteDocBtn" data-id="' . $row->id . '" title="Delete" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-trash fs-6"></i></button>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['user_info', 'doc_type_name', 'doc_number', 'front_proof', 'back_proof', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Store Document via AJAX
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'             => 'required|integer|exists:users,id',
            'document_type'       => 'required|integer|in:1,2,3,4,5,6',
            'document_number'     => 'required|string|max:100',
            'document_front_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'document_back_path'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'user_id.required'         => 'Please select a user.',
            'document_type.required'   => 'Please select document type.',
            'document_number.required' => 'Document number is required.',
        ]);

        $companyId = auth()->user()->company_id;
        $userId = $request->user_id;
        $frontPath = null;
        $backPath = null;

        $folderPath = "{$companyId}/um/documents";

        if ($request->hasFile('document_front_path')) {
            $frontPath = $request->file('document_front_path')->store($folderPath, 'public');
        }

        if ($request->hasFile('document_back_path')) {
            $backPath = $request->file('document_back_path')->store($folderPath, 'public');
        }

        try {
            UserDocument::create([
                'company_id'          => $companyId,
                'user_id'             => $userId,
                'document_type'       => $request->document_type,
                'document_number'     => trim($request->document_number),
                'document_front_path' => $frontPath,
                'document_back_path'  => $backPath,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Document uploaded successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Document via AJAX
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $document = UserDocument::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'user_id'             => 'required|integer|exists:users,id',
            'document_type'       => 'required|integer|in:1,2,3,4,5,6',
            'document_number'     => 'required|string|max:100',
            'document_front_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'document_back_path'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $userId = $request->user_id;
        $folderPath = "{$companyId}/um/documents";

        $frontPath = $document->document_front_path;
        if ($request->hasFile('document_front_path')) {
            if ($frontPath && Storage::disk('public')->exists($frontPath)) {
                Storage::disk('public')->delete($frontPath);
            }
            $frontPath = $request->file('document_front_path')->store($folderPath, 'public');
        }

        $backPath = $document->document_back_path;
        if ($request->hasFile('document_back_path')) {
            if ($backPath && Storage::disk('public')->exists($backPath)) {
                Storage::disk('public')->delete($backPath);
            }
            $backPath = $request->file('document_back_path')->store($folderPath, 'public');
        }

        try {
            $document->update([
                'user_id'             => $userId,
                'document_type'       => $request->document_type,
                'document_number'     => trim($request->document_number),
                'document_front_path' => $frontPath,
                'document_back_path'  => $backPath,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Document updated successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Verification Status Toggle
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'id'          => 'required|integer|exists:user_documents,id',
            'is_verified' => 'required|in:0,1',
        ]);

        $companyId = auth()->user()->company_id;
        $document = UserDocument::where('company_id', $companyId)->findOrFail($request->id);

        $document->is_verified = (int)$request->is_verified;
        $document->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Verification status updated successfully.',
        ]);
    }

    /**
     * Delete Document Record
     */
    public function destroy(int $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $document = UserDocument::where('company_id', $companyId)->findOrFail($id);

        try {
            if ($document->document_front_path && Storage::disk('public')->exists($document->document_front_path)) {
                Storage::disk('public')->delete($document->document_front_path);
            }
            if ($document->document_back_path && Storage::disk('public')->exists($document->document_back_path)) {
                Storage::disk('public')->delete($document->document_back_path);
            }
            $document->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Document deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export User Documents to PDF
     */
    public function exportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $documents = UserDocument::with('user')->where('company_id', $companyId)->get();
        $company = Company::find($companyId);

        $data = [
            'documents'    => $documents,
            'company_name' => $company ? $company->company_name : 'OMSystem Portal',
            'company_logo' => $company ? $company->company_logo : 'assets/img/main-logo.png'
        ];

        $pdf = Pdf::loadView('um.pdf.user_documents_list', $data);
        return $pdf->download('User_Documents_' . date('Ymd_His') . '.pdf');
    }
}
