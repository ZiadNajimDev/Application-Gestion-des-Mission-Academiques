@php
    use App\Helpers\NotificationHelper;
@endphp
<div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
        <div class="d-flex align-items-center justify-content-center py-4 mb-3">
    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" height="40" class="me-2">
    <span class="fs-4 text-white">{{ Auth::user()->name }}</span>
</div>
            <p class="text-muted small">Service Comptabilité</p>
        </div>
        
        <ul class="nav flex-column">
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
    <a class="nav-link" href="{{ route('notifications.index') }}">
        <i class="fas fa-bell me-2"></i> Notifications
        @if(NotificationHelper::getUnreadCount() > 0)
            <span class="badge bg-danger notification-badge">{{ NotificationHelper::getUnreadCount() }}</span>
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
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>