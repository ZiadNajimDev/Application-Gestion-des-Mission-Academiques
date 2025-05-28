<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Enseignant</title>
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
                            <a class="nav-link active" href="{{ route('teacher.dashboard') }}">
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
                    <h1 class="h2">Tableau de bord</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            
                        </div>
                    </div>
                </div>

                <!-- Stats Cards Row -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total des missions</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMissions }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Missions terminées</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedMissions }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Missions en cours</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inProgressMissions }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            En attente de validation</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingValidationMissions }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Upcoming Missions Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Missions à venir</h5>
                            </div>
                            <div class="card-body">
                                @if($upcomingMissions->isEmpty())
                                    <p class="text-muted">Aucune mission à venir.</p>
                                @else
                                    <div class="list-group">
                                        @foreach($upcomingMissions as $mission)
                                            <a href="{{ route('teacher.missions.show', $mission->id) }}" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{ $mission->title }}</h6>
                                                    <small>
                                                        @if($mission->days_until > 0)
                                                            <span class="badge bg-info">Dans {{ $mission->days_until }} jour(s)</span>
                                                        @else
                                                            <span class="badge bg-danger">Aujourd'hui</span>
                                                        @endif
                                                    </small>
                                                </div>
                                                <p class="mb-1">{{ $mission->destination_city }} - {{ $mission->destination_institution }}</p>
                                                <small class="text-muted">{{ $mission->formatted_start_date }} - {{ $mission->formatted_end_date }}</small>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-white text-center">
                                <a href="{{ route('teacher.missions.index') }}" class="btn btn-sm btn-outline-primary">
                                    Voir toutes les missions
                                </a>
                            </div>
                        </div>

                        <!-- Missions Activity Chart -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Activité des missions</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="missionsChart" width="100%" height="30"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Notifications Card -->
                        <div class="card mb-4">
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

                        <!-- Mission Status Card -->
                        
                        <!-- Quick Actions Card -->
                        
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Missions Activity Chart
        const missionsCtx = document.getElementById('missionsChart').getContext('2d');
        const missionsChart = new Chart(missionsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Missions',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
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

        // Mission Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Soumise', 'Validée (chef)', 'Validée (directeur)', 'Billet réservé', 'Terminée', 'Rejetée'],
                datasets: [{
                    data: [
                        {{ $missionsByStatus['soumise'] }},
                        {{ $missionsByStatus['validee_chef'] }},
                        {{ $missionsByStatus['validee_directeur'] }},
                        {{ $missionsByStatus['billet_reserve'] }},
                        {{ $missionsByStatus['terminee'] }},
                        {{ $missionsByStatus['rejetee'] }}
                    ],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(108, 117, 125, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>