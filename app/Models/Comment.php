<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Comment extends Model  
{
    use SoftDeletes;

    public function thread() {
        return $this->belongsTo('App\Models\Thread');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}