<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $fillable = ['login', 'password', 'email', 'description', 'birth', 'idAvatar', 'firstname', 'lastname'];

    protected $hidden = ['password'];


    public function level() {
        return $this->belongsTo(Level::class);
    }

    public function game() {
        return $this->belongsTo(game::class);
    }
}
