@extends('pdf.master_layout')

@section('title', $role_title)

@section('content')
<h3 style="color: #334e68; margin-bottom: 15px; text-transform: uppercase; font-size: 14px;">{{ $role_title }}</h3>

<table class="report-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="35%">User Name & Email</th>
            <th width="30%">Company</th>
            <th width="20%">Contact No</th>
            <th width="10%" style="text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $index => $user)
        @php
        $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? 'N/A');
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $name }}</strong>
                <br><span style="color: #666; font-size: 10px;">{{ $user->email }}</span>
            </td>
            <td>{{ $user->company ? $user->company->company_name : 'N/A' }}</td>
            <td>{{ $user->phone ?? '-' }}</td>
            <td style="text-align: center;">
                @if((int)$user->status === 1)
                <span style="color: #065f46; font-weight: bold;">Active</span>
                @else
                <span style="color: #991b1b; font-weight: bold;">Inactive</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center; color: #777; padding: 20px;">No records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection