<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique();
            $table->json('maze');
            $table->integer('size');
            $table->float('branch_weight');
            $table->float('hallway_weight');
            $table->integer('entry_x');
            $table->integer('entry_y');
            $table->integer('exit_x');
            $table->integer('exit_y');
            $table->integer('current_turn')->default(1);
            $table->integer('turn_total')->default(0);
            $table->string('status')->default('waiting');
            $table->integer('winner_order')->nullable();
            $table->boolean('draw')->default(false);
            $table->boolean('first_finished')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};