<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectFocusAreaEvaluation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_focus_area_evaluation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_focus_area_id');
            $table->foreignId('user_id');
            $table->string('lu_class');
            $table->smallInteger('soil_value')->default(0);
            $table->smallInteger('water_value')->default(0);
            $table->smallInteger('biodiversity_value')->default(0);
            $table->smallInteger('climate_change_resilience_value')->default(0);
            $table->smallInteger('production_value')->default(0);
            $table->smallInteger('economic_viability_value')->default(0);
            $table->smallInteger('food_security_value')->default(0);
            $table->smallInteger('equality_of_opportunity_value')->default(0);
            $table->string('anticipated_ld_impact')->nullable();
            $table->timestamps();

            $table->unique(['project_focus_area_id', 'user_id', 'lu_class'], 'project_member_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_focus_area_evaluation');
    }
}
