<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'cin',
        'phone',
        'department',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the missions for the user.
     */
    public function missions()
    {
        return $this->hasMany(Mission::class);
    }

    // Add helper methods for role checking
    public function isEnseignant()
    {
        return $this->role === 'enseignant';
    }

    public function isDirecteur()
    {
        return $this->role === 'directeur';
    }

    public function isChefDepartement()
    {
        return $this->role === 'chef_departement';
    }

    public function isComptable()
    {
        return $this->role === 'comptable';
    }
    public function isHeadOfDepartment($departmentName = null)
{
    if ($this->role !== 'chef_departement') {
        return false;
    }
    
    if ($departmentName) {
        return $this->department === $departmentName;
    }
    
    return true;
}
public function reservations()
{
    return $this->hasMany(Reservation::class);
}

public function departmentMissions()
{
    if (!$this->isChefDepartement() || !$this->department) {
        return Mission::where('id', 0); // Return empty query builder
    }
    
    return Mission::whereHas('user', function($query) {
        $query->where('department', $this->department);
    });
}
public function setDepartmentAttribute($value)
{
    if ($value && $this->attributes['department'] !== $value) {
        $this->attributes['department'] = $value;
        
        // Check if department exists in settings
        $exists = \App\Models\DepartmentSetting::where('department', $value)->exists();
        
        if (!$exists) {
            // Create new department setting
            \App\Models\DepartmentSetting::create([
                'department' => $value,
                'budget' => 0,
                'description' => 'Automatically created when assigned to a user'
            ]);
        }
    }
}
public function notifications()
{
    return $this->hasMany(Notification::class);
}
}