<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Chef de Département</title>
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
            cursor: pointer;
        }
        .validation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
        .status-card {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
        .quick-stat {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .quick-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
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
                            <a class="nav-link active" href="{{ route('chef.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chef.mission_validate') }}">
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
                            <a class="nav-link" href="{{ route('chef.settings') }}">
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
                    <h1 class="h2">Tableau de bord - Département {{ $user->department }}</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Ce mois</a></li>
                                <li><a class="dropdown-item" href="#">Ce trimestre</a></li>
                                <li><a class="dropdown-item" href="#">Cette année</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Personnalisé</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if(!$user->department)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Vous devez configurer votre département dans les paramètres pour accéder à cette page.
                        <a href="{{ route('chef.settings') }}" class="alert-link">Configurer maintenant</a>
                    </div>
                @else
                    <!-- Status Cards Row -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card status-card border-0 shadow-sm h-100" onclick="window.location.href='{{ route('chef.mission_validate') }}'">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3">
                                            <i class="fas fa-hourglass-half fa-2x"></i>
                                        </div>
                                        <div>
                                            <h2 class="mb-0">{{ $missionStats['pending'] }}</h2>
                                            <p class="mb-0 text-muted">En attente</p>
                                        </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Missions nécessitant votre validation
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card status-card border-0 shadow-sm h-100" onclick="window.location.href='{{ route('chef.department_missions') }}?status=validee_chef'">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 me-3">
                                            <i class="fas fa-thumbs-up fa-2x"></i>
                                        </div>
                                        <div>
                                            <h2 class="mb-0">{{ $missionStats['validated'] }}</h2>
                                            <p class="mb-0 text-muted">Validées</p>
                                        </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Missions que vous avez validées
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card status-card border-0 shadow-sm h-100" onclick="window.location.href='{{ route('chef.department_missions') }}?status=terminee'">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                        <div>
                                            <h2 class="mb-0">{{ $missionStats['completed'] }}</h2>
                                            <p class="mb-0 text-muted">Terminées</p>
                                        </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Missions complétées avec succès
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card status-card border-0 shadow-sm h-100" onclick="window.location.href='{{ route('chef.department_missions') }}?status=rejetee'">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 me-3">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                        <div>
                                            <h2 class="mb-0">{{ $missionStats['rejected'] }}</h2>
                                            <p class="mb-0 text-muted">Rejetées</p>
                                        </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Missions rejetées
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <!-- Missions Awaiting Validation -->
                        <div class="col-lg-8">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                    <h5 class="mb-0">Missions en attente de validation</h5>
                                    <a href="{{ route('chef.mission_validate') }}" class="btn btn-sm btn-primary">
                                        Voir tout
                                    </a>
                                </div>
                                <div class="card-body p-0">
                                    @if($pendingMissions->isEmpty())
                                        <div class="p-4 text-center">
                                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                            <p class="text-muted mb-0">Aucune mission en attente de validation.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Enseignant</th>
                                                        <th>Mission</th>
                                                        <th>Destination</th>
                                                        <th>Dates</th>
                                                        <th>Soumise le</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pendingMissions as $mission)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if($mission->user->profile_photo_path)
                                                                        <img src="{{ Storage::url($mission->user->profile_photo_path) }}" alt="Photo" class="rounded-circle me-2" width="32" height="32">
                                                                    @else
                                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                                                            {{ substr($mission->user->name, 0, 1) }}
                                                                        </div>
                                                                    @endif
                                                                    <span>{{ $mission->user->name }}</span>
                                                                </div>
                                                            </td>
                                                            <td>{{ $mission->title }}</td>
                                                            <td>{{ $mission->destination_city }}</td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($mission->created_at)->format('d/m/Y') }}</td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('chef.mission_details', $mission->id) }}" class="btn btn-outline-primary">
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
                                    @endif
                                </div>
                            </div>

                            <!-- Monthly Missions Chart -->
                            <div class="card shadow-sm">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Activité mensuelle ({{ date('Y') }})</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyMissionsChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Notifications Card -->
                            <div class="card shadow-sm">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Notifications récentes</h5>
                                    @if($notifications->count() > 0)
                                        <span class="badge bg-danger">{{ $notifications->where('read', false)->count() }}</span>
                                    @endif
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse($notifications as $notification)
                                            <a href="{{ $notification->link ?? '#' }}" class="list-group-item list-group-item-action {{ $notification->read ? 'bg-light' : '' }}">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1 text-{{ $notification->type }}">
                                                        <i class="fas fa-{{ $notification->icon }} me-2"></i> {{ $notification->title }}
                                                    </h6>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-1">{{ $notification->message }}</p>
                                            </a>
                                        @empty
                                            <div class="list-group-item text-center text-muted py-3">
                                                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                                <p class="mb-0">Aucune notification</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="card-footer bg-white text-center">
                                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-primary">Voir toutes les notifications</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
                        <h5 class="modal-title" id="approveModalLabel{{ $mission->id }}">Valider la mission</h5>
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
                                <span>Dates : {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="comments{{ $mission->id }}" class="form-label">Commentaires (optionnel)</label>
                                <textarea class="form-control" id="comments{{ $mission->id }}" name="comments" rows="3"></textarea>
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
        <div class="modal fade" id="rejectModal{{ $mission->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $mission->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel{{ $mission->id }}">Rejeter la mission</h5>
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
                                <span>Dates : {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="rejection_reason{{ $mission->id }}" class="form-label">Raison du rejet *</label>
                                <textarea class="form-control" id="rejection_reason{{ $mission->id }}" name="rejection_reason" rows="3" required></textarea>
                                <div class="form-text">Veuillez expliquer la raison du rejet. Cette explication sera visible par l'enseignant.</div>
                            </div>
                            <div class="mb-3">
                                <label for="reject_comments{{ $mission->id }}" class="form-label">Commentaires supplémentaires (optionnel)</label>
                                <textarea class="form-control" id="reject_comments{{ $mission->id }}" name="comments" rows="2"></textarea>
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Only initialize charts if department is set
        @if($user->department)
            // Monthly Missions Chart
            const monthlyCtx = document.getElementById('monthlyMissionsChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json($monthLabels),
                    datasets: [{
                        label: 'Nombre de missions',
                        data: @json($monthData),
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        @endif
    </script>
</body>
</html>