@extends('ps.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm" style="border-radius: 12px;">

                <!-- Top Action Bar (Optional if you want add button) -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-buildings text-primary me-2"></i>Projects Directory</h5>
                    <a href="#" class="btn btn-primary btn-sm px-3 shadow-sm" style="border-radius: 8px; font-weight: 600;">
                        <i class="bi bi-plus-lg me-1"></i> Add Project
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="projectsTable">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Project Name</th>
                                <th>Owner Name</th>
                                <th>Project Type</th>
                                <th>Address</th>
                                <th>Timeline</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Share</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
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
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
            buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-outline-success btn-sm',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel'
                },
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-outline-danger btn-sm',
                    text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF'
                }
            ],
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<input type="checkbox" class="form-check-input row-select" value="' + row.id + '">';
                    }
                },
                {
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
                    data: 'share_action',
                    name: 'share_action',
                    orderable: false,
                    searchable: false,
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
    });
</script>
@endsection