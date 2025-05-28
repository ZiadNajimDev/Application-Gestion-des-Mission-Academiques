<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missions du Département {{ $user->department }} - Chef de Département</title>
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
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
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
                            <a class="nav-link" href="{{ route('chef.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
    <a class="nav-link {{ Route::currentRouteName() == 'chef.mission_validate' ? 'active' : '' }}" href="{{ route('chef.mission_validate') }}">
        <i class="fas fa-check-circle me-2"></i> Validations
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ Route::currentRouteName() == 'chef.department_missions' ? 'active' : '' }}" href="{{ route('chef.department_missions') }}">
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
                    <h1 class="h2">Missions du Département {{ $user->department }}</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                       
                    </div>
                </div>

                @if(!$user->department)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Vous devez configurer votre département dans les paramètres pour accéder à cette page.
                        <a href="{{ route('chef.settings') }}" class="alert-link">Configurer maintenant</a>
                    </div>
                @else
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0">Toutes les missions du département {{ $user->department }}</h5>
                            <div class="d-flex gap-2">
                                <form action="{{ route('chef.department_missions') }}" method="GET" class="d-flex gap-2">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Toutes</option>
                                        <option value="soumise" {{ request('status') == 'soumise' ? 'selected' : '' }}>En attente</option>
                                        <option value="validee_chef" {{ request('status') == 'validee_chef' ? 'selected' : '' }}>Validées (chef)</option>
                                        <option value="validee_directeur" {{ request('status') == 'validee_directeur' ? 'selected' : '' }}>Validées (directeur)</option>
                                        <option value="billet_reserve" {{ request('status') == 'billet_reserve' ? 'selected' : '' }}>Billets réservés</option>
                                        <option value="terminee" {{ request('status') == 'terminee' ? 'selected' : '' }}>Terminées</option>
                                        <option value="rejetee" {{ request('status') == 'rejetee' ? 'selected' : '' }}>Rejetées</option>
                                    </select>
                                </form>
                               
                            </div>
                        </div>
                        <div class="card-body">
                        @if($pendingMissions->isEmpty())
                                <div class="alert alert-info">
                                Aucune mission en attente de validation pour le département {{ $user->department }}.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Enseignant</th>
                                                <th>Mission</th>
                                                <th>Dates</th>
                                                <th>Type</th>
                                                <th>Statut</th>
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
                                                            <div>{{ $mission->user->name }}</div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>{{ $mission->title }}</div>
                                                        <small class="text-muted">{{ $mission->destination_city }}</small>
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}
                                                        <br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($mission->start_date)->diffInDays(\Carbon\Carbon::parse($mission->end_date)) + 1 }} jour(s)</small>
                                                    </td>
                                                    <td>
                                                        @if($mission->type === 'nationale')
                                                            <span class="badge bg-primary">Nationale</span>
                                                        @else
                                                            <span class="badge bg-info">Internationale</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @switch($mission->status)
                                                            @case('soumise')
                                                                <span class="badge bg-warning status-badge">En attente</span>
                                                                @break
                                                            @case('validee_chef')
                                                                <span class="badge bg-info status-badge">Validée (chef)</span>
                                                                @break
                                                            @case('validee_directeur')
                                                                <span class="badge bg-primary status-badge">Validée (directeur)</span>
                                                                @break
                                                            @case('billet_reserve')
                                                                <span class="badge bg-secondary status-badge">Billet réservé</span>
                                                                @break
                                                            @case('terminee')
                                                                <span class="badge bg-success status-badge">Terminée</span>
                                                                @break
                                                            @case('rejetee')
                                                                <span class="badge bg-danger status-badge">Rejetée</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary status-badge">{{ $mission->status }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($mission->created_at)->format('d/m/Y') }}
                                                        <br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($mission->created_at)->diffForHumans() }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('chef.mission_details', $mission->id) }}" class="btn btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($mission->status === 'soumise')
                                                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $mission->id }}">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $mission->id }}">
                                                                    <i class="fas fa-times"></i>
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
                                <div class="d-flex justify-content-center mt-4">
                                {{ $pendingMissions->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <!-- Approve Mission Modals -->
    @isset($pendingMissions)
    @foreach($pendingMissions as $mission)
            @if($mission->status === 'soumise')
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
            @endif
        @endforeach
    @endisset

    <!-- Reject Mission Modals -->
    @isset($missions)
        @foreach($missions as $mission)
            @if($mission->status === 'soumise')
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
            @endif
        @endforeach
    @endisset

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Only initialize charts if department is set
        @if($user->department)
            // Get mission counts by status
            const statusCounts = {
                soumise: {{ $user->departmentMissions()->where('status', 'soumise')->count() }},
                validee_chef: {{ $user->departmentMissions()->where('status', 'validee_chef')->count() }},
                validee_directeur: {{ $user->departmentMissions()->where('status', 'validee_directeur')->count() }},
                billet_reserve: {{ $user->departmentMissions()->where('status', 'billet_reserve')->count() }},
                terminee: {{ $user->departmentMissions()->where('status', 'terminee')->count() }},
                rejetee: {{ $user->departmentMissions()->where('status', 'rejetee')->count() }}
            };

            // Missions Stats Chart
            const missionsStatsCtx = document.getElementById('missionsStatsChart').getContext('2d');
            new Chart(missionsStatsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'Validées (chef)', 'Validées (directeur)', 'Billets réservés', 'Terminées', 'Rejetées'],
                    datasets: [{
                        data: [
                            statusCounts.soumise,
                            statusCounts.validee_chef,
                            statusCounts.validee_directeur,
                            statusCounts.billet_reserve,
                            statusCounts.terminee,
                            statusCounts.rejetee
                        ],
                        backgroundColor: [
                            '#ffc107', // warning
                            '#17a2b8', // info
                            '#007bff', // primary
                            '#6c757d', // secondary
                            '#28a745', // success
                            '#dc3545'  // danger
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
                        }
                    }
                }
            });

            // Monthly Missions Chart
            @php
                // Get missions by month for current year
                $monthlyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $monthlyMissions = $user->departmentMissions()
                    ->whereYear('created_at', date('Y'))
                    ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                    ->groupBy('month')
                    ->pluck('count', 'month')
                    ->toArray();
                
                foreach ($monthlyMissions as $month => $count) {
                    $monthlyData[$month - 1] = $count;
                }
            @endphp

            const monthlyMissionsCtx = document.getElementById('monthlyMissionsChart').getContext('2d');
            new Chart(monthlyMissionsCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Missions',
                        data: @json($monthlyData),
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
                    }
                }
            });
        @endif
    </script>
</body>
</html>