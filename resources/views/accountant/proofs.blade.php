@extends('layouts.accountant')

@section('title', 'Justificatifs')

@section('page-title', 'Gestion des justificatifs')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestion des justificatifs</h5>
        <div>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="proofFilter" id="filter-all" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="filter-all">Tous</label>

                <input type="radio" class="btn-check" name="proofFilter" id="filter-pending" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-pending">En attente</label>

                <input type="radio" class="btn-check" name="proofFilter" id="filter-approved" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-approved">Approuvés</label>
                
                <input type="radio" class="btn-check" name="proofFilter" id="filter-rejected" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-rejected">Rejetés</label>
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
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mission</th>
                        <th>Enseignant</th>
                        <th>Document</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Date soumission</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($proofDocuments as $proof)
    <tr class="proof-row" data-proof-status="{{ $proof->status }}">
        <td>{{ $proof->mission->title }}</td>
        <td>{{ $proof->mission->user->name }}</td>
        <td>{{ $proof->file_name }}</td>
        <td>
            @if($proof->category == 'financier')
                <span class="badge bg-info">Financier</span>
            @elseif($proof->category == 'execution')
                <span class="badge bg-primary">Exécution</span>
            @else
                <span class="badge bg-secondary">Retour</span>
            @endif
        </td>
        <td>{{ $proof->amount ? number_format($proof->amount, 0) . ' DH' : 'N/A' }}</td>
        <td>{{ $proof->created_at->format('d/m/Y') }}</td>
        <td>
            @if($proof->status == 'pending')
                <span class="badge bg-warning">En attente</span>
            @elseif($proof->status == 'approved')
                <span class="badge bg-success">Approuvé</span>
            @else
                <span class="badge bg-danger">Rejeté</span>
            @endif
        </td>
        <td>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('accountant.proofs.show', $proof->id) }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ Storage::disk('public')->url($proof->file_path) }}" 
                   class="btn btn-outline-secondary" download>
                    <i class="fas fa-download"></i>
                </a>
                @if($proof->status == 'pending')
                    <button class="btn btn-outline-success process-proof-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#approveProofModal" 
                            data-proof-id="{{ $proof->id }}"
                            data-proof-title="{{ $proof->file_name }}">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-outline-danger process-proof-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#rejectProofModal" 
                            data-proof-id="{{ $proof->id }}"
                            data-proof-title="{{ $proof->file_name }}">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">Aucun justificatif à afficher</td>
    </tr>
@endforelse
                </tbody>
            </table>
        </div>

        @if($proofDocuments->count() > 0)
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $proofDocuments->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $proofDocuments->previousPageUrl() }}" tabindex="-1" aria-disabled="{{ $proofDocuments->onFirstPage() ? 'true' : 'false' }}">Précédent</a>
                    </li>
                    
                    @for($i = 1; $i <= $proofDocuments->lastPage(); $i++)
                        <li class="page-item {{ $proofDocuments->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $proofDocuments->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    
                    <li class="page-item {{ $proofDocuments->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $proofDocuments->nextPageUrl() }}">Suivant</a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>
</div>

<!-- Approve Proof Modal -->
<div class="modal fade" id="approveProofModal" tabindex="-1" aria-labelledby="approveProofModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveProofModalLabel">Approuver le justificatif</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveProofForm" method="POST" action="">
                @csrf
                <input type="hidden" name="status" value="approved">
                <div class="modal-body">
                    <p>Vous êtes sur le point d'approuver le justificatif: <strong id="approve-proof-title"></strong></p>
                    
                    <div class="mb-3">
                        <label for="approveComment" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" id="approveComment" name="reviewer_comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Approuver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Proof Modal -->
<div class="modal fade" id="rejectProofModal" tabindex="-1" aria-labelledby="rejectProofModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectProofModalLabel">Rejeter le justificatif</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectProofForm" method="POST" action="">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <div class="modal-body">
                    <p>Vous êtes sur le point de rejeter le justificatif: <strong id="reject-proof-title"></strong></p>
                    
                    <div class="mb-3">
                        <label for="rejectComment" class="form-label">Motif du rejet *</label>
                        <textarea class="form-control" id="rejectComment" name="reviewer_comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle proof document processing
        const processProofBtns = document.querySelectorAll('.process-proof-btn');
        processProofBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const proofId = this.getAttribute('data-proof-id');
                const proofTitle = this.getAttribute('data-proof-title');
                const isApprove = this.closest('.modal-content').id === 'approveProofModal';
                
                if (isApprove) {
                    document.getElementById('approve-proof-title').textContent = proofTitle;
                    document.getElementById('approveProofForm').action = `/accountant/proofs/${proofId}/process`;
                } else {
                    document.getElementById('reject-proof-title').textContent = proofTitle;
                    document.getElementById('rejectProofForm').action = `/accountant/proofs/${proofId}/process`;
                }
            });
        });
        
        // Ensure modals update their form action URLs correctly
        const approveProofModal = document.getElementById('approveProofModal');
        approveProofModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const proofId = button.getAttribute('data-proof-id');
            const proofTitle = button.getAttribute('data-proof-title');
            
            this.querySelector('#approve-proof-title').textContent = proofTitle;
            this.querySelector('#approveProofForm').action = `/accountant/proofs/${proofId}/process`;
        });
        
        const rejectProofModal = document.getElementById('rejectProofModal');
        rejectProofModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const proofId = button.getAttribute('data-proof-id');
            const proofTitle = button.getAttribute('data-proof-title');
            
            this.querySelector('#reject-proof-title').textContent = proofTitle;
            this.querySelector('#rejectProofForm').action = `/accountant/proofs/${proofId}/process`;
        });
        
        // Filter functionality
        const filterButtons = document.querySelectorAll('[name="proofFilter"]')
        filterButtons.forEach(button => {
            button.addEventListener('change', function() {
                const filterValue = this.id.replace('filter-', '')
                const rows = document.querySelectorAll('.proof-row')
                
                rows.forEach(row => {
                    if (filterValue === 'all') {
                        row.style.display = 'table-row'
                    } else {
                        const status = row.getAttribute('data-proof-status')
                        if (status === filterValue) {
                            row.style.display = 'table-row'
                        } else {
                            row.style.display = 'none'
                        }
                    }
                })
            })
        })
    })
</script>
@endsection