<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missions à Valider - Directeur</title>
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
                    <h1 class="h2">Missions en attente de validation</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i> Département
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item {{ !request('department') ? 'active' : '' }}" href="{{ route('director.pending_missions') }}">Tous les départements</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($departments as $dept)
                                    <li>
                                        <a class="dropdown-item {{ request('department') === $dept ? 'active' : '' }}" 
                                           href="{{ route('director.pending_missions', ['department' => $dept]) }}">
                                            {{ $dept }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
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

                <!-- Date Range Filter Form -->
                <div class="collapse mb-3" id="dateRangeFilter">
                    <div class="card card-body">
                        <form action="{{ route('director.pending_missions') }}" method="GET" class="row g-3">
                            @if(request()->has('department'))
                                <input type="hidden" name="department" value="{{ request('department') }}">
                            @endif
                            <div class="col-md-5">
                                <label for="date_from" class="form-label">Du</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-5">
                                <label for="date_to" class="form-label">Au</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Status Cards -->
               

                <!-- Search and Filters -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Liste des missions à valider</h5>
                        <form action="{{ route('director.pending_missions') }}" method="GET" class="d-flex">
                            @if(request()->has('department'))
                                <input type="hidden" name="department" value="{{ request('department') }}">
                            @endif
                            @if(request()->has('date_from'))
                                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                            @endif
                            @if(request()->has('date_to'))
                                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                            @endif
                            <div class="input-group">
                                
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        @if($pendingMissions->isEmpty())
                            <div class="p-4 text-center">
                                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                <p class="text-muted mb-0">Aucune mission en attente de validation.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Enseignant</th>
                                            <th>Département</th>
                                            <th>Mission</th>
                                            <th>Destination</th>
                                            <th>Type</th>
                                            <th>Dates</th>
                                            <th>Validée par</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingMissions as $mission)
                                            <tr class="mission-row">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($mission->user->profile_photo_path)
                                                            <img src="{{ Storage::url($mission->user->profile_photo_path) }}" alt="Photo" class="rounded-circle me-2" width="40" height="40">
                                                        @else
                                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; font-size: 16px;">
                                                                {{ substr($mission->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $mission->user->name }}</div>
                                                            <div class="text-muted small">{{ $mission->user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info department-badge">{{ $mission->user->department }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $mission->title }}</div>
                                                    <div class="text-muted small">{{ Str::limit($mission->objective, 50) }}</div>
                                                </td>
                                                <td>{{ $mission->destination_city }}</td>
                                                <td>
                                                    @if($mission->type === 'nationale')
                                                        <span class="badge bg-primary">Nationale</span>
                                                    @else
                                                        <span class="badge bg-info">Internationale</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>{{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }}</div>
                                                    <div>{{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</div>
                                                    <div class="text-muted small">
                                                        {{ \Carbon\Carbon::parse($mission->start_date)->diffInDays(\Carbon\Carbon::parse($mission->end_date)) + 1 }} jour(s)
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-user-check me-1"></i> Chef de département
                                                    </div>
                                                    <div class="small">{{ \Carbon\Carbon::parse($mission->chef_approval_date)->format('d/m/Y') }}</div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('director.mission_details', $mission->id) }}" class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $mission->id }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $mission->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center py-3">
                                {{ $pendingMissions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Approval Modals -->
    @foreach($pendingMissions as $mission)
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
                            
                            @if($mission->chef_comments)
                                <div class="alert alert-secondary">
                                    <strong>Commentaires du chef de département :</strong><br>
                                    {{ $mission->chef_comments }}
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <label for="comments{{ $mission->id }}" class="form-label">Vos commentaires (optionnel)</label>
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

        <!-- Reject Mission Modal -->
        <div class="modal fade" id="rejectModal{{ $mission->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $mission->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel{{ $mission->id }}">Rejeter la mission</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('director.process_mission', $mission->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="decision" value="reject">
                        <div class="modal-body">
                            <p>Vous êtes sur le point de rejeter la mission suivante :</p>
                            <div class="alert alert-warning">
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
                            
                            @if($mission->chef_comments)
                                <div class="alert alert-secondary">
                                    <strong>Commentaires du chef de département :</strong><br>
                                    {{ $mission->chef_comments }}
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <label for="rejection_reason{{ $mission->id }}" class="form-label">Raison du rejet *</label>
                                <textarea class="form-control" id="rejection_reason{{ $mission->id }}" name="rejection_reason" rows="3" required></textarea>
                                <div class="form-text">Cette explication sera visible par l'enseignant et le chef de département.</div>
                            </div>
                            <div class="mb-3">
                                <label for="comments{{ $mission->id }}" class="form-label">Commentaires supplémentaires (optionnel)</label>
                                <textarea class="form-control" id="comments{{ $mission->id }}" name="comments" rows="2"></textarea>
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
    @endforeach

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle date range filter
        document.getElementById('dateRangeBtn').addEventListener('click', function() {
            var dateRangeFilter = document.getElementById('dateRangeFilter');
            var bsCollapse = new bootstrap.Collapse(dateRangeFilter, {
                toggle: true
            });
        });
        
        // Show date range filter if date filters are applied
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('date_from') || urlParams.has('date_to')) {
                var dateRangeFilter = document.getElementById('dateRangeFilter');
                var bsCollapse = new bootstrap.Collapse(dateRangeFilter, {
                    show: true
                });
            }
        });
    </script>
</body>
</html>