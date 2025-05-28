<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques du Département {{ $user->department }} - Chef de Département</title>
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
        .stats-card {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .stats-card .card-body {
            padding: 1.5rem;
        }
        .stats-card .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stats-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 0.8rem;
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
                            <a class="nav-link active" href="{{ route('chef.department_stats') }}">
                                <i class="fas fa-chart-pie me-2"></i> Statistiques
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
                    <h1 class="h2">Statistiques du Département {{ $user->department }}</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download me-1"></i> Exporter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" id="yearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar-alt me-1"></i> {{ date('Y') }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="yearDropdown">
                                @foreach(array_keys($missionsByYear) as $year)
                                    <li><a class="dropdown-item" href="#">{{ $year }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                @if(!$user->department)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Vous devez configurer votre département dans les paramètres pour accéder aux statistiques.
                        <a href="{{ route('chef.settings') }}" class="alert-link">Configurer maintenant</a>
                    </div>
                @else
                    <!-- Stats Overview Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card stats-card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                                            <i class="fas fa-clipboard-list"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-muted">Total Missions</h6>
                                            <h2 class="mb-0">{{ $missionsThisYear }}</h2>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="text-primary">{{ date('Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card stats-card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-muted">Taux de Complétion</h6>
                                            <h2 class="mb-0">{{ $completionRate }}%</h2>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionRate }}%;" aria-valuenow="{{ $completionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="text-success">{{ $statusStats['terminee'] }}/{{ array_sum($statusStats) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card stats-card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-muted">Durée Moyenne</h6>
                                            <h2 class="mb-0">{{ round($averageDuration) }} jours</h2>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $averageDuration * 5) }}%;" aria-valuenow="{{ min(100, $averageDuration * 5) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="text-info">Missions</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card stats-card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-muted">Taux de Rejet</h6>
                                            <h2 class="mb-0">{{ $rejectionRate }}%</h2>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $rejectionRate }}%;" aria-valuenow="{{ $rejectionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="text-danger">{{ $statusStats['rejetee'] }}/{{ array_sum($statusStats) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <!-- Status Distribution Chart -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Distribution par statut</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" height="260"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Type Distribution Chart -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Distribution par type</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="typeChart" height="260"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <!-- Monthly Missions Chart -->
                        <div class="col-md-8 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Missions par mois ({{ date('Y') }})</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Popular Destinations -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Destinations populaires</h5>
                                </div>
                                <div class="card-body">
                                    @if($popularDestinations->isEmpty())
                                        <div class="alert alert-info">
                                            Aucune donnée disponible.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Ville</th>
                                                        <th>Missions</th>
                                                        <th>%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($popularDestinations as $destination)
                                                        <tr>
                                                            <td>{{ $destination->destination_city }}</td>
                                                            <td>{{ $destination->count }}</td>
                                                            <td>
                                                                @php
                                                                    $percentage = $missionsThisYear > 0 ? round(($destination->count / $missionsThisYear) * 100) : 0;
                                                                @endphp
                                                                <div class="d-flex align-items-center">
                                                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                    <span class="text-muted small">{{ $percentage }}%</span>
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
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <!-- Yearly Missions Chart -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Évolution annuelle</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="yearlyChart" height="260"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Teachers with Most Missions -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Enseignants les plus actifs ({{ date('Y') }})</h5>
                                </div>
                                <div class="card-body">
                                    @if($teachersWithMostMissions->isEmpty())
                                        <div class="alert alert-info">
                                            Aucune donnée disponible.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Enseignant</th>
                                                        <th>Missions</th>
                                                        <th>%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($teachersWithMostMissions as $teacher)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if($teacher->profile_photo_path)
                                                                        <img src="{{ Storage::url($teacher->profile_photo_path) }}" alt="Photo" class="rounded-circle me-2" width="32" height="32">
                                                                    @else
                                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                                                            {{ substr($teacher->name, 0, 1) }}
                                                                        </div>
                                                                    @endif
                                                                    <div>{{ $teacher->name }}</div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $teacher->missions_count }}</td>
                                                            <td>
                                                                @php
                                                                    $percentage = $missionsThisYear > 0 ? round(($teacher->missions_count / $missionsThisYear) * 100) : 0;
                                                                @endphp
                                                                <div class="d-flex align-items-center">
                                                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                    <span class="text-muted small">{{ $percentage }}%</span>
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
                        </div>
                    </div>
                    
                    <!-- Budget Overview -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Budget du département</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="mb-4">Budget total: {{ number_format($departmentBudget, 2) }} DH</h4>
                                    
                                    <h6 class="text-muted">Répartition par type de mission</h6>
                                    <div class="progress mb-4" style="height: 20px;">
                                        @php
                                            $nationalePercentage = array_sum($typeStats) > 0 ? ($typeStats['nationale'] / array_sum($typeStats)) * 100 : 0;
                                            $internationalePercentage = array_sum($typeStats) > 0 ? ($typeStats['internationale'] / array_sum($typeStats)) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $nationalePercentage }}%;" aria-valuenow="{{ $nationalePercentage }}" aria-valuemin="0" aria-valuemax="100">
                                            Nationale ({{ $typeStats['nationale'] }})
                                        </div>
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $internationalePercentage }}%;" aria-valuenow="{{ $internationalePercentage }}" aria-valuemin="0" aria-valuemax="100">
                                            Internationale ({{ $typeStats['internationale'] }})
                                        </div>
                                    </div>
                                    
                                    <!-- Budget Estimation -->
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Note:</strong> Le suivi détaillé des dépenses sera disponible dans une prochaine mise à jour.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <canvas id="budgetChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Only initialize charts if department is set
        @if($user->department)
            // Status Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'Validées (chef)', 'Validées (directeur)', 'Billets réservés', 'Terminées', 'Rejetées'],
                    datasets: [{
                        data: [
                            {{ $statusStats['soumise'] }},
                            {{ $statusStats['validee_chef'] }},
                            {{ $statusStats['validee_directeur'] }},
                            {{ $statusStats['billet_reserve'] }},
                            {{ $statusStats['terminee'] }},
                            {{ $statusStats['rejetee'] }}
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
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });

            // Type Chart
            const typeCtx = document.getElementById('typeChart').getContext('2d');
            new Chart(typeCtx, {
                type: 'pie',
                data: {
                    labels: ['Nationales', 'Internationales'],
                    datasets: [{
                        data: [
                            {{ $typeStats['nationale'] }},
                            {{ $typeStats['internationale'] }}
                        ],
                        backgroundColor: [
                            '#007bff', // primary
                            '#17a2b8'  // info
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });

            // Monthly Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json($monthLabels),
                    datasets: [{
                        label: 'Missions',
                        data: @json($monthData),
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
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

            // Yearly Chart
            const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
            new Chart(yearlyCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(@json($missionsByYear)),
                    datasets: [{
                        label: 'Missions',
                        data: Object.values(@json($missionsByYear)),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
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

            // Budget Chart
            const budgetCtx = document.getElementById('budgetChart').getContext('2d');
            new Chart(budgetCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Nationales', 'Internationales'],
                    datasets: [{
                        data: [
                            {{ $typeStats['nationale'] }},
                            {{ $typeStats['internationale'] }}
                        ],
                        backgroundColor: [
                            '#007bff', // primary
                            '#17a2b8'  // info
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Répartition des missions'
                        }
                    }
                }
            });
        @endif
    </script>
</body>
</html>