<?php

namespace Database\Seeders;

use App\Models\Indicator;
use Illuminate\Database\Seeder;

class IndicatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Indicator::truncate();

        $ecologicalImpacts = Indicator::create([
            'name' => 'Ecological Impacts',
        ]);

        $soil = Indicator::create([
            'name' => 'Soil',
            'parent_indicator_id' => $ecologicalImpacts->id,
        ]);

        Indicator::insert([
            [
                'name' => 'Soil moisture',
                'transferable' => true,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced soil loss',
                'transferable' => true,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Soil accumulation/development',
                'transferable' => true,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced soil crusting/sealing',
                'transferable' => true,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced soil compaction',
                'transferable' => true,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Nutrient cycling/recharge',
                'transferable' => true,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced salinity',
                'transferable' => true,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced acidity',
                'transferable' => true,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Soil organic matter (SOC)',
                'transferable' => false,
                'parent_indicator_id' => $soil->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        $water = Indicator::create([
            'name' => 'Water',
            'parent_indicator_id' => $ecologicalImpacts->id,
        ]);

        Indicator::insert([
            [
                'name' => 'Water availability',
                'transferable' => true,
                'parent_indicator_id' => $water->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Water quality',
                'transferable' => true,
                'parent_indicator_id' => $water->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Improved harvesting/collection of water (e.g. rainfall, runoff, dew, snow)',
                'transferable' => true,
                'parent_indicator_id' => $water->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced surface runoff',
                'transferable' => true,
                'parent_indicator_id' => $water->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Drainage of excess water',
                'transferable' => true,
                'parent_indicator_id' => $water->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Groundwater table/aquifer recharge',
                'transferable' => true,
                'parent_indicator_id' => $water->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced water loss by evaporation',
                'transferable' => true,
                'parent_indicator_id' => $water->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);

        $biodiversity = Indicator::create([
            'name' => 'Biodiversity',
            'parent_indicator_id' => $ecologicalImpacts->id,
        ]);

        Indicator::insert([
            [
                'name' => 'Increased or maintained ecologically healthy land covers',
                'transferable' => false,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Biomass productivity',
                'transferable' => false,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'No or reduced expansion of cultivation areas into natural/semi-natural systems',
                'transferable' => true,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Increased standing timber yield (e.g. in protected forest/woodland)',
                'transferable' => true,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Improved plant diversity',
                'transferable' => true,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced occurrence of invasive alien species',
                'transferable' => true,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Improved animal diversity',
                'transferable' => true,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Maintained or increased habitat diversity',
                'transferable' => true,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced occurrence of pests/diseases',
                'transferable' => true,
                'parent_indicator_id' => $biodiversity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        $climate = Indicator::create([
            'name' => 'Climate change resilience',
            'parent_indicator_id' => $ecologicalImpacts->id,
        ]);

        Indicator::insert([
            [
                'name' => 'Reduced damages by flooding',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced landslides/debris flows',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced damages by drought',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced damages by cyclones, rain storms',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced emission of carbon and greenhouse gases',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced fire risk',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Reduced wind velocity',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Reduced wind transported sediments',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Favorable micro-climate',
                'transferable' => true,
                'parent_indicator_id' => $climate->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);

        $socioEconomicImpacts = Indicator::create([
            'name' => 'Socio-economic Impacts',
        ]);

        $production = Indicator::create([
            'name' => 'Production',
            'parent_indicator_id' => $socioEconomicImpacts->id,
        ]);

        Indicator::insert([
            [
                'name' => 'Crop yield (for foods or biofuels)',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Crop quality (for foods or biofuels)',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Fodder yield',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Fodder quality',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Animal production',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Wood production (e.g. in production forests/woodlands)',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Non-wood forest production',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced risk of production failure',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Product diversity',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Water availability for livestock',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Water quality for livestock',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Irrigation water availability',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Irrigation water quality',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Improved irrigation water use efficiency',
                'transferable' => true,
                'parent_indicator_id' => $production->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        $economicViability = Indicator::create([
            'name' => 'Economic viability',
            'parent_indicator_id' => $socioEconomicImpacts->id,
        ]);

        Indicator::insert([
            [
                'name' => 'Improved agricultural inputs use efficiency',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Farm income',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Diversity of income sources',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced economic disparities',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Reduced workload',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Net present value (NPV) (of the SLM practice)',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Cost-benefit ratio (CBR) (of the SLM practice)',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Return on investment (RoI) (on the SLM practice)',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Total Economic Value (TEV) (of the whole LU/LM type)',
                'transferable' => true,
                'parent_indicator_id' => $economicViability->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);

        $socioCulturalImpacts = Indicator::create([
            'name' => 'Sociocultural Impacts',
        ]);

        $foodSecurity = Indicator::create([
            'name' => 'Food security',
            'parent_indicator_id' => $socioCulturalImpacts->id,
        ]);

        Indicator::insert([
            [
                'name' => 'Food security / availability',
                'transferable' => true,
                'parent_indicator_id' => $foodSecurity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Food security / accessibility',
                'transferable' => true,
                'parent_indicator_id' => $foodSecurity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Food security / stability',
                'transferable' => true,
                'parent_indicator_id' => $foodSecurity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Food security / utilization',
                'transferable' => true,
                'parent_indicator_id' => $foodSecurity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Food security / self-sufficiency',
                'transferable' => true,
                'parent_indicator_id' => $foodSecurity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        $equalityOfOpportunity = Indicator::create([
            'name' => 'Equality of opportunity',
            'parent_indicator_id' => $socioCulturalImpacts->id,
        ]);

        Indicator::insert([
            [
                'name' => 'Social adoption of SLM technology, innovations',
                'transferable' => true,
                'parent_indicator_id' => $equalityOfOpportunity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Improved security of land use/water rights',
                'transferable' => true,
                'parent_indicator_id' => $equalityOfOpportunity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Cultural opportunities (spiritual, religious, aesthetic etc.)',
                'transferable' => true,
                'parent_indicator_id' => $equalityOfOpportunity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'name' => 'Social empowerment of disadvantaged groups (aspects of gender, age, social status, ethnicity, etc.)',
                'transferable' => true,
                'parent_indicator_id' => $equalityOfOpportunity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
