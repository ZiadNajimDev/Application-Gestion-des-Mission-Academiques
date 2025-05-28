<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordre de Mission - {{ $reference }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .university-name {
            font-size: 18pt;
            font-weight: bold;
        }
        .document-title {
            font-size: 20pt;
            font-weight: bold;
            margin: 30px 0;
            text-decoration: underline;
            text-align: center;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 250px;
        }
        .signature-section {
            margin-top: 60px;
            page-break-inside: avoid;
        }
        .signature-box {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 5px;
            width: 300px;
            height: 100px;
        }
        .footer {
            margin-top: 50px;
            font-size: 10pt;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
            page-break-inside: avoid;
        }
        .reference-number {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 10pt;
        }
        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <!-- Print Button (only visible on screen) -->
        <div class="no-print mb-3">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
            <a href="{{ route('teacher.missions.show', $mission->id) }}" class="btn btn-secondary">
                Retour aux détails
            </a>
        </div>
        
        <!-- Header -->
        <div class="header">
            <div class="university-name">UNIVERSITÉ MOHAMMED V DE RABAT</div>
            <div>FACULTÉ DES SCIENCES</div>
        </div>
        
        <!-- Reference Number -->
        <div class="reference-number">
            <strong>Réf:</strong> {{ $reference }}
        </div>
        
        <!-- Document Title -->
        <div class="document-title">ORDRE DE MISSION</div>
        
        <!-- Teacher Information Section -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Nom et Prénom:</div>
                <div>{{ $user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">CIN:</div>
                <div>{{ $user->cin ?? 'Non spécifié' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Département:</div>
                <div>{{ $user->department ?? 'Non spécifié' }}</div>
            </div>
        </div>
        
        <!-- Mission Information Section -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Type de mission:</div>
                <div>{{ $mission->type === 'nationale' ? 'Nationale' : 'Internationale' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Objet de la mission:</div>
                <div>{{ $mission->title }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Objectif:</div>
                <div>{{ $mission->objective }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Lieu:</div>
                <div>{{ $mission->destination_city }}, {{ $mission->destination_institution }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de début:</div>
                <div>{{ $mission->formatted_start_date }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de fin:</div>
                <div>{{ $mission->formatted_end_date }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Durée totale:</div>
                <div>{{ $mission->duration }} jour(s)</div>
            </div>
            <div class="info-row">
                <div class="info-label">Mode de transport:</div>
                <div>
                    @switch($mission->transport_type)
                        @case('voiture')
                            Voiture personnelle
                            @break
                        @case('transport_public')
                            Transport public
                            @break
                        @case('train')
                            Train
                            @break
                        @case('avion')
                            Avion
                            @break
                        @default
                            {{ $mission->transport_type }}
                    @endswitch
                </div>
            </div>
            @if($mission->supervisor_name)
            <div class="info-row">
                <div class="info-label">Encadrant:</div>
                <div>{{ $mission->supervisor_name }}</div>
            </div>
            @endif
        </div>
        
        <!-- Date and Signature Section -->
        <div class="row signature-section">
            <div class="col-md-6">
                <div>Fait à Rabat, le {{ $today }}</div>
                <div class="mt-2">Signature de l'intéressé(e):</div>
                <div class="signature-box"></div>
            </div>
            <div class="col-md-6 text-end">
                <div>Le Doyen de la Faculté</div>
                <div class="signature-box ms-auto"></div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div>Université Mohammed V de Rabat - Faculté des Sciences</div>
            <div>4 Avenue Ibn Battouta, B.P. 1014 RP, Rabat, Maroc</div>
            <div>Tel: +212 5 37 77 18 34 | Fax: +212 5 37 77 42 61 | www.fsr.ac.ma</div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>