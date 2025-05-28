<?php
// app/Models/Notification.php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type', // primary, success, warning, danger, info
        'icon',
        'link',
        'read',
        'related_id'
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Get the notification's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['private-App.Models.User.' . $this->user_id];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => $this->icon,
            'link' => $this->link,
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}