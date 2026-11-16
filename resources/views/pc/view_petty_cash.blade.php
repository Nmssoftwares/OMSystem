@extends('pc.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm" style="border-radius: 12px;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Worker Name</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Given By</th>
                                <th>Notes / Remarks</th>
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
<div class="modal fade" id="deleteTxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Are you sure?</h5>
                <p class="text-muted small mb-3">This cash entry will be deleted permanently.</p>
                <input type="hidden" id="delete_tx_id">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="confirmDeleteTxBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('pc.transactions.create') }}" title="Add New Transaction" class="floating-action-btn">
   <i class="bi bi-plus-lg"></i>
</a>
@endsection

@section('scripts')
<script>
    let transactionsTable;

    $(document).ready(function() {
        transactionsTable = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pc.transactions.datatable') }}",
            },
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    text: 'Excel',
                    className: 'buttons-excel dt-button',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        format: {
                            body: function(data, row, column, node) {
                                return data ? data.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim() : '';
                            }
                        }
                    }
                },
                {
                    text: 'PDF',
                    className: 'buttons-pdf dt-button',
                    action: function(e, dt, node, config) {
                        window.location.href = "{{ route('pc.transactions.export.pdf') }}";
                    }
                }
            ],
            columns: [{
                    data: 'worker_name',
                    name: 'user_id'
                },
                {
                    data: 'formatted_amount',
                    name: 'amount'
                },
                {
                    data: 'type_badge',
                    name: 'transaction_type'
                },
                {
                    data: 'formatted_date',
                    name: 'transaction_date'
                },
                {
                    data: 'given_by_admin',
                    name: 'given_by'
                },
                {
                    data: 'notes',
                    name: 'notes'
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
        $(document).on('click', '.deleteTxBtn', function() {
            let txId = $(this).data('id');
            $('#delete_tx_id').val(txId);
            $('#deleteTxModal').modal('show');
        });

        // 2. Confirm Delete AJAX Request
        $('#confirmDeleteTxBtn').on('click', function() {
            let txId = $('#delete_tx_id').val();
            let btn = $(this);

            btn.prop('disabled', true).text('Deleting...');
            $('#nms-loader-overlay').addClass('active');

            $.ajax({
                url: "{{ url('pc/transactions/delete') }}/" + txId,
                type: "POST",
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#nms-loader-overlay').removeClass('active');
                    $('#deleteTxModal').modal('hide');

                    if (response.status === 'success') {
                        if (typeof showAlert === 'function') {
                            showAlert('success', response.message);
                        } else {
                            alert(response.message);
                        }
                        transactionsTable.ajax.reload(null, false);
                    } else {
                        if (typeof showAlert === 'function') {
                            showAlert('danger', response.message);
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Yes, Delete');
                    $('#nms-loader-overlay').removeClass('active');
                    $('#deleteTxModal').modal('hide');
                    alert(xhr.responseJSON?.message || 'Error deleting transaction.');
                }
            });
        });
    });
</script>
@endsection