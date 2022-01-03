<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\ProjectLandUseMatrix;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function addUser($id, $role = User::ROLE_USER)
    {
        return ProjectUser::create([
            'project_id' => $this->id,
            'user_id' => $id,
            'role' => $role,
        ]);
    }

    public function setUsers($users)
    {
        $this->users()->detach();

        foreach ($users as $id) {
            $this->addUser($id);
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

    public function createDefaultLandUseMatrix()
    {
        if (!$this->landUseMatrix()->exists()) {
            $matrix = ProjectLandUseMatrix::create(['project_id' => $this->id]);
            $matrix->createDefaultMatrix();
        }
    }

    public function setOwner($id)
    {
        ProjectUser::updateOrCreate([
            'user_id' => $id, 'project_id' => $this->id, 'role' => User::ROLE_OWNER
        ]);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
            ->withPivot('role');
    }

    public function owner()
    {
        return $this->hasOneThrough(
            User::class,
            ProjectUser::class,
            'project_id',
            'id',
            'id',
            'user_id'
        )->where('role', User::ROLE_OWNER);
    }

    public function invites()
    {
        return $this->hasMany(ProjectInvite::class);
    }

    public function indicators()
    {
        return $this->belongsToMany(
            Indicator::class,
            'project_indicator',
            'project_id',
            'indicator_id'
        );
    }

    public function landUseMatrix()
    {
        return $this->hasOne(ProjectLandUseMatrix::class);
    }

    public function scenarios()
    {
        return $this->hasMany(ProjectScenario::class);
    }
}
