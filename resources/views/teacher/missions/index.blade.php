<!-- resources/views/teacher/missions/index.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Missions - Enseignant</title>
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
        .mission-filters .btn-check:checked + .btn-outline-secondary,
        .mission-filters .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
        .page-item.active .page-link {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                <div class="d-flex align-items-center justify-content-center py-4 mb-3">
    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" height="40" class="me-2">
    <span class="fs-4 text-white">{{ Auth::user()->name }}</span>
</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('teacher.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('teacher.missions.create') }}">
                                <i class="fas fa-plus-circle me-2"></i> Nouvelle mission
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('teacher.missions.index') }}">
                                <i class="fas fa-list me-2"></i> Mes missions
                            </a>
                        </li>                     
                        @php
                            use App\Helpers\NotificationHelper;
                        @endphp
                                                <li class="nav-item position-relative">
                            <a class="nav-link" href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell me-2"></i> Notifications
                                @if(\App\Helpers\NotificationHelper::getUnreadCount() > 0)
                                    <span class="badge bg-danger notification-badge">{{ \App\Helpers\NotificationHelper::getUnreadCount() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            
                        </li>
                        <li class="nav-item mt-3">
                        <a class="nav-link" href="{{ route('teacher.settings') }}">
    <i class="fas fa-cog me-2"></i> Paramètres
</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Mes Missions</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="{{ route('teacher.missions.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Nouvelle mission
                            </a>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <h5 class="mb-0">Liste de toutes mes missions</h5>
                            </div>
                           
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Status Filter Buttons -->
                        <div class="mb-4 mission-filters">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('teacher.missions.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
                                   class="btn btn-outline-secondary {{ request('status', 'all') === 'all' ? 'active' : '' }}">
                                    Toutes ({{ $statusCounts['all'] }})
                                </a>
                                <a href="{{ route('teacher.missions.index', array_merge(request()->except('status', 'page'), ['status' => 'soumise'])) }}" 
                                   class="btn btn-outline-warning {{ request('status') === 'soumise' ? 'active' : '' }}">
                                    Soumises ({{ $statusCounts['soumise'] }})
                                </a>
                                <a href="{{ route('teacher.missions.index', array_merge(request()->except('status', 'page'), ['status' => 'validee_chef'])) }}" 
                                   class="btn btn-outline-info {{ request('status') === 'validee_chef' ? 'active' : '' }}">
                                    Validées (chef) ({{ $statusCounts['validee_chef'] }})
                                </a>
                                <a href="{{ route('teacher.missions.index', array_merge(request()->except('status', 'page'), ['status' => 'validee_directeur'])) }}" 
                                   class="btn btn-outline-primary {{ request('status') === 'validee_directeur' ? 'active' : '' }}">
                                    Validées (directeur) ({{ $statusCounts['validee_directeur'] }})
                                </a>
                                <a href="{{ route('teacher.missions.index', array_merge(request()->except('status', 'page'), ['status' => 'terminee'])) }}" 
                                   class="btn btn-outline-success {{ request('status') === 'terminee' ? 'active' : '' }}">
                                    Terminées ({{ $statusCounts['terminee'] }})
                                </a>
                                <a href="{{ route('teacher.missions.index', array_merge(request()->except('status', 'page'), ['status' => 'rejetee'])) }}" 
                                   class="btn btn-outline-danger {{ request('status') === 'rejetee' ? 'active' : '' }}">
                                    Rejetées ({{ $statusCounts['rejetee'] }})
                                </a>
                            </div>
                        </div>

                        @if($missions->isEmpty())
                            <div class="alert alert-info">
                                Aucune mission trouvée. <a href="{{ route('teacher.missions.create') }}">Créer une nouvelle mission</a>.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover border">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Référence</th>
                                            <th>Titre</th>
                                            <th>Destination</th>
                                            <th>Dates</th>
                                            <th>Type</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($missions as $mission)
                                            <tr>
                                                <td>MIS-{{ date('Y', strtotime($mission->created_at)) }}-{{ sprintf('%03d', $mission->id) }}</td>
                                                <td>{{ $mission->title }}</td>
                                                <td>{{ $mission->destination_city }} <br> <small class="text-muted">{{ $mission->destination_institution }}</small></td>
                                                <td>{{ $mission->date_range }} <br> <small class="text-muted">{{ $mission->duration }} jour(s)</small></td>
                                                <td>
                                                    @if($mission->type === 'nationale')
                                                        <span class="badge bg-primary">Nationale</span>
                                                    @else
                                                        <span class="badge bg-info text-dark">Internationale</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $mission->status_class }}">
                                                        {{ $mission->status_label }}
                                                    </span>
                                                </td>
                                                <td>
    <div class="btn-group btn-group-sm">
        <a href="{{ route('teacher.missions.show', $mission->id) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Voir les détails">
            <i class="fas fa-eye"></i>
        </a>
        
        @if($mission->status === 'soumise')
            <a href="{{ route('teacher.missions.edit', $mission->id) }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Modifier">
                <i class="fas fa-edit"></i>
            </a>
            
            <button type="button" class="btn btn-outline-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteMissionModal{{ $mission->id }}" 
                    title="Supprimer">
                <i class="fas fa-trash"></i>
            </button>
        @endif
        
        @if(in_array($mission->status, ['validee_chef', 'validee_directeur', 'billet_reserve', 'terminee']))
            <a href="{{ route('teacher.proofs.create', $mission->id) }}" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Soumettre des justificatifs">
                <i class="fas fa-file-upload"></i>
            </a>
        @endif
        
        
    </div>
</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $missions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Mission Modals -->
    @foreach($missions as $mission)
        @if($mission->status === 'soumise')
            <div class="modal fade" id="deleteMissionModal{{ $mission->id }}" tabindex="-1" aria-labelledby="deleteMissionModalLabel{{ $mission->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteMissionModalLabel{{ $mission->id }}">Confirmer la suppression</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer cette mission ?</p>
                            <div class="alert alert-warning">
                                <strong>{{ $mission->title }}</strong><br>
                                Destination: {{ $mission->destination_city }}<br>
                                Dates: {{ $mission->date_range }}
                            </div>
                            <p class="text-danger"><strong>Attention:</strong> Cette action est irréversible.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <form action="{{ route('teacher.missions.destroy', $mission->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>