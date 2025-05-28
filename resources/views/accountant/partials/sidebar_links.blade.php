<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('accountant.dashboard') ? 'active' : '' }}" href="{{ route('accountant.dashboard') }}">
        <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('accountant.reservations') ? 'active' : '' }}" href="{{ route('accountant.reservations') }}">
        <i class="fas fa-plane me-2"></i> Réservations
        @php
            $pendingReservations = \App\Models\Mission::where('status', 'validee_directeur')->count();
        @endphp
       
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('accountant.payments') ? 'active' : '' }}" href="{{ route('accountant.payments') }}">
        <i class="fas fa-money-bill-wave me-2"></i> Paiements
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('accountant.proofs') ? 'active' : '' }}" href="{{ route('accountant.proofs') }}">
        <i class="fas fa-clipboard-check me-2"></i> Justificatifs
    </a>
</li>
<li class="nav-item position-relative">
    <a class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
        <i class="fas fa-bell me-2"></i> Notifications
        @if(\App\Helpers\NotificationHelper::getUnreadCount() > 0)
            <span class="badge bg-danger notification-badge">{{ \App\Helpers\NotificationHelper::getUnreadCount() }}</span>
        @endif
    </a>
</li>
<li class="nav-item mt-3">
    <a class="nav-link {{ request()->routeIs('accountant.settings') ? 'active' : '' }}" href="{{ route('accountant.settings') }}">
        <i class="fas fa-cog me-2"></i> Paramètres
    </a>
</li>
<li class="nav-item">
    <a class="nav-link text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
    </a>
</li>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>