@extends('pc.layouts.app')

@section('content')
<section class="section">
   <!-- Top Statistics Cards -->
   <div class="row g-4 mb-4">
      <!-- Total Disbursed Amount -->
      <div class="col-xxl-4 col-md-6">
         <div class="card info-card sales-card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
               <h5 class="card-title text-muted small text-uppercase fw-bold">Total Disbursed (₹)</h5>
               <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px; font-size: 20px;">
                     <i class="bi bi-currency-rupee"></i>
                  </div>
                  <div class="ps-3">
                     <h4 class="fw-bold text-dark mb-0">{{ number_format($totalDisbursed ?? 0, 0) }}</h4>
                     <span class="text-success small pt-1 fw-semibold">Overall cash given</span>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Active Workers / Recipients -->
      <div class="col-xxl-4 col-md-6">
         <div class="card info-card revenue-card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
               <h5 class="card-title text-muted small text-uppercase fw-bold">Active Recipients</h5>
               <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px; font-size: 20px;">
                     <i class="bi bi-people-fill"></i>
                  </div>
                  <div class="ps-3">
                     <h4 class="fw-bold text-dark mb-0">{{ $activeWorkersCount ?? 0 }}</h4>
                     <span class="text-muted small pt-1">Workers with entries</span>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Total Transactions -->
      <div class="col-xxl-4 col-md-6">
         <div class="card info-card customers-card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
               <h5 class="card-title text-muted small text-uppercase fw-bold">Total Transactions</h5>
               <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning" style="width: 48px; height: 48px; font-size: 20px;">
                     <i class="bi bi-receipt"></i>
                  </div>
                  <div class="ps-3">
                     <h4 class="fw-bold text-dark mb-0">{{ $totalTransactions ?? 0 }}</h4>
                     <span class="text-muted small pt-1">Recorded entries</span>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Recent Transactions Table Preview -->
   <div class="row">
      <div class="col-12">
         <div class="card shadow-sm border-0 p-4" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Recent Petty Cash Transactions</h5>
               <a href="#" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">View All Entries</a>
            </div>
            <div class="table-responsive">
               <table class="table table-hover align-middle">
                  <thead class="table-light">
                     <tr>
                        <th>Worker / Title</th>
                        <th>Amount (₹)</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($recentTransactions ?? [] as $tx)
                     <tr>
                        <td class="fw-bold text-dark">{{ $tx->user->first_name ?? 'General Expense' }}</td>
                        <td><span class="text-success fw-bold">₹ {{ number_format($tx->amount ?? 0, 0) }}</span></td>
                        <td>{{ $tx->category ?: 'Labour Kharcha' }}</td>
                        <td>{{ $tx->created_at ? $tx->created_at->format('d-M-Y H:i') : '-' }}</td>
                        <td><span class="badge bg-success">Completed</span></td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="5" class="text-center text-muted py-4">No petty cash records found. Start by adding a new advance or expense!</td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>
@endsection