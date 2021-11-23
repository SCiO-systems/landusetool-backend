<?php

use App\Models\User;
use App\Models\ProjectInvite;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('transferable')->default(false);
            $table->unsignedBigInteger('parent_indicator_id')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('land_use_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('identity_provider')->default(User::IDENTITY_PROVIDER_LOCAL);
            $table->string('identity_provider_external_id')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('acronym')->nullable();
            $table->text('description')->nullable();
            $table->string('country_iso_code_3')->nullable();
            $table->string('administrative_level')->nullable();
            $table->json('tif_images')->nullable();
            $table->timestamps();
        });

        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->default(User::ROLE_USER);
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });

        Schema::create('project_invite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default(ProjectInvite::STATUS_PENDING);
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });

        Schema::create('project_indicator', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('indicator_id');
            $table->timestamps();

            $table->unique(['project_id', 'indicator_id']);
        });

        Schema::create('project_focus_area', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');
            $table->string('name')->nullable();
            $table->string('path');

            $table->timestamps();
        });

        Schema::create('project_focus_area_land_use_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_focus_area_id');
            $table->unsignedBigInteger('land_use_type_id');
            $table->boolean('enabled')->default(false);

            $table->unique(['project_focus_area_id', 'land_use_type_id'], 'pfa_lut_id');
        });

        Schema::create('project_focus_area_land_use_type_rating', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_focus_area_land_use_type_id');
            $table->string('soil')->default(4);
            $table->string('water')->default(4);
            $table->string('biodiversity')->default(4);
            $table->string('climate_change_resilience')->default(4);
            $table->string('production')->default(4);
            $table->string('economic_viability')->default(4);
            $table->string('food_security')->default(4);
            $table->string('equality_of_opportunity')->default(4);
            $table->string('overall_anticipated_impact')->default(null);

            $table->unique(['user_id', 'project_focus_area_land_use_type_id'], 'user_pfalut_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_invite');
        Schema::drop('project_user');
        Schema::drop('projects');
        Schema::drop('users');
    }
}
