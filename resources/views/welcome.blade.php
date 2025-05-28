<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Missions</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #343a40;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 500;
        }
        .main-content {
            padding: 4rem 0;
            min-height: calc(100vh - 132px);
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .logo-icon {
            font-size: 3.5rem;
            color: #007bff;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 0.6rem 2rem;
            font-weight: 500;
            border-radius: 30px;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 1rem 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <i class="fas fa-paper-plane me-2"></i>
                <span>Gestion des Missions</span>
            </a>
            <div class="ms-auto">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-light">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light">Connexion</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="card p-5">
                    <div class="logo-wrapper mb-3">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="img-fluid mb-3" style="max-height: 150px;">
                    </div>
                        <h1 class="display-5 fw-bold mb-4">Gestion des Missions</h1>
                        <p class="lead mb-4">Plateforme de gestion des demandes de missions, approbations et suivi</p>
                        
                        @if (Route::has('login'))
                            <div class="mt-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg px-5">Accéder au tableau de bord</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-dark btn-lg px-5">Commencer maintenant</a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>