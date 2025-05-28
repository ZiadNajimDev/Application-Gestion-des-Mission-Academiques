<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports et Analyses - Directeur</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DateRangePicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
        .report-card {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
        }
        .kpi-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .kpi-trend {
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .trend-up {
            color: #198754;
        }
        .trend-down {
            color: #dc3545;
        }
        .trend-neutral {
            color: #6c757d;
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
        .date-range-picker {
            background: #fff;
            cursor: pointer;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
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
                            <a class="nav-link" href="{{ route('director.departments') }}">
                                <i class="fas fa-university me-2"></i> Départements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('director.reports') }}">
                                <i class="fas fa-chart-line me-2"></i> Rapports
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
                    <h1 class="h2">Rapports et Analyses</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <form action="{{ route('director.reports') }}" method="GET" class="me-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="text" id="daterange" name="daterange" class="form-control date-range-picker" 
                                    value="{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}" />
                                <input type="hidden" id="start_date" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" id="end_date" name="end_date" value="{{ $endDate }}">
                                <button class="btn btn-outline-primary" type="submit">Appliquer</button>
                            </div>
                        </form>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-1"></i> Exporter
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i> PDF</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i> Excel</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i> Imprimer</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Selected Period Banner -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <strong>Période sélectionnée:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                            <div class="mt-1">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('director.reports', ['start_date' => \Carbon\Carbon::now()->startOfMonth()->toDateString(), 'end_date' => \Carbon\Carbon::now()->toDateString()]) }}" class="btn btn-outline-primary">Ce mois</a>
                                    <a href="{{ route('director.reports', ['start_date' => \Carbon\Carbon::now()->startOfQuarter()->toDateString(), 'end_date' => \Carbon\Carbon::now()->toDateString()]) }}" class="btn btn-outline-primary">Ce trimestre</a>
                                    <a href="{{ route('director.reports', ['start_date' => \Carbon\Carbon::now()->startOfYear()->toDateString(), 'end_date' => \Carbon\Carbon::now()->toDateString()]) }}" class="btn btn-outline-primary">Cette année</a>
                                    <a href="{{ route('director.reports', ['start_date' => \Carbon\Carbon::now()->subYear()->toDateString(), 'end_date' => \Carbon\Carbon::now()->toDateString()]) }}" class="btn btn-outline-primary">12 derniers mois</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card report-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="kpi-label">Total des Missions</div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="kpi-value">{{ $missionsCount }}</div>
                                    <div class="kpi-trend {{ $missionGrowth > 0 ? 'trend-up' : ($missionGrowth < 0 ? 'trend-down' : 'trend-neutral') }}">
                                        @if($missionGrowth > 0)
                                            <i class="fas fa-arrow-up me-1"></i>
                                        @elseif($missionGrowth < 0)
                                            <i class="fas fa-arrow-down me-1"></i>
                                        @else
                                            <i class="fas fa-equals me-1"></i>
                                        @endif
                                        {{ abs($missionGrowth) }}%
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card report-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="kpi-label">Taux de Completion</div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="kpi-value">{{ $completionRate }}%</div>
                                    <div class="kpi-trend">
                                        <span class="badge bg-success">{{ $statusBreakdown['terminee'] ?? 0 }} terminées</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionRate }}%" aria-valuenow="{{ $completionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card report-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="kpi-label">Durée Moyenne</div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="kpi-value">{{ round($avgDuration) }} <small>jours</small></div>
                                    <div class="kpi-trend">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $avgDuration * 10) }}%" aria-valuenow="{{ min(100, $avgDuration * 10) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card report-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="kpi-label">Délai d'Approbation</div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="kpi-value">{{ round($timeToApproval, 1) }} <small>jours</small></div>
                                    <div class="kpi-trend">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, $timeToApproval * 20) }}%" aria-valuenow="{{ min(100, $timeToApproval * 20) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Monthly Trend Chart -->
                    <div class="col-lg-8 mb-4">
                        <div class="card report-card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Évolution des missions</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyTrendChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Breakdown Chart -->
                    <div class="col-lg-4 mb-4">
                        <div class="card report-card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Répartition par statut</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="statusChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Department Breakdown -->
                    <div class="col-lg-6 mb-4">
                        <div class="card report-card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Missions par département</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="departmentChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Type Breakdown & Top Destinations -->
                    <div class="col-lg-6 mb-4">
                        <div class="card report-card shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Répartition par type</h5>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary active" data-bs-target="#typeChart" data-bs-toggle="tab">Types</button>
                                    <button type="button" class="btn btn-outline-primary" data-bs-target="#destinationsPanel" data-bs-toggle="tab">Destinations</button>
                                </div>
                            </div>
                            <div class="card-body tab-content">
                                <div class="tab-pane fade show active" id="typeChart">
                                    <canvas id="missionTypeChart" height="300"></canvas>
                                </div>
                                <div class="tab-pane fade" id="destinationsPanel">
                                    <h6 class="text-muted mb-3">Top destinations</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Destination</th>
                                                    <th>Missions</th>
                                                    <th>%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topDestinations as $destination)
                                                    <tr>
                                                        <td>{{ $destination->destination_city }}</td>
                                                        <td>{{ $destination->count }}</td>
                                                        <td>
                                                            @php
                                                                $percent = $missionsCount > 0 ? ($destination->count / $missionsCount * 100) : 0;
                                                            @endphp
                                                            <div class="progress" style="height: 5px; width: 100px;">
                                                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <small class="text-muted">{{ round($percent, 1) }}%</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Teachers Table -->
                <div class="card report-card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Enseignants les plus actifs</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Enseignant</th>
                                        <th>Département</th>
                                        <th>Missions</th>
                                        <th>Taux de complétion</th>
                                        <th>Missions internationales</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topTeachers as $teacher)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($teacher->profile_photo_path)
                                                        <img src="{{ Storage::url($teacher->profile_photo_path) }}" alt="Photo" class="rounded-circle me-2" width="36" height="36">
                                                    @else
                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 14px;">
                                                            {{ substr($teacher->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div>{{ $teacher->name }}</div>
                                                </div>
                                            </td>
                                            <td>{{ $teacher->department ?? 'Non défini' }}</td>
                                            <td class="fw-bold">{{ $teacher->missions_count }}</td>
                                            <td>
                                                @php
                                                    $completedCount = $teacher->missions()->where('status', 'terminee')->count();
                                                    $completionRate = $teacher->missions_count > 0 ? round(($completedCount / $teacher->missions_count) * 100) : 0;
                                                @endphp
                                                <div class="progress" style="height: 5px; width: 100px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionRate }}%" aria-valuenow="{{ $completionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small>{{ $completionRate }}%</small>
                                            </td>
                                            <td>
                                                @php
                                                    $internationalCount = $teacher->missions()->where('type', 'internationale')->count();
                                                    $internationalPercent = $teacher->missions_count > 0 ? round(($internationalCount / $teacher->missions_count) * 100) : 0;
                                                @endphp
                                                <div class="progress" style="height: 5px; width: 100px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $internationalPercent }}%" aria-valuenow="{{ $internationalPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small>{{ $internationalCount }} ({{ $internationalPercent }}%)</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('director.all_missions', ['user_id' => $teacher->id]) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Missions
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Report Sections -->
                <div class="row mb-4">
                    <!-- Mission Status Flow -->
                    <div class="col-lg-6 mb-4">
                        <div class="card report-card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Flux d'approbation</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-center">
                                        <div class="badge bg-primary py-2 px-3 mb-2">{{ $statusBreakdown['soumise'] ?? 0 }} missions</div>
                                        <div>Soumises</div>
                                    </div>
                                    <i class="fas fa-arrow-down mb-3 text-primary"></i>
                                    <div class="mb-3 text-center">
                                        <div class="badge bg-info py-2 px-3 mb-2">{{ $statusBreakdown['validee_chef'] ?? 0 }} missions</div>
                                        <div>Validées par chef de département</div>
                                    </div>
                                    <i class="fas fa-arrow-down mb-3 text-primary"></i>
                                    <div class="mb-3 text-center">
                                        <div class="badge bg-info py-2 px-3 mb-2">{{ $statusBreakdown['validee_directeur'] ?? 0 }} missions</div>
                                        <div>Validées par directeur</div>
                                    </div>
                                    <i class="fas fa-arrow-down mb-3 text-primary"></i>
                                    <div class="mb-3 text-center d-flex">
                                        <div class="me-5 text-center">
                                            <div class="badge bg-success py-2 px-3 mb-2">{{ $statusBreakdown['terminee'] ?? 0 }} missions</div>
                                            <div>Terminées</div>
                                        </div>
                                        <div class="ms-5 text-center">
                                            <div class="badge bg-danger py-2 px-3 mb-2">{{ $statusBreakdown['rejetee'] ?? 0 }} missions</div>
                                            <div>Rejetées</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rejection Analysis -->
                    <div class="col-lg-6 mb-4">
                        <div class="card report-card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Analyse des rejets</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="text-center mb-3">
                                            <div class="display-6">{{ $rejectionRate }}%</div>
                                            <div class="text-muted">Taux de rejet</div>
                                        </div>
                                        <div class="progress mb-3" style="height: 10px;">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $rejectionRate }}%" aria-valuenow="{{ $rejectionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="stat-item">
                                            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                                <i class="fas fa-times"></i>
                                            </div>
                                            <div class="stat-text">
                                                <div class="fw-bold">{{ $statusBreakdown['rejetee'] ?? 0 }} missions</div>
                                                <div class="small text-muted">rejetées au total</div>
                                            </div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="stat-text">
                                                <div class="fw-bold">{{ round($timeToApproval, 1) }} jours</div>
                                                <div class="small text-muted">délai moyen d'approbation</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    L'analyse détaillée des motifs de rejet sera disponible dans une prochaine mise à jour.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Moment.js -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- DateRangePicker JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        // Monthly Trend Chart
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(monthlyTrendCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($monthlyTrend as $data)
                        "{{ $data['month'] }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'Nombre de missions',
                    data: [
                        @foreach($monthlyTrend as $data)
                            {{ $data['count'] }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                }]
            },
            options: {
                responsive: true,
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

        // Status Chart
        const statusLabels = {
            'soumise': 'En attente',
            'validee_chef': 'Validée (chef)',
            'validee_directeur': 'Validée (dir.)',
            'billet_reserve': 'Billet réservé',
            'terminee': 'Terminée',
            'rejetee': 'Rejetée'
        };

        const statusColors = {
            'soumise': 'rgba(255, 193, 7, 0.7)',
            'validee_chef': 'rgba(23, 162, 184, 0.7)',
            'validee_directeur': 'rgba(13, 110, 253, 0.7)',
            'billet_reserve': 'rgba(108, 117, 125, 0.7)',
            'terminee': 'rgba(40, 167, 69, 0.7)',
            'rejetee': 'rgba(220, 53, 69, 0.7)'
        };

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusLabels).map(key => statusLabels[key]),
                datasets: [{
                    data: [
                        {{ $statusBreakdown['soumise'] ?? 0 }},
                        {{ $statusBreakdown['validee_chef'] ?? 0 }},
                        {{ $statusBreakdown['validee_directeur'] ?? 0 }},
                        {{ $statusBreakdown['billet_reserve'] ?? 0 }},
                        {{ $statusBreakdown['terminee'] ?? 0 }},
                        {{ $statusBreakdown['rejetee'] ?? 0 }}
                    ],
                    backgroundColor: Object.values(statusColors),
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
                },
                cutout: '60%'
            }
        });

        // Department Chart
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        new Chart(departmentCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($departmentBreakdown as $dept => $count)
                        "{{ $dept }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'Missions',
                    data: [
                        @foreach($departmentBreakdown as $count)
                            {{ $count }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                indexAxis: 'y',
            }
        });

        // Mission Type Chart
        const typeCtx = document.getElementById('missionTypeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: ['Nationale', 'Internationale'],
                datasets: [{
                    data: [
                        {{ $typeBreakdown['nationale'] ?? 0 }},
                        {{ $typeBreakdown['internationale'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(23, 162, 184, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Initialize DateRangePicker
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'DD/MM/YYYY',
                    separator: ' - ',
                    applyLabel: 'Appliquer',
                    cancelLabel: 'Annuler',
                    fromLabel: 'Du',
                    toLabel: 'Au',
                    customRangeLabel: 'Période personnalisée',
                    daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                    monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                    firstDay: 1
                }
            }, function(start, end, label) {
                // Update hidden inputs with selected dates
                document.getElementById('start_date').value = start.format('YYYY-MM-DD');
                document.getElementById('end_date').value = end.format('YYYY-MM-DD');
            });
        });
    </script>
</body>
</html>