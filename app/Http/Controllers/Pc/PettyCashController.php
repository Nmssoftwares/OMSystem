<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Models\PettyCashTransaction;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PettyCashController extends Controller
{
    /**
     * Show Single View Page for Petty Cash / Labour Kharcha
     */
    public function index(Request $request)
    {
        $data['title'] = 'Petty Cash - Labour Advances & Kharcha';
        return view('pc.view_petty_cash', $data);
    }

    /**
     * Datatable AJAX Endpoint
     */
    public function datatable(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $transactions = PettyCashTransaction::with(['user', 'admin'])
            ->where('company_id', $companyId);

        return DataTables::of($transactions)
            ->addColumn('worker_name', function ($row) {
                $name = $row->user ? trim($row->user->first_name . ' ' . ($row->user->last_name ?? '')) : 'General Expense';
                return '<div class="fw-bold text-dark">' . e($name) . '</div>';
            })
            ->addColumn('formatted_amount', function ($row) {
                return '<span class="fw-bold text-success">₹ ' . number_format($row->amount ?? 0, 0) . '</span>';
            })
            ->addColumn('type_badge', function ($row) {
                if ($row->transaction_type == 1) {
                    return '<span class="badge bg-primary">Advance</span>';
                } elseif ($row->transaction_type == 2) {
                    return '<span class="badge bg-warning text-dark">Daily Kharcha</span>';
                }
                return '<span class="badge bg-secondary">Other</span>';
            })
            ->addColumn('formatted_date', function ($row) {
                return $row->transaction_date ? date('d-M-Y', strtotime($row->transaction_date)) : '-';
            })
            ->addColumn('given_by_admin', function ($row) {
                return $row->admin ? e($row->admin->first_name) : 'System';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="d-flex align-items-center justify-content-center gap-1">';
                $html .= '<a href="' . route('pc.transactions.edit', $row->id) . '" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-pencil fs-6"></i></a>';
                $html .= '<button type="button" class="btn btn-sm btn-outline-danger rounded-circle deleteTxBtn" data-id="' . $row->id . '" title="Delete" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-trash fs-6"></i></button>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['worker_name', 'formatted_amount', 'type_badge', 'formatted_date', 'given_by_admin', 'action'])
            ->make(true);
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $data['title'] = 'Record Petty Cash / Advance';
        // Role 3 generally workers/labour ke liye hota hai aapke system me
        $data['workers'] = User::where('company_id', $companyId)
            ->where('role', 3)
            ->where('status', 1)
            ->get();

        return view('pc.add_transaction', $data);
    }

    /**
     * Store New Petty Cash Transaction via AJAX
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'          => 'required|integer|exists:users,id',
            'amount'           => 'required|numeric|min:1',
            'transaction_type' => 'required|in:1,2,3',
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string|max:500',
        ]);

        $companyId = auth()->user()->company_id;

        try {
            PettyCashTransaction::create([
                'company_id'       => $companyId,
                'user_id'          => $request->user_id,
                'amount'           => (int) $request->amount,
                'transaction_type' => $request->transaction_type,
                'notes'            => $request->notes,
                'given_by'         => auth()->id(),
                'transaction_date' => $request->transaction_date,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Petty cash transaction recorded successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to record transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Edit Petty Cash Transaction Page
     */
    public function edit(int $id)
    {
        $companyId = auth()->user()->company_id;

        $transaction = PettyCashTransaction::where('company_id', $companyId)->findOrFail($id);

        $data['title'] = 'Edit Petty Cash / Advance';
        $data['transaction'] = $transaction;
        $data['workers'] = User::where('company_id', $companyId)
            ->where('role', 3)
            ->where('status', 1)
            ->get();

        return view('pc.edit_transaction', $data);
    }

    /**
     * Update Petty Cash Transaction via AJAX
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'user_id'          => 'required|integer|exists:users,id',
            'amount'           => 'required|numeric|min:1',
            'transaction_type' => 'required|in:1,2,3',
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string|max:500',
        ]);

        $companyId = auth()->user()->company_id;
        $transaction = PettyCashTransaction::where('company_id', $companyId)->findOrFail($id);

        try {
            $transaction->update([
                'user_id'          => $request->user_id,
                'amount'           => (int) $request->amount,
                'transaction_type' => $request->transaction_type,
                'notes'            => $request->notes,
                'transaction_date' => $request->transaction_date,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Petty cash transaction updated successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Petty Cash Transaction via AJAX
     */
    public function destroy(int $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $transaction = PettyCashTransaction::where('company_id', $companyId)->findOrFail($id);

        try {
            $transaction->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Cash entry deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete entry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Petty Cash Transactions to PDF
     */
    public function exportPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $transactions = PettyCashTransaction::with(['user', 'admin'])
            ->where('company_id', $companyId)
            ->latest()
            ->get();

        $company = Company::find($companyId);

        $data = [
            'transactions' => $transactions,
            'company_name' => $company ? $company->company_name : 'OMSystem Portal',
            'company_logo' => $company ? $company->company_logo : 'assets/img/main-logo.png'
        ];

        $pdf = Pdf::loadView('pc.pdf.transactions_list', $data);
        return $pdf->download('Petty_Cash_' . date('Ymd_His') . '.pdf');
    }
}
