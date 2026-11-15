@extends('ps.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm position-relative" style="border-radius: 12px;">
                <form id="addProjectForm" novalidate>
                    @csrf
                    <div class="row g-4 mb-4">
                        <!-- Project Name -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="project_name" name="project_name" placeholder=" " required>
                                <label class="form-label-joint">Project Name*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="project_name_error"></small>
                        </div>

                        <!-- Project Label / Code -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="project_label" name="project_label" placeholder=" ">
                                <label class="form-label-joint">Project Code / Label (e.g. GM)</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="project_label_error"></small>
                        </div>

                        <!-- Project Type -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <select name="project_type_id" id="project_type_id" class="form-select">
                                    <option value="">Select Project Type</option>
                                    @foreach($projectTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                    @endforeach
                                </select>
                                <label class="form-label-joint">Project Type</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="project_type_id_error"></small>
                        </div>

                        <!-- Owner Name -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder=" ">
                                <label class="form-label-joint">Client / Owner Name</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="owner_name_error"></small>
                        </div>

                        <!-- Owner Label -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="text" class="form-control" id="owner_label" name="owner_label" placeholder=" ">
                                <label class="form-label-joint">Owner Code / Reference</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="owner_label_error"></small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <select name="status" id="status" class="form-select" required>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive / On Hold</option>
                                </select>
                                <label class="form-label-joint">Status*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="status_error"></small>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="date" class="form-control" id="start_date" name="start_date" onkeydown="return false;" onpaste="return false" autocomplete="off">
                                <label class="form-label-joint">Start Date</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="start_date_error"></small>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-4">
                            <div class="input-floating-wrap">
                                <input type="date" class="form-control" id="end_date" name="end_date" onkeydown="return false;" onpaste="return false" autocomplete="off">
                                <label class="form-label-joint">Estimated End Date</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="end_date_error"></small>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <div class="input-floating-wrap">
                                <textarea class="form-control" id="project_address" name="project_address" rows="2" placeholder=" "></textarea>
                                <label class="form-label-joint">Site Address</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="project_address_error"></small>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-danger px-4 py-2" id="clearBtn" style="border-radius:6px; font-weight:600; font-size:13px;">CLEAR</button>
                        <button type="submit" class="btn px-5 py-2" id="saveBtn" style="background-color: #334e68; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px;">SAVE PROJECT</button>
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
        $('input, select, textarea').on('input change', function() {
            $(this).removeClass('is-invalid');
            let nameAttr = $(this).attr('name');
            if (nameAttr) {
                $('#' + nameAttr + '_error').html('');
            }
        });

        $('#addProjectForm').on('submit', function(e) {
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

            const btn = $('#saveBtn');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>SAVING...');
            if (typeof $('#nms-loader-overlay').addClass === 'function') {
                $('#nms-loader-overlay').addClass('active');
            }

            $.ajax({
                url: "{{ route('ps.projects.store') }}",
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
                        btn.prop('disabled', false).text('SAVE PROJECT');
                        if (typeof showAlert === 'function') {
                            showAlert('danger', response.message || 'Something went wrong.');
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('SAVE PROJECT');
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
                        let errorMsg = xhr.responseJSON?.message || 'Error creating project.';
                        if (typeof showAlert === 'function') {
                            showAlert('danger', errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                    }
                }
            });
        });

        $('#clearBtn').on('click', () => $('#addProjectForm')[0].reset());
    });
</script>
@endsection