<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Mission - Chef de Département</title>
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
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                <div class="d-flex align-items-center justify-content-center py-4 mb-3">
    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" height="40" class="me-2">
    <span class="fs-4 text-white">{{ Auth::user()->name }}</span>
</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chef.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('chef.mission_validate') }}">
                                <i class="fas fa-check-circle me-2"></i> Validations
                               
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chef.department_missions') }}">
                                <i class="fas fa-list me-2"></i> Missions du département
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
                            <a class="nav-link" href="#">
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
                            <a href="{{ route('chef.mission_validate') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour aux validations
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $mission->title }}</h5>
                                <span class="badge bg-light text-dark">Réf: MIS-{{ date('Y', strtotime($mission->created_at)) }}-{{ sprintf('%03d', $mission->id) }}</span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Informations de base</h6>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>Type de mission:</span>
                                                <span class="fw-bold">{{ $mission->type === 'nationale' ? 'Mission nationale' : 'Mission internationale' }}</span>
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
                                                <span class="fw-bold">{{ $mission->start_date->format('d/m/Y') }} - {{ $mission->end_date->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>Durée:</span>
                                                <span class="fw-bold">{{ $mission->duration }} jour(s)</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Détails de la destination</h6>
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
                                                <span>Soumise le:</span>
                                                <span class="fw-bold">{{ $mission->created_at->format('d/m/Y') }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <h6 class="fw-bold mb-2">Objectif de la mission</h6>
                                <p class="mb-4">{{ $mission->objective }}</p>
                                
                                @if($mission->documents->isNotEmpty())
                                    <h6 class="fw-bold mb-2">Documents joints</h6>
                                    <div class="list-group mb-4">
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
                                @endif
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
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
                                <div class="mb-3">
                                    <h5 class="text-center mb-1">{{ $mission->user->name }}</h5>
                                    <p class="text-center text-muted mb-0">{{ $mission->user->email }}</p>
                                    <p class="text-center text-muted">{{ $mission->user->department }}</p>
                                </div>
                                <hr>
                                <h6 class="mb-3">Coordonnées</h6>
                                <ul class="list-group list-group-flush">
                                    @if($mission->user->cin)
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span>CIN:</span>
                                            <span class="fw-bold">{{ $mission->user->cin }}</span>
                                        </li>
                                    @endif
                                    @if($mission->user->phone)
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span>Téléphone:</span>
                                            <span class="fw-bold">{{ $mission->user->phone }}</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Missions précédentes</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $previousMissions = $mission->user->missions()
                                        ->where('id', '!=', $mission->id)
                                        ->latest()
                                        ->take(3)
                                        ->get();
                                @endphp
                                
                                @if($previousMissions->isEmpty())
                                    <p class="text-muted">Aucune mission précédente.</p>
                                @else
                                    <div class="list-group">
                                        @foreach($previousMissions as $prevMission)
                                            <a href="#" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{ $prevMission->title }}</h6>
                                                    <small>
                                                        @switch($prevMission->status)
                                                            @case('soumise')
                                                                <span class="badge bg-warning">En attente</span>
                                                                @break
                                                            @case('validee_chef')
                                                                <span class="badge bg-info">Validée (chef)</span>
                                                                @break
                                                            @case('validee_directeur')
                                                                <span class="badge bg-primary">Validée (directeur)</span>
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
                                                                <span class="badge bg-secondary">{{ $prevMission->status }}</span>
                                                        @endswitch
                                                    </small>
                                                </div>
                                                <p class="mb-1">{{ $prevMission->destination_city }}</p>
                                                <small>{{ $prevMission->start_date->format('d/m/Y') }} - {{ $prevMission->end_date->format('d/m/Y') }}</small>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Approve Mission Modal -->
    <div class="modal fade" id="approveMissionModal" tabindex="-1" aria-labelledby="approveMissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveMissionModalLabel">Valider la mission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('chef.mission_validate_post', $mission->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="decision" value="approve">
                    <div class="modal-body">
                        <p>Vous êtes sur le point de valider la mission suivante :</p>
                        <div class="alert alert-info">
                            <strong>{{ $mission->title }}</strong><br>
                            <span>Enseignant : {{ $mission->user->name }}</span><br>
                            <span>Dates : {{ $mission->start_date->format('d/m/Y') }} - {{ $mission->end_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="mb-3">
                            <label for="comments" class="form-label">Commentaires (optionnel)</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Valider la mission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Mission Modal -->
    <div class="modal fade" id="rejectMissionModal" tabindex="-1" aria-labelledby="rejectMissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectMissionModalLabel">Rejeter la mission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('chef.mission_validate_post', $mission->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="decision" value="reject">
                    <div class="modal-body">
                        <p>Vous êtes sur le point de rejeter la mission suivante :</p>
                        <div class="alert alert-warning">
                            <strong>{{ $mission->title }}</strong><br>
                            <span>Enseignant : {{ $mission->user->name }}</span><br>
                            <span>Dates : {{ $mission->start_date->format('d/m/Y') }} - {{ $mission->end_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Raison du rejet *</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                            <div class="form-text">Veuillez expliquer la raison du rejet. Cette explication sera visible par l'enseignant.</div>
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