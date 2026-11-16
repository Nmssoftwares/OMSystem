@extends('um.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm position-relative">
                <form id="addUserForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <!-- 1. Account Information Section -->
                    <div class="section-title mt-0 mb-3 fw-bold text-dark" style="font-size: 15px;">
                        <i class="bi bi-person-fill text-primary me-1"></i> Account Information
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder=" " required>
                                <label class="form-label-joint">First Name*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="first_name_error"></small>
                        </div>
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder=" ">
                                <label class="form-label-joint">Last Name</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="last_name_error"></small>
                        </div>
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="email" class="form-control" id="email" name="email" placeholder=" " required>
                                <label class="form-label-joint">Email ID (Login ID)*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="email_error"></small>
                        </div>
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="phone" name="phone" maxlength="12" placeholder=" " required>
                                <label class="form-label-joint">Contact Number*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="phone_error"></small>
                        </div>
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <select name="gender" id="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Other</option>
                                </select>
                                <label class="form-label-joint">Gender</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="gender_error"></small>
                        </div>
                        <!-- Added Daily Wage Field -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="number" step="1" class="form-control" id="current_wage" name="current_wage" placeholder=" " required>
                                <label class="form-label-joint">Daily Wage (₹)*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="current_wage_error"></small>
                        </div>
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <select name="role" id="role" class="form-select" required>
                                    <option value="3" {{ old('role', $role ?? 3) == 3 ? 'selected' : '' }}>User</option>
                                    <option value="2" {{ old('role', $role ?? 3) == 2 ? 'selected' : '' }}>Admin</option>
                                </select>
                                <label class="form-label-joint">User Role*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="role_error"></small>
                        </div>
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="password" class="form-control" id="password" name="password" placeholder=" " required>
                                <label class="form-label-joint">Password (Min. 6 chars)*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="password_error"></small>
                        </div>
                    </div>

                    <!-- 2. Assign Modules Section (Company-wise) -->
                    @if(isset($groupedModules) && $groupedModules->isNotEmpty())
                    <div class="section-title mt-4 mb-3 fw-bold text-dark" style="font-size: 15px;">
                        <i class="bi bi-grid-3x3-gap-fill text-primary me-1"></i> 2. Assign Modules
                    </div>

                    <div class="row g-4 mb-3">
                        @foreach($groupedModules as $groupName => $modulesList)
                        <div class="col-md-4">
                            <div class="card border shadow-none h-100" style="border-radius: 8px; background: #fafbfe;">
                                <div class="card-header bg-light py-2 px-3 fw-bold small text-dark border-bottom">
                                    {{ $loop->iteration }}. {{ $groupName }}
                                </div>
                                <div class="card-body p-3 d-flex flex-column gap-2">
                                    @foreach($modulesList as $module)
                                    <div class="p-2 border rounded d-flex align-items-center justify-content-between bg-white mb-1">
                                        <label class="form-check-label small fw-medium text-dark mb-0" for="gridCheck{{ $module->id }}" style="cursor: pointer;">
                                            <i class="{{ $module->icon ?: 'bi bi-app' }} text-primary me-1"></i> {{ $module->name }}
                                        </label>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input module-checkbox" type="checkbox" id="gridCheck{{ $module->id }}" value="{{ $module->id }}" name="modules[]" checked>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="col-12 mb-3"><small class="text-danger error" id="modules_error"></small></div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-danger px-4 py-2" id="clearBtn" style="border-radius:6px; font-weight:600; font-size:13px;">CLEAR</button>
                        <button type="submit" class="btn px-5 py-2" id="saveBtn" style="background-color: #334e68; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px;">SAVE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Phone formatter
        $('#phone').on('input', function() {
            let val = $(this).val().replace(/\D/g, '').substring(0, 10);
            let formatted = '';
            if (val.length > 0) formatted += val.substring(0, 3);
            if (val.length >= 4) formatted += '-' + val.substring(3, 6);
            if (val.length >= 7) formatted += '-' + val.substring(6, 10);
            $(this).val(formatted);
        });

        // Clear error on input change
        $('input, select').on('input change', function() {
            $(this).removeClass('is-invalid');
            let nameAttr = $(this).attr('name');
            if (nameAttr) {
                let cleanName = nameAttr.replace('[]', '');
                $('#' + cleanName + '_error').html('');
            }
            $('#modules_error').html('');
        });

        // Form Submit via AJAX
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            let isValid = true;
            $('.error').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            if (!$('#role').val()) {
                $('#role_error').text('Please select a role.');
                $('#role').addClass('is-invalid');
                isValid = false;
            }
            if (!$('#first_name').val().trim()) {
                $('#first_name_error').text('First name is required.');
                $('#first_name').addClass('is-invalid');
                isValid = false;
            }
            if (!$('#email').val().trim()) {
                $('#email_error').text('Email is required.');
                $('#email').addClass('is-invalid');
                isValid = false;
            }
            if (!$('#phone').val().trim()) {
                $('#phone_error').text('Contact number is required.');
                $('#phone').addClass('is-invalid');
                isValid = false;
            }
            if ($('#current_wage').val() === '' || parseFloat($('#current_wage').val()) < 0) {
                $('#current_wage_error').text('Valid daily wage is required.');
                $('#current_wage').addClass('is-invalid');
                isValid = false;
            }
            if (!$('#password').val() || $('#password').val().length < 6) {
                $('#password_error').text('Password must be at least 6 characters.');
                $('#password').addClass('is-invalid');
                isValid = false;
            }
            if ($('.module-checkbox').length > 0 && $('.module-checkbox:checked').length === 0) {
                $('#modules_error').text('Please select at least one module.');
                isValid = false;
            }

            if (!isValid) {
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 300);
                return false;
            }

            const btn = $('#saveBtn');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>SAVING...');
            $('#nms-loader-overlay').addClass('active');

            $.ajax({
                url: "{{ route('um.users.store') }}",
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#nms-loader-overlay').removeClass('active');
                    if (response.status === 'success') {
                        if (typeof showAlert === 'function') {
                            showAlert('success', response.message);
                        } else {
                            alert(response.message);
                        }
                        setTimeout(() => {
                            window.location.href = "{{ route('um.users.index') }}";
                        }, 1000);
                    } else {
                        btn.prop('disabled', false).text('SAVE');
                        if (typeof showAlert === 'function') {
                            showAlert('danger', response.message || 'Something went wrong.');
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('SAVE');
                    $('#nms-loader-overlay').removeClass('active');

                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        let firstError = '';
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('#' + key + '_error').html(val[0]);
                            $('#' + key).addClass('is-invalid');
                            if (!firstError) firstError = val[0];
                        });

                        if (typeof showAlert === 'function') {
                            showAlert('danger', firstError || 'Please fix the validation errors below.');
                        }

                        $('html, body').animate({
                            scrollTop: $('.is-invalid').first().offset().top - 100
                        }, 300);
                    } else {
                        let errorMsg = xhr.responseJSON?.message || 'Error creating user account.';
                        if (typeof showAlert === 'function') {
                            showAlert('danger', errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                    }
                }
            });
        });

        // Clear Form Button
        $('#clearBtn').on('click', () => $('#addUserForm')[0].reset());
    });
</script>
@endsection