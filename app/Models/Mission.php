<?php
// app/Models/Mission.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'transport_type',
        'start_date',
        'end_date',
        'destination_city',
        'destination_institution',
        'title',
        'objective',
        'supervisor_name',
        'status',
        'chef_approval_date',
        'chef_comments',
        'director_approval_date',
        'director_comments',
        'rejection_reason',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'chef_approval_date' => 'datetime',
        'director_approval_date' => 'datetime',
    ];
  
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function documents()
    {
        return $this->hasMany(MissionDocument::class);
    }
    public function proofs()
    {
        return $this->hasMany(MissionProof::class);
    }
    public function financialProofs()
    {
        return $this->proofs()->where('category', 'financier');
    }
    public function executionProofs()
    {
        return $this->proofs()->where('category', 'execution');
    }
    public function returnProofs()
    {
        return $this->proofs()->where('category', 'retour');
    }
    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
    public function booking()
{
    return $this->hasOne(TravelBooking::class);
}

public function ticketReservation()
{
    return $this->hasOne(TicketReservation::class);
}
public function proofDocuments()
{
    return $this->hasMany(ProofDocument::class);
}
public function hasRequiredProofDocuments()
{
    $requiredTypes = ['attendance_certificate', 'mission_report'];
    $existingTypes = $this->proofDocuments()->whereIn('document_type', $requiredTypes)->pluck('document_type')->toArray();
    
    return count(array_intersect($requiredTypes, $existingTypes)) == count($requiredTypes);
}
public function reservation()
{
    return $this->hasOne(Reservation::class);
}
public function expenses()
{
    return $this->hasMany(MissionExpense::class);
}

public function reservations()
{
    return $this->hasMany(Reservation::class);
}
public function payment()
{
    return $this->hasOne(Payment::class);
}

public function hasAllProofsApproved()
{
    $requiredCategories = ['financier', 'execution', 'retour'];
    $approvedCount = $this->proofs()
        ->whereIn('category', $requiredCategories)
        ->where('status', 'approved')
        ->count();
    
    return $approvedCount >= count($requiredCategories);
}
}