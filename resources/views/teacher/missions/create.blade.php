<!-- resources/views/teacher/missions/create.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Mission - Enseignant</title>
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
                            <a class="nav-link" href="{{ route('teacher.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('teacher.missions.create') }}">
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
                        @endphp                        <li class="nav-item position-relative">
                            <a class="nav-link" href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell me-2"></i> Notifications
                                @if(\App\Helpers\NotificationHelper::getUnreadCount() > 0)
                                    <span class="badge bg-danger notification-badge">{{ \App\Helpers\NotificationHelper::getUnreadCount() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            
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
                    <h1 class="h2">Nouvelle Mission</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="{{ route('teacher.missions.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour aux missions
                            </a>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Nouvel ordre de mission</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('teacher.missions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="missionType" class="form-label">Type de mission *</label>
                                    <select class="form-select" id="missionType" name="missionType" required>
                                        <option value="" selected disabled>Choisir le type</option>
                                        <option value="nationale" {{ old('missionType') == 'nationale' ? 'selected' : '' }}>Mission nationale</option>
                                        <option value="internationale" {{ old('missionType') == 'internationale' ? 'selected' : '' }}>Mission internationale</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="transportType" class="form-label">Mode de transport *</label>
                                    <select class="form-select" id="transportType" name="transportType" required {{ old('missionType') == 'internationale' ? 'disabled' : '' }}>
                                        <option value="" selected disabled>Choisir le mode</option>
                                        <option value="voiture" {{ old('transportType') == 'voiture' ? 'selected' : '' }}>Voiture personnelle</option>
                                        <option value="transport_public" {{ old('transportType') == 'transport_public' ? 'selected' : '' }}>Transport public</option>
                                        <option value="train" {{ old('transportType') == 'train' ? 'selected' : '' }}>Train</option>
                                        <option value="avion" {{ old('transportType') == 'avion' || old('missionType') == 'internationale' ? 'selected' : '' }}>Avion</option>
                                    </select>
                                    <!-- Hidden input for internationale missions -->
                                    <input type="hidden" name="transportType" value="avion" id="hiddenTransportType" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="startDate" class="form-label">Date de début *</label>
                                    <input type="date" class="form-control" id="startDate" name="startDate" value="{{ old('startDate') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="endDate" class="form-label">Date de fin *</label>
                                    <input type="date" class="form-control" id="endDate" name="endDate" value="{{ old('endDate') }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="destinationCity" class="form-label">Ville de destination *</label>
                                    <input type="text" class="form-control" id="destinationCity" name="destinationCity" value="{{ old('destinationCity') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="destinationInstitution" class="form-label">Institution de destination *</label>
                                    <input type="text" class="form-control" id="destinationInstitution" name="destinationInstitution" value="{{ old('destinationInstitution') }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="missionTitle" class="form-label">Titre de la mission *</label>
                                <input type="text" class="form-control" id="missionTitle" name="missionTitle" value="{{ old('missionTitle') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="missionObjective" class="form-label">Objectif de la mission *</label>
                                <textarea class="form-control" id="missionObjective" name="missionObjective" rows="3" required>{{ old('missionObjective') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="supervisorName" class="form-label">Nom de l'inviteur</label>
                                <input type="text" class="form-control" id="supervisorName" name="supervisorName" value="{{ old('supervisorName') }}">
                            </div>
                            <div class="mb-3">
                                <label for="additionalDocuments" class="form-label">Documents supplémentaires</label>
                                <input class="form-control" type="file" id="additionalDocuments" name="additionalDocuments[]" multiple accept=".pdf">
                                <div class="form-text">
                                    Vous pouvez joindre plusieurs documents supplémentaires si nécessaire, tels que :
                                    <ul class="mb-0 ps-3">
                                        <li>Lettre d'invitation</li>
                                        <li>Programme de la conférence/formation</li>
                                        <li>Devis de transport</li>
                                        <li>Devis d'hébergement</li>
                                        <li>Autres justificatifs pertinents</li>
                                    </ul>
                                    Formats acceptés: PDF uniquement (max 10MB par fichier).
                                </div>
                                <div id="selectedFiles" class="mt-2"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('teacher.missions.index') }}" class="btn btn-outline-secondary">Annuler</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i> Soumettre la mission
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation for date range
        document.getElementById('endDate').addEventListener('change', function() {
            const startDate = new Date(document.getElementById('startDate').value);
            const endDate = new Date(this.value);
            
            if (endDate < startDate) {
                alert('La date de fin ne peut pas être antérieure à la date de début.');
                this.value = document.getElementById('startDate').value;
            }
        });

        // Dynamic transport type based on mission type
        document.getElementById('missionType').addEventListener('change', function() {
            const transportTypeSelect = document.getElementById('transportType');
            const hiddenTransportType = document.getElementById('hiddenTransportType');
            
            if (this.value === 'internationale') {
                // For international missions, set transport to 'avion' and disable select
                transportTypeSelect.value = 'avion';
                transportTypeSelect.disabled = true;
                transportTypeSelect.classList.add('bg-light');
                
                // Enable the hidden input to ensure 'avion' is submitted
                hiddenTransportType.disabled = false;
            } else {
                // For national missions, enable the select
                transportTypeSelect.disabled = false;
                transportTypeSelect.classList.remove('bg-light');
                
                // Disable the hidden input
                hiddenTransportType.disabled = true;
            }
        });

        // Initialize based on initial value (for form validation errors)
        if (document.getElementById('missionType').value === 'internationale') {
            document.getElementById('transportType').value = 'avion';
            document.getElementById('transportType').disabled = true;
            document.getElementById('transportType').classList.add('bg-light');
            document.getElementById('hiddenTransportType').disabled = false;
        }

        // Multiple file selection display
        document.getElementById('additionalDocuments').addEventListener('change', function() {
            const selectedFilesDiv = document.getElementById('selectedFiles');
            selectedFilesDiv.innerHTML = '';
            
            if (this.files.length > 0) {
                const fileList = document.createElement('ul');
                fileList.className = 'list-group';
                
                for (let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    const fileItem = document.createElement('li');
                    fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    
                    // File name and size
                    const fileInfo = document.createElement('div');
                    fileInfo.innerHTML = `
                        <i class="fas fa-file-pdf text-danger me-2"></i>
                        <span>${file.name}</span>
                        <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
                    `;
                    
                    // Remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'btn btn-sm btn-outline-danger';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.type = 'button';
                    removeBtn.addEventListener('click', function() {
                        // We can't directly modify the FileList, so we'll create a new one
                        // This is a bit tricky - in practice, we'd use a more sophisticated solution
                        // For simplicity, we'll just clear the file input entirely
                        document.getElementById('additionalDocuments').value = '';
                        selectedFilesDiv.innerHTML = '';
                    });
                    
                    fileItem.appendChild(fileInfo);
                    fileItem.appendChild(removeBtn);
                    fileList.appendChild(fileItem);
                }
                
                selectedFilesDiv.appendChild(fileList);
            }
        });
        
        // Helper function to format file size
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' bytes';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' KB';
            else return (bytes / 1048576).toFixed(2) + ' MB';
        }

        // Show warning if user tries to leave the page with unsaved changes
        const form = document.querySelector('form');
        const originalFormData = new FormData(form);
        
        window.addEventListener('beforeunload', function(e) {
            const currentFormData = new FormData(form);
            let formChanged = false;
            
            for (const [key, value] of currentFormData.entries()) {
                if (key !== 'additionalDocuments[]' && originalFormData.get(key) !== value) {
                    formChanged = true;
                    break;
                }
            }
            
            if (formChanged || document.getElementById('additionalDocuments').files.length > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>