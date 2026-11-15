<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ps.dashboard') ? '' : 'collapsed' }}" href="{{ route('ps.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ps.project-types.*') ? '' : 'collapsed' }}" href="{{ route('ps.project-types.index') }}">
                <i class="bi bi-tags-fill"></i>
                <span>Project Types</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ps.projects.create') ? '' : 'collapsed' }}" href="{{ route('ps.projects.create') }}">
                <i class="bi bi-plus-square-fill"></i>
                <span>Add Project</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ps.projects.index') ? '' : 'collapsed' }}" href="{{ route('ps.projects.index') }}">
                <i class="bi bi-buildings-fill"></i>
                <span>View Projects</span>
            </a>
        </li>
    </ul>
</aside>