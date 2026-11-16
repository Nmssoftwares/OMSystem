@extends('pdf.master_layout')

@section('title', 'Official Documents Directory')

@section('content')
<h3 style="color: #334e68; margin-bottom: 15px; text-transform: uppercase; font-size: 14px;">Official Documents Directory</h3>

<table class="report-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="30%">User Name & Phone</th>
            <th width="25%">Document Type</th>
            <th width="25%">Document Number</th>
            <th width="15%" style="text-align: center;">Verified</th>
        </tr>
    </thead>
    <tbody>
        @forelse($documents as $index => $doc)
        @php
        $userName = trim(($doc->user->first_name ?? '') . ' ' . ($doc->user->last_name ?? '')) ?: ($doc->user->name ?? 'N/A');
        $phone = $doc->user->phone ?? 'No Phone';
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $userName }}</strong>
                <br><span style="color: #666; font-size: 10px;">{{ $phone }}</span>
            </td>
            <td>{{ $doc->document_type_name }}</td>
            <td>{{ $doc->document_number }}</td>
            <td style="text-align: center;">
                @if((int)$doc->is_verified === 1)
                <span style="color: #065f46; font-weight: bold;">Verified</span>
                @else
                <span style="color: #991b1b; font-weight: bold;">Pending</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center; color: #777; padding: 20px;">No document records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection