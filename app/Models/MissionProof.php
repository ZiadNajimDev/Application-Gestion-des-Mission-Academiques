<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'category',
        'proof_type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'amount',
        'description',
        'reviewer_id',
    'reviewer_comment',
    'reviewed_at',
        'status',
        'rejection_reason',
    ];

    /**
     * Get the mission that owns the proof.
     */
    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
    public function reviewer()
{
    return $this->belongsTo(User::class, 'reviewer_id');
}
}