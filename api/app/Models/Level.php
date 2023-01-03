<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Level
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $userId
 * @property int $size
 * @property string $pattern
 * @property int $finished
 * @property int $published
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @method static Builder|Level newModelQuery()
 * @method static Builder|Level newQuery()
 * @method static Builder|Level query()
 * @method static Builder|Level whereCreatedAt($value)
 * @method static Builder|Level whereDescription($value)
 * @method static Builder|Level whereFinished($value)
 * @method static Builder|Level whereId($value)
 * @method static Builder|Level wherePattern($value)
 * @method static Builder|Level wherePublished($value)
 * @method static Builder|Level whereSize($value)
 * @method static Builder|Level whereTitle($value)
 * @method static Builder|Level whereUpdatedAt($value)
 * @method static Builder|Level whereUserId($value)
 * @mixin Eloquent
 */
class Level extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'userId', 'finished', 'published', 'size', 'pattern'];
    public function user(): HasOne {
        return $this->hasOne(User::class);
    }
}
