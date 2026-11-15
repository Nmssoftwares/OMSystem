@extends('pdf.master_layout')

@section('title', 'Construction Projects Directory')

@section('content')
<h3 style="color: #334e68; margin-bottom: 15px; text-transform: uppercase; font-size: 14px;">Construction Projects Directory</h3>

<table class="report-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="25%">Project Name</th>
            <th width="20%">Owner Name</th>
            <th width="15%">Type</th>
            <th width="15%">Timeline</th>
            <th width="10%" style="text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($projects as $index => $project)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $project->project_name }}</strong>
                @if($project->project_label)
                <span style="color: #0d6efd;">({{ $project->project_label }})</span>
                @endif
                @if($project->project_address)
                <br><span style="color: #666; font-size: 10px;">{{ $project->project_address }}</span>
                @endif
            </td>
            <td>
                {{ $project->owner_name ?? 'NA' }}
                @if($project->owner_label)
                <br><span style="color: #666; font-size: 10px;">Label: {{ $project->owner_label }}</span>
                @endif
            </td>
            <td>{{ $project->project_type ?? 'NA' }}</td>
            <td>
                <small style="color: #666;">
                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') : '-' }}
                    <br>to
                    {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') : '-' }}
                </small>
            </td>
            <td style="text-align: center;">
                @if((int)$project->status === 1)
                <span style="color: #065f46; font-weight: bold;">Active</span>
                @else
                <span style="color: #991b1b; font-weight: bold;">Inactive</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center; color: #777; padding: 20px;">No projects found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection