<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'year', 'rating', 'poster', 'is_banned'
    ];

    // public function categories()
    // {
    //     return $this->belongsToMany(Category::class, 'category_movie');
    // }

    public function bannedMovie()
    {
        return $this->hasOne(BannedMovie::class, 'movie_id');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_movie_list')->withTimestamps();
    }
    
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable', 'categoryables');
    }

}
