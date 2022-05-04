<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectWocatTechnologyVote extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'project_wocat_slm_technology_votes';
    public $timestamps = false;

    public function projectWocatTechnology()
    {
        return $this->belongsTo(ProjectWocatTechnology::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
