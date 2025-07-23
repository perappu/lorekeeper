<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaveDataToGameScores extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('game_scores', function (Blueprint $table) {
            $table->json('save_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('game_scores', function (Blueprint $table) {
            $table->dropColumn('save_data');
        });
    }
}
