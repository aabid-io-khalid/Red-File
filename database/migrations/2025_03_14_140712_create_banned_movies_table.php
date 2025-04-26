<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('banned_movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id')->nullable(); // If it's a locally created movie
            $table->unsignedBigInteger('tmdb_id')->nullable(); // If it's from TMDB API
            $table->boolean('is_tmdb')->default(false);
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('banned_movies');
    }
};
