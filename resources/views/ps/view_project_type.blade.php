@extends('ps.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm" style="border-radius: 12px;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="projectTypesTable">
                        <thead>
                            <tr>
                                <th>Project Type Name</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 1. ADD MODAL -->
<div class="modal fade" id="addTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="addTypeForm">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-plus-circle me-1 text-primary"></i> Add New Project Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Project Type Name*</label>
                        <input type="text" class="form-control" name="type_name" id="add_type_name" placeholder="e.g. Commercial, Warehouse" required>
                        <small class="text-danger error mt-1 d-block" id="add_type_name_error"></small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="saveTypeBtn">Save Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. EDIT MODAL -->
<div class="modal fade" id="editTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="editTypeForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_type_id">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-pencil-square me-1 text-primary"></i> Edit Project Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Project Type Name*</label>
                        <input type="text" class="form-control" name="type_name" id="edit_type_name" required>
                        <small class="text-danger error mt-1 d-block" id="edit_type_name_error"></small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="updateTypeBtn">Update Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. DELETE MODAL -->
<div class="modal fade" id="deleteTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Are you sure?</h5>
                <p class="text-muted small mb-3">This project type will be deleted permanently.</p>
                <input type="hidden" id="delete_type_id">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="confirmDeleteTypeBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Bottom-Right Add Button -->
<button type="button" title="Add New Project Type" class="floating-action-btn floating-btn-add border-0" data-bs-toggle="modal" data-bs-target="#addTypeModal" style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
    <i class="bi bi-plus-lg"></i>
</button>
@endsection

@section('scripts')
<script>
    let typesTable;

    $(document).ready(function() {
        // Initialize DataTable
        typesTable = $('#projectTypesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ps.project-types.datatable') }}",
            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                text: 'Excel',
                className: 'buttons-excel dt-button',
                exportOptions: {
                    columns: [0, 1],
                    format: {
                        body: function(data, row, column, node) {
                            return data ? data.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim() : '';
                        }
                    }
                }
            }, {
                text: 'PDF',
                className: 'buttons-pdf dt-button',
                action: function(e, dt, node, config) {
                    window.location.href = "{{ route('ps.project-types.export.pdf') }}";
                }
            }],
            columns: [{
                    data: 'type_name_col',
                    name: 'type_name'
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // 1. Add Type Form Submit
        $('#addTypeForm').on('submit', function(e) {
            e.preventDefault();
            let btn = $('#saveTypeBtn');
            btn.prop('disabled', true).text('Saving...');
            $('#add_type_name_error').text('');

            $.ajax({
                url: "{{ route('ps.project-types.store') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Save Type');
                    $('#addTypeModal').modal('hide');
                    $('#addTypeForm')[0].reset();
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    typesTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Type');
                    if (xhr.status === 422 && xhr.responseJSON.errors?.type_name) {
                        $('#add_type_name_error').text(xhr.responseJSON.errors.type_name[0]);
                    } else {
                        alert(xhr.responseJSON?.message || 'Error adding project type.');
                    }
                }
            });
        });

        // 2. Open Edit Modal & Populate Data
        $(document).on('click', '.editTypeBtn', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            $('#edit_type_id').val(id);
            $('#edit_type_name').val(name);
            $('#edit_type_name_error').text('');
            $('#editTypeModal').modal('show');
        });

        // Edit Type Form Submit
        $('#editTypeForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_type_id').val();
            let btn = $('#updateTypeBtn');
            btn.prop('disabled', true).text('Updating...');
            $('#edit_type_name_error').text('');

            $.ajax({
                url: "{{ url('ps/project-types/update') }}/" + id,
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Update Type');
                    $('#editTypeModal').modal('hide');
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    typesTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Update Type');
                    if (xhr.status === 422 && xhr.responseJSON.errors?.type_name) {
                        $('#edit_type_name_error').text(xhr.responseJSON.errors.type_name[0]);
                    } else {
                        alert(xhr.responseJSON?.message || 'Error updating project type.');
                    }
                }
            });
        });

        // 3. Status Toggle AJAX
        $(document).on('change', '.status-toggle', function() {
            let toggle = $(this);
            let id = toggle.data('id');
            let status = toggle.is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('ps.project-types.update-status') }}",
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    typesTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    toggle.prop('checked', !status);
                    alert(xhr.responseJSON?.message || 'Error updating status.');
                }
            });
        });

        // 4. Open Delete Modal
        $(document).on('click', '.deleteTypeBtn', function() {
            let id = $(this).data('id');
            $('#delete_type_id').val(id);
            $('#deleteTypeModal').modal('show');
        });

        // Confirm Delete AJAX
        $('#confirmDeleteTypeBtn').on('click', function() {
            let id = $('#delete_type_id').val();
            let btn = $(this);
            btn.prop('disabled', true).text('Deleting...');

            $.ajax({
                url: "{{ url('ps/project-types/delete') }}/" + id,
                type: "POST",
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#deleteTypeModal').modal('hide');
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    typesTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#deleteTypeModal').modal('hide');
                    alert(xhr.responseJSON?.message || 'Error deleting project type.');
                }
            });
        });
    });
</script>
@endsection