<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'department',
        'mission_id',
        'payment_id',
        'amount',
        'type',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function departmentSetting()
    {
        return $this->belongsTo(DepartmentSetting::class, 'department', 'department');
    }
} 