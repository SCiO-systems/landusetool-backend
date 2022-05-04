<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLandUseMatrix extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'project_land_use_matrix';

    protected const DEFAULT_MATRIX_TABLE = [
        [
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_STABLE
        ], [
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_STABLE
        ], [
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_STABLE
        ], [
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_STABLE
        ], [
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_STABLE
        ], [
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_IMPROVEMENT,
            LandUseType::LU_STATUS_DEGRADATION,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_STABLE
        ], [
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_STABLE,
            LandUseType::LU_STATUS_STABLE
        ]
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function values()
    {
        return $this->hasMany(ProjectLandUseMatrixValue::class);
    }

    public function createDefaultMatrix()
    {
        // TODO.
        $this->values()->delete();

        // We need a matrix that is 7x7.
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->values()->create([
                    'row' => $i,
                    'column' => $j,
                    'value' => self::DEFAULT_MATRIX_TABLE[$i][$j],
                ]);
            }
        }
    }
}
