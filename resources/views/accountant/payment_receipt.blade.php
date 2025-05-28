
@extends('layouts.accountant')

@section('title', 'Reçu de paiement')

@section('page-title', 'Reçu de paiement')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Reçu de paiement</h5>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-primary">
                <i class="fas fa-print me-1"></i> Imprimer
            </button>
            <a href="{{ route('accountant.payments') }}" class="btn btn-sm btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>
    <div class="card-body">
        <div id="printable-area" class="p-4">
            <div class="text-center mb-4">
                <h4 class="mb-0">REÇU DE PAIEMENT</h4>
                <p class="text-muted mb-0">Université Mohammed V</p>
                <p class="text-muted">Faculté des Sciences</p>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">MISSION</h6>
                    <p class="mb-1">{{ $payment->mission->title }}</p>
                    <p class="mb-1">Référence: MIS-{{ date('Y', strtotime($payment->mission->created_at)) }}-{{ sprintf('%03d', $payment->mission->id) }}</p>
                    <p class="mb-1">Destination: {{ $payment->mission->destination }}</p>
                    <p class="mb-0">Dates: {{ \Carbon\Carbon::parse($payment->mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($payment->mission->end_date)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="fw-bold">BÉNÉFICIAIRE</h6>
                    <p class="mb-1">{{ $payment->mission->user->name }}</p>
                    <p class="mb-1">Département: {{ $payment->mission->user->department }}</p>
                    <p class="mb-1">CIN: {{ $payment->mission->user->cin ?? 'Non spécifié' }}</p>
                    <p class="mb-0">Email: {{ $payment->mission->user->email }}</p>
                </div>
            </div>
            
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Montant (DH)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Indemnités journalières</td>
                            <td class="text-end">{{ number_format($payment->allowance_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Frais de transport</td>
                            <td class="text-end">{{ number_format($payment->transport_amount, 2) }}</td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td>Total</td>
                            <td class="text-end">{{ number_format($payment->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">DÉTAILS DU PAIEMENT</h6>
                    <p class="mb-1">Date de paiement: {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</p>
                    <p class="mb-1">Méthode: 
                        @if($payment->payment_method == 'virement')
                            Virement bancaire
                        @elseif($payment->payment_method == 'cheque')
                            Chèque
                        @else
                            Espèces
                        @endif
                    </p>
                    @if($payment->payment_reference)
                        <p class="mb-0">Référence: {{ $payment->payment_reference }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    @if($payment->comments)
                        <h6 class="fw-bold">COMMENTAIRES</h6>
                        <p class="mb-0">{{ $payment->comments }}</p>
                    @endif
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="border-top pt-2">
                        <p class="mb-1 text-center">Service Comptabilité</p>
                        <p class="mb-0 text-center">Signature & Cachet</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border-top pt-2">
                        <p class="mb-1 text-center">Bénéficiaire</p>
                        <p class="mb-0 text-center">Signature</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <p class="small text-muted mb-0">Ce document a été généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
                <p class="small text-muted">Système de Gestion des Missions - Université Mohammed V</p>
            </div>
        </div>
    </div>
</div>

<style type="text/css" media="print">
    @page {
        size: A4;
        margin: 10mm;
    }
    
    body {
        background-color: #fff;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-header, .btn, .nav, .sidebar, .footer {
        display: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    #printable-area {
        padding: 0 !important;
    }
    
    .table {
        border-collapse: collapse;
    }
</style>
@endsection