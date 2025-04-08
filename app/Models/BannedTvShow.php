<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannedTvShow extends Model
{
    use HasFactory;

    protected $table = 'banned_tv_shows'; 

    protected $fillable = ['tv_show_id', 'tmdb_id', 'is_tmdb', 'reason'];

    public function tvShow()
    {
        return $this->belongsTo(TvShow::class);
    }
}
