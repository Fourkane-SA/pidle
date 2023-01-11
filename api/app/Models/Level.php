<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends Model {

    protected $fillable = ['title', 'description', 'userId', 'finished', 'published', 'size', 'pattern'];
    public function user(): HasOne {
        return $this->hasOne(User::class);
    }
    public function game() {
        return $this->belongsTo(game::class);
    }
}
