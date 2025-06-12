<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('sort')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();
        });
        Schema::table('games', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_categories');
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
