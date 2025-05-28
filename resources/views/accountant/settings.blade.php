<!-- resources/views/accountant/settings.blade.php -->
@extends('layouts.accountant')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres du compte')

@section('content')
<div class="row">
    <div class="col-lg-3">
        <!-- Settings navigation pills -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                        <i class="fas fa-user me-2"></i> Profil
                    </button>
                    <button class="nav-link text-start" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                        <i class="fas fa-lock me-2"></i> Sécurité
                    </button>
                    <button class="nav-link text-start" id="notifications-tab" data-bs-toggle="pill" data-bs-target="#" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                        <i class="fas fa-bell me-2"></i> Notifications
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-9">
        <!-- Tab content -->
        <div class="tab-content" id="settings-tab-content">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Informations personnelles</h5>
                    </div>
                    <div class="card-body">
                        @if(session('profile_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('profile_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('accountant.settings.profile') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nom complet *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="cin" class="form-label">CIN</label>
                                    <input type="text" class="form-control @error('cin') is-invalid @enderror" id="cin" name="cin" value="{{ old('cin', $user->cin) }}">
                                    @error('cin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
            
            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Changer le mot de passe</h5>
                    </div>
                    <div class="card-body">
                        @if(session('password_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('password_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('accountant.settings.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mot de passe actuel *</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe *</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-1"></i> Mettre à jour le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
               
            </div>
            
            <!-- Notifications Tab -->
            <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                <div class="card">
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection