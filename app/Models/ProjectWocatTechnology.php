<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectWocatTechnology extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'project_wocat_slm_technology';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
