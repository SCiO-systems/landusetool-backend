<?php

namespace Database\Seeders;

use App\Models\LandUseType;
use Illuminate\Database\Seeder;

class LandUseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LandUseType::insert([
            [
                'name' => 'Tree-covered',
            ], [
                'name' => 'Grassland',
            ], [
                'name' => 'Cropland'
            ], [
                'name' => 'Wetland'
            ], [
                'name' => 'Artificial area'
            ], [
                'name' => 'Bare land'
            ], [
                'name' => 'Water body'
            ]
        ]);
    }
}
