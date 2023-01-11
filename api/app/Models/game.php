<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class game extends Model
{

    protected $fillable = [];

    public function user(): HasOne {
        return $this->hasOne(User::class);
    }

    public function level(): HasOne {
        return $this->hasOne(Level::class);
    }
}
