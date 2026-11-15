@extends('pdf.master_layout')

@section('title', 'Registered Companies Directory')

@section('content')
<h3 style="color: #334e68; margin-bottom: 15px; text-transform: uppercase; font-size: 14px;">Registered Companies Directory</h3>

<table class="report-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="35%">Company Details</th>
            <th width="25%">Admin Name</th>
            <th width="25%">Admin Email</th>
            <th width="10%" style="text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($companies as $index => $company)
        @php
        $admin = $company->users->first();
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $company->company_name }}</strong>
                @if($company->company_code)
                <span style="color: #0d6efd;">({{ $company->company_code }})</span>
                @endif
                <br><span style="color: #666; font-size: 10px;">{{ $company->company_address }}</span>
            </td>
            <td>{{ $admin ? $admin->first_name . ' ' . $admin->last_name : 'No Admin' }}</td>
            <td>{{ $admin ? $admin->email : '-' }}</td>
            <td style="text-align: center;">
                @if((int)$company->status === 1)
                <span style="color: #065f46; font-weight: bold;">Active</span>
                @else
                <span style="color: #991b1b; font-weight: bold;">Inactive</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center; color: #777; padding: 20px;">No companies found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection