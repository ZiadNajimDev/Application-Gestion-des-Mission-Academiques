<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProofDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'user_id',
        'status',
        'type',
        'title',
        'description',
        'amount',
        'document_path',
        'reviewer_id',
        'reviewer_comment',
        'reviewed_at',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}