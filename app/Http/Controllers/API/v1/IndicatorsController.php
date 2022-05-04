<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Indicator;
use App\Http\Controllers\Controller;

class IndicatorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $root = Indicator::whereNull('parent_indicator_id')->with('children')->get();

        return response()->json($root);
    }
}
