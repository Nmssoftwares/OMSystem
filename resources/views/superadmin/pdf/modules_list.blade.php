@extends('pdf.master_layout')

@section('title', 'System Modules Directory')

@section('content')
<h3 style="color: #334e68; margin-bottom: 15px; text-transform: uppercase; font-size: 14px;">System Modules Directory</h3>

<table class="report-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="30%">Module Name</th>
            <th width="15%">Code</th>
            <th width="25%">Functional Suite / Group</th>
            <th width="15%">Dashboard URL</th>
            <th width="10%" style="text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($modules as $index => $module)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $module->name }}</strong>
                @if($module->description)
                <br><span style="color: #666; font-size: 10px;">{{ $module->description }}</span>
                @endif
            </td>
            <td><span style="font-family: monospace; color: #0d6efd; font-weight: bold;">{{ $module->short_name }}</span></td>
            <td>{{ $module->group_name ?: 'General' }}</td>
            <td><small style="color: #666;">{{ $module->dashboard_url ?: '-' }}</small></td>
            <td style="text-align: center;">
                @if((int)$module->status === 1)
                <span style="color: #065f46; font-weight: bold;">Active</span>
                @else
                <span style="color: #991b1b; font-weight: bold;">Inactive</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center; color: #777; padding: 20px;">No modules found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection