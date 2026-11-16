<?php

namespace App\Http\Controllers\Um;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBankDetail;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class UserBankController extends Controller
{
    /**
     * Show Users Banking Listing View
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $data['title'] = 'User Management - Bank & UPI Details';
        $data['users'] = User::where('company_id', $companyId)
            ->orderBy('first_name', 'asc')
            ->get();
        return view('um.view_user_bank', $data);
    }

    /**
     * Datatable AJAX Endpoint
     */
    public function datatable(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $banks = UserBankDetail::with('user')->where('company_id', $companyId);

        return DataTables::of($banks)
            ->addColumn('user_info', function ($row) {
                $userName = trim(($row->user->first_name ?? '') . ' ' . ($row->user->last_name ?? '')) ?: ($row->user->name ?? 'NA');
                $phone = $row->user->phone ?? 'No Phone';
                return '<div class="fw-bold text-dark">' . e($userName) . '</div>
                        <small class="text-muted"><i class="bi bi-telephone me-1"></i>' . e($phone) . '</small>';
            })
            ->addColumn('holder_name', fn($row) => e($row->account_holder_name ?? 'NA'))
            ->addColumn('bank_info', function ($row) {
                return '<div><strong>' . e($row->bank_name ?? 'NA') . '</strong><br>
                        <small class="text-muted">A/C: ' . e($row->account_number ?? 'NA') . '<br>
                        IFSC: ' . e($row->ifsc_code ?? 'NA') . '</small></div>';
            })
            ->addColumn('upi_info', function ($row) {
                return '<div>UPI ID: <strong>' . e($row->upi_id ?? 'NA') . '</strong><br>
                        <small class="text-muted">UPI No: ' . e($row->upi_number ?? 'NA') . '</small></div>';
            })
            ->addColumn('proof_col', function ($row) {
                if ($row->bank_proof) {
                    $url = asset('storage/' . $row->bank_proof);
                    return '<div class="d-flex justify-content-center align-items-center">
                                <a href="' . $url . '" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" title="View Proof" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-eye fs-6"></i></a>
                            </div>';
                }
                return '<div class="text-center"><span class="text-muted small">No Proof</span></div>';
            })
            ->addColumn('status_badge', function ($row) {
                $checked = (int)$row->is_verified === 1 ? 'checked' : '';
                return '<div class="form-check form-switch d-flex justify-content-center align-items-center mb-0">
                    <input class="form-check-input verify-toggle" type="checkbox" data-id="' . $row->id . '" ' . $checked . ' style="cursor:pointer; width:38px; height:20px;" title="Toggle Verification">
                </div>';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="d-flex align-items-center justify-content-center gap-1">';
                $html .= '<button type="button" class="btn btn-sm btn-outline-primary rounded-circle editBankBtn" 
                            data-id="' . $row->id . '" 
                            data-user_id="' . $row->user_id . '"
                            data-holder="' . e($row->account_holder_name) . '"
                            data-bank="' . e($row->bank_name) . '"
                            data-account="' . e($row->account_number) . '"
                            data-ifsc="' . e($row->ifsc_code) . '"
                            data-branch="' . e($row->branch_name) . '"
                            data-upino="' . e($row->upi_number) . '"
                            data-upiid="' . e($row->upi_id) . '"
                            title="Edit" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-pencil fs-6"></i></button>';
                $html .= '<button type="button" class="btn btn-sm btn-outline-danger rounded-circle deleteBankBtn" data-id="' . $row->id . '" title="Delete" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-trash fs-6"></i></button>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['user_info', 'bank_info', 'upi_info', 'proof_col', 'status_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'             => 'required|integer|exists:users,id',
            'submission_type'     => 'required|in:bank,upi,both',

            // Conditional rules based on submission_type
            'account_holder_name' => 'required_if:submission_type,bank,both|nullable|string|max:255',
            'bank_name'           => 'required_if:submission_type,bank,both|nullable|string|max:255',
            'account_number'      => 'required_if:submission_type,bank,both|nullable|string|max:50',
            'ifsc_code'           => 'required_if:submission_type,bank,both|nullable|string|size:11',

            'branch_name'         => 'nullable|string|max:255',
            'upi_number'          => 'nullable|string|max:15',
            'upi_id'              => 'required_if:submission_type,upi,both|nullable|string|max:255',
            'bank_proof'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'user_id.required'             => 'Please select a user.',
            'submission_type.required'     => 'Please select a submission type.',
            'account_holder_name.required_if' => 'Account holder name is required.',
            'bank_name.required_if'           => 'Bank name is required.',
            'account_number.required_if'      => 'Account number is required.',
            'ifsc_code.required_if'           => 'IFSC code is required.',
            'ifsc_code.size'                  => 'IFSC code must be exactly 11 characters.',
            'upi_id.required_if'              => 'UPI ID is required when selecting UPI details.',
        ]);

        $companyId = auth()->user()->company_id;
        $proofPath = null;

        if ($request->hasFile('bank_proof')) {
            $proofPath = $request->file('bank_proof')->store("{$companyId}/um/bank", 'public');
        }

        try {
            UserBankDetail::updateOrCreate(
                ['user_id' => $request->user_id],
                [
                    'company_id'          => $companyId,
                    'account_holder_name' => trim($request->account_holder_name),
                    'bank_name'           => trim($request->bank_name),
                    'account_number'      => trim($request->account_number),
                    'ifsc_code'           => strtoupper(trim($request->ifsc_code)),
                    'branch_name'         => trim($request->branch_name),
                    'upi_number'          => trim($request->upi_number),
                    'upi_id'              => trim($request->upi_id),
                    'bank_proof'          => $proofPath ?? UserBankDetail::where('user_id', $request->user_id)->value('bank_proof'),
                ]
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Bank & UPI details saved successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $bankDetail = UserBankDetail::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'user_id'             => 'required|integer|exists:users,id',
            'submission_type'     => 'required|in:bank,upi,both',

            'account_holder_name' => 'required_if:submission_type,bank,both|nullable|string|max:255',
            'bank_name'           => 'required_if:submission_type,bank,both|nullable|string|max:255',
            'account_number'      => 'required_if:submission_type,bank,both|nullable|string|max:50',
            'ifsc_code'           => 'required_if:submission_type,bank,both|nullable|string|size:11',

            'branch_name'         => 'nullable|string|max:255',
            'upi_number'          => 'nullable|string|max:15',
            'upi_id'              => 'required_if:submission_type,upi,both|nullable|string|max:255',
            'bank_proof'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'user_id.required'             => 'Please select a user.',
            'submission_type.required'     => 'Please select a submission type.',
            'account_holder_name.required_if' => 'Account holder name is required.',
            'bank_name.required_if'           => 'Bank name is required.',
            'account_number.required_if'      => 'Account number is required.',
            'ifsc_code.required_if'           => 'IFSC code is required.',
            'ifsc_code.size'                  => 'IFSC code must be exactly 11 characters.',
            'upi_id.required_if'              => 'UPI ID is required when selecting UPI details.',
        ]);

        $proofPath = $bankDetail->bank_proof;

        if ($request->hasFile('bank_proof')) {
            if ($proofPath && Storage::disk('public')->exists($proofPath)) {
                Storage::disk('public')->delete($proofPath);
            }
            $proofPath = $request->file('bank_proof')->store("{$companyId}/um/bank", 'public');
        }

        try {
            $bankDetail->update([
                'user_id'             => $request->user_id,
                'account_holder_name' => trim($request->account_holder_name),
                'bank_name'           => trim($request->bank_name),
                'account_number'      => trim($request->account_number),
                'ifsc_code'           => strtoupper(trim($request->ifsc_code)),
                'branch_name'         => trim($request->branch_name),
                'upi_number'          => trim($request->upi_number),
                'upi_id'              => trim($request->upi_id),
                'bank_proof'          => $proofPath,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Bank & UPI details updated successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Verification Status Toggle
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'id'          => 'required|integer|exists:user_bank_details,id',
            'is_verified' => 'required|in:0,1',
        ]);

        $companyId = auth()->user()->company_id;
        $bankDetail = UserBankDetail::where('company_id', $companyId)->findOrFail($request->id);

        $bankDetail->is_verified = (int)$request->is_verified;
        $bankDetail->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Verification status updated successfully.',
        ]);
    }

    /**
     * Delete Bank Detail Record
     */
    public function destroy(int $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $bankDetail = UserBankDetail::where('company_id', $companyId)->findOrFail($id);

        try {
            if ($bankDetail->bank_proof && Storage::disk('public')->exists($bankDetail->bank_proof)) {
                Storage::disk('public')->delete($bankDetail->bank_proof);
            }
            $bankDetail->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Bank details deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export User Bank Details to PDF
     */
    public function exportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $banks = UserBankDetail::with('user')->where('company_id', $companyId)->get();
        $company = Company::find($companyId);

        $data = [
            'banks'        => $banks,
            'company_name' => $company ? $company->company_name : 'OMSystem Portal',
            'company_logo' => $company ? $company->company_logo : 'assets/img/main-logo.png'
        ];

        $pdf = Pdf::loadView('um.pdf.user_banks_list', $data);
        return $pdf->download('User_Bank_Details_' . date('Ymd_His') . '.pdf');
    }
}
