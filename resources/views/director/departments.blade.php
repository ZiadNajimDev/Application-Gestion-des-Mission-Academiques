<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Départements - Directeur</title>
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
        .validation-card {
            transition: all 0.3s ease;
        }
        .validation-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .dept-card {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .dept-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .budget-meter {
            height: 8px;
            border-radius: 4px;
            margin-top: 8px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        .budget-progress {
            height: 100%;
            background-color: #28a745;
        }
        .stat-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .stat-text {
            flex-grow: 1;
        }
        .mission-type-chart {
            width: 60px;
            height: 60px;
            position: relative;
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
                    <h1 class="h2">Gestion des Départements</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <form action="{{ route('director.departments') }}" method="GET" class="me-2 d-flex">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Rechercher un département..." name="search" value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                            <i class="fas fa-plus me-1"></i> Nouveau département
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#budgetAllocationModal">
                            <i class="fas fa-coins me-1"></i> Allouer des budgets
                        </button>
                    </div>
                </div>

                <!-- Success/Error Messages -->
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

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm validation-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                                        <i class="fas fa-university fa-2x"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0">{{ $departments->total() }}</h2>
                                        <p class="mb-0 text-muted">Départements</p>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm validation-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                                        <i class="fas fa-coins fa-2x"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0">{{ number_format($totalBudget, 2) }} DH</h2>
                                        <p class="mb-0 text-muted">Budget total</p>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm validation-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 me-3">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0">{{ $totalTeachers }}</h2>
                                        <p class="mb-0 text-muted">Enseignants</p>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Allocation Chart -->
               

                <!-- Departments List -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des départements</h5>
                        <span class="text-muted">{{ $departments->total() }} département(s)</span>
                    </div>
                    <div class="card-body p-0">
                        @if($departments->isEmpty())
                            <div class="p-4 text-center">
                                <i class="fas fa-university text-secondary fa-3x mb-3"></i>
                                <p class="text-muted mb-3">Aucun département n'a été trouvé.</p>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                                    <i class="fas fa-plus me-1"></i> Créer un département
                                </button>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Département</th>
                                            <th>Chef de département</th>
                                            <th>Enseignants</th>
                                            <th>Budget alloué</th>
                                            <th>Missions</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departments as $dept)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $dept->department }}</div>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-calendar-alt me-1"></i> Créé le {{ \Carbon\Carbon::parse($dept->created_at)->format('d/m/Y') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($dept->department_head)
                                                        <div class="d-flex align-items-center">
                                                            @if($dept->department_head->profile_photo_path)
                                                                <img src="{{ Storage::url($dept->department_head->profile_photo_path) }}" alt="Photo" class="rounded-circle me-2" width="36" height="36">
                                                            @else
                                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 14px;">
                                                                    {{ substr($dept->department_head->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div>{{ $dept->department_head->name }}</div>
                                                                <div class="text-muted small">{{ $dept->department_head->email }}</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Non assigné</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <i class="fas fa-users text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <div>{{ $dept->teacherCount }} enseignant(s)</div>
                                                            
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <i class="fas fa-coins text-success"></i>
                                                        </div>
                                                        <div>
                                                            <div>{{ number_format($dept->budget, 2) }} DH</div>
                                                            <small class="text-muted">Budget annuel</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <i class="fas fa-plane text-info"></i>
                                                        </div>
                                                        <div>
                                                            <div>{{ $dept->missionCount }} mission(s)</div>
                                                            <a href="{{ route('director.all_missions', ['department' => $dept->department]) }}" class="small text-info">
                                                                Voir les missions
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('director.all_missions', ['department' => $dept->department]) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#editBudgetModal{{ $dept->id }}">
                                                            <i class="fas fa-coins"></i>
                                                        </button>
                                                        
                                                        
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
                                    Affichage de {{ $departments->firstItem() ?? 0 }} à {{ $departments->lastItem() ?? 0 }} sur {{ $departments->total() }} départements
                                </div>
                                <div>
                                    {{ $departments->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Department Details Cards -->
               
    <!-- Create Department Modal -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDepartmentModalLabel">Créer un nouveau département</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('director.create_department') }}" method="POST">
                    @csrf
                    @if($errors->any())
                        <div class="alert alert-danger m-3">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="department_name" class="form-label">Nom du département *</label>
                            <input type="text" class="form-control" id="department_name" name="department_name" required>
                            <div class="form-text">Exemple: Informatique, Mathématiques, Physique, etc.</div>
                        </div>
                        <div class="mb-3">
                            <label for="department_budget" class="form-label">Budget initial (DH) *</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="department_budget" name="department_budget" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="department_description" class="form-label">Description</label>
                            <textarea class="form-control" id="department_description" name="department_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Créer le département</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Budget Allocation Modal -->
    <div class="modal fade" id="budgetAllocationModal" tabindex="-1" aria-labelledby="budgetAllocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="budgetAllocationModalLabel">Allocation de budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('director.allocate_budgets') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="totalBudget" class="form-label">Budget total pour l'année {{ date('Y') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control" id="totalBudget" name="totalBudget" value="{{ $totalBudget }}">
                                <span class="input-group-text">DH</span>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label">Allocation par département</label>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="progress mb-3" style="height: 24px;">
                                        @foreach($departments as $dept)
                                            @php
                                                $percent = $totalBudget > 0 ? ($dept->budget / $totalBudget) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-{{ $loop->index % 4 == 0 ? 'primary' : ($loop->index % 4 == 1 ? 'success' : ($loop->index % 4 == 2 ? 'info' : 'warning')) }}" 
                                                role="progressbar" 
                                                style="width: {{ $percent }}%;" 
                                                aria-valuenow="{{ $percent }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100"
                                                title="{{ $dept->department }}: {{ number_format($dept->budget, 2) }} DH">
                                                {{ round($percent) }}%
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="text-muted">
                                        Budget restant: <span id="remainingBudget">0.00</span> DH
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Département</th>
                                            <th>Budget actuel</th>
                                            <th>Nouveau budget</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departments as $dept)
                                            <tr>
                                                <td>{{ $dept->department }}</td>
                                                <td>{{ number_format($dept->budget, 2) }} DH</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" min="0" class="form-control dept-budget" 
                                                            id="budget_{{ $dept->id }}" 
                                                            name="budgets[{{ $dept->id }}]" 
                                                            value="{{ $dept->budget }}"
                                                            data-dept-id="{{ $dept->id }}">
                                                        <span class="input-group-text">DH</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span id="percent_{{ $dept->id }}">
                                                        {{ $totalBudget > 0 ? round(($dept->budget / $totalBudget) * 100) : 0 }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer les allocations</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Department Budget Modals -->
    @foreach($departments as $dept)
        <div class="modal fade" id="editBudgetModal{{ $dept->id }}" tabindex="-1" aria-labelledby="editBudgetModalLabel{{ $dept->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBudgetModalLabel{{ $dept->id }}">Modifier le budget - {{ $dept->department }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('director.update_department_budget', $dept->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="budget_edit_{{ $dept->id }}" class="form-label">Budget pour {{ $dept->department }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="budget_edit_{{ $dept->id }}" name="budget" value="{{ $dept->budget }}">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description_{{ $dept->id }}" class="form-label">Description / Notes</label>
                                <textarea class="form-control" id="description_{{ $dept->id }}" name="description" rows="3">{{ $dept->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Delete Department Modals -->
    @foreach($departments as $dept)
        <div class="modal fade" id="deleteDepartmentModal{{ $dept->id }}" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel{{ $dept->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteDepartmentModalLabel{{ $dept->id }}">Supprimer le département</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer le département <strong>{{ $dept->department }}</strong> ?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Cette action est irréversible. Le département ne peut être supprimé que s'il n'a pas d'utilisateurs ou de missions associés.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="{{ route('director.delete_department', $dept->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Budget Allocation Chart
        @if(!$departments->isEmpty())
        const budgetCtx = document.getElementById('budgetChart').getContext('2d');
        const budgetChart = new Chart(budgetCtx, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($departments as $dept)
                        '{{ $dept->department }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($departments as $dept)
                            {{ $dept->budget }},
                        @endforeach
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)',
                        'rgba(83, 102, 255, 0.7)',
                        'rgba(40, 159, 64, 0.7)',
                        'rgba(210, 102, 255, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.raw;
                                label += new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MAD' }).format(value);
                                return label;
                            }
                        }
                    }
                }
            }
        });
        @endif

        // Mission Type Charts for each department
        @foreach($departments as $index => $dept)
            const missionTypeCtx{{ $index }} = document.getElementById('missionTypeChart{{ $index }}').getContext('2d');
            new Chart(missionTypeCtx{{ $index }}, {
                type: 'doughnut',
                data: {
                    labels: ['Nationales', 'Internationales'],
                    datasets: [{
                        data: [{{ $dept->nationale_count }}, {{ $dept->internationale_count }}],
                        backgroundColor: [
                            'rgba(13, 110, 253, 0.7)',
                            'rgba(13, 202, 240, 0.7)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                }
            });
        @endforeach

        // Budget allocation form calculations
        document.addEventListener('DOMContentLoaded', function() {
            const totalBudgetInput = document.getElementById('totalBudget');
            const deptBudgetInputs = document.querySelectorAll('.dept-budget');
            const remainingBudgetSpan = document.getElementById('remainingBudget');

            function updateCalculations() {
                const totalBudget = parseFloat(totalBudgetInput.value) || 0;
                let allocatedBudget = 0;

                deptBudgetInputs.forEach(input => {
                    const deptBudget = parseFloat(input.value) || 0;
                    allocatedBudget += deptBudget;

                    const deptId = input.dataset.deptId;
                    const percentSpan = document.getElementById(`percent_${deptId}`);
                    if (percentSpan) {
                        const percent = totalBudget > 0 ? ((deptBudget / totalBudget) * 100).toFixed(1) : 0;
                        percentSpan.textContent = `${percent}%`;
                    }
                });

                const remainingBudget = totalBudget - allocatedBudget;
                remainingBudgetSpan.textContent = remainingBudget.toFixed(2);

                if (remainingBudget < 0) {
                    remainingBudgetSpan.classList.add('text-danger');
                    remainingBudgetSpan.classList.remove('text-success');
                } else {
                    remainingBudgetSpan.classList.add('text-success');
                    remainingBudgetSpan.classList.remove('text-danger');
                }
            }

            totalBudgetInput.addEventListener('input', updateCalculations);
            deptBudgetInputs.forEach(input => {
                input.addEventListener('input', updateCalculations);
            });

            // Initial calculation
            updateCalculations();
        });
    </script>
</body>
</html>