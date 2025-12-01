<?php

namespace Workbench\App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workbench\Database\Factories\DummyFactory;

/**
 * @mixin EloquentBuilder<Dummy>
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $age
 * @property bool $is_active
 */
class Dummy extends Model
{
    /** @use HasFactory<DummyFactory> */
    use HasFactory;

    protected $table = 'dummies';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'age',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * @return HasMany<DummyChild, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(DummyChild::class);
    }

    /**
     * @return DummyFactory<Dummy>
     */
    protected static function newFactory(): DummyFactory
    {
        return DummyFactory::new();
    }

    /**
     * @throws Exception
     */
    public function updateRandomChild(bool $shouldFail = false): bool
    {
        if ($shouldFail) {
            throw new Exception('Child updating failed.');
        }

        $child = $this->children()->get()->random()->first();

        if (is_null($child)) {
            return false;
        }

        $child->update(['label' => 'updated label']);

        return true;
    }
}
