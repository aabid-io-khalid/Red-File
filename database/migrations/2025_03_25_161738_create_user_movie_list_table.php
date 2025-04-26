<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('user_movie_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('tmdb_id'); // Store TMDB Movie ID
            
            // $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->timestamps();      
            $table->unique(['user_id', 'tmdb_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('user_movie_list');
    }
};



