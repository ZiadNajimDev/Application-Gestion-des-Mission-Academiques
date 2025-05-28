<!-- resources/views/director/mission_details.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Mission - Directeur</title>
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
        .mission-header {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            border-left: 5px solid #0d6efd;
        }
        .detail-item {
            display: flex;
            margin-bottom: 0.5rem;
        }
        .detail-label {
            min-width: 150px;
            font-weight: 600;
        }
        .timeline {
            position: relative;
            padding-left: 32px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 9px;
            top: 0;
            height: 100%;
            width: 2px;
            background-color: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .timeline-marker {
            position: absolute;
            left: -32px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid;
        }
        .timeline-marker.pending {
            border-color: #ffc107;
        }
        .timeline-marker.approved {
            border-color: #28a745;
        }
        .timeline-marker.rejected {
            border-color: #dc3545;
        }
        .timeline-marker.completed {
            border-color: #0d6efd;
        }
        .timeline-content {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
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
                            <a class="nav-link" href="{{ route('director.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('director.pending_missions') }}">
                                <i class="fas fa-check-circle me-2"></i> Missions à valider
                                
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('director.all_missions') }}">
                                <i class="fas fa-list me-2"></i> Toutes les missions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('director.departments') }}">
                                <i class="fas fa-university me-2"></i> Départements
                            </a>
                        </li>
                        <li class="nav-item">
                        
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
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="{{ route('director.settings') }}">
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
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Détails de la Mission</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="{{ route('director.pending_missions') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour aux validations
                            </a>
                            
                        </div>
                    </div>
                </div>

                <!-- Mission Header -->
                <div class="mission-header p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="mb-1">{{ $mission->title }}</h3>
                            <p class="mb-2 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $mission->destination_city }}, {{ $mission->destination_institution }}
                            </p>
                            <div class="mb-2">
                                <span class="badge bg-{{ $mission->type === 'nationale' ? 'primary' : 'info' }} me-2">{{ $mission->type === 'nationale' ? 'Nationale' : 'Internationale' }}</span>
                                <span class="badge bg-warning text-dark">En attente de validation</span>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <div class="mb-2">
                                <strong>Ref:</strong> MIS-{{ date('Y', strtotime($mission->created_at)) }}-{{ sprintf('%03d', $mission->id) }}
                            </div>
                            <div class="mb-2">
                                <strong>Soumise le:</strong> {{ \Carbon\Carbon::parse($mission->created_at)->format('d/m/Y') }}
                            </div>
                            <div>
                                <strong>Validée par le chef le:</strong> {{ \Carbon\Carbon::parse($mission->chef_approval_date)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Mission Details -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Détails de la mission</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Informations générales</h6>
                                        <div class="detail-item">
                                            <div class="detail-label">Type de mission:</div>
                                            <div>{{ $mission->type === 'nationale' ? 'Nationale' : 'Internationale' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Date de début:</div>
                                            <div>{{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Date de fin:</div>
                                            <div>{{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Durée:</div>
                                            <div>{{ \Carbon\Carbon::parse($mission->start_date)->diffInDays(\Carbon\Carbon::parse($mission->end_date)) + 1 }} jour(s)</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Transport:</div>
                                            <div>
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
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Destination et objectif</h6>
                                        <div class="detail-item">
                                            <div class="detail-label">Ville:</div>
                                            <div>{{ $mission->destination_city }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Institution:</div>
                                            <div>{{ $mission->destination_institution }}</div>
                                        </div>
                                        @if($mission->supervisor_name)
                                            <div class="detail-item">
                                                <div class="detail-label">Encadrant:</div>
                                                <div>{{ $mission->supervisor_name }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h6 class="text-muted mb-3">Objectif de la mission</h6>
                                <p>{{ $mission->objective }}</p>
                                
                                @if($mission->chef_comments)
                                    <hr class="my-4">
                                    <h6 class="text-muted mb-3">Commentaires du chef de département</h6>
                                    <div class="alert alert-info">
                                        {{ $mission->chef_comments }}
                                    </div>
                                @endif
                                
                                <div class="d-flex justify-content-between mt-4">
                                    @if($mission->status === 'validee_chef')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="fas fa-times me-2"></i> Rejeter la mission
                                        </button>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="fas fa-check me-2"></i> Approuver la mission
                                        </button>
                                    @else
                                        <div class="alert alert-info w-100 text-center">
                                            @if($mission->status === 'validee_directeur')
                                                <i class="fas fa-check-circle me-2"></i> Cette mission a déjà été approuvée
                                            @elseif($mission->status === 'rejetee')
                                                <i class="fas fa-times-circle me-2"></i> Cette mission a été rejetée
                                            @else
                                                <i class="fas fa-info-circle me-2"></i> Cette mission ne peut pas être approuvée ou rejetée dans son état actuel
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Documents -->
                        @if($mission->documents->isNotEmpty())
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Documents joints</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        @foreach($mission->documents as $document)
                                            <a href="{{ Storage::url($document->file_path) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                                                <div>
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                                    {{ $document->file_name }}
                                                </div>
                                                <span class="badge bg-primary rounded-pill">
                                                    <i class="fas fa-download"></i>
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Sidebar Information -->
                    <div class="col-lg-4">
                        <!-- Teacher Information -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Informations sur l'enseignant</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    @if($mission->user->profile_photo_path)
                                        <img src="{{ Storage::url($mission->user->profile_photo_path) }}" class="rounded-circle" alt="Photo de profil" style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                            {{ substr($mission->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3 text-center">
                                    <h5 class="mb-1">{{ $mission->user->name }}</h5>
                                    <p class="text-muted mb-1">{{ $mission->user->email }}</p>
                                    <span class="badge bg-info">{{ $mission->user->department }}</span>
                                </div>
                                <hr>
                                <div class="detail-item">
                                    <div class="detail-label">CIN:</div>
                                    <div>{{ $mission->user->cin ?? 'Non spécifié' }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Téléphone:</div>
                                    <div>{{ $mission->user->phone ?? 'Non spécifié' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mission Status Timeline -->
                      

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
                            <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $progressWidth }}%" aria-valuenow="{{ $progressWidth }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Soumise</span>
                            <span>Validée chef</span>
                            <span>Validée directeur</span>
                            <span>Billet réservé</span>
                            <span>Terminée</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Approve Mission Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveModalLabel">Approuver la mission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('director.process_mission', $mission->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="decision" value="approve">
                    <div class="modal-body">
                        <p>Vous êtes sur le point d'approuver cette mission. Une fois approuvée, elle sera transmise au service comptable pour la réservation des billets.</p>
                        <div class="mb-3">
                            <label for="comments" class="form-label">Commentaires (optionnel)</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Approuver la mission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Mission Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Rejeter la mission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('director.process_mission', $mission->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="decision" value="reject">
                    <div class="modal-body">
                        <p>Vous êtes sur le point de rejeter cette mission. Veuillez fournir une raison pour ce rejet.</p>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Raison du rejet *</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                            <div class="form-text">Cette explication sera visible par l'enseignant et le chef de département.</div>
                        </div>
                        <div class="mb-3">
                            <label for="comments" class="form-label">Commentaires supplémentaires (optionnel)</label>
                            <textarea class="form-control" id="comments" name="comments" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Rejeter la mission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>