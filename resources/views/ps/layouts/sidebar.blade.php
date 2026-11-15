<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ps.dashboard') ? '' : 'collapsed' }}" href="{{ route('ps.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <!-- Projects Listing (Jo screenshot me thi) -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ps.projects.index') ? '' : 'collapsed' }}" href="#">
                <i class="bi bi-buildings-fill"></i>
                <span>Projects</span>
            </a>
        </li>

        <!-- Add Project -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ps.projects.create') ? '' : 'collapsed' }}" href="#">
                <i class="bi bi-plus-square-fill"></i>
                <span>Add Project</span>
            </a>
        </li>

    </ul>
</aside>