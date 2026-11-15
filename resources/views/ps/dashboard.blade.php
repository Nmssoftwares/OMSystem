@extends('um.layouts.app')

@section('styles')
<style>
   /* App Launcher Grid & Tile Custom Styling */
   .sa-metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
   }

   .sa-metric-card {
      background: #fff;
      padding: 20px;
      border-radius: 14px;
      box-shadow: 0 4px 20px rgba(1, 41, 112, 0.05);
      display: flex;
      align-items: center;
      gap: 16px;
      border: 1px solid #eef2f7;
      transition: transform 0.2s ease;
   }

   .sa-metric-card:hover {
      transform: translateY(-2px);
   }

   .sa-metric-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
   }

   .sa-metric-val {
      font-size: 24px;
      font-weight: 700;
      color: #012970;
      line-height: 1.2;
   }

   .sa-metric-lbl {
      font-size: 13px;
      color: #6c757d;
   }

   .card-soft {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 4px 20px rgba(1, 41, 112, 0.05);
      border: 1px solid #eef2f7;
   }
</style>
@endsection

@section('content')
<!-- TOP METRIC CARDS -->
<div class="sa-metrics-grid mb-4">
   <div class="sa-metric-card">
      <div class="sa-metric-icon" style="background:#dcfce7; color:#16a34a;">
         <i class="bi bi-person-badge-fill"></i>
      </div>
      <div>
         <div class="sa-metric-val">{{ $totalAdmins }}</div>
         <div class="sa-metric-lbl">Company Admins</div>
      </div>
   </div>

   <div class="sa-metric-card">
      <div class="sa-metric-icon" style="background:#e0f2fe; color:#0284c7;">
         <i class="bi bi-people-fill"></i>
      </div>
      <div>
         <div class="sa-metric-val">{{ $totalUsers }}</div>
         <div class="sa-metric-lbl">Total Users</div>
      </div>
   </div>

   <div class="sa-metric-card">
      <div class="sa-metric-icon" style="background:#fef3c7; color:#d97706;">
         <i class="bi bi-person-check-fill"></i>
      </div>
      <div>
         <div class="sa-metric-val">{{ $activeUsersCount }}</div>
         <div class="sa-metric-lbl">Active Accounts</div>
      </div>
   </div>
</div>

<!-- ANALYTICS CHARTS SECTION -->
<div class="row g-4 mb-4">
   <!-- Chart 1: Role Distribution Donut Chart -->
   <div class="col-lg-6">
      <div class="card-soft p-4 h-100">
         <h5 class="card-title fs-6 fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill text-primary me-2"></i>User Role Breakdown</h5>
         <div style="position: relative; height: 260px; display: flex; justify-content: center; align-items: center;">
            <canvas id="roleDistributionChart"></canvas>
         </div>
      </div>
   </div>

   <!-- Chart 2: Account Status Bar Chart -->
   <div class="col-lg-6">
      <div class="card-soft p-4 h-100">
         <h5 class="card-title fs-6 fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Account Status Overview</h5>
         <div style="position: relative; height: 260px; display: flex; justify-content: center; align-items: center;">
            <canvas id="statusOverviewChart"></canvas>
         </div>
      </div>
   </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
      // Safe Blade to JS Variable Binding
      const totalAdmins = Number("{{ $totalAdmins ?? 0 }}");
      const totalUsers = Number("{{ $totalUsers ?? 0 }}");
      const activeUsersCount = Number("{{ $activeUsersCount ?? 0 }}");
      const inactiveUsersCount = Number("{{ $inactiveUsersCount ?? 0 }}");

      // 1. Role Distribution Donut Chart
      const ctxRole = document.getElementById('roleDistributionChart').getContext('2d');
      new Chart(ctxRole, {
         type: 'doughnut',
         data: {
            labels: ['Company Admins', 'Users'],
            datasets: [{
               data: [totalAdmins, totalUsers],
               backgroundColor: ['#16a34a', '#0284c7'],
               borderWidth: 2,
               borderColor: '#ffffff'
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
               legend: {
                  position: 'bottom',
                  labels: {
                     font: {
                        size: 12,
                        family: 'inherit'
                     },
                     boxWidth: 15
                  }
               }
            }
         }
      });

      // 2. Account Status Bar Chart
      const ctxStatus = document.getElementById('statusOverviewChart').getContext('2d');
      new Chart(ctxStatus, {
         type: 'bar',
         data: {
            labels: ['Active Accounts', 'Inactive Accounts'],
            datasets: [{
               label: 'Users Count',
               data: [activeUsersCount, inactiveUsersCount],
               backgroundColor: ['#334e68', '#94a3b8'],
               borderRadius: 6,
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
               legend: {
                  display: false
               }
            },
            scales: {
               y: {
                  beginAtZero: true,
                  ticks: {
                     precision: 0
                  }
               },
               x: {
                  grid: {
                     display: false
                  }
               }
            }
         }
      });
   });
</script>
@endsection