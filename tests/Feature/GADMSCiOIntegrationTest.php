<?php

namespace Tests\Feature;

use Cache;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GADMSCiOIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);

        $this->withHeaders(['Authorization' => 'Bearer ' . $this->token]);
    }

    /** @test */
    public function it_returns_the_country_level_links_using_a_proper_format()
    {
        $response = $this->json('get', route('api.v1.country_level_links', [
            'country_iso_code_3' => 'GRC'
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'iso_316_1_alpha_3',
            'iso_316_1_alpha_2',
            'country',
            'full_name',
            'unccd_annex',
            'who_unfcc',
            'usaid',
            'undrr',
            'gfdrr',
            'gfdrrdrf',
        ]);
    }

    /** @test */
    public function it_returns_not_found_for_the_country_level_links_if_the_country_iso_is_wrong()
    {
        $response = $this->json('get', route('api.v1.country_level_links', [
            'country_iso_code_3' => 'GRE'
        ]));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_unprocessable_entity_if_the_country_is_not_specified()
    {
        $response = $this->json('get', route('api.v1.country_level_links'));

        $response->assertStatus(422);
    }

    /** @test */
    public function it_caches_the_response_for_country_level_links_if_it_is_successful()
    {
        $country = 'GRC';
        $cacheKey = "country_level_links_$country";

        $response = $this->json('get', route('api.v1.country_level_links', [
            'country_iso_code_3' => $country
        ]));

        $response->assertStatus(200);
        $this->assertTrue(Cache::has($cacheKey));
    }

    /** @test */
    public function it_returns_the_admin_level_area_polygons_for_a_country()
    {
        $country = 'GRC';
        $adminLevel = 1;

        $response = $this->json('get', route('api.v1.polygons.admin_level_areas', [
            'country_iso_code_3' => $country,
            'administrative_level' => $adminLevel
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['polygon']);
    }

    /** @test */
    public function it_caches_the_admin_level_area_polygons_for_a_country_and_an_admin_level()
    {
        $country = 'GRC';
        $adminLevel = 1;
        $cacheKey = "admin_level_area_polygons_$country$adminLevel";

        $response = $this->json('get', route('api.v1.polygons.admin_level_areas', [
            'country_iso_code_3' => $country,
            'administrative_level' => $adminLevel
        ]));

        $response->assertStatus(200);
        $this->assertTrue(Cache::has($cacheKey));
    }

    /** @test */
    public function it_returns_a_polygon_based_on_coordinates_and_admin_level()
    {
        $point = [
            'type' => 'Feature',
            'properties' => [],
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    0 => 37.617188,
                    1 => 46.073231,
                ],
            ],
        ];
        $adminLevel = 1;

        $response = $this->json('post', route('api.v1.polygons.coordinates', [
            'administrative_level' => $adminLevel,
            'point' => $point,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['polygon']);
    }
}
