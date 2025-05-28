<?php
// app/Http/Controllers/NotificationController.php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('notifications.index', compact('notifications'));
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }
        
        $notification->read = true;
        $notification->save();
        
        if ($notification->link) {
            return redirect($notification->link);
        }
        
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }
    
    public function markAllAsRead()
    {
        Auth::user()->notifications()->update(['read' => true]);
        
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
    
    public function deleteAll()
    {
        Auth::user()->notifications()->delete();
        
        return redirect()->back()->with('success', 'Toutes les notifications ont été supprimées.');
    }
}