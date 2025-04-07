<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvShow extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'year', 'rating', 'poster',
        'is_banned', 'seasons', 'episodes_per_season'
    ];

    // public function categories()
    // {
    //     return $this->belongsToMany(Category::class, 'category_tv_show');
    // }



    public function banned()
    {
        return $this->hasOne(BannedTvShow::class);
    }

    public function comments()
{
    return $this->morphMany(Comment::class, 'commentable');
}

public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable', 'categoryables');
    }

}
