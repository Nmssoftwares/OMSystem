<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pc.dashboard') ? '' : 'collapsed' }}" href="{{ route('pc.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pc.transactions.*') ? '' : 'collapsed' }}" href="{{ route('pc.transactions.index') }}">
                <i class="bi bi-wallet2"></i>
                <span>Petty Cash / Kharcha</span>
            </a>
        </li>
    </ul>
</aside>