<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? '' : 'collapsed' }}" href="{{ route('superadmin.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('superadmin.companies.*') ? '' : 'collapsed' }}" href="{{ route('superadmin.companies.index') }}">
                <i class="bi bi-buildings"></i>
                <span>Companies</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('superadmin.modules*') ? '' : 'collapsed' }}" href="{{ route('superadmin.modules') }}">
                <i class="bi bi-grid-fill"></i>
                <span>Modules</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('superadmin.profile*') ? '' : 'collapsed' }}" href="{{ route('superadmin.profile') }}">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </a>
        </li>
    </ul>
</aside>