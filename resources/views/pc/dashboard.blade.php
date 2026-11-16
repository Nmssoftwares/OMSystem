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
</section>
@endsection