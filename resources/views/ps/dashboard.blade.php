@extends('ps.layouts.app')

@section('content')
<section class="section">
   <!-- Top Statistics Cards -->
   <div class="row g-4 mb-4">
      <!-- Total Projects -->
      <div class="col-xxl-4 col-md-6">
         <div class="card info-card sales-card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
               <h5 class="card-title text-muted small text-uppercase fw-bold">Total Projects</h5>
               <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px; font-size: 20px;">
                     <i class="bi bi-buildings-fill"></i>
                  </div>
                  <div class="ps-3">
                     <h4 class="fw-bold text-dark mb-0">{{ $totalProjects }}</h4>
                     <span class="text-success small pt-1 fw-semibold">All registered sites</span>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Active Projects -->
      <div class="col-xxl-4 col-md-6">
         <div class="card info-card revenue-card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
               <h5 class="card-title text-muted small text-uppercase fw-bold">Active Projects</h5>
               <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px; font-size: 20px;">
                     <i class="bi bi-check-circle-fill"></i>
                  </div>
                  <div class="ps-3">
                     <h4 class="fw-bold text-dark mb-0">{{ $activeProjects }}</h4>
                     <span class="text-muted small pt-1">Currently in progress</span>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Inactive Projects -->
      <div class="col-xxl-4 col-md-6">
         <div class="card info-card customers-card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
               <h5 class="card-title text-muted small text-uppercase fw-bold">Inactive / Completed</h5>
               <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 48px; height: 48px; font-size: 20px;">
                     <i class="bi bi-archive-fill"></i>
                  </div>
                  <div class="ps-3">
                     <h4 class="fw-bold text-dark mb-0">{{ $inactiveProjects }}</h4>
                     <span class="text-danger small pt-1">Closed or on hold</span>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Recent Projects Table Preview -->
   <div class="row">
      <div class="col-12">
         <div class="card shadow-sm border-0 p-4" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Recent Projects Overview</h5>
               <a href="#" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">View All Projects</a>
            </div>
            <div class="table-responsive">
               <table class="table table-hover align-middle">
                  <thead class="table-light">
                     <tr>
                        <th>Project Name</th>
                        <th>Owner Name</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($recentProjects as $project)
                     <tr>
                        <td class="fw-bold text-dark">{{ $project->project_name }} <span class="badge bg-light text-primary border ms-1">{{ $project->project_label }}</span></td>
                        <td>{{ $project->owner_name ?: 'N/A' }}</td>
                        <td>{{ $project->project_type ?: 'General' }}</td>
                        <td>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') : '-' }}</td>
                        <td>
                           @if($project->status == 1)
                           <span class="badge bg-success">Active</span>
                           @else
                           <span class="badge bg-secondary">Inactive</span>
                           @endif
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="5" class="text-center text-muted py-4">No projects found. Start by adding a new project!</td>
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