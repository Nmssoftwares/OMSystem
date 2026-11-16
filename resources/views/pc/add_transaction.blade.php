@extends('pc.layouts.app')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-soft p-4 bg-white shadow-sm position-relative" style="border-radius: 12px;">
                <form id="addTransactionForm" novalidate>
                    @csrf
                    <div class="row g-4 mb-4">
                        <!-- Select Worker / Labour -->
                        <div class="col-md-6">
                            <div class="input-floating-wrap">
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">Choose worker...</option>
                                    @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}">{{ $worker->first_name }} {{ $worker->last_name }} ({{ $worker->phone }})</option>
                                    @endforeach
                                </select>
                                <label class="form-label-joint">Select Worker / Labour*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="user_id_error"></small>
                        </div>

                        <!-- Transaction Type -->
                        <div class="col-md-6">
                            <div class="input-floating-wrap">
                                <select name="transaction_type" id="transaction_type" class="form-select" required>
                                    <option value="1">Advance Payment</option>
                                    <option value="2">Daily Kharcha / Expense</option>
                                    <option value="3">Other</option>
                                </select>
                                <label class="form-label-joint">Transaction Type*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="transaction_type_error"></small>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <div class="input-floating-wrap">
                                <input type="number" step="1" min="1" class="form-control" id="amount" name="amount" placeholder=" " required>
                                <label class="form-label-joint">Amount (₹)*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="amount_error"></small>
                        </div>

                        <!-- Transaction Date -->
                        <div class="col-md-6">
                            <div class="input-floating-wrap">
                                <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                                <label class="form-label-joint">Transaction Date*</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="transaction_date_error"></small>
                        </div>

                        <!-- Notes / Remarks -->
                        <div class="col-md-12">
                            <div class="input-floating-wrap">
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder=" "></textarea>
                                <label class="form-label-joint">Notes / Remarks (Optional)</label>
                            </div>
                            <small class="text-danger error mt-1 d-block" id="notes_error"></small>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-danger px-4 py-2" id="clearBtn" style="border-radius:6px; font-weight:600; font-size:13px;">CLEAR</button>
                        <button type="submit" class="btn px-5 py-2" id="saveBtn" style="background-color: #334e68; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px;">SAVE ENTRY</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<a href="{{ route('pc.transactions.index') }}" title="View Transactions" class="floating-action-btn">
   <i class="bi bi-eye-fill"></i>
</a>
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

        $('#addTransactionForm').on('submit', function(e) {
            e.preventDefault();
            let isValid = true;
            $('.error').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            if (!$('#user_id').val()) {
                $('#user_id_error').text('Please select a worker.');
                $('#user_id').addClass('is-invalid');
                isValid = false;
            }

            if (!$('#amount').val() || $('#amount').val() <= 0) {
                $('#amount_error').text('Please enter a valid amount.');
                $('#amount').addClass('is-invalid');
                isValid = false;
            }

            if (!$('#transaction_date').val()) {
                $('#transaction_date_error').text('Transaction date is required.');
                $('#transaction_date').addClass('is-invalid');
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
                url: "{{ route('pc.transactions.store') }}",
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
                            window.location.href = "{{ route('pc.transactions.index') }}";
                        }, 1000);
                    } else {
                        btn.prop('disabled', false).text('SAVE ENTRY');
                        if (typeof showAlert === 'function') {
                            showAlert('danger', response.message || 'Something went wrong.');
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('SAVE ENTRY');
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
                        let errorMsg = xhr.responseJSON?.message || 'Error recording transaction.';
                        if (typeof showAlert === 'function') {
                            showAlert('danger', errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                    }
                }
            });
        });

        $('#clearBtn').on('click', () => $('#addTransactionForm')[0].reset());
    });
</script>
@endsection