@extends('pdf.master_layout')

@section('title', 'Project Types Directory')

@section('content')
<h3 style="color: #334e68; margin-bottom: 15px; text-transform: uppercase; font-size: 14px;">Project Types Directory</h3>

<table class="report-table">
    <thead>
        <tr>
            <th width="10%">#</th>
            <th width="65%">Project Type Name</th>
            <th width="25%" style="text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($projectTypes as $index => $type)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td><strong>{{ $type->type_name }}</strong></td>
            <td style="text-align: center;">
                @if((int)$type->status === 1)
                <span style="color: #065f46; font-weight: bold;">Active</span>
                @else
                <span style="color: #991b1b; font-weight: bold;">Inactive</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" style="text-align: center; color: #777; padding: 20px;">No project types found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection