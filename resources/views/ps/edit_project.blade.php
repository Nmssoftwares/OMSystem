@extends('ps.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm position-relative" style="border-radius: 12px;">
                <form id="editProjectForm" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Section Title -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mt-0 fw-bold text-dark" style="font-size: 15px;">
                            <i class="bi bi-pencil-square text-primary me-1"></i> Edit Project Information
                        </div>
                        <a href="{{ route('ps.projects.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;">
                            <i class="bi bi-arrow-left me-1"></i> Back to Directory
                        </a>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- Project Name -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="project_name" name="project_name" placeholder=" " value="{{ $project->project_name }}" required>
                                <label class="form-label-joint">Project Name*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="project_name_error"></small>
                        </div>

                        <!-- Project Label / Code -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="project_label" name="project_label" placeholder=" " value="{{ $project->project_label }}">
                                <label class="form-label-joint">Project Code / Label (e.g. GM)</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="project_label_error"></small>
                        </div>

                        <!-- Project Type -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <select name="project_type" id="project_type" class="form-select">
                                    <option value="">Select Type</option>
                                    <option value="Factory" {{ $project->project_type == 'Factory' ? 'selected' : '' }}>Factory</option>
                                    <option value="Commercial" {{ $project->project_type == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                    <option value="Residential" {{ $project->project_type == 'Residential' ? 'selected' : '' }}>Residential</option>
                                    <option value="Infrastructure" {{ $project->project_type == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                                </select>
                                <label class="form-label-joint">Project Type</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="project_type_error"></small>
                        </div>

                        <!-- Owner Name -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder=" " value="{{ $project->owner_name }}">
                                <label class="form-label-joint">Client / Owner Name</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="owner_name_error"></small>
                        </div>

                        <!-- Owner Label -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="owner_label" name="owner_label" placeholder=" " value="{{ $project->owner_label }}">
                                <label class="form-label-joint">Owner Code / Reference</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="owner_label_error"></small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <select name="status" id="status" class="form-select" required>
                                    <option value="1" {{ $project->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $project->status == 0 ? 'selected' : '' }}>Inactive / On Hold</option>
                                </select>
                                <label class="form-label-joint">Status*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="status_error"></small>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="date" class="form-control" id="start_date" name="start_date" onkeydown="return false;" onpaste="return false" autocomplete="off" value="{{ $project->start_date }}">
                                <label class="form-label-joint">Start Date</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="start_date_error"></small>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="date" class="form-control" id="end_date" name="end_date" onkeydown="return false;" onpaste="return false" autocomplete="off" value="{{ $project->end_date }}">
                                <label class="form-label-joint">Estimated End Date</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="end_date_error"></small>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <div class="input-floating-wrap">
                                <textarea class="form-control" id="project_address" name="project_address" rows="2" placeholder=" ">{{ $project->project_address }}</textarea>
                                <label class="form-label-joint">Site Address</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="project_address_error"></small>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('ps.projects.index') }}" class="btn btn-secondary px-4 py-2" style="border-radius:6px; font-weight:600; font-size:13px;">CANCEL</a>
                        <button type="submit" class="btn px-5 py-2" id="updateBtn" style="background-color: #334e68; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px;">UPDATE PROJECT</button>
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
        // Clear error on input change
        $('input, select, textarea').on('input change', function() {
            $(this).removeClass('is-invalid');
            let nameAttr = $(this).attr('name');
            if (nameAttr) {
                $('#' + nameAttr + '_error').html('');
            }
        });

        // Update Form Submit via AJAX
        $('#editProjectForm').on('submit', function(e) {
            e.preventDefault();
            let isValid = true;
            $('.error').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            if (!$('#project_name').val().trim()) {
                $('#project_name_error').text('Project name is required.');
                $('#project_name').addClass('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 300);
                return false;
            }

            const btn = $('#updateBtn');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>UPDATING...');
            if (typeof $('#nms-loader-overlay').addClass === 'function') {
                $('#nms-loader-overlay').addClass('active');
            }

            $.ajax({
                url: "{{ route('ps.projects.update', $project->id) }}",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (typeof $('#nms-loader-overlay').removeClass === 'function') {
                        $('#nms-loader-overlay').removeClass('active');
                    }
                    if (response.status === 'success') {
                        if (typeof showAlert === 'function') {
                            showAlert('success', response.message);
                        } else {
                            alert(response.message);
                        }
                        setTimeout(() => {
                            window.location.href = "{{ route('ps.projects.index') }}";
                        }, 1000);
                    } else {
                        btn.prop('disabled', false).text('UPDATE PROJECT');
                        if (typeof showAlert === 'function') {
                            showAlert('danger', response.message || 'Something went wrong.');
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('UPDATE PROJECT');
                    if (typeof $('#nms-loader-overlay').removeClass === 'function') {
                        $('#nms-loader-overlay').removeClass('active');
                    }

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
                        let errorMsg = xhr.responseJSON?.message || 'Error updating project.';
                        if (typeof showAlert === 'function') {
                            showAlert('danger', errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                    }
                }
            });
        });
    });
</script>
@endsection