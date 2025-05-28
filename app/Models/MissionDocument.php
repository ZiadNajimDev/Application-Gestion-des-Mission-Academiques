<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mission_id',
        'file_path',
        'file_name',
        'document_type', // Added document_type
        'description',
        'status',
        'validated_by',
        'validated_at',
        'validation_comments'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'validated_at' => 'datetime',
    ];

    /**
     * Get the mission that owns the document.
     */
    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Get the user who validated the document.
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}