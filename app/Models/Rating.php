<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Rating extends Model  
{
    use SoftDeletes;

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function comment() {
        return $this->belongsTo('App\Models\Comment');
    }
}