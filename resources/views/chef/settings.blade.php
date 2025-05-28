<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Chef de Département</title>
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
                            <a class="nav-link active" href="{{ route('chef.settings') }}">
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
                    <h1 class="h2">Paramètres du compte</h1>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header bg-white p-0">
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                                    <i class="fas fa-user me-2"></i> Profil
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                                    <i class="fas fa-shield-alt me-2"></i> Sécurité
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="settingsTabsContent">
                            <!-- Profile Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                @if(session('profile_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('profile_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('chef.settings.updateProfile') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="firstName" class="form-label">Prénom *</label>
                                            <input type="text" class="form-control @error('firstName') is-invalid @enderror" id="firstName" name="firstName" value="{{ old('firstName', explode(' ', $user->name)[0] ?? '') }}" required>
                                            @error('firstName')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lastName" class="form-label">Nom *</label>
                                            <input type="text" class="form-control @error('lastName') is-invalid @enderror" id="lastName" name="lastName" value="{{ old('lastName', explode(' ', $user->name)[1] ?? '') }}" required>
                                            @error('lastName')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="cin" class="form-label">CIN *</label>
                                            <input type="text" class="form-control @error('cin') is-invalid @enderror" id="cin" name="cin" value="{{ old('cin', $user->cin) }}" required>
                                            @error('cin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Téléphone</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="department" class="form-label">Département *</label>
                                        <select class="form-select @error('department') is-invalid @enderror" id="department" name="department" required>
                                            <option value="">Sélectionner un département</option>
                                            <option value="Informatique" {{ old('department', $user->department) === 'Informatique' ? 'selected' : '' }}>Informatique</option>
                                            <option value="Mathématiques" {{ old('department', $user->department) === 'Mathématiques' ? 'selected' : '' }}>Mathématiques</option>
                                            <option value="Physique" {{ old('department', $user->department) === 'Physique' ? 'selected' : '' }}>Physique</option>
                                            <option value="Chimie" {{ old('department', $user->department) === 'Chimie' ? 'selected' : '' }}>Chimie</option>
                                            <option value="Biologie" {{ old('department', $user->department) === 'Biologie' ? 'selected' : '' }}>Biologie</option>
                                            <option value="Géologie" {{ old('department', $user->department) === 'Géologie' ? 'selected' : '' }}>Géologie</option>
                                        </select>
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Le département doit être configuré pour pouvoir accéder aux fonctionnalités de validation.
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="photo" class="form-label">Photo de profil</label>
                                        @if($user->profile_photo_path)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($user->profile_photo_path) }}" class="img-thumbnail" alt="Photo de profil" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        <input class="form-control @error('photo') is-invalid @enderror" type="file" id="photo" name="photo">
                                        <div class="form-text">Formats acceptés: JPG, JPEG, PNG (max 2MB)</div>
                                        @error('photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Security Tab -->
                            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                @if(session('security_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('security_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('chef.settings.updatePassword') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Mot de passe actuel *</label>
                                        <input type="password" class="form-control @error('currentPassword') is-invalid @enderror" id="currentPassword" name="currentPassword" required>
                                        @error('currentPassword')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="newPassword" class="form-label">Nouveau mot de passe *</label>
                                            <input type="password" class="form-control @error('newPassword') is-invalid @enderror" id="newPassword" name="newPassword" required>
                                            @error('newPassword')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="newPassword_confirmation" class="form-label">Confirmer le nouveau mot de passe *</label>
                                            <input type="password" class="form-control" id="newPassword_confirmation" name="newPassword_confirmation" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-text">
                                            <strong>Exigences de sécurité:</strong>
                                            <ul class="mb-0">
                                                <li>Minimum 8 caractères</li>
                                                <li>Utiliser au moins un chiffre</li>
                                                <li>Utiliser au moins une lettre majuscule</li>
                                                <li>Utiliser au moins un caractère spécial</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key me-1"></i> Mettre à jour le mot de passe
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Department Tab -->
                            <div class="tab-pane fade" id="department" role="tabpanel" aria-labelledby="department-tab">
                                @if(session('department_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('department_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('chef.settings.updateDepartmentSettings') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="departmentBudget" class="form-label">Budget annuel (DH) *</label>
                                        <input type="number" class="form-control @error('departmentBudget') is-invalid @enderror" id="departmentBudget" name="departmentBudget" value="{{ old('departmentBudget', $departmentSettings->budget ?? 0) }}" required>
                                        @error('departmentBudget')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="departmentDescription" class="form-label">Description</label>
                                        <textarea class="form-control @error('departmentDescription') is-invalid @enderror" id="departmentDescription" name="departmentDescription" rows="3">{{ old('departmentDescription', $departmentSettings->description ?? '') }}</textarea>
                                        @error('departmentDescription')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Options de validation</label>
                                        <div class="form-check">
                                            <input class="form-check-input @error('enableDirectorValidation') is-invalid @enderror" type="checkbox" id="enableDirectorValidation" name="enableDirectorValidation" {{ old('enableDirectorValidation', $departmentSettings->director_validation ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enableDirectorValidation">
                                                Requérir la validation du directeur après mon approbation
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input @error('enableBudgetCheck') is-invalid @enderror" type="checkbox" id="enableBudgetCheck" name="enableBudgetCheck" {{ old('enableBudgetCheck', $departmentSettings->budget_check ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enableBudgetCheck">
                                                Vérification automatique du budget disponible
                                            </label>
                                        </div>
                                        @error('enableDirectorValidation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('enableBudgetCheck')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Enregistrer les paramètres
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>