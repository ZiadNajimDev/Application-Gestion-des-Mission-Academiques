<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Département {{ $department }} - Directeur</title>
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
        .dept-header {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            border-left: 5px solid #0d6efd;
        }
        .info-card {
            transition: all 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .teacher-card {
            transition: all 0.2s ease;
        }
        .teacher-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        }
        .mission-row:hover {
            background-color: rgba(0,0,0,0.025);
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
                            <a class="nav-link" href="{{ route('director.pending_missions') }}">
                                <i class="fas fa-check-circle me-2"></i> Missions à valider
                               
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('director.all_missions') }}">
                                <i class="fas fa-list me-2"></i> Toutes les missions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('director.departments') }}">
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
                    <div>
                        <h1 class="h2">Département {{ $department }}</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('director.departments') }}">Départements</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $department }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="{{ route('director.departments') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour aux départements
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print me-1"></i> Imprimer
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editDepartmentModal">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Department Header -->
                <div class="dept-header p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h3 class="mb-2">{{ $department }}</h3>
                            <p class="mb-2 text-muted">{{ $departmentSettings->description ?? 'Aucune description disponible.' }}</p>
                            <div class="d-flex align-items-center">
                                <div class="me-4">
                                    <small class="text-muted d-block">Budget annuel</small>
                                    <strong>{{ number_format($departmentSettings->budget, 2) }} DH</strong>
                                </div>
                                <div class="me-4">
                                    <small class="text-muted d-block">Validation directeur</small>
                                    <strong>{{ $departmentSettings->director_validation ? 'Requise' : 'Non requise' }}</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Vérification budget</small>
                                    <strong>{{ $departmentSettings->budget_check ? 'Activée' : 'Désactivée' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <div class="mb-2">
                                <span class="text-muted">Chef de département:</span>
                                @if($departmentHead)
                                    <div class="d-inline-flex align-items-center mt-1">
                                        @if($departmentHead->profile_photo_path)
                                            <img src="{{ Storage::url($departmentHead->profile_photo_path) }}" alt="Photo" class="rounded-circle me-2" width="32" height="32">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ substr($departmentHead->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span>{{ $departmentHead->name }}</span>
                                    </div>
                                @else
                                    <span class="text-danger">Non assigné</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm info-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-users text-primary fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">Enseignants</h5>
                                        <h2 class="mb-0">{{ $teachers->count() }}</h2>
                                    </div>
                                </div>
                                <p class="card-text text-muted">Enseignants enregistrés dans ce département</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm info-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-plane-departure text-success fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">Missions</h5>
                                        <h2 class="mb-0">{{ $missions->total() }}</h2>
                                    </div>
                                </div>
                                <p class="card-text text-muted">Total des missions du département</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm info-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-check-circle text-info fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">Complétées</h5>
                                        <h2 class="mb-0">{{ $missionsByStatus['terminee'] ?? 0 }}</h2>
                                    </div>
                                </div>
                                <p class="card-text text-muted">Missions terminées avec succès</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm info-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-hourglass-half text-warning fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">En attente</h5>
                                        <h2 class="mb-0">{{ ($missionsByStatus['soumise'] ?? 0) + ($missionsByStatus['validee_chef'] ?? 0) }}</h2>
                                    </div>
                                </div>
                                <p class="card-text text-muted">Missions en attente de validation</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Recent Activity Chart -->
                    <div class="col-md-7 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Activité mensuelle ({{ date('Y') }})</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Distribution -->
                    <div class="col-md-5 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Distribution par statut</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="statusChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Teachers List -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0">Enseignants du département</h5>
                                <span class="badge bg-primary">{{ $teachers->count() }} enseignant(s)</span>
                            </div>
                            <div class="card-body p-0">
                                @if($teachers->isEmpty())
                                    <div class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucun enseignant dans ce département</p>
                                    </div>
                                @else
                                    <div class="list-group list-group-flush">
                                        @foreach($teachers as $teacher)
                                            <div class="list-group-item teacher-card">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        @if($teacher->profile_photo_path)
                                                            <img src="{{ Storage::url($teacher->profile_photo_path) }}" alt="Photo" class="rounded-circle me-3" width="40" height="40">
                                                        @else
                                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 16px;">
                                                                {{ substr($teacher->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $teacher->name }}</h6>
                                                            <small class="text-muted">{{ $teacher->email }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Department Settings -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0">Paramètres du département</h5>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editDepartmentModal">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </button>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="budget" class="form-label">Budget annuel (DH)</label>
                                        <input type="text" class="form-control-plaintext" id="budget" value="{{ number_format($departmentSettings->budget, 2) }}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control-plaintext" id="description" rows="3" readonly>{{ $departmentSettings->description }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Options de validation</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="director_validation" {{ $departmentSettings->director_validation ? 'checked' : '' }} disabled>
                                            <label class="form-check-label" for="director_validation">
                                                Requérir la validation du directeur
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="budget_check" {{ $departmentSettings->budget_check ? 'checked' : '' }} disabled>
                                            <label class="form-check-label" for="budget_check">
                                                Vérification automatique du budget disponible
                                            </label>
                                        </div>
                                    </div>
                                </form>
                                
                                <div class="mt-4">
                                    <h6 class="mb-3">Chef de département</h6>
                                    @if($departmentHead)
                                        <div class="d-flex align-items-center">
                                            @if($departmentHead->profile_photo_path)
                                                <img src="{{ Storage::url($departmentHead->profile_photo_path) }}" alt="Photo" class="rounded-circle me-3" width="48" height="48">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 18px;">
                                                    {{ substr($departmentHead->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $departmentHead->name }}</h6>
                                                <small class="text-muted">{{ $departmentHead->email }}</small>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changeDepartmentHeadModal">
                                                <i class="fas fa-exchange-alt me-1"></i> Changer le chef
                                            </button>
                                        </div>
                                    @else
                                        <p class="text-danger">Aucun chef de département n'est actuellement assigné.</p>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignDepartmentHeadModal">
                                            <i class="fas fa-user-plus me-1"></i> Assigner un chef
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Missions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Missions récentes</h5>
                        <a href="{{ route('director.all_missions', ['department' => $department]) }}" class="btn btn-sm btn-outline-primary">
                            Voir toutes les missions
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if($missions->isEmpty())
                            <div class="p-4 text-center">
                                <p class="text-muted mb-0">Aucune mission trouvée pour ce département.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Enseignant</th>
                                            <th>Mission</th>
                                            <th>Destination</th>
                                            <th>Dates</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($missions as $mission)
                                            <tr class="mission-row">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($mission->user->profile_photo_path)
                                                            <img src="{{ Storage::url($mission->user->profile_photo_path) }}" alt="Photo" class="rounded-circle me-2" width="36" height="36">
                                                        @else
                                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 14px;">
                                                                {{ substr($mission->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div>{{ $mission->user->name }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $mission->title }}</div>
                                                    <div class="small">{{ Str::limit($mission->objective, 50) }}</div>
                                                </td>
                                                <td>{{ $mission->destination_city }}</td>
                                                <td>
                                                    <div>{{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }}</div>
                                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</div>
                                                </td>
                                                <td>
                                                    @switch($mission->status)
                                                        @case('soumise')
                                                            <span class="badge bg-warning text-dark">En attente</span>
                                                            @break
                                                        @case('validee_chef')
                                                            <span class="badge bg-info">Validée (chef)</span>
                                                            @break
                                                        @case('validee_directeur')
                                                            <span class="badge bg-primary">Validée (dir.)</span>
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
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('director.mission_details', $mission->id) }}" class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($mission->status === 'validee_chef')
                                                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $mission->id }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="text-muted small">
                                    Affichage de {{ $missions->firstItem() ?? 0 }} à {{ $missions->lastItem() ?? 0 }} sur {{ $missions->total() }} résultats
                                </div>
                                <div>
                                    {{ $missions->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modals for Editing Department Settings -->
                <div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('director.update_department', $department) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editDepartmentModalLabel">Modifier le département</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="budgetEdit" class="form-label">Budget annuel (DH)</label>
                                        <input type="number" step="0.01" class="form-control" id="budgetEdit" name="budget" value="{{ $departmentSettings->budget }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="descriptionEdit" class="form-label">Description</label>
                                        <textarea class="form-control" id="descriptionEdit" name="description" rows="3">{{ $departmentSettings->description }}</textarea>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="directorValidationEdit" name="director_validation" {{ $departmentSettings->director_validation ? 'checked' : '' }}>
                                        <label class="form-check-label" for="directorValidationEdit">Requérir la validation du directeur</label>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="budgetCheckEdit" name="budget_check" {{ $departmentSettings->budget_check ? 'checked' : '' }}>
                                        <label class="form-check-label" for="budgetCheckEdit">Vérification automatique du budget</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modals for Changing/Assigning Department Head -->
                <div class="modal fade" id="changeDepartmentHeadModal" tabindex="-1" aria-labelledby="changeDepartmentHeadModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('director.change_department_head', $department) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changeDepartmentHeadModalLabel">Changer le chef de département</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="newDepartmentHead" class="form-label">Sélectionner un nouveau chef</label>
                                        <select class="form-select" id="newDepartmentHead" name="department_head_id" required>
                                            <option value="">Choisir...</option>
                                            @foreach($allTeachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Changer le chef</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="assignDepartmentHeadModal" tabindex="-1" aria-labelledby="assignDepartmentHeadModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('director.assign_department_head', $department) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="assignDepartmentHeadModalLabel">Assigner un chef de département</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="departmentHeadAssign" class="form-label">Sélectionner le chef</label>
                                        <select class="form-select" id="departmentHeadAssign" name="department_head_id" required>
                                            <option value="">Choisir...</option>
                                            @foreach($allTeachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Assigner</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Missions Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyLabels) !!},
                datasets: [{
                    label: 'Missions',
                    data: {!! json_encode($monthlyData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($missionsByStatus)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($missionsByStatus)) !!},
                    backgroundColor: [
                        '#ffc107',
                        '#17a2b8',
                        '#007bff',
                        '#6c757d',
                        '#28a745',
                        '#dc3545'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'right' } }
            }
        });
    </script>
</body>
</html>