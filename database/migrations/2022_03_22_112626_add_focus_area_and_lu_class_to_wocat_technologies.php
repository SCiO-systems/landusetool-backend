<?php

use App\Models\ProjectWocatTechnology;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFocusAreaAndLuClassToWocatTechnologies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_wocat_slm_technology', function (Blueprint $table) {
            $table->string('status')->default(ProjectWocatTechnology::STATUS_PROPOSAL);
            $table->foreignId('project_focus_area_id');
            $table->string('lu_class');

            $table->unique(['project_focus_area_id', 'project_id', 'lu_class'], 'project_focus_area_lu_class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_wocat_slm_technology', function (Blueprint $table) {
            $table->dropUnique('project_focus_area_lu_class');
            $table->dropColumn(['status', 'project_focus_area_id', 'lu_class']);
        });
    }
}
