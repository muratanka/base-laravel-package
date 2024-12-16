<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->enum('type', ['main', 'customer'])->default('customer'); // Ana site veya müşteri sitesi
            $table->string('default_language')->default('en'); // Varsayılan dil
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['type', 'default_language']);
        });
    }
};
