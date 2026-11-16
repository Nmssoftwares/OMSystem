@extends('um.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm position-relative" style="border-radius: 12px;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="userDocumentsTable">
                        <thead>
                            <tr>
                                <th>User Info</th>
                                <th>Document Type</th>
                                <th>Document Number</th>
                                <th class="text-center">Front Proof</th>
                                <th class="text-center">Back Proof</th>
                                <th class="text-center">Verified</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating Add Button -->
<button type="button" title="Add Document" class="floating-action-btn floating-btn-add border-0" data-bs-toggle="modal" data-bs-target="#addDocModal" style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
    <i class="bi bi-plus-lg"></i>
</button>

<!-- 1. ADD MODAL -->
<div class="modal fade" id="addDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="addDocForm" novalidate enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-file-earmark-text me-1 text-primary"></i> Upload User Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-floating-wrap">
                                <select name="user_id" id="add_user_id" class="form-select" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                    @php
                                    $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? 'User');
                                    $phoneStr = $user->phone ? ' - ' . $user->phone : '';
                                    @endphp
                                    <option value="{{ $user->id }}">{{ $fullName }}{{ $phoneStr }}</option>
                                    @endforeach
                                </select>
                                <label class="form-label-joint">Select User*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_user_id_error"></small>
                        </div>
                        <div class="col-md-6">
                            <div class="input-floating-wrap">
                                <select name="document_type" id="add_document_type" class="form-select" required>
                                    <option value="">Select Document Type</option>
                                    @foreach($documentTypes as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <label class="form-label-joint">Document Type*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_document_type_error"></small>
                        </div>
                        <div class="col-md-12">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" name="document_number" id="add_document_number" placeholder=" " required>
                                <label class="form-label-joint">Document Number* (e.g., Aadhaar No / PAN No)</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_document_number_error"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Front Image / PDF (Max 2MB)</label>
                            <input type="file" class="form-control" name="document_front_path" id="add_document_front_path" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-danger error mt-1 d-block" id="add_document_front_path_error"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Back Image / PDF (Optional, Max 2MB)</label>
                            <input type="file" class="form-control" name="document_back_path" id="add_document_back_path" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-danger error mt-1 d-block" id="add_document_back_path_error"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="saveDocBtn">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. EDIT MODAL -->
<div class="modal fade" id="editDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="editDocForm" novalidate enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_doc_id">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-pencil-square me-1 text-primary"></i> Edit User Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-floating-wrap">
                                <select name="user_id" id="edit_user_id" class="form-select" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                    @php
                                    $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? 'User');
                                    $phoneStr = $user->phone ? ' - ' . $user->phone : '';
                                    @endphp
                                    <option value="{{ $user->id }}">{{ $fullName }}{{ $phoneStr }}</option>
                                    @endforeach
                                </select>
                                <label class="form-label-joint">Select User*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_user_id_error"></small>
                        </div>
                        <div class="col-md-6">
                            <div class="input-floating-wrap">
                                <select name="document_type" id="edit_document_type" class="form-select" required>
                                    <option value="">Select Document Type</option>
                                    @foreach($documentTypes as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <label class="form-label-joint">Document Type*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_document_type_error"></small>
                        </div>
                        <div class="col-md-12">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" name="document_number" id="edit_document_number" placeholder=" " required>
                                <label class="form-label-joint">Document Number*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_document_number_error"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Update Front Proof (Optional)</label>
                            <input type="file" class="form-control" name="document_front_path" id="edit_document_front_path" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-danger error mt-1 d-block" id="edit_document_front_path_error"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Update Back Proof (Optional)</label>
                            <input type="file" class="form-control" name="document_back_path" id="edit_document_back_path" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-danger error mt-1 d-block" id="edit_document_back_path_error"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="updateDocBtn">Update Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. DELETE MODAL -->
<div class="modal fade" id="deleteDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Are you sure?</h5>
                <p class="text-muted small mb-3">This document record will be deleted permanently.</p>
                <input type="hidden" id="delete_doc_id">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="confirmDeleteDocBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let docsTable;

    $(document).ready(function() {
        docsTable = $('#userDocumentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('um.user-documents.datatable') }}",
            },
            columns: [{
                    data: 'user_info',
                    name: 'user_id'
                },
                {
                    data: 'doc_type_name',
                    name: 'document_type'
                },
                {
                    data: 'doc_number',
                    name: 'document_number'
                },
                {
                    data: 'front_proof',
                    name: 'document_front_path',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'back_proof',
                    name: 'document_back_path',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status_badge',
                    name: 'is_verified',
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

        // Clear error on input change
        $('input, select').on('input change', function() {
            $(this).removeClass('is-invalid');
            let nameAttr = $(this).attr('name');
            if (nameAttr) {
                $('#add_' + nameAttr + '_error').html('');
                $('#edit_' + nameAttr + '_error').html('');
            }
        });

        // Add Form Submit with File Upload
        $('#addDocForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let btn = $('#saveDocBtn');
            btn.prop('disabled', true).text('Uploading...');
            $('.error').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            $.ajax({
                url: "{{ route('um.user-documents.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Upload Document');
                    $('#addDocModal').modal('hide');
                    $('#addDocForm')[0].reset();
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    docsTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Upload Document');
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('#add_' + key + '_error').html(val[0]);
                            $('#add_' + key).addClass('is-invalid');
                        });
                    } else {
                        alert(xhr.responseJSON?.message || 'Error uploading document.');
                    }
                }
            });
        });

        // Open Edit Modal
        $(document).on('click', '.editDocBtn', function() {
            $('#edit_doc_id').val($(this).data('id'));
            $('#edit_user_id').val($(this).data('user_id'));
            $('#edit_document_type').val($(this).data('doctype'));
            $('#edit_document_number').val($(this).data('docnum'));
            $('#edit_document_front_path').val('');
            $('#edit_document_back_path').val('');
            $('#editDocModal').modal('show');
        });

        // Edit Form Submit with File Upload
        $('#editDocForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_doc_id').val();
            let formData = new FormData(this);
            let btn = $('#updateDocBtn');
            btn.prop('disabled', true).text('Updating...');
            $('.error').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            $.ajax({
                url: "{{ url('um/user-documents/update') }}/" + id,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Update Document');
                    $('#editDocModal').modal('hide');
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    docsTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Update Document');
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('#edit_' + key + '_error').html(val[0]);
                            $('#edit_' + key).addClass('is-invalid');
                        });
                    } else {
                        alert(xhr.responseJSON?.message || 'Error updating document.');
                    }
                }
            });
        });

        // Status Verification Toggle
        $(document).on('change', '.verify-toggle', function() {
            let toggle = $(this);
            let id = toggle.data('id');
            let is_verified = toggle.is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('um.user-documents.update-status') }}",
                type: 'POST',
                data: {
                    id: id,
                    is_verified: is_verified,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    docsTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    toggle.prop('checked', !is_verified);
                    alert(xhr.responseJSON?.message || 'Error updating status.');
                }
            });
        });

        // Open Delete Modal
        $(document).on('click', '.deleteDocBtn', function() {
            $('#delete_doc_id').val($(this).data('id'));
            $('#deleteDocModal').modal('show');
        });

        // Confirm Delete
        $('#confirmDeleteDocBtn').on('click', function() {
            let id = $('#delete_doc_id').val();
            let btn = $(this);
            btn.prop('disabled', true).text('Deleting...');

            $.ajax({
                url: "{{ url('um/user-documents/delete') }}/" + id,
                type: "POST",
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#deleteDocModal').modal('hide');
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    docsTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#deleteDocModal').modal('hide');
                    alert(xhr.responseJSON?.message || 'Error deleting record.');
                }
            });
        });
    });
</script>
@endsection