<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'user_id',
        'status',
        'type',
        'reservation_number',
        'provider',
        'cost',
        'reservation_date',
        'notes',
        'attachment',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to get formatted reservation date
    public function getFormattedReservationDateAttribute()
    {
        return $this->reservation_date ? date('d M Y', strtotime($this->reservation_date)) : null;
    }
}