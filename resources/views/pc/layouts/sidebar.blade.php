<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pc.dashboard') ? '' : 'collapsed' }}" href="{{ route('pc.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pc.expenses.*') ? '' : 'collapsed' }}" href="#">
                <i class="bi bi-wallet2"></i>
                <span>Expense & Kharcha</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pc.advances.*') ? '' : 'collapsed' }}" href="#">
                <i class="bi bi-cash-coin"></i>
                <span>Labour Advance</span>
            </a>
        </li>
    </ul>
</aside>