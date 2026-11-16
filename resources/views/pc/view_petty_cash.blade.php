@extends('pc.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-wallet2 text-primary me-2"></i>Labour Advances & Petty Cash</h5>
                </div>

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
@endsection

@section('scripts')
<script>
    let dataTable;

    $(document).ready(function() {
        dataTable = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pc.transactions.datatable') }}",
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

        // Delete Transaction
        $(document).on('click', '.deleteTxBtn', function() {
            let id = $(this).data('id');
            if (confirm('Are you sure you want to delete this cash entry?')) {
                $.ajax({
                    url: "/petty-cash/transactions/delete/" + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (typeof showAlert === 'function') showAlert('success', response.message);
                        dataTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Failed to delete entry.');
                    }
                });
            }
        });
    });
</script>
@endsection