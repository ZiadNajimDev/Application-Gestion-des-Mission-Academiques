@extends('layouts.accountant')

@section('title', 'Gestion des Paiements')

@section('page-title', 'Gestion des Paiements')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    

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

    <!-- Department Budgets Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Budgets des Départements</h5>
                    <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="collapse" data-bs-target="#budgetsCollapse" aria-expanded="false" aria-controls="budgetsCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse" id="budgetsCollapse">
                    <div class="card-body">
                        <div class="row">
                            @foreach($departmentBudgets as $dept => $budget)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="card-title mb-0">{{ $dept }}</h6>
                                            <span class="badge bg-{{ $budget['remaining'] > 0 ? 'success' : 'danger' }}">
                                                {{ $budget['remaining'] > 0 ? 'Budget Disponible' : 'Budget Épuisé' }}
                                            </span>
                                        </div>
                                        <div class="progress mb-3" style="height: 8px;">
                                            @php
                                                $percentage = ($budget['spent'] / $budget['budget']) * 100;
                                            @endphp
                                            <div class="progress-bar bg-{{ $percentage > 90 ? 'danger' : ($percentage > 70 ? 'warning' : 'success') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $percentage }}%" 
                                                 aria-valuenow="{{ $percentage }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Budget Total:</span>
                                            <span class="fw-bold">{{ number_format($budget['budget'], 2) }} DH</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Dépensé:</span>
                                            <span class="text-danger">{{ number_format($budget['spent'], 2) }} DH</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Reste:</span>
                                            <span class="text-{{ $budget['remaining'] > 0 ? 'success' : 'danger' }} fw-bold">
                                                {{ number_format($budget['remaining'], 2) }} DH
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Missions Table -->
    <div class="card shadow">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Missions en attente de paiement</h5>
            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="collapse" data-bs-target="#missionsCollapse" aria-expanded="false" aria-controls="missionsCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse" id="missionsCollapse">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mission</th>
                                <th>Enseignant</th>
                                <th>Département</th>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Montants</th>
                                <th>Statut</th>
                                <th class="actions-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($missions as $mission)
                            <tr class="mission-row" data-payment-status="{{ $mission->payment ? 'paid' : 'pending' }}">
                                <td>
                                    <div class="fw-bold">{{ $mission->title }}</div>
                                    <small class="text-muted">{{ $mission->destination_city }}</small>
                                </td>
                                <td>
                                    {{ $mission->user->name }}
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $mission->user->department }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $mission->type === 'internationale' ? 'primary' : 'secondary' }}">
                                        {{ $mission->type === 'internationale' ? 'Internationale' : 'Nationale' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small">
                                        <div>Du: {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }}</div>
                                        <div>Au: {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $startDate = \Carbon\Carbon::parse($mission->start_date);
                                        $endDate = \Carbon\Carbon::parse($mission->end_date);
                                        $durationDays = $startDate->diffInDays($endDate) + 1;
                                        $dailyAllowance = $mission->type === 'internationale' ? 2000 : 400;
                                        $allowanceAmount = $dailyAllowance * $durationDays;
                                        $transportAmount = $mission->reservations->sum('cost');
                                        $additionalCosts = $mission->proofs->sum('amount');
                                        $totalAmount = $allowanceAmount + $transportAmount + $additionalCosts;
                                        
                                        $deptBudget = $departmentBudgets[$mission->user->department] ?? null;
                                        $canPay = $deptBudget && $deptBudget['remaining'] >= $totalAmount;
                                    @endphp
                                    <div class="small">
                                        <div class="d-flex justify-content-between">
                                            <span>Indemnités:</span>
                                            <span>{{ number_format($allowanceAmount, 2) }} DH</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Transport:</span>
                                            <span>{{ number_format($transportAmount, 2) }} DH</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Additionnels:</span>
                                            <span>{{ number_format($additionalCosts, 2) }} DH</span>
                                        </div>
                                        <hr class="my-1">
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span>{{ number_format($totalAmount, 2) }} DH</span>
                                        </div>
                                        @if($deptBudget)
                                        <div class="text-{{ $canPay ? 'success' : 'danger' }} small">
                                            Budget restant: {{ number_format($deptBudget['remaining'], 2) }} DH
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($mission->payment)
                                        <span class="badge bg-success">Payé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                                <td class="actions-column">
                                    @if(!$mission->payment && $mission->proofs->isNotEmpty() && $canPay)
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $mission->id }}">
                                            <i class="fas fa-money-bill me-1"></i> Payer
                                        </button>
                                    @elseif(!$mission->payment && !$canPay)
                                        <span class="text-danger small">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            Budget insuffisant
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Payment Modal -->
                            @if(!$mission->payment && $mission->proofs->isNotEmpty() && $canPay)
                            <div class="modal fade" id="paymentModal{{ $mission->id }}" tabindex="-1" aria-labelledby="paymentModalLabel{{ $mission->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="paymentModalLabel{{ $mission->id }}">
                                                Enregistrer le Paiement
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('accountant.payments.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="mission_id" value="{{ $mission->id }}">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Enseignant</label>
                                                    <input type="text" class="form-control" value="{{ $mission->user->name }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Mission</label>
                                                    <input type="text" class="form-control" value="{{ $mission->title }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Montant Total</label>
                                                    <input type="text" class="form-control" value="{{ number_format($totalAmount, 2) }} DH" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Méthode de Paiement *</label>
                                                    <select name="payment_method" class="form-select" required>
                                                        <option value="" selected disabled>Choisir</option>
                                                        <option value="virement">Virement bancaire</option>
                                                        <option value="cheque">Chèque</option>
                                                        <option value="especes">Espèces</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Référence du paiement</label>
                                                    <input type="text" name="payment_reference" class="form-control" placeholder="Numéro de chèque, référence virement, etc.">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Date du paiement *</label>
                                                    <input type="date" name="payment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Commentaires</label>
                                                    <textarea name="comments" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-check me-1"></i> Confirmer le paiement
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $missions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-title {
        font-size: 1rem;
    }
    .progress {
        border-radius: 10px;
    }
    .table > :not(caption) > * > * {
        padding: 1rem;
    }
    .badge {
        padding: 0.5em 0.75em;
    }
    .btn-link {
        text-decoration: none;
    }
    .btn-link:hover {
        opacity: 0.8;
    }
    .collapse {
        transition: all 0.3s ease;
    }
    .actions-column {
        display: none;
    }
    .mission-row[data-payment-status="pending"] .actions-column {
        display: table-cell;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('[data-filter]');
    const missionRows = document.querySelectorAll('.mission-row');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            
            missionRows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else {
                    const status = row.dataset.paymentStatus;
                    row.style.display = status === filter ? '' : 'none';
                }
            });
        });
    });

    // Payment method specific fields
    const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            const referenceField = this.closest('form').querySelector('input[name="payment_reference"]');
            if (this.value === 'virement') {
                referenceField.placeholder = 'Référence du virement bancaire';
            } else if (this.value === 'cheque') {
                referenceField.placeholder = 'Numéro du chèque';
            } else {
                referenceField.placeholder = 'Référence du paiement';
            }
        });
    }

    // Collapse toggle icons
    const collapseElements = {
        'budgetsCollapse': document.getElementById('budgetsCollapse'),
        'missionsCollapse': document.getElementById('missionsCollapse')
    };

    Object.entries(collapseElements).forEach(([id, element]) => {
        if (element) {
            const toggleButton = document.querySelector(`[data-bs-target="#${id}"]`);
            
            element.addEventListener('show.bs.collapse', function () {
                toggleButton.querySelector('i').classList.remove('fa-chevron-down');
                toggleButton.querySelector('i').classList.add('fa-chevron-up');
            });

            element.addEventListener('hide.bs.collapse', function () {
                toggleButton.querySelector('i').classList.remove('fa-chevron-up');
                toggleButton.querySelector('i').classList.add('fa-chevron-down');
            });
        }
    });
});
</script>
@endpush
@endsection