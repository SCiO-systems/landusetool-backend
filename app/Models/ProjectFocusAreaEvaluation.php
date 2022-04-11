<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFocusAreaEvaluation extends Model
{
    use HasFactory;

    // The available ld impact evaluations
    public const LD_IMPACT_IMPROVED = 'improved';
    public const LD_IMPACT_SLIGHTLY_IMPROVED = 'slightly_improved';
    public const LD_IMPACT_NEUTRAL = 'neutral';
    public const LD_IMPACT_SLIGHTLY_REDUCED = 'slightly_reduced';
    public const LD_IMPACT_REDUCED = 'reduced';

    protected $guarded = [];
    protected $table = 'project_focus_area_evaluation';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function focusArea()
    {
        return $this->belongsTo(ProjectFocusArea::class);
    }

    public function slmProposal()
    {
        return $this->belongsTo(ProjectWocatTechnology::class, 'for_slm_proposal');
    }

}
