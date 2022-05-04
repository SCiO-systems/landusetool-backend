<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectLandUseMatrixValue extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'project_land_use_matrix_values';

    public function landUseMatrix()
    {
        return $this->belongsTo(ProjectLandUseMatrix::class);
    }
}
