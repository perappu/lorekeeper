<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('sort')->unsigned()->nullable();
            $table->integer('currency_id')->unsigned()->index();
            $table->integer('currency_cap')->unsigned();
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();
            $table->boolean('has_image')->default(false);
            $table->string('hash', 10)->nullable();
            $table->boolean('is_active')->default(false);
            $table->decimal('score_ratio', 5, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('games');
    }
};
