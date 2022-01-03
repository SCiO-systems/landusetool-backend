<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandUseType extends Model
{
    use HasFactory;

    // The types.
    const LU_TREE_COVERED = 0;
    const LU_GRASSLAND = 1;
    const LU_CROPLAND = 2;
    const LU_WETLAND = 3;
    const LU_ARTIFICIAL_AREA = 4;
    const LU_BARE_LAND = 5;
    const LU_WATER_BODY = 6;

    // Lookup tables.
    protected const LOOKUP_TYPE_TO_NAME = [
        self::LU_TREE_COVERED => 'Tree-covered',
        self::LU_GRASSLAND => 'Grassland',
        self::LU_CROPLAND => 'Cropland',
        self::LU_WETLAND => 'Wetland',
        self::LU_ARTIFICIAL_AREA => 'Artificial area',
        self::LU_BARE_LAND => 'Bare land',
        self::LU_WATER_BODY => 'Water body',
    ];

    protected const LOOKUP_NAME_TO_TYPE = [
        'Tree-covered' => self::LU_TREE_COVERED,
        'Grassland' => self::LU_GRASSLAND,
        'Cropland' => self::LU_CROPLAND,
        'Wetland' => self::LU_WETLAND,
        'Artificial area' => self::LU_ARTIFICIAL_AREA,
        'Bare land' => self::LU_BARE_LAND,
        'Water body' => self::LU_WATER_BODY,
    ];

    // The statuses.
    const LU_STATUS_STABLE = 'stable';
    const LU_STATUS_DEGRADATION = 'degradation';
    const LU_STATUS_IMPROVEMENT = 'improvement';

    protected $guarded = [];

    public function getNameFromType($type)
    {
        return self::LOOKUP_TYPE_TO_NAME[$type];
    }

    public function getTypeFromName($name)
    {
        return self::LOOKUP_NAME_TO_TYPE[$name];
    }
}
