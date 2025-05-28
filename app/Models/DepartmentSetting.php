<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'department',
        'budget',
        'description',
        'director_validation',
        'budget_check',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'director_validation' => 'boolean',
        'budget_check' => 'boolean',
    ];

    public function expenses()
    {
        return $this->hasMany(DepartmentExpense::class, 'department', 'department');
    }

    public function getRemainingBudgetAttribute()
    {
        $currentYearExpenses = $this->expenses()
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        return $this->budget - $currentYearExpenses;
    }
}