@extends('um.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm position-relative" style="border-radius: 12px;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="userBanksTable">
                        <thead>
                            <tr>
                                <th>User Info</th>
                                <th>Holder Name</th>
                                <th>Bank Details</th>
                                <th>UPI Information</th>
                                <th class="text-center">Proof</th>
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
<button type="button" title="Add Bank Details" class="floating-action-btn floating-btn-add border-0" data-bs-toggle="modal" data-bs-target="#addBankModal" style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
    <i class="bi bi-plus-lg"></i>
</button>

<!-- ADD MODAL -->
<div class="modal fade" id="addBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="addBankForm" novalidate enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-bank me-1 text-primary"></i> Add User Bank & UPI Details</h5>
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
                                <select name="submission_type" id="add_submission_type" class="form-select submission-type-select" required>
                                    <option value="both">Both (Bank & UPI)</option>
                                    <option value="bank">Bank Details Only</option>
                                    <option value="upi">UPI Details Only</option>
                                </select>
                                <label class="form-label-joint">Submission Type*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_submission_type_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control bank-req" name="account_holder_name" id="add_account_holder_name" placeholder=" ">
                                <label class="form-label-joint">Account Holder Name <span class="bank-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_account_holder_name_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control bank-req" name="bank_name" id="add_bank_name" placeholder=" ">
                                <label class="form-label-joint">Bank Name <span class="bank-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_bank_name_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control bank-req" name="account_number" id="add_account_number" placeholder=" ">
                                <label class="form-label-joint">Account Number <span class="bank-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_account_number_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control bank-req" name="ifsc_code" id="add_ifsc_code" placeholder=" " maxlength="11">
                                <label class="form-label-joint">IFSC Code <span class="bank-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_ifsc_code_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" name="branch_name" id="add_branch_name" placeholder=" ">
                                <label class="form-label-joint">Branch Name</label>
                            </div>
                        </div>
                        <div class="col-md-6 upi-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" name="upi_number" id="add_upi_number" placeholder=" ">
                                <label class="form-label-joint">UPI Number (Mobile)</label>
                            </div>
                        </div>
                        <div class="col-md-12 upi-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control upi-req" name="upi_id" id="add_upi_id" placeholder=" ">
                                <label class="form-label-joint">UPI ID (VPA) <span class="upi-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="add_upi_id_error"></small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small mb-1">Passbook / Cancelled Cheque (Image or PDF)</label>
                            <input type="file" class="form-control" name="bank_proof" id="add_bank_proof" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-danger error mt-1 d-block" id="add_bank_proof_error"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="saveBankBtn">Save Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT MODAL (Similar Structure with prefix edit_) -->
<div class="modal fade" id="editBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="editBankForm" novalidate enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_bank_id">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-pencil-square me-1 text-primary"></i> Edit Bank & UPI Details</h5>
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
                                <select name="submission_type" id="edit_submission_type" class="form-select submission-type-select" required>
                                    <option value="both">Both (Bank & UPI)</option>
                                    <option value="bank">Bank Details Only</option>
                                    <option value="upi">UPI Details Only</option>
                                </select>
                                <label class="form-label-joint">Submission Type*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_submission_type_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control bank-req" name="account_holder_name" id="edit_account_holder_name" placeholder=" ">
                                <label class="form-label-joint">Account Holder Name <span class="bank-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_account_holder_name_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control bank-req" name="bank_name" id="edit_bank_name" placeholder=" ">
                                <label class="form-label-joint">Bank Name <span class="bank-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_bank_name_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control bank-req" name="account_number" id="edit_account_number" placeholder=" ">
                                <label class="form-label-joint">Account Number <span class="bank-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_account_number_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control bank-req" name="ifsc_code" id="edit_ifsc_code" placeholder=" " maxlength="11">
                                <label class="form-label-joint">IFSC Code <span class="bank-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_ifsc_code_error"></small>
                        </div>
                        <div class="col-md-6 bank-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" name="branch_name" id="edit_branch_name" placeholder=" ">
                                <label class="form-label-joint">Branch Name</label>
                            </div>
                        </div>
                        <div class="col-md-6 upi-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" name="upi_number" id="edit_upi_number" placeholder=" ">
                                <label class="form-label-joint">UPI Number</label>
                            </div>
                        </div>
                        <div class="col-md-12 upi-field-wrap">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control upi-req" name="upi_id" id="edit_upi_id" placeholder=" ">
                                <label class="form-label-joint">UPI ID (VPA) <span class="upi-star">*</span></label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="edit_upi_id_error"></small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small mb-1">Update Passbook / Cancelled Cheque (Optional)</label>
                            <input type="file" class="form-control" name="bank_proof" id="edit_bank_proof" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-danger error mt-1 d-block" id="edit_bank_proof_error"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="updateBankBtn">Update Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. DELETE MODAL -->
<div class="modal fade" id="deleteBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Are you sure?</h5>
                <p class="text-muted small mb-3">This bank record will be deleted permanently.</p>
                <input type="hidden" id="delete_bank_id">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="confirmDeleteBankBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let banksTable;

    $(document).ready(function() {
        banksTable = $('#userBanksTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('um.user-banks.datatable') }}",
            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                text: 'Excel',
                className: 'buttons-excel dt-button',
                exportOptions: {
                    columns: [0, 1, 2, 3],
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
                    window.location.href = "{{ route('um.user-banks.export.pdf') }}";
                }
            }],
            columns: [{
                    data: 'user_info',
                    name: 'user_id'
                },
                {
                    data: 'holder_name',
                    name: 'account_holder_name'
                },
                {
                    data: 'bank_info',
                    name: 'bank_name'
                },
                {
                    data: 'upi_info',
                    name: 'upi_id'
                },
                {
                    data: 'proof_col',
                    name: 'bank_proof',
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

        // Function to toggle required state based on submission type
        function handleSubmissionTypeChange(formPrefix) {
            let val = $('#' + formPrefix + '_submission_type').val();

            if (val === 'bank') {
                $('.bank-req').attr('required', true);
                $('.bank-star').show();
                $('.upi-req').removeAttr('required');
                $('.upi-star').hide();
            } else if (val === 'upi') {
                $('.bank-req').removeAttr('required');
                $('.bank-star').hide();
                $('.upi-req').attr('required', true);
                $('.upi-star').show();
            } else { // both
                $('.bank-req').attr('required', true);
                $('.bank-star').show();
                $('.upi-req').attr('required', true);
                $('.upi-star').show();
            }
        }

        $(document).on('change', '#add_submission_type', function() {
            handleSubmissionTypeChange('add');
        });

        $(document).on('change', '#edit_submission_type', function() {
            handleSubmissionTypeChange('edit');
        });

        // Add Form Submit with File Upload
        $('#addBankForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let btn = $('#saveBankBtn');
            btn.prop('disabled', true).text('Saving...');
            $('.error').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            $.ajax({
                url: "{{ route('um.user-banks.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Save Details');
                    $('#addBankModal').modal('hide');
                    $('#addBankForm')[0].reset();
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    banksTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Details');
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('#add_' + key + '_error').html(val[0]);
                            $('#add_' + key).addClass('is-invalid');
                        });
                    } else {
                        alert(xhr.responseJSON?.message || 'Error saving details.');
                    }
                }
            });
        });

        // Open Edit Modal
        $(document).on('click', '.editBankBtn', function() {
            $('#edit_bank_id').val($(this).data('id'));
            $('#edit_user_id').val($(this).data('user_id'));
            $('#edit_account_holder_name').val($(this).data('holder'));
            $('#edit_bank_name').val($(this).data('bank'));
            $('#edit_account_number').val($(this).data('account'));
            $('#edit_ifsc_code').val($(this).data('ifsc'));
            $('#edit_branch_name').val($(this).data('branch'));
            $('#edit_upi_number').val($(this).data('upino'));
            $('#edit_upi_id').val($(this).data('upiid'));
            $('#edit_bank_proof').val(''); // reset file input
            $('#editBankModal').modal('show');
        });

        // Edit Form Submit with File Upload
        $('#editBankForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_bank_id').val();
            let formData = new FormData(this);
            let btn = $('#updateBankBtn');
            btn.prop('disabled', true).text('Updating...');
            $('.error').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            $.ajax({
                url: "{{ url('um/user-banks/update') }}/" + id,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Update Details');
                    $('#editBankModal').modal('hide');
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    banksTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Update Details');
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('#edit_' + key + '_error').html(val[0]);
                            $('#edit_' + key).addClass('is-invalid');
                        });
                    } else {
                        alert(xhr.responseJSON?.message || 'Error updating details.');
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
                url: "{{ route('um.user-banks.update-status') }}",
                type: 'POST',
                data: {
                    id: id,
                    is_verified: is_verified,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    banksTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    toggle.prop('checked', !is_verified);
                    alert(xhr.responseJSON?.message || 'Error updating status.');
                }
            });
        });

        // Open Delete Modal
        $(document).on('click', '.deleteBankBtn', function() {
            $('#delete_bank_id').val($(this).data('id'));
            $('#deleteBankModal').modal('show');
        });

        // Confirm Delete
        $('#confirmDeleteBankBtn').on('click', function() {
            let id = $('#delete_bank_id').val();
            let btn = $(this);
            btn.prop('disabled', true).text('Deleting...');

            $.ajax({
                url: "{{ url('um/user-banks/delete') }}/" + id,
                type: "POST",
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#deleteBankModal').modal('hide');
                    if (typeof showAlert === 'function') showAlert('success', response.message);
                    banksTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#deleteBankModal').modal('hide');
                    alert(xhr.responseJSON?.message || 'Error deleting record.');
                }
            });
        });
    });
</script>
@endsection