<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'role',
        'email',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* ================= ROLE CHECK ================= */

    public function isSuperadmin()
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeknik()
    {
        return $this->role === 'teknik';
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }
    
    public function payrollRequests()
    {
        return $this->hasMany(PayrollRequest::class, 'requested_by', 'user_id');
    }

    public function kavling()
    {
        return $this->hasMany(ProjectLocation::class, 'user_id', 'user_id');
    }

    public function itemJurnal()
    {
        return $this->hasMany(ItemJurnal::class, 'id_user', 'user_id');
    }

    public function cicilan()
    {
        return $this->hasMany(CicilanKavling::class, 'id_user', 'user_id');
    }

    public function jurnalCreated()
    {
        return $this->hasMany(Jurnal::class, 'created_by', 'user_id');
    }
}
