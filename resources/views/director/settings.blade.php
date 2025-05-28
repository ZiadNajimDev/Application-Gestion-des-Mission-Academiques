<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Directeur</title>
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
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .settings-card {
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .settings-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .nav-pills .nav-link.active {
            background-color: #0d6efd;
        }
        .profile-photo-container {
            width: 150px;
            height: 150px;
            overflow: hidden;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 5px solid #f8f9fa;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-edit-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 5px;
            text-align: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .profile-photo-container:hover .photo-edit-overlay {
            opacity: 1;
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
                                @php
                                    $pendingCount = \App\Models\Mission::where('status', 'validee_chef')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-danger notification-badge">{{ $pendingCount }}</span>
                                @endif
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
                            <a class="nav-link active" href="{{ route('director.settings') }}">
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
                    <h1 class="h2">Paramètres</h1>
                </div>

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

                <div class="row">
                    <!-- Settings Navigation -->
                    <div class="col-md-3 mb-4">
                        <div class="card settings-card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Paramètres</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <button class="nav-link active text-start py-3 px-4 border-0 rounded-0" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                                        <i class="fas fa-user me-2"></i> Profil
                                    </button>
                                    <button class="nav-link text-start py-3 px-4 border-0 rounded-0" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab" aria-controls="v-pills-security" aria-selected="false">
                                        <i class="fas fa-lock me-2"></i> Sécurité
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Info Card -->
                        <div class="card settings-card shadow-sm mt-4">
                            <div class="card-body text-center">
                                @if($user->profile_photo_path)
                                    <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Photo de profil" class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="text-muted mb-0">{{ $user->email }}</p>
                                <div class="badge bg-primary mt-2">Directeur</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Settings Content -->
                    <div class="col-md-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <!-- Profile Settings -->
                            <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                <div class="card settings-card shadow-sm">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Paramètres du profil</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('director.update_profile') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row mb-4">
                                                <div class="col-md-3 text-center">
                                                    <div class="profile-photo-container" id="profilePhotoContainer">
                                                        @if($user->profile_photo_path)
                                                            <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Photo de profil" class="profile-photo" id="profilePhotoPreview">
                                                        @else
                                                            <div class="profile-photo bg-primary text-white d-flex align-items-center justify-content-center" id="profileInitials">
                                                                {{ substr($user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div class="photo-edit-overlay" id="photoEditOverlay">
                                                            <i class="fas fa-camera"></i> Modifier
                                                        </div>
                                                    </div>
                                                    <input type="file" class="d-none" id="profilePhotoInput" name="profile_photo" accept="image/*">
                                                    <div class="small text-muted mt-2">Cliquez sur la photo pour la modifier</div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label for="name" class="form-label">Nom complet</label>
                                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                                                @error('name')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label for="email" class="form-label">Email</label>
                                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                                                @error('email')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label for="cin" class="form-label">CIN</label>
                                                                <input type="text" class="form-control @error('cin') is-invalid @enderror" id="cin" name="cin" value="{{ old('cin', $user->cin) }}">
                                                                @error('cin')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label for="phone" class="form-label">Téléphone</label>
                                                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                                                @error('phone')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Security Settings -->
                            <div class="tab-pane fade" id="v-pills-security" role="tabpanel" aria-labelledby="v-pills-security-tab">
                                <div class="card settings-card shadow-sm">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Paramètres de sécurité</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="mb-3">Changer le mot de passe</h6>
                                        <form action="{{ route('director.update_password') }}" method="POST">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label for="current_password" class="form-label">Mot de passe actuel</label>
                                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Le mot de passe doit contenir au moins 8 caractères.</div>
                                            </div>
                                            <div class="form-group mb-4">
                                                <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-lock me-1"></i> Mettre à jour le mot de passe
                                                </button>
                                            </div>
                                        </form>

                                        <hr class="my-4">

                                        <h6 class="mb-3">Sessions de connexion</h6>
                                        <div class="alert alert-info">
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <i class="fas fa-info-circle fa-2x"></i>
                                                </div>
                                                <div>
                                                    <strong>Session actuelle</strong>
                                                    <p class="mb-0">Windows · Chrome · {{ request()->ip() }}</p>
                                                    <small class="text-muted">Connecté depuis {{ now()->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle profile photo upload
            const profilePhotoContainer = document.getElementById('profilePhotoContainer');
            const profilePhotoInput = document.getElementById('profilePhotoInput');
            const profilePhotoPreview = document.getElementById('profilePhotoPreview');
            const profileInitials = document.getElementById('profileInitials');
            
            profilePhotoContainer.addEventListener('click', function() {
                profilePhotoInput.click();
            });
            
            profilePhotoInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        // Show the preview image
                        if (profilePhotoPreview) {
                            profilePhotoPreview.src = e.target.result;
                        } else {
                            // Create preview element if it doesn't exist
                            const preview = document.createElement('img');
                            preview.src = e.target.result;
                            preview.className = 'profile-photo';
                            preview.id = 'profilePhotoPreview';
                            
                            // Replace initials with preview
                            if (profileInitials) {
                                profileInitials.remove();
                            }
                            
                            profilePhotoContainer.prepend(preview);
                        }
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Handle tab selection based on URL hash
            const hash = window.location.hash;
            if (hash) {
                const tab = document.querySelector(`[data-bs-target="${hash}"]`);
                if (tab) {
                    const bsTab = new bootstrap.Tab(tab);
                    bsTab.show();
                }
            }
            
            // Update URL hash when tab changes
            const tabs = document.querySelectorAll('[data-bs-toggle="pill"]');
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    window.location.hash = e.target.dataset.bsTarget;
                });
            });
        });
    </script>
</body>
</html>