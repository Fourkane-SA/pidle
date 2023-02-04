<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model {

    protected $fillable = ['login', 'password', 'email', 'description', 'birth', 'idAvatar', 'firstname', 'lastname'];

    protected $hidden = ['password'];


    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(game::class);
    }
}
