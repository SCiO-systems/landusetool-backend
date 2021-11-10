<?php

use App\Models\ProjectInvite;
use App\Models\User;
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
