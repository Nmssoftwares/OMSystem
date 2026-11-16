@extends('pdf.master_layout')

@section('title', 'User Bank & UPI Directory')

@section('content')
<h3 style="color: #334e68; margin-bottom: 15px; text-transform: uppercase; font-size: 14px;">User Bank & UPI Directory</h3>

<table class="report-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="25%">User Name & Email</th>
            <th width="20%">Account Holder</th>
            <th width="25%">Bank Details</th>
            <th width="25%">UPI Information</th>
        </tr>
    </thead>
    <tbody>
        @forelse($banks as $index => $row)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $row->user->name ?? 'NA' }}</strong><br>
                <small style="color: #555;">{{ $row->user->email ?? '' }}</small>
            </td>
            <td>{{ $row->account_holder_name ?? 'NA' }}</td>
            <td>
                <strong>{{ $row->bank_name ?? 'NA' }}</strong><br>
                <small>A/C: {{ $row->account_number ?? 'NA' }}</small><br>
                <small>IFSC: {{ $row->ifsc_code ?? 'NA' }}</small>
            </td>
            <td>
                UPI ID: <strong>{{ $row->upi_id ?? 'NA' }}</strong><br>
                <small>UPI No: {{ $row->upi_number ?? 'NA' }}</small>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center; color: #777; padding: 20px;">No bank details found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection