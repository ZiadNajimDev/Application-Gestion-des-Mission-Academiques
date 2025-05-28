
@php
    use App\Helpers\NotificationHelper;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(25%, -25%);
        }
        .progress {
            height: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" height="40" class="me-2">
                        <h5 class="mt-2 text-white">{{ Auth::user()->name }}</h5>
                        <p class="text-muted small">
                            @if(Auth::user()->isEnseignant())
                                Enseignant
                            @elseif(Auth::user()->isDirecteur())
                                Directeur
                            @elseif(Auth::user()->isChefDepartement())
                                Chef de Département
                            @elseif(Auth::user()->isComptable())
                                Service Comptabilité
                            @endif
                            @if(Auth::user()->department)
                                - {{ Auth::user()->department }}
                            @endif
                        </p>
                    </div>
                    
                    <ul class="nav flex-column">
                        @if(Auth::user()->isEnseignant())
                            @include('teacher.partials.sidebar_links')
                        @elseif(Auth::user()->isDirecteur())
                            @include('director.partials.sidebar_links')
                        @elseif(Auth::user()->isChefDepartement())
                            @include('chef.partials.sidebar_links')
                        @elseif(Auth::user()->isComptable())
                            @include('accountant.partials.sidebar_links')
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Notifications</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync-alt me-1"></i> Actualiser
                        </a>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Notifications</h5>
                        <div>
                            <form action="{{ route('notifications.delete-all') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger me-2">
                                    <i class="fas fa-trash-alt me-1"></i> Tout supprimer
                                </button>
                            </form>
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-check-circle me-1"></i> Tout marquer comme lu
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($notifications as $notification)
                                <a href="#" class="list-group-item list-group-item-action {{ $notification->read ? 'bg-light' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 text-{{ $notification->type }}">
                                            <i class="fas fa-{{ $notification->icon }} me-2"></i> {{ $notification->title }}
                                        </h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->message }}</p>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">
                                            @if($notification->read)
                                                <i class="fas fa-check me-1"></i> Lu
                                            @else
                                                Non lu
                                            @endif
                                        </small>
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none">
                                                {{ $notification->link ? 'Voir détails' : 'Marquer comme lu' }}
                                            </button>
                                        </form>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                    <p>Vous n'avez pas de notifications.</p>
                                </div>
                            @endforelse
                        </div>

                        <nav aria-label="Page navigation" class="mt-3">
                            <div class="d-flex justify-content-center">
                                {{ $notifications->links() }}
                            </div>
                        </nav>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>