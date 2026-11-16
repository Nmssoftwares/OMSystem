<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Models\PettyCashTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PettyCashController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Petty Cash - Labour Advances & Kharcha';
        return view('pc.view_petty_cash', $data);
    }

    public function datatable(Request $request)
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
                return '<div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle deleteTxBtn" data-id="' . $row->id . '" title="Delete Entry" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-trash fs-6"></i></button>
                </div>';
            })
            ->rawColumns(['worker_name', 'formatted_amount', 'type_badge', 'formatted_date', 'given_by_admin', 'action'])
            ->make(true);
    }
}
