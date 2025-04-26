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
    Schema::create('tv_shows', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->integer('year')->nullable();
        $table->float('rating')->nullable();
        $table->string('poster')->nullable();
        $table->boolean('is_banned')->default(false);
        $table->unsignedInteger('seasons')->default(1); 
        $table->unsignedInteger('episodes_per_season')->default(1); 
        $table->timestamps();
    });
    
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_shows');
    }
};
