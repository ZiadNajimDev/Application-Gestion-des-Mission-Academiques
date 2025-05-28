
@extends('layouts.accountant')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord du service financier')

@section('content')
<!-- Stats Cards Row -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                        <i class="fas fa-plane fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">{{ $pendingReservations }}</h2>
                        <p class="mb-0 text-muted">Réservations en attente</p>
                    </div>
                </div>
                <div class="progress mb-2">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-end">
                    <a href="{{ route('accountant.reservations') }}" class="text-primary text-decoration-none">
                        Voir les détails <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="fas fa-file-invoice fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">{{ $pendingProofs }}</h2>
                        <p class="mb-0 text-muted">Justificatifs à vérifier</p>
                    </div>
                </div>
                <div class="progress mb-2">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-end">
                    <a href="{{ route('accountant.proofs') }}" class="text-success text-decoration-none">
                        Voir les détails <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">{{ $unpaidMissions }}</h2>
                        <p class="mb-0 text-muted">Paiements à effectuer</p>
                    </div>
                </div>
                <div class="progress mb-2">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-end">
                    <a href="{{ route('accountant.payments') }}" class="text-warning text-decoration-none">
                        Voir les détails <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 me-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">{{ $readyForCompletion }}</h2>
                        <p class="mb-0 text-muted">Prêtes à finaliser</p>
                    </div>
                </div>
                <div class="progress mb-2">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-end">
                    <a href="{{ route('accountant.proofs') }}?filter=ready" class="text-info text-decoration-none">
                        Voir les détails <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Stats Row -->
<div class="row mb-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Statistiques des missions</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active">
                            Cette année
                        </button>
                        <button type="button" class="btn btn-outline-secondary">
                            Tout
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="missionsChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Résumé financier</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Total des paiements (année en cours)</span>
                        <span class="fw-bold">{{ number_format($totalPayments, 0) }} DH</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Paiements du mois</span>
                        <span class="fw-bold">{{ number_format($monthlyPayments, 0) }} DH</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($monthlyPayments/$totalPayments)*100 }}%"></div>
                    </div>
                </div>
                
                <div class="row text-center mt-4">
                    <div class="col-6 border-end">
                        <h3 class="fw-bold text-primary">{{ $completedReservations }}</h3>
                        <p class="text-muted mb-0">Réservations complétées</p>
                    </div>
                    <div class="col-6">
                        <h3 class="fw-bold text-success">{{ $totalMissions }}</h3>
                        <p class="text-muted mb-0">Total des missions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Items Row -->
<div class="row">
    <!-- Missions Needing Reservations -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Missions en attente de réservation</h5>
                    <a href="{{ route('accountant.reservations') }}" class="btn btn-sm btn-primary">
                        Voir tout
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($reservationsMissions->isEmpty())
                    <div class="p-4 text-center">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="text-muted mb-0">Aucune mission en attente de réservation.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($reservationsMissions as $mission)
                            <a href="{{ route('accountant.reservations') }}" class="list-group-item list-group-item-action px-4 py-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $mission->title }}</h6>
                                    <small class="text-muted">{{ Carbon\Carbon::parse($mission->created_at)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-muted">
                                    <i class="fas fa-user me-1"></i> {{ $mission->user->name }}
                                    <span class="ms-2"><i class="fas fa-map-marker-alt me-1"></i> {{ $mission->destination_city }}</span>
                                </p>
                                <div>
                                    <span class="badge bg-primary">{{ Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }}</span>
                                    <small>→</small>
                                    <span class="badge bg-primary">{{ Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recent Proof Documents -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Justificatifs récents</h5>
                    <a href="{{ route('accountant.proofs') }}" class="btn btn-sm btn-primary">
                        Voir tout
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($recentProofs->isEmpty())
                    <div class="p-4 text-center">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="text-muted mb-0">Aucun justificatif récent à afficher.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($recentProofs as $proof)
                            <a href="{{ route('accountant.proofs.show', $proof->id) }}" class="list-group-item list-group-item-action px-4 py-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $proof->title }}</h6>
                                    <span class="badge 
                                        @if($proof->status === 'approved') bg-success 
                                        @elseif($proof->status === 'rejected') bg-danger 
                                        @else bg-warning @endif">
                                        {{ $proof->status === 'approved' ? 'Approuvé' : ($proof->status === 'rejected' ? 'Rejeté' : 'En attente') }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted">
                                    <i class="fas fa-user me-1"></i> {{ $proof->mission->user->name }}
                                    <span class="ms-2"><i class="fas fa-money-bill-wave me-1"></i> 
                                        {{ $proof->amount ? number_format($proof->amount, 0) . ' DH' : 'N/A' }}
                                    </span>
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i> {{ $proof->created_at->diffForHumans() }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Missions Chart
    const ctx = document.getElementById('missionsChart').getContext('2d');
    const missionsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Missions par mois',
                data: @json($missionCounts),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
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
</script>
@endsection