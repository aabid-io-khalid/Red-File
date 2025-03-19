<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannedMovie extends Model
{
    use HasFactory;

    protected $fillable = ['tmdb_id'];
}
