<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannedTvShow extends Model
{
    use HasFactory;

    protected $fillable = ['tv_show_id', 'reason'];

    public function tvShow()
    {
        return $this->belongsTo(TvShow::class);
    }
}
