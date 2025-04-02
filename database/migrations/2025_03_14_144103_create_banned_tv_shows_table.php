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
    Schema::create('banned_tv_shows', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tv_show_id')->constrained()->onDelete('cascade');
        $table->bigInteger('tmdb_id')->unsigned();
        $table->boolean('is_tmdb')->default(false)->after('tmdb_id');
        $table->text('reason')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_tv_shows');
    }
};
