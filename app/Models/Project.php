<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function inviteUsers($users)
    {
        foreach ($users as $id) {
            try {
                ProjectInvite::create([
                    'project_id' => $this->id,
                    'user_id' => $id,
                    'status' => ProjectInvite::STATUS_PENDING,
                ]);
            } catch (Exception $ex) {
                Log::error('The user is already invited. ' . $ex->getMessage());
            }
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
