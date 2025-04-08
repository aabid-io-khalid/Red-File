<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMovieList extends Model
{
    use HasFactory;

    protected $table = 'user_movie_list';

    protected $fillable = [
        'user_id',
        'tmdb_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
