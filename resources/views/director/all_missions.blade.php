<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Missions - Directeur</title>
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
        .mission-row:hover {
            background-color: rgba(0,0,0,0.025);
        }
        .filter-card {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .filter-toggle {
            cursor: pointer;
        }
        .sort-column {
            cursor: pointer;
        }
        .sort-column:hover {
            background-color: rgba(0,0,0,0.025);
        }
        .sort-icon {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            margin-left: 0.25rem;
        }
        .department-badge {
            min-width: 100px;
            text-align: center;
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
                            <a class="nav-link active" href="{{ route('director.all_missions') }}">
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
                    <h1 class="h2">Toutes les Missions</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="filterToggle">
                                <i class="fas fa-filter me-1"></i> Filtres
                            </button>
                            <a href="{{ route('director.all_missions') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Réinitialiser
                            </a>
                        </div>
                       
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-1"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-1"></i> PDF</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv me-1"></i> CSV</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="row mb-4">
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('director.all_missions', ['status' => 'soumise']) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm validation-card h-100">
                                <div class="card-body text-center">
                                    <div class="badge bg-warning text-dark mb-2 py-2 px-3 rounded-pill">En attente</div>
                                    <h2 class="mb-0">{{ $statusStats['soumise'] }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('director.all_missions', ['status' => 'validee_chef']) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm validation-card h-100">
                                <div class="card-body text-center">
                                    <div class="badge bg-info text-white mb-2 py-2 px-3 rounded-pill">Validée (chef)</div>
                                    <h2 class="mb-0">{{ $statusStats['validee_chef'] }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('director.all_missions', ['status' => 'validee_directeur']) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm validation-card h-100">
                                <div class="card-body text-center">
                                    <div class="badge bg-primary text-white mb-2 py-2 px-3 rounded-pill">Validée (dir.)</div>
                                    <h2 class="mb-0">{{ $statusStats['validee_directeur'] }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('director.all_missions', ['status' => 'billet_reserve']) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm validation-card h-100">
                                <div class="card-body text-center">
                                    <div class="badge bg-secondary text-white mb-2 py-2 px-3 rounded-pill">Billet réservé</div>
                                    <h2 class="mb-0">{{ $statusStats['billet_reserve'] }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('director.all_missions', ['status' => 'terminee']) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm validation-card h-100">
                                <div class="card-body text-center">
                                    <div class="badge bg-success text-white mb-2 py-2 px-3 rounded-pill">Terminée</div>
                                    <h2 class="mb-0">{{ $statusStats['terminee'] }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('director.all_missions', ['status' => 'rejetee']) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm validation-card h-100">
                                <div class="card-body text-center">
                                    <div class="badge bg-danger text-white mb-2 py-2 px-3 rounded-pill">Rejetée</div>
                                    <h2 class="mb-0">{{ $statusStats['rejetee'] }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="card shadow-sm mb-4 filter-card" id="filterSection" style="{{ !request()->hasAny(['status', 'department', 'type', 'search', 'date_from', 'date_to', 'created_from', 'created_to', 'sort_by']) ? 'display: none;' : '' }}">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filtres avancés</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('director.all_missions') }}" method="GET" class="row g-3">
                            <!-- Mission Status -->
                            <div class="col-md-4 col-sm-6">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="all">Tous les statuts</option>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Department Filter -->
                            <div class="col-md-4 col-sm-6">
                                <label for="department" class="form-label">Département</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="all">Tous les départements</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>{{ $department }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Mission Type -->
                            <div class="col-md-4 col-sm-6">
                                <label for="type" class="form-label">Type de mission</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="all">Tous les types</option>
                                    <option value="nationale" {{ request('type') == 'nationale' ? 'selected' : '' }}>Nationale</option>
                                    <option value="internationale" {{ request('type') == 'internationale' ? 'selected' : '' }}>Internationale</option>
                                </select>
                            </div>
                            
                            <!-- Mission Dates -->
                            <div class="col-md-3 col-sm-6">
                                <label for="date_from" class="form-label">Date de mission (début)</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <label for="date_to" class="form-label">Date de mission (fin)</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            
                            <!-- Created Dates -->
                            <div class="col-md-3 col-sm-6">
                                <label for="created_from" class="form-label">Date de création (début)</label>
                                <input type="date" class="form-control" id="created_from" name="created_from" value="{{ request('created_from') }}">
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <label for="created_to" class="form-label">Date de création (fin)</label>
                                <input type="date" class="form-control" id="created_to" name="created_to" value="{{ request('created_to') }}">
                            </div>
                            
                            <!-- Search -->
                            <div class="col-md-8">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Rechercher par titre, destination, enseignant..." value="{{ request('search') }}">
                            </div>
                            
                            <!-- Submit & Reset -->
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Appliquer les filtres
                                </button>
                                <a href="{{ route('director.all_missions') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-1"></i> Réinitialiser
                                </a>
                            </div>
                            
                            <!-- Sort fields (hidden) -->
                            <input type="hidden" name="sort_by" id="sort_by" value="{{ $sortBy }}">
                            <input type="hidden" name="sort_order" id="sort_order" value="{{ $sortOrder }}">
                        </form>
                    </div>
                </div>

                <!-- Missions List -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">
                            <span>Liste des missions</span>
                            @if(request()->hasAny(['status', 'department', 'type', 'search', 'date_from', 'date_to', 'created_from', 'created_to']))
                                <span class="badge bg-info ms-2">Filtré</span>
                            @endif
                        </h5>
                        <span class="text-muted">{{ $missions->total() }} résultat(s)</span>
                    </div>
                    <div class="card-body p-0">
                        @if($missions->isEmpty())
                            <div class="p-4 text-center">
                                <i class="fas fa-search text-secondary fa-3x mb-3"></i>
                                <p class="text-muted mb-0">Aucune mission trouvée avec les critères spécifiés.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort-column" data-sort="created_at">
                                                Soumise le
                                                @if($sortBy == 'created_at')
                                                    <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ms-1 text-secondary"></i>
                                                @endif
                                            </th>
                                            <th>Enseignant</th>
                                            <th>Département</th>
                                            <th class="sort-column" data-sort="title">
                                                Mission
                                                @if($sortBy == 'title')
                                                    <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ms-1 text-secondary"></i>
                                                @endif
                                            </th>
                                            <th class="sort-column" data-sort="destination_city">
                                                Destination
                                                @if($sortBy == 'destination_city')
                                                    <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ms-1 text-secondary"></i>
                                                @endif
                                            </th>
                                            <th class="sort-column" data-sort="start_date">
                                                Dates
                                                @if($sortBy == 'start_date')
                                                    <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ms-1 text-secondary"></i>
                                                @endif
                                            </th>
                                            <th class="sort-column" data-sort="status">
                                                Statut
                                                @if($sortBy == 'status')
                                                    <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ms-1 text-secondary"></i>
                                                @endif
                                            </th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($missions as $mission)
                                            <tr class="mission-row">
                                                <td>{{ \Carbon\Carbon::parse($mission->created_at)->format('d/m/Y') }}</td>
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
                                                    <span class="badge bg-info department-badge">{{ $mission->user->department }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $mission->title }}</div>
                                                    <div class="small">
                                                        <span class="badge bg-{{ $mission->type === 'nationale' ? 'primary' : 'info' }}">
                                                            {{ $mission->type === 'nationale' ? 'Nationale' : 'Internationale' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>{{ $mission->destination_city }}</td>
                                                <td>
                                                    <div>{{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }}</div>
                                                    <div>{{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</div>
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
            </main>
        </div>
    </div>

    <!-- Approval Modals for missions with status 'validee_chef' -->
    @foreach($missions as $mission)
        @if($mission->status === 'validee_chef')
            <!-- Approve Mission Modal -->
            <div class="modal fade" id="approveModal{{ $mission->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $mission->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="approveModalLabel{{ $mission->id }}">Approuver la mission</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('director.process_mission', $mission->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="decision" value="approve">
                            <div class="modal-body">
                                <p>Vous êtes sur le point d'approuver la mission suivante :</p>
                                <div class="alert alert-info">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>{{ $mission->title }}</strong>
                                        <span class="badge bg-{{ $mission->type === 'nationale' ? 'primary' : 'info' }}">{{ $mission->type === 'nationale' ? 'Nationale' : 'Internationale' }}</span>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <i class="fas fa-user me-1"></i> {{ $mission->user->name }}
                                        </div>
                                        <div class="col-md-6">
                                            <i class="fas fa-university me-1"></i> {{ $mission->user->department }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <i class="fas fa-map-marker-alt me-1"></i> {{ $mission->destination_city }}
                                        </div>
                                        <div class="col-md-6">
                                            <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="comments{{ $mission->id }}" class="form-label">Commentaires (optionnel)</label>
                                    <textarea class="form-control" id="comments{{ $mission->id }}" name="comments" rows="3"></textarea>
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
        @endif
    @endforeach

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle filters section
        document.getElementById('filterToggle').addEventListener('click', function() {
            const filterSection = document.getElementById('filterSection');
            if (filterSection.style.display === 'none') {
                filterSection.style.display = 'block';
            } else {
                filterSection.style.display = 'none';
            }
        });
        
        // Column sorting
        document.querySelectorAll('.sort-column').forEach(column => {
            column.addEventListener('click', function() {
                const sortBy = this.dataset.sort;
                const currentSortBy = document.getElementById('sort_by').value;
                const currentSortOrder = document.getElementById('sort_order').value;
                
                let newSortOrder = 'desc';
                if (sortBy === currentSortBy && currentSortOrder === 'desc') {
                    newSortOrder = 'asc';
                }
                
                document.getElementById('sort_by').value = sortBy;
                document.getElementById('sort_order').value = newSortOrder;
                
                // Submit the form
                document.querySelector('form').submit();
            });
        });
    </script>
</body>
</html>