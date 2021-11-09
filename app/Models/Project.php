<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function addUser($userId, $role = User::ROLE_USER)
    {
        return ProjectUser::create([
            'project_id' => $this->id,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function setUsers($userIds)
    {
        $this->users()->detach();

        foreach ($userIds as $userId) {
            $this->addUser($userId);
        }
    }

    public function inviteUsers($userIds = [])
    {
        foreach ($userIds as $id) {
            ProjectInvite::create([
                'project_id' => $this->id,
                'user_id' => $id,
                'status' => ProjectInvite::STATUS_PENDING,
            ]);
        }
    }

    public function setOwner($userId)
    {
        ProjectUser::updateOrCreate([
            'user_id' => $userId,
            'project_id' => $this->id,
            'role' => User::ROLE_OWNER
        ]);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
            ->withPivot('role');
    }

    public function owner()
    {
        return $this->users()->wherePivot('role', '=', User::ROLE_OWNER)->first();
    }

    public function invites()
    {
        return $this->hasMany(ProjectInvite::class);
    }
}
