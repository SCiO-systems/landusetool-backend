<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    // The available identity providers.
    public const IDENTITY_PROVIDER_LOCAL = 'local';
    public const IDENTITY_PROVIDER_ORCID = 'orcid';

    // The available roles within a project.
    public const ROLE_USER = 'user';
    public const ROLE_OWNER = 'owner';

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    }

    public function invites()
    {
        return $this->belongsToMany(ProjectInvite::class, 'project_invite', 'user_id', 'project_id')
            ->wherePivot('status', '=', ProjectInvite::STATUS_PENDING);
    }
}
