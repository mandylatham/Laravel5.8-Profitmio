<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'is_admin',
    ];

    /**
     * The roles that belong to the user.
     */
    public function companies()
    {
        return $this->belongsToMany('App\Company')->withPivot('role');
    }

    public function isAdmin(): bool
    {
        return (bool)$this->is_admin;
    }

    public function isCompanyAdmin(int $companyId): bool
    {
        $company = $this->companies()->find($companyId);
        if (empty($company) || $company->pivot->role != self::ROLE_ADMIN) {
            return false;
        }
        return true;
    }

    public function isCompanyUser(int $companyId): bool
    {
        $company = $this->companies()->find($companyId);
        if (empty($company) || $company->pivot->role != self::ROLE_USER) {
            return false;
        }
        return true;
    }
}
