<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectWocatTechnology extends Model
{
    use HasFactory;

    // The available project statuses.
    public const STATUS_PROPOSAL        = 'proposal';
    public const STATUS_FINAL           = 'final';

    protected $guarded = [];

    protected $attributes = [
        'status' => self::STATUS_PROPOSAL
    ];

    protected $table = 'project_wocat_slm_technology';


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function focusArea()
    {
        return $this->belongsTo(ProjectFocusArea::class, 'project_focus_area_id');
    }

    public function votes()
    {
        return $this->hasMany(ProjectWocatTechnologyVote::class, 'project_wocat_slm_technology_id');
    }
}
