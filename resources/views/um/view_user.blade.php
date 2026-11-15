@extends('um.layouts.app')

@section('content')
<section class="section">
   <div class="row">
      <div class="col-lg-12">
         <div class="card-soft p-4 bg-white shadow-sm">
            <div class="table-responsive">
               <table class="table table-hover align-middle w-100" id="usersTable">
                  <thead>
                     <tr>
                        <th>User Details</th>
                        <th>Company</th>
                        <th>Contact Info</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Floating Bottom-Right Add Button -->
<a href="{{ route('um.users.create') }}" title="Add New Account" class="floating-action-btn floating-btn-add">
   <i class="bi bi-plus-lg"></i>
</a>
@endsection

@section('scripts')
<script>
   let dataTable;

   $(document).ready(function() {
      dataTable = $('#usersTable').DataTable({
         processing: true,
         serverSide: true,
         ajax: {
            url: "{{ route('um.users.datatable') }}",
            data: {
               role: "{{ $role ?? 3 }}"
            }
         },
         columns: [{
               data: 'user_details',
               name: 'first_name'
            },
            {
               data: 'company_name',
               name: 'company_id',
               orderable: false,
               searchable: false
            },
            {
               data: 'contact_info',
               name: 'phone'
            },
            {
               data: 'status_badge',
               name: 'status',
               className: 'text-center'
            },
            {
               data: 'action',
               name: 'action',
               orderable: false,
               searchable: false,
               className: 'text-center'
            }
         ]
      });

      // Status Toggle AJAX
      $(document).on('change', '.status-toggle', function() {
         const toggle = $(this);
         const id = toggle.data('id');
         const status = toggle.is(':checked') ? 1 : 0;
         $('#nms-loader-overlay').addClass('active');

         $.ajax({
            url: "{{ route('um.users.update-status') }}",
            type: 'POST',
            data: {
               id: id,
               status: status,
               _token: '{{ csrf_token() }}'
            },
            success: function(response) {
               if (typeof showAlert === 'function') {
                  showAlert('success', response.message);
               }
               dataTable.ajax.reload(null, false);
               $('#nms-loader-overlay').removeClass('active');
            },
            error: function(xhr) {
               toggle.prop('checked', !status);
               $('#nms-loader-overlay').removeClass('active');
               alert(xhr.responseJSON?.message || 'Error updating status.');
            }
         });
      });
   });
</script>
@endsection