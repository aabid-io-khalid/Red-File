<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvShow extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'rating', 'is_banned'];

    public function banned()
    {
        return $this->hasOne(BannedTvShow::class);
    }

    public function comments()
{
    return $this->morphMany(Comment::class, 'commentable');
}

}
