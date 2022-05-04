<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForSlmProposalsToEvaluations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_focus_area_evaluation', function (Blueprint $table) {
            $table->foreignId('for_slm_proposal')->nullable();
            $table->dropIndex('project_member_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_focus_area_evaluation', function (Blueprint $table) {
            $table->dropColumn('for_slm_proposal');
            $table->unique(['project_focus_area_id', 'user_id', 'lu_class'], 'project_member_evaluation');
        });
    }
}
