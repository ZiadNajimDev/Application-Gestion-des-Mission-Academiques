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
                            <a class="nav-link" href="{{ route('chef.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chef.mission_validate') }}">
                                <i class="fas fa-check-circle me-2"></i> Validations
                               
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chef.department_missions') }}">
                                <i class="fas fa-list me-2"></i> Missions du département
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chef.department_stats') }}">
                                <i class="fas fa-chart-pie me-2"></i> Statistiques
                            </a>
                        </li>
                        
                                                
                        @php
                            use App\Helpers\NotificationHelper;
                        @endphp
                                                <li class="nav-item position-relative">
                            <a class="nav-link active" href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell me-2"></i> Notifications
                                @if(\App\Helpers\NotificationHelper::getUnreadCount() > 0)
                                    <span class="badge bg-danger notification-badge">{{ \App\Helpers\NotificationHelper::getUnreadCount() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="{{ route('chef.settings') }}">
                                <i class="fas fa-cog me-2"></i> Paramètres
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
    </div>
</div>