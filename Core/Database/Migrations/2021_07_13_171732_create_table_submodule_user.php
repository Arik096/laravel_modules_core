<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSubmoduleUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submodule_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submodule_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('submodule_id')->references('id')->on('submodules');
            $table->foreign('user_id')->references('id')->on('sujog_users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submodule_user');
    }
}
