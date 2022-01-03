<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectScenario extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'project_scenario';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
