<!-- resources/views/teacher/proofs/create.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumettre des Justificatifs - {{ $mission->title }}</title>
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
        .nav-pills .nav-link.active {
            background-color: #6c757d;
        }
        .proof-list-item {
            border-left: 3px solid #6c757d;
        }
        .proof-list-item.approved {
            border-left: 3px solid #28a745;
        }
        .proof-list-item.rejected {
            border-left: 3px solid #dc3545;
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
                            <a class="nav-link" href="{{ route('teacher.missions.index') }}">
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
                    <h1 class="h2">Justificatifs de Mission</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="{{ route('teacher.missions.show', $mission->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Détails de la mission
                            </a>
                            <a href="{{ route('teacher.missions.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-list me-1"></i> Liste des missions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mission Info Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title">{{ $mission->title }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Destination:</strong> {{ $mission->destination_city }}, {{ $mission->destination_institution }}</p>
                                <p class="mb-1"><strong>Dates:</strong> {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Type:</strong> {{ $mission->type === 'nationale' ? 'Mission nationale' : 'Mission internationale' }}</p>
                                <p class="mb-1"><strong>Status:</strong> 
                                    @switch($mission->status)
                                        @case('soumise')
                                            <span class="badge bg-warning">Soumise</span>
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
                                            <span class="badge bg-secondary">{{ $mission->status }}</span>
                                    @endswitch
                                </p>
                            </div>
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

                <!-- Proof Submission Tabs -->
                <div class="card">
                    <div class="card-header bg-white p-0">
                        <ul class="nav nav-pills nav-fill" id="proofTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab" aria-controls="financial" aria-selected="true">
                                    <i class="fas fa-money-bill-wave me-2"></i> Justificatifs Financiers
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="execution-tab" data-bs-toggle="tab" data-bs-target="#execution" type="button" role="tab" aria-controls="execution" aria-selected="false">
                                    <i class="fas fa-clipboard-check me-2"></i> Justificatifs d'Exécution
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="return-tab" data-bs-toggle="tab" data-bs-target="#return" type="button" role="tab" aria-controls="return" aria-selected="false">
                                    <i class="fas fa-file-alt me-2"></i> Justificatifs de Retour
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="proofTabsContent">
                            <!-- Financial Proofs Tab -->
                            <div class="tab-pane fade show active" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Justificatifs Financiers</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFinancialProofModal">
                                        <i class="fas fa-plus me-1"></i> Ajouter un justificatif financier
                                    </button>
                                </div>
                                @if($financialProofs->isEmpty())
                                    <div class="alert alert-info">
                                        Aucun justificatif financier n'a été soumis pour cette mission.
                                    </div>
                                @else
                                    <div class="list-group">
                                        @foreach($financialProofs as $proof)
                                            <div class="list-group-item proof-list-item {{ $proof->status === 'approved' ? 'approved' : ($proof->status === 'rejected' ? 'rejected' : '') }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $proof->proof_type)) }}</h6>
                                                        <p class="mb-1 text-muted small">
                                                            Soumis le {{ $proof->created_at->format('d/m/Y à H:i') }}
                                                            @if($proof->amount)
                                                                | Montant: {{ number_format($proof->amount, 2) }} DH
                                                            @endif
                                                        </p>
                                                        @if($proof->description)
                                                            <p class="mb-1">{{ $proof->description }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex">
                                                        <a href="{{ route('teacher.proofs.show', $proof->id) }}" class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($proof->status === 'pending')
                                                            <form action="{{ route('teacher.proofs.destroy', $proof->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce justificatif?');">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="badge {{ $proof->status === 'approved' ? 'bg-success' : 'bg-danger' }} ms-2">
                                                                {{ $proof->status === 'approved' ? 'Approuvé' : 'Rejeté' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($proof->status === 'rejected' && $proof->rejection_reason)
                                                    <div class="alert alert-danger py-1 px-2 mt-2 mb-0">
                                                        <small><strong>Raison du rejet:</strong> {{ $proof->rejection_reason }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Execution Proofs Tab -->
                            <div class="tab-pane fade" id="execution" role="tabpanel" aria-labelledby="execution-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Justificatifs d'Exécution</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExecutionProofModal">
                                        <i class="fas fa-plus me-1"></i> Ajouter un justificatif d'exécution
                                    </button>
                                </div>
                                @if($executionProofs->isEmpty())
                                    <div class="alert alert-info">
                                        Aucun justificatif d'exécution n'a été soumis pour cette mission.
                                    </div>
                                @else
                                    <div class="list-group">
                                        @foreach($executionProofs as $proof)
                                            <div class="list-group-item proof-list-item {{ $proof->status === 'approved' ? 'approved' : ($proof->status === 'rejected' ? 'rejected' : '') }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $proof->proof_type)) }}</h6>
                                                        <p class="mb-1 text-muted small">Soumis le {{ $proof->created_at->format('d/m/Y à H:i') }}</p>
                                                        @if($proof->description)
                                                            <p class="mb-1">{{ $proof->description }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex">
                                                        <a href="{{ route('teacher.proofs.show', $proof->id) }}" class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($proof->status === 'pending')
                                                            <form action="{{ route('teacher.proofs.destroy', $proof->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce justificatif?');">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="badge {{ $proof->status === 'approved' ? 'bg-success' : 'bg-danger' }} ms-2">
                                                                {{ $proof->status === 'approved' ? 'Approuvé' : 'Rejeté' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($proof->status === 'rejected' && $proof->rejection_reason)
                                                    <div class="alert alert-danger py-1 px-2 mt-2 mb-0">
                                                        <small><strong>Raison du rejet:</strong> {{ $proof->rejection_reason }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Return Proofs Tab -->
                            <div class="tab-pane fade" id="return" role="tabpanel" aria-labelledby="return-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Justificatifs de Retour</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addReturnProofModal">
                                        <i class="fas fa-plus me-1"></i> Ajouter un justificatif de retour
                                    </button>
                                </div>
                                @if($returnProofs->isEmpty())
                                    <div class="alert alert-info">
                                        Aucun justificatif de retour n'a été soumis pour cette mission.
                                    </div>
                                @else
                                    <div class="list-group">
                                        @foreach($returnProofs as $proof)
                                            <div class="list-group-item proof-list-item {{ $proof->status === 'approved' ? 'approved' : ($proof->status === 'rejected' ? 'rejected' : '') }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $proof->proof_type)) }}</h6>
                                                        <p class="mb-1 text-muted small">Soumis le {{ $proof->created_at->format('d/m/Y à H:i') }}</p>
                                                        @if($proof->description)
                                                            <p class="mb-1">{{ $proof->description }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex">
                                                        <a href="{{ route('teacher.proofs.show', $proof->id) }}" class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($proof->status === 'pending')
                                                            <form action="{{ route('teacher.proofs.destroy', $proof->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce justificatif?');">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="badge {{ $proof->status === 'approved' ? 'bg-success' : 'bg-danger' }} ms-2">
                                                                {{ $proof->status === 'approved' ? 'Approuvé' : 'Rejeté' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($proof->status === 'rejected' && $proof->rejection_reason)
                                                    <div class="alert alert-danger py-1 px-2 mt-2 mb-0">
                                                        <small><strong>Raison du rejet:</strong> {{ $proof->rejection_reason }}</small>
                                                    </div>
                                                @endif
                                            </div>
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

    <!-- Add Financial Proof Modal -->
    <div class="modal fade" id="addFinancialProofModal" tabindex="-1" aria-labelledby="addFinancialProofModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addFinancialProofModalLabel">Ajouter un justificatif financier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('teacher.proofs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="mission_id" value="{{ $mission->id }}">
                    <input type="hidden" name="category" value="financier">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="financial_proof_type" class="form-label">Type de justificatif *</label>
                            <select class="form-select" id="financial_proof_type" name="proof_type" required>
                                <option value="" selected disabled>Sélectionner le type</option>
                                <option value="facture_hotel">Facture d'hôtel</option>
                                <option value="billet_avion">Billet d'avion</option>
                                <option value="billet_train">Billet de train</option>
                                <option value="ticket_transport">Ticket de transport public</option>
                                <option value="recu_taxi">Reçu de taxi</option>
                                <option value="facture_repas">Facture de repas</option>
                                <option value="autre_depense">Autre dépense</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="financial_proof_file" class="form-label">Document *</label>
                            <input type="file" class="form-control" id="financial_proof_file" name="proof_file" required>
                            <div class="form-text">Formats acceptés: PDF, JPG, JPEG, PNG (max 10MB)</div>
                        </div>
                        <div class="mb-3">
                            <label for="financial_amount" class="form-label">Montant (DH) *</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="financial_amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="financial_description" class="form-label">Description</label>
                            <textarea class="form-control" id="financial_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Soumettre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Execution Proof Modal -->
    <div class="modal fade" id="addExecutionProofModal" tabindex="-1" aria-labelledby="addExecutionProofModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addExecutionProofModalLabel">Ajouter un justificatif d'exécution</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('teacher.proofs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="mission_id" value="{{ $mission->id }}">
                    <input type="hidden" name="category" value="execution">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="execution_proof_type" class="form-label">Type de justificatif *</label>
                            <select class="form-select" id="execution_proof_type" name="proof_type" required>
                                <option value="" selected disabled>Sélectionner le type</option>
                                <option value="attestation_participation">Attestation de participation</option>
                                <option value="programme_evenement">Programme de l'événement</option>
                                <option value="certificat_presentation">Certificat de présentation</option>
                                <option value="badge_acces">Badge d'accès</option>
                                <option value="photos_evenement">Photos de l'événement</option>
                                <option value="autre_execution">Autre justificatif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="execution_proof_file" class="form-label">Document *</label>
                            <input type="file" class="form-control" id="execution_proof_file" name="proof_file" required>
                            <div class="form-text">Formats acceptés: PDF, JPG, JPEG, PNG (max 10MB)</div>
                        </div>
                        <div class="mb-3">
                            <label for="execution_description" class="form-label">Description</label>
                            <textarea class="form-control" id="execution_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Soumettre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Return Proof Modal -->
    <div class="modal fade" id="addReturnProofModal" tabindex="-1" aria-labelledby="addReturnProofModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addReturnProofModalLabel">Ajouter un justificatif de retour</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('teacher.proofs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="mission_id" value="{{ $mission->id }}">
                    <input type="hidden" name="category" value="retour">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="return_proof_type" class="form-label">Type de justificatif *</label>
                            <select class="form-select" id="return_proof_type" name="proof_type" required>
                                <option value="" selected disabled>Sélectionner le type</option>
                                <option value="rapport_mission">Rapport de mission</option>
                                <option value="presentation">Présentation réalisée</option>
                                <option value="article_publie">Article publié</option>
                                <option value="compte_rendu">Compte-rendu</option>
                                <option value="autre_retour">Autre document</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="return_proof_file" class="form-label">Document *</label>
                            <input type="file" class="form-control" id="return_proof_file" name="proof_file" required>
                            <div class="form-text">Formats acceptés: PDF, JPG, JPEG, PNG (max 10MB)</div>
                        </div>
                        <div class="mb-3">
                            <label for="return_description" class="form-label">Description</label>
                            <textarea class="form-control" id="return_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Soumettre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>