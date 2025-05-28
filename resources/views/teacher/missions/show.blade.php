<!-- resources/views/teacher/missions/show.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de Mission - Enseignant</title>
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
        .timeline {
            position: relative;
            padding-left: 3rem;
            margin-bottom: 1rem;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        .timeline-item-marker {
            position: absolute;
            left: -1.5rem;
            width: 1rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .timeline-item-marker-indicator {
            width: 1rem;
            height: 1rem;
            border-radius: 100%;
        }
        .timeline-item-marker::after {
            content: '';
            position: absolute;
            top: 1rem;
            bottom: 0;
            border-left: 1px solid #e0e0e0;
        }
        .timeline-item:last-child .timeline-item-marker::after {
            display: none;
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
                    <h1 class="h2">Détails de la Mission</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="{{ route('teacher.missions.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour aux missions
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $mission->title }}</h5>
                        <span class="badge bg-light text-dark">Référence: MIS-{{ date('Y') }}-{{ sprintf('%03d', $mission->id) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Informations générales</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Type de mission:</span>
                                        <span class="fw-bold">{{ $mission->type === 'nationale' ? 'Nationale' : 'Internationale' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Mode de transport:</span>
                                        <span class="fw-bold">
                                            @switch($mission->transport_type)
                                                @case('voiture')
                                                    Voiture personnelle
                                                    @break
                                                @case('transport_public')
                                                    Transport public
                                                    @break
                                                @case('train')
                                                    Train
                                                    @break
                                                @case('avion')
                                                    Avion
                                                    @break
                                                @default
                                                    {{ $mission->transport_type }}
                                            @endswitch
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Dates:</span>
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Durée:</span>
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($mission->start_date)->diffInDays(\Carbon\Carbon::parse($mission->end_date)) + 1 }} jours</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Destination</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Ville:</span>
                                        <span class="fw-bold">{{ $mission->destination_city }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Institution:</span>
                                        <span class="fw-bold">{{ $mission->destination_institution }}</span>
                                    </li>
                                    @if($mission->supervisor_name)
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span>Encadrant:</span>
                                            <span class="fw-bold">{{ $mission->supervisor_name }}</span>
                                        </li>
                                    @endif
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Status actuel:</span>
                                        <span class="fw-bold">
                                            @switch($mission->status)
                                                @case('soumise')
                                                    <span class="badge bg-warning">Soumise</span>
                                                    @break
                                                @case('validee_chef')
                                                    <span class="badge bg-info">Validée par chef dép.</span>
                                                    @break
                                                @case('validee_directeur')
                                                    <span class="badge bg-primary">Validée par directeur</span>
                                                    @break
                                                @case('billet_reserve')
                                                    <span class="badge bg-secondary">Billet réservé</span>
                                                    @break
                                                @case('terminee')
                                                    <span class="badge bg-success">Terminée</span>
                                                    @break
                                                @case('rejetee')
                                                    <span class="badge bg-danger">Rejetée</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $mission->status }}</span>
                                            @endswitch
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold">Objectif de la mission</h6>
                        <p>{{ $mission->objective }}</p>
                        
                        @if($mission->documents->isNotEmpty())
    <div class="mb-3">
        <h6 class="fw-bold">Documents joints</h6>
        <div class="list-group">
            @foreach($mission->documents as $document)
                <a href="{{ Storage::url($document->file_path) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                    <div>
                        <i class="fas fa-file-pdf text-danger me-2"></i>
                        {{ $document->file_name }}
                        <small class="text-muted ms-2">({{ number_format($document->file_size / 1048576, 2) }} MB)</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">
                        <i class="fas fa-download"></i>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
@endif
                        
                        <h6 class="fw-bold">Progression</h6>
                        <div class="progress mb-2">
                            @php
                                switch($mission->status) {
                                    case 'soumise':
                                        $progressWidth = 20;
                                        $progressColor = 'bg-warning';
                                        break;
                                    case 'validee_chef':
                                        $progressWidth = 40;
                                        $progressColor = 'bg-info';
                                        break;
                                    case 'validee_directeur':
                                        $progressWidth = 60;
                                        $progressColor = 'bg-primary';
                                        break;
                                    case 'billet_reserve':
                                        $progressWidth = 80;
                                        $progressColor = 'bg-secondary';
                                        break;
                                    case 'terminee':
                                        $progressWidth = 100;
                                        $progressColor = 'bg-success';
                                        break;
                                    case 'rejetee':
                                        $progressWidth = 100;
                                        $progressColor = 'bg-danger';
                                        break;
                                    default:
                                        $progressWidth = 0;
                                        $progressColor = 'bg-secondary';
                                }
                            @endphp
                            <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $progressWidth }}%;" aria-valuenow="{{ $progressWidth }}" aria-valuemin="0" aria-valuemax="100">{{ $progressWidth }}%</div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Soumise</span>
                            <span>Validée chef</span>
                            <span>Validée directeur</span>
                            <span>Billet réservé</span>
                            <span>Terminée</span>
                        </div>
                        
                        <div class="mt-4">
                        
                            <div class="d-flex gap-2">
                                @if($mission->status === 'soumise')
                                    <a href="#" class="btn btn-outline-secondary">
                                        <i class="fas fa-edit me-2"></i> Modifier
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMissionModal">
                                        <i class="fas fa-trash me-2"></i> Supprimer
                                    </button>
                                @endif
                                
                                @if(in_array($mission->status, ['validee_chef', 'validee_directeur', 'billet_reserve']))
                                    <a href="{{ route('teacher.proofs.create', $mission->id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-file-upload me-2"></i> Téléverser des justificatifs
                                    </a>
                                @endif
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Mission Modal -->
    <div class="modal fade" id="deleteMissionModal" tabindex="-1" aria-labelledby="deleteMissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteMissionModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette mission ?</p>
                    <p><strong>{{ $mission->title }}</strong> - {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }}</p>
                    <p class="text-danger">Cette action est irréversible.</p>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>