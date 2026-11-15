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