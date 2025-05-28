<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'user_id',
        'allowance_amount',
        'transport_amount',
        'total_amount',
        'status',
        'payment_method',
        'payment_reference',
        'payment_date',
        'comments',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}