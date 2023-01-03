<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\User
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $login
 * @property string $email
 * @property string $description
 * @property string $birth
 * @property int $idAvatar
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereBirth($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDescription($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereFirstname($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIdAvatar($value)
 * @method static Builder|User whereLastname($value)
 * @method static Builder|User whereLogin($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read \App\Models\Level|null $level
 */
class User extends Model
{
    use HasFactory;

    protected $fillable = ['login', 'password', 'email', 'description', 'birth', 'idAvatar', 'firstname', 'lastname'];

    protected $hidden = ['password'];


    public function level() {
        return $this->belongsTo(Level::class);
    }
}
