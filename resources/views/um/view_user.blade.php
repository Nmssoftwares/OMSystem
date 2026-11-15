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
<a href="{{ route('um.users.create', ['role' => $role ?? 3]) }}" title="Add New Account" class="floating-action-btn floating-btn-add">
   <i class="bi bi-plus-lg"></i>
</a>

<!-- 1. Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
         <form id="changePasswordForm">
            @csrf
            <input type="hidden" name="user_id" id="modal_user_id">
            <div class="modal-header bg-light">
               <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-key me-1 text-success"></i> Reset User Password</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
               <div class="mb-3">
                  <label class="form-label fw-semibold small">New Password*</label>
                  <input type="password" class="form-control" name="password" id="modal_password" placeholder="Min. 6 characters" required>
                  <small class="text-danger error mt-1 d-block" id="modal_password_error"></small>
               </div>
            </div>
            <div class="modal-footer bg-light">
               <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-success btn-sm px-4" id="savePasswordBtn">Update Password</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- 2. Manage Modules Modal -->
<div class="modal fade" id="manageModulesModal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow">
         <form id="manageModulesForm">
            @csrf
            <input type="hidden" name="user_id" id="module_user_id">
            <div class="modal-header bg-light">
               <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-gear me-1 text-primary"></i> Manage Assigned Modules</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
               <div id="modulesContainer" class="row g-3">
                  <!-- Dynamically Loaded Modules via AJAX -->
               </div>
               <small class="text-danger error mt-2 d-block" id="modal_modules_error"></small>
            </div>
            <div class="modal-footer bg-light">
               <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary btn-sm px-4" id="saveModulesBtn">Save Modules</button>
            </div>
         </form>
      </div>
   </div>
</div>
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
               if (typeof showAlert === 'function') showAlert('success', response.message);
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

      // 1. Open Change Password Modal
      $(document).on('click', '.changePasswordBtn', function() {
         let userId = $(this).data('userid');
         $('#modal_user_id').val(userId);
         $('#modal_password').val('');
         $('#modal_password_error').html('');
      });

      // Submit Password Change
      $('#changePasswordForm').on('submit', function(e) {
         e.preventDefault();
         let btn = $('#savePasswordBtn');
         btn.prop('disabled', true).text('Updating...');

         $.ajax({
            url: "{{ route('um.users.password.update') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
               btn.prop('disabled', false).text('Update Password');
               $('#changePasswordModal').modal('hide');
               if (typeof showAlert === 'function') showAlert('success', response.message);
            },
            error: function(xhr) {
               btn.prop('disabled', false).text('Update Password');
               if (xhr.status === 422 && xhr.responseJSON.errors?.password) {
                  $('#modal_password_error').text(xhr.responseJSON.errors.password[0]);
               } else {
                  alert(xhr.responseJSON?.message || 'Failed to update password.');
               }
            }
         });
      });

      // 2. Open Manage Modules Modal & Load Modules via AJAX
      $(document).on('click', '.manageModulesBtn', function() {
         let userId = $(this).data('userid');
         $('#module_user_id').val(userId);
         $('#modulesContainer').html('<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div> Loading modules...</div>');
         $('#modal_modules_error').html('');

         $.ajax({
            url: "{{ route('um.users.modules.get') }}",
            type: 'GET',
            data: {
               user_id: userId
            },
            success: function(response) {
               if (response.status === 'success') {
                  let html = '';
                  let grouped = response.groupedModules;
                  let userModules = response.userModules;

                  $.each(grouped, function(groupName, modulesList) {
                     html += `<div class="col-md-6">
                        <div class="card border shadow-none h-100" style="background: #fafbfe;">
                           <div class="card-header bg-light py-2 px-3 fw-bold small text-dark border-bottom">${groupName}</div>
                           <div class="card-body p-3 d-flex flex-column gap-2">`;

                     $.each(modulesList, function(index, mod) {
                        let isChecked = userModules.includes(mod.id) ? 'checked' : '';
                        html += `<div class="p-2 border rounded d-flex align-items-center justify-content-between bg-white">
                           <label class="form-check-label small fw-medium text-dark mb-0" for="modCheck${mod.id}" style="cursor: pointer;">
                              <i class="${mod.icon || 'bi bi-app'} text-primary me-1"></i> ${mod.name}
                           </label>
                           <div class="form-check mb-0">
                              <input class="form-check-input module-checkbox" type="checkbox" id="modCheck${mod.id}" value="${mod.id}" name="modules[]" ${isChecked}>
                           </div>
                        </div>`;
                     });

                     html += `</div></div></div>`;
                  });

                  $('#modulesContainer').html(html);
               }
            },
            error: function() {
               $('#modulesContainer').html('<div class="text-danger text-center py-3">Failed to load modules.</div>');
            }
         });
      });

      // Submit Manage Modules Form
      $('#manageModulesForm').on('submit', function(e) {
         e.preventDefault();
         let btn = $('#saveModulesBtn');
         btn.prop('disabled', true).text('Saving...');
         $('#modal_modules_error').html('');

         if ($('.module-checkbox:checked').length === 0) {
            $('#modal_modules_error').text('Please select at least one module.');
            btn.prop('disabled', false).text('Save Modules');
            return;
         }

         $.ajax({
            url: "{{ route('um.users.modules.update') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
               btn.prop('disabled', false).text('Save Modules');
               $('#manageModulesModal').modal('hide');
               if (typeof showAlert === 'function') showAlert('success', response.message);
            },
            error: function(xhr) {
               btn.prop('disabled', false).text('Save Modules');
               alert(xhr.responseJSON?.message || 'Failed to update modules.');
            }
         });
      });
   });
</script>
@endsection