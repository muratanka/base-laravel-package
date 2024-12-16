<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('theme')->nullable();
            $table->unsignedBigInteger('hosting_id')->nullable();
            $table->string('db_name')->nullable();
            $table->timestamps();

            $table->foreign('hosting_id')->references('id')->on('hostings')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sites');
    }
}
