@extends('layouts.accountant')

@section('title', 'Réservations')

@section('page-title', 'Gestion des réservations')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestion des réservations</h5>
        <div>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="reservationFilter" id="filter-all" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="filter-all">Toutes</label>

                <input type="radio" class="btn-check" name="reservationFilter" id="filter-pending" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-pending">En attente</label>

                <input type="radio" class="btn-check" name="reservationFilter" id="filter-completed" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-completed">Complétées</label>

                <input type="radio" class="btn-check" name="reservationFilter" id="filter-urgent" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-urgent">Urgentes</label>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mission</th>    
                        <th>Enseignant</th>
                        <th>Département</th>
                        <th>Dates</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($missions as $mission)
                        <tr class="reservation-row" 
                            data-status="{{ $mission->status }}" 
                            data-days-until="{{ $mission->start_date ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($mission->start_date), false) : 0 }}">
                            <td>
                                <div class="fw-bold">{{ $mission->title }}</div>
                                <small>{{ $mission->destination }}</small>
                            </td>
                            <td>{{ $mission->user->name }}</td>
                            <td>{{ $mission->user->department }}</td>
                            <td>{{ \Carbon\Carbon::parse($mission->start_date)->format('d-d') }} {{ \Carbon\Carbon::parse($mission->start_date)->format('M Y') }}</td>
                            <td>
                                @if($mission->type == 'internationale')
                                    <span class="badge bg-info">Internationale</span>
                                @else
                                    <span class="badge bg-primary">Nationale</span>
                                @endif
                            </td>
                           
                            <td>
                                @php
                                    $daysUntil = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($mission->start_date), false);
                                @endphp
                                
                                @if($mission->status == 'validee_directeur')
    @if($daysUntil < 7)
        <span class="badge bg-danger">À faire avant {{ \Carbon\Carbon::now()->addDays(3)->format('d/m') }}</span>
    @elseif($daysUntil < 14)
        <span class="badge bg-warning">À faire avant {{ \Carbon\Carbon::now()->addDays(5)->format('d/m') }}</span>
    @else
        <span class="badge bg-info">À réserver</span>
    @endif
@elseif($mission->status == 'billet_reserve')
    <span class="badge bg-success">Complétée</span>
@else
    <span class="badge bg-secondary">{{ ucfirst($mission->status) }}</span>
@endif
                            </td>
                            <td>
                                @if($mission->status == 'validee_directeur')
                                    {{-- For missions waiting for reservation --}}
                                    <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#reservationModal" 
                                        data-mission-id="{{ $mission->id }}"
                                        data-mission-details="{{ json_encode([
                                            'id' => $mission->id,
                                            'teacher' => $mission->user->name,
                                            'department' => $mission->user->department,
                                            'title' => $mission->title,
                                            'destination_city' => $mission->destination_city,
                                            'destination_institution' => $mission->destination_institution,
                                            'start_date' => \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y'),
                                            'end_date' => \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y'),
                                            'type' => $mission->type,
                                            'transport_type' => $mission->transport_type,
                                        ]) }}">
                                        <i class="fas fa-plus"></i> Ajouter réservation
                                    </button>
                                @elseif($mission->status == 'billet_reserve' || $mission->status == 'terminee')
                                    {{-- For missions with reservations or completed missions --}}
                                    <button class="btn btn-sm btn-info me-1" data-bs-toggle="modal" data-bs-target="#reservationDetailsModal" 
                                        data-mission-id="{{ $mission->id }}"
                                        data-mission-details="{{ json_encode([
                                            'id' => $mission->id,
                                            'teacher' => $mission->user->name,
                                            'department' => $mission->user->department,
                                            'title' => $mission->title,
                                            'destination_city' => $mission->destination_city,
                                            'destination_institution' => $mission->destination_institution,
                                            'start_date' => \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y'),
                                            'end_date' => \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y'),
                                            'type' => $mission->type,
                                            'transport_type' => $mission->transport_type,
                                            'reservation' => $mission->reservations->first() ? [
                                                'type' => $mission->reservations->first()->type,
                                                'provider' => $mission->reservations->first()->provider,
                                                'reservation_number' => $mission->reservations->first()->reservation_number,
                                                'cost' => $mission->reservations->first()->cost,
                                                'reservation_date' => \Carbon\Carbon::parse($mission->reservations->first()->reservation_date)->format('d/m/Y'),
                                                'notes' => $mission->reservations->first()->notes,
                                                'attachment' => $mission->reservations->first()->attachment
                                            ] : null
                                        ]) }}">
                                        <i class="fas fa-info-circle"></i> Détails
                                    </button>
                                    
                                    @if(!$mission->payment && $mission->status == 'billet_reserve')
                                        
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucune mission à afficher</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($missions->count() > 0)
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $missions->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $missions->previousPageUrl() }}" tabindex="-1" aria-disabled="{{ $missions->onFirstPage() ? 'true' : 'false' }}">Précédent</a>
                    </li>
                    
                    @for($i = 1; $i <= $missions->lastPage(); $i++)
                        <li class="page-item {{ $missions->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $missions->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    
                    <li class="page-item {{ $missions->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $missions->nextPageUrl() }}">Suivant</a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reservationModalLabel">Gestion de réservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reservationForm" method="POST" action="{{ route('accountant.reservations.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="mission_id" id="mission_id">
                <input type="hidden" name="type" id="reservation-type">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Détails de la mission</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Enseignant:</th>
                                            <td id="teacher-name"></td>
                                        </tr>
                                        <tr>
                                            <th>Département:</th>
                                            <td id="department-name"></td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td id="mission-type"></td>
                                        </tr>
                                        <tr>
                                            <th>Titre:</th>
                                            <td id="mission-title"></td>
                                        </tr>
                                        <tr>
                                            <th>Dates:</th>
                                            <td id="mission-dates"></td>
                                        </tr>
                                        <tr>
                                            <th>Destination:</th>
                                            <td id="mission-destination"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Réservation</h6>
                            <div class="mb-3">
    <label class="form-label">Type de transport *</label>
    <select class="form-select" name="type" id="transport-type" required>
        <option value="" selected disabled>Choisir</option>
        <option value="flight">Avion</option>
        <option value="train">Train</option>
        <option value="bus">Bus</option>
        <option value="car">Voiture personnelle</option>
    </select>
</div>

<!-- Flight specific fields -->
<div id="flight-fields" class="transport-fields" style="display: none;">
    <div class="mb-3">
        <label class="form-label">Compagnie aérienne *</label>
        <input type="text" name="provider" class="form-control" placeholder="Air France, Royal Air Maroc, etc.">
    </div>
    <div class="mb-3">
        <label class="form-label">Numéro de vol *</label>
        <input type="text" name="reservation_number" class="form-control" placeholder="AF1234, AT123, etc.">
    </div>
    <div class="mb-3">
        <label class="form-label">Coût du billet (DH) *</label>
        <input type="number" name="cost" class="form-control" step="0.01" required>
    </div>
</div>

<!-- Hotel section -->
<div class="mb-3">
    <label class="form-label">Hébergement nécessaire</label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="needHotel" name="need_hotel">
        <label class="form-check-label" for="needHotel">
            Réserver un hôtel
        </label>
    </div>
</div>

<div id="hotelInfo" style="display: none;">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Nom de l'hôtel</label>
            <input type="text" name="hotel_name" class="form-control" placeholder="Hôtel Mercure">
        </div>
        <div class="col-md-6">
            <label class="form-label">Coût de l'hôtel (DH)</label>
            <input type="number" name="hotel_cost" class="form-control" step="0.01">
        </div>
    </div>
</div>

                            <!-- Hidden input for the actual cost value -->
                            <input type="hidden" name="cost" id="final-cost">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date de départ *</label>
                                    <input type="date" name="departure_date" class="form-control" id="departure-date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de retour *</label>
                                    <input type="date" name="return_date" class="form-control" id="return-date" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Documents</label>
                                <input class="form-control" type="file" name="attachment" id="reservationDocuments">
                                <div class="form-text">Joindre les billets/réservations</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer la réservation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmCompleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Marquer comme complété</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir marquer cette réservation comme complétée? Cette action mettra à jour le statut de la mission.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="complete-form" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">Confirmer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reservation Details Modal -->
<div class="modal fade" id="reservationDetailsModal" tabindex="-1" aria-labelledby="reservationDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="reservationDetailsModalLabel">Détails de la réservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Détails de la mission</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th width="40%">Enseignant:</th>
                                        <td id="details-teacher-name"></td>
                                    </tr>
                                    <tr>
                                        <th>Département:</th>
                                        <td id="details-department-name"></td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td id="details-mission-type"></td>
                                    </tr>
                                    <tr>
                                        <th>Titre:</th>
                                        <td id="details-mission-title"></td>
                                    </tr>
                                    <tr>
                                        <th>Dates:</th>
                                        <td id="details-mission-dates"></td>
                                    </tr>
                                    <tr>
                                        <th>Destination:</th>
                                        <td id="details-mission-destination"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Détails de la réservation</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th width="40%">Type de transport:</th>
                                        <td id="details-transport-type"></td>
                                    </tr>
                                    <tr>
                                        <th>Coût:</th>
                                        <td id="details-cost"></td>
                                    </tr>
                                    <tr>
                                        <th>Date de réservation:</th>
                                        <td id="details-reservation-date"></td>
                                    </tr>
                                    <tr>
                                        <th>Notes:</th>
                                        <td id="details-notes"></td>
                                    </tr>
                                    <tr>
                                        <th>Documents:</th>
                                        <td id="details-attachment"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle reservation modal
        var reservationModal = document.getElementById('reservationModal')
        reservationModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var missionId = button.getAttribute('data-mission-id')
            var missionDetails = JSON.parse(button.getAttribute('data-mission-details'))
            
            var modal = this
            modal.querySelector('#mission_id').value = missionId
            modal.querySelector('#teacher-name').textContent = missionDetails.teacher
            modal.querySelector('#department-name').textContent = missionDetails.department
            modal.querySelector('#mission-title').textContent = missionDetails.title
            modal.querySelector('#mission-destination').textContent = missionDetails.destination_city + ' - ' + missionDetails.destination_institution
            modal.querySelector('#mission-dates').textContent = missionDetails.start_date + ' - ' + missionDetails.end_date
            
            // Set dates in form
            document.getElementById('departure-date').value = missionDetails.start_date.split('/').reverse().join('-');
            document.getElementById('return-date').value = missionDetails.end_date.split('/').reverse().join('-');
            
            // Get mission transport type from the mission details
            const missionTransportType = missionDetails.transport_type;
            const isInternational = missionDetails.type === 'internationale';
            
            // Map mission transport types to reservation types
            const transportTypeMap = {
                'voiture': 'car',
                'transport_public': 'bus',
                'train': 'train',
                'avion': 'flight'
            };
            
            // Set the transport type based on the mission's transport type
            const transportTypeSelect = document.getElementById('transport-type');
            const reservationTypeInput = document.getElementById('reservation-type');
            const mappedType = transportTypeMap[missionTransportType];
            
            // For international missions, force flight type and disable selection
            if (isInternational) {
                transportTypeSelect.value = 'flight';
                transportTypeSelect.disabled = true;
                transportTypeSelect.classList.add('bg-light');
                reservationTypeInput.value = 'flight'; // Set the hidden input
            } else {
                transportTypeSelect.disabled = false;
                transportTypeSelect.classList.remove('bg-light');
                if (mappedType) {
                    transportTypeSelect.value = mappedType;
                    reservationTypeInput.value = mappedType; // Set the hidden input
                }
            }
            
            // Show the corresponding transport fields
            document.querySelectorAll('.transport-fields').forEach(field => {
                field.style.display = 'none';
            });
            
            // Always show flight fields for international missions
            if (isInternational || mappedType === 'flight') {
                document.getElementById('flight-fields').style.display = 'block';
                // Make flight fields required for international missions
                const flightFields = document.querySelectorAll('#flight-fields input, #flight-fields select');
                flightFields.forEach(field => {
                    field.required = isInternational;
                });
            } else if (mappedType) {
                document.getElementById(mappedType + '-fields').style.display = 'block';
            }
            
            // Set mission type
            if (isInternational) {
                modal.querySelector('#mission-type').innerHTML = '<span class="badge bg-info">Internationale</span>'
            } else {
                modal.querySelector('#mission-type').innerHTML = '<span class="badge bg-primary">Nationale</span>'
            }
        });
        
        // Update hidden type input when transport type changes
        document.getElementById('transport-type').addEventListener('change', function() {
            document.getElementById('reservation-type').value = this.value;
        });
        
        // Handle cost input changes
        document.querySelectorAll('.cost-input').forEach(input => {
            input.addEventListener('input', function() {
                const transportType = document.getElementById('transport-type').value;
                const costInput = document.getElementById('final-cost');
                
                // Get the cost value based on transport type
                let cost = 0;
                switch(transportType) {
                    case 'flight':
                        cost = document.querySelector('input[name="cost"]').value;
                        break;
                    case 'train':
                        cost = document.querySelector('input[name="train_cost"]').value;
                        break;
                    case 'bus':
                        cost = document.querySelector('input[name="bus_cost"]').value;
                        break;
                    case 'car':
                        cost = document.querySelector('input[name="car_cost"]').value;
                        break;
                }
                
                costInput.value = cost;
            });
        });
        
        // Calculate car cost based on distance and rate
        const carFields = document.getElementById('car-fields');
        if (carFields) {
            const distanceInput = carFields.querySelector('input[name="distance"]');
            const rateInput = carFields.querySelector('input[name="rate_per_km"]');
            const costInput = carFields.querySelector('input[name="car_cost"]');
            
            function calculateCost() {
                const distance = parseFloat(distanceInput.value) || 0;
                const rate = parseFloat(rateInput.value) || 0;
                const cost = (distance * rate).toFixed(2);
                costInput.value = cost;
                document.getElementById('final-cost').value = cost;
            }
            
            distanceInput.addEventListener('input', calculateCost);
            rateInput.addEventListener('input', calculateCost);
        }
        
        // Mark complete button functionality
        const markCompleteBtns = document.querySelectorAll('.mark-complete-btn');
        markCompleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const missionId = this.getAttribute('data-mission-id');
                const completeForm = document.getElementById('complete-form');
                completeForm.action = `/accountant/reservations/${missionId}/complete`;
                
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmCompleteModal'));
                confirmModal.show();
            });
        });
        
        // Filter functionality
        const filterButtons = document.querySelectorAll('[name="reservationFilter"]')
        filterButtons.forEach(button => {
            button.addEventListener('change', function() {
                const filterValue = this.id.replace('filter-', '')
                const rows = document.querySelectorAll('.reservation-row')
                
                rows.forEach(row => {
                    if (filterValue === 'all') {
                        row.style.display = 'table-row'
                    } else if (filterValue === 'pending') {
                        const status = row.getAttribute('data-status')
                        if (status === 'validee_directeur') {
                            row.style.display = 'table-row'
                        } else {
                            row.style.display = 'none'
                        }
                    } else if (filterValue === 'completed') {
                        const status = row.getAttribute('data-status')
                        if (status === 'billet_reserve') {
                            row.style.display = 'table-row'
                        } else {
                            row.style.display = 'none'
                        }
                    } else if (filterValue === 'urgent') {
                        const daysUntil = parseInt(row.getAttribute('data-days-until'), 10)
                        if (daysUntil >= 0 && daysUntil < 10) {
                            row.style.display = 'table-row'
                        } else {
                            row.style.display = 'none'
                        }
                    }
                })
            })
        })

        // Handle reservation details modal
        var reservationDetailsModal = document.getElementById('reservationDetailsModal')
        reservationDetailsModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var missionDetails = JSON.parse(button.getAttribute('data-mission-details'))
            var reservation = missionDetails.reservation
            
            var modal = this
            modal.querySelector('#details-teacher-name').textContent = missionDetails.teacher
            modal.querySelector('#details-department-name').textContent = missionDetails.department
            modal.querySelector('#details-mission-title').textContent = missionDetails.title
            modal.querySelector('#details-mission-destination').textContent = missionDetails.destination_city + ' - ' + missionDetails.destination_institution
            modal.querySelector('#details-mission-dates').textContent = missionDetails.start_date + ' - ' + missionDetails.end_date
            
            // Set mission type
            if (missionDetails.type === 'internationale') {
                modal.querySelector('#details-mission-type').innerHTML = '<span class="badge bg-info">Internationale</span>'
            } else {
                modal.querySelector('#details-mission-type').innerHTML = '<span class="badge bg-primary">Nationale</span>'
            }
            
            // Set reservation details
            if (reservation) {
                const transportTypeMap = {
                    'flight': 'Avion',
                    'train': 'Train',
                    'bus': 'Bus',
                    'car': 'Voiture personnelle'
                }
                
                modal.querySelector('#details-transport-type').textContent = transportTypeMap[reservation.type] || reservation.type
                modal.querySelector('#details-cost').textContent = reservation.cost ? reservation.cost + ' DH' : 'Non spécifié'
                modal.querySelector('#details-reservation-date').textContent = reservation.reservation_date || 'Non spécifié'
                modal.querySelector('#details-notes').textContent = reservation.notes || 'Aucune note'
                
                if (reservation.attachment) {
                    modal.querySelector('#details-attachment').innerHTML = `
                        <a href="/storage/${reservation.attachment}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-download"></i> Télécharger
                        </a>
                    `
                } else {
                    modal.querySelector('#details-attachment').textContent = 'Aucun document'
                }
            } else {
                modal.querySelector('#details-transport-type').textContent = 'Non spécifié'
                modal.querySelector('#details-cost').textContent = 'Non spécifié'
                modal.querySelector('#details-reservation-date').textContent = 'Non spécifié'
                modal.querySelector('#details-notes').textContent = 'Aucune note'
                modal.querySelector('#details-attachment').textContent = 'Aucun document'
            }
        })
    })
</script>
@endsection