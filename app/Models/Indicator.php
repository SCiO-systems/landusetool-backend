<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Indicator::class, 'parent_indicator_id');
    }

    public function children()
    {
        return $this->hasMany(Indicator::class, 'parent_indicator_id', 'id')->with('children');
    }
}
