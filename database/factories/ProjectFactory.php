<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word,
            'acronym' => strtoupper($this->faker->word),
            'description' => $this->faker->sentence,
            'country_iso_code_3' => 'BFA',
            'administrative_level' => 1,
            'tif_images' => '{"soc": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_soc.tif", "ndvi": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_ndvi.tif", "ld_risk": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_ld_risk.tif", "soil_type": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_soil_type.tif", "suitability": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_suitability.tif", "land_cover_7": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_hist_lc_7.tif", "land_cover_22": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_hist_lc_22.tif", "sdg_2001_2010": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_hist_sdg_2001_2010.tif", "sdg_2010_2020": "https://lup4ldn-lambdas.s3.eu-central-1.amazonaws.com/66deba4c960d8444ba61de61b7ecb18b3ca59d1da179a36af7e00cefa24a5159/cropped_hist_sdg_2010_2020.tif"}'
        ];
    }
}
