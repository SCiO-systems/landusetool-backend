<?php

namespace App\Models;

use Storage;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\ProjectLandUseMatrix;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    // The available project statuses.
    public const STATUS_DRAFT           = 'draft';
    public const STATUS_PREPROCESSING   = 'preprocessing';
    public const STATUS_PUBLISHED       = 'published';

    protected $guarded = [];

    protected $attributes = [
        'status' => self::STATUS_DRAFT
    ];

    public function addUser($id, $role = User::ROLE_USER)
    {
        return ProjectUser::create([
            'project_id' => $this->id,
            'user_id' => $id,
            'role' => $role,
        ]);
    }

    public function setPolygon($polygon)
    {
        $this->polygon = json_encode($polygon);
        $this->save();
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
        )->withTimestamps();
    }

    public function landUseMatrix()
    {
        return $this->hasOne(ProjectLandUseMatrix::class);
    }

    public function scenarios()
    {
        return $this->hasMany(ProjectScenario::class);
    }

    public function technologies()
    {
        return $this->hasMany(ProjectWocatTechnology::class);
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function focusAreas()
    {
        return $this->hasMany(ProjectFocusArea::class);
    }

    public function delete()
    {
        $this->deleteFiles();

        $this->users()->detach();
        $this->indicators()->detach();

        $this->scenarios()->delete();
        $this->technologies()->delete();
        $this->invites()->delete();
        $this->landUseMatrix()->delete();
        $this->focusAreas()->delete();

        return parent::delete();
    }

    private function deleteFiles()
    {
        $count = $this->files()->count();
        $files = $this->files()->get();

        $deleted = 0;
        foreach ($files as $file) {
            $isFileDeleted = Storage::delete($file->path);
            if ($isFileDeleted) {
                $file->delete();
                $deleted++;
            }
        }

        return $deleted === $count;
    }
}
