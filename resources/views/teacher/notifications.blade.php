
@php
    use App\Helpers\NotificationHelper;
@endphp

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('teacher.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Notifications</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('teacher.notifications') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sync-alt me-1"></i> Actualiser
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notifications</h5>
                    <div>
                        <form action="{{ route('notifications.delete-all') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger me-2">
                                <i class="fas fa-trash-alt me-1"></i> Tout supprimer
                            </button>
                        </form>
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-check-circle me-1"></i> Tout marquer comme lu
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($notifications as $notification)
                            <div class="list-group-item list-group-item-action {{ $notification->read ? 'bg-light' : '' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-{{ $notification->type }}">
                                        <i class="fas fa-{{ $notification->icon }} me-2"></i> {{ $notification->title }}
                                    </h6>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notification->message }}</p>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        @if($notification->read)
                                            <i class="fas fa-check me-1"></i> Lu
                                        @else
                                            Non lu
                                        @endif
                                    </small>
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none">
                                            {{ $notification->link ? 'Voir détails' : 'Marquer comme lu' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <p>Vous n'avez pas de notifications.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($notifications->count() > 0)
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection