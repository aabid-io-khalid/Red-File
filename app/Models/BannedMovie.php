<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannedMovie extends Model
{
    use HasFactory;

    protected $fillable = ['movie_id', 'tmdb_id', 'is_tmdb', 'reason'];

    public function bannedMovie()
    {
        return $this->hasOne(BannedMovie::class, 'movie_id');
    }
}
