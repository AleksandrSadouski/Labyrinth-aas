<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('password');
            $table->integer('pvp_rating')->default(150);
            $table->integer('sp_rating')->default(0);
            $table->integer('pvp_game_total')->default(0);
            $table->integer('pvp_win_total')->default(0);
            $table->integer('pvp_draw_total')->default(0);
            $table->integer('pvp_lose_total')->default(0);
            $table->integer('sp_game_completed')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
