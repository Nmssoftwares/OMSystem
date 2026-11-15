@extends('ps.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm" style="border-radius: 12px;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="projectsTable">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Owner Name</th>
                                <th>Project Type</th>
                                <th>Address</th>
                                <th>Timeline</th>
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
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Are you sure?</h5>
                <p class="text-muted small mb-3">This project will be deleted permanently.</p>
                <input type="hidden" id="delete_project_id">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let projectsTable;

    $(document).ready(function() {
        projectsTable = $('#projectsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ps.projects.datatable') }}",
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
                    let role = "{{ $role ?? 3 }}";
                    window.location.href = "{{ route('ps.projects.export.pdf') }}";
                }
            }],
            columns: [{
                    data: 'project_info',
                    name: 'project_name'
                },
                {
                    data: 'owner_info',
                    name: 'owner_name'
                },
                {
                    data: 'project_type_col',
                    name: 'project_type'
                },
                {
                    data: 'address_col',
                    name: 'project_address'
                },
                {
                    data: 'timeline',
                    name: 'start_date'
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

        // 1. Open Delete Modal on Delete Button Click
        $(document).on('click', '.deleteProjectBtn', function() {
            let projectId = $(this).data('id');
            $('#delete_project_id').val(projectId);
            $('#deleteProjectModal').modal('show');
        });

        // 2. Confirm Delete AJAX Request
        $('#confirmDeleteBtn').on('click', function() {
            let projectId = $('#delete_project_id').val();
            let btn = $(this);

            btn.prop('disabled', true).text('Deleting...');
            $('#nms-loader-overlay').addClass('active');

            $.ajax({
                url: "{{ url('ps/projects/delete') }}/" + projectId,
                type: "POST",
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#nms-loader-overlay').removeClass('active');
                    $('#deleteProjectModal').modal('hide');

                    if (response.status === 'success') {
                        if (typeof showAlert === 'function') {
                            showAlert('success', response.message);
                        } else {
                            alert(response.message);
                        }
                        projectsTable.ajax.reload(null, false);
                    } else {
                        if (typeof showAlert === 'function') {
                            showAlert('danger', response.message);
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#nms-loader-overlay').removeClass('active');
                    $('#deleteProjectModal').modal('hide');
                    alert(xhr.responseJSON?.message || 'Error deleting project.');
                }
            });
        });

        // Status Toggle AJAX Handler
        $(document).on('change', '.status-toggle', function() {
            let toggle = $(this);
            let id = toggle.data('id');
            let status = toggle.is(':checked') ? 1 : 0;

            $('#nms-loader-overlay').addClass('active');

            $.ajax({
                url: "{{ route('ps.projects.update-status') }}",
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#nms-loader-overlay').removeClass('active');
                    if (response.status === 'success') {
                        if (typeof showAlert === 'function') {
                            showAlert('success', response.message);
                        } else {
                            alert(response.message);
                        }
                        projectsTable.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    $('#nms-loader-overlay').removeClass('active');
                    toggle.prop('checked', !status); // Revert switch if error
                    alert(xhr.responseJSON?.message || 'Error updating status.');
                }
            });
        });
    });
</script>
@endsection