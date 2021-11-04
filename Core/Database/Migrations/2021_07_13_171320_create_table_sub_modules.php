<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSubModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submodules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('action')->nullable();
            $table->enum('action_type',['url','route']);
            $table->string('icons')->nullable();
            $table->string('upload_icon')->nullable();
            $table->longText('comments')->nullable();
            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('id')->on('modules');
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('submodules');
    }
}
