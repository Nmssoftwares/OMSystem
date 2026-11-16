@extends('pdf.master_layout')

@section('title', 'Petty Cash & Labour Advances Report')

@section('content')
<h3 style="color: #334e68; margin-bottom: 15px; text-transform: uppercase; font-size: 14px;">Petty Cash & Labour Advances Report</h3>

<table class="report-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="25%">Worker Name</th>
            <th width="15%">Amount</th>
            <th width="15%">Type</th>
            <th width="15%">Date</th>
            <th width="15%">Given By</th>
            <th width="10%" style="text-align: center;">Notes</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $index => $tx)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $tx->user ? $tx->user->first_name . ' ' . ($tx->user->last_name ?? '') : 'General Expense' }}</strong>
                @if($tx->user && $tx->user->phone)
                <br><span style="color: #666; font-size: 10px;">Phone: {{ $tx->user->phone }}</span>
                @endif
            </td>
            <td>
                <span style="color: #065f46; font-weight: bold;">{{ number_format($tx->amount ?? 0, 0) }}</span>
            </td>
            <td>
                @if($tx->transaction_type == 1)
                <span>Advance</span>
                @elseif($tx->transaction_type == 2)
                <span>Daily Kharcha</span>
                @else
                <span>Other</span>
                @endif
            </td>
            <td>
                <small style="color: #666;">
                    {{ $tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('d-m-Y') : '-' }}
                </small>
            </td>
            <td>{{ $tx->admin ? $tx->admin->first_name : 'System' }}</td>
            <td style="text-align: center;">
                <span style="color: #555; font-size: 10px;">{{ $tx->notes ?: '-' }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center; color: #777; padding: 20px;">No petty cash transactions found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection