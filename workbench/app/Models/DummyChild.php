<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workbench\Database\Factories\DummyChildFactory;

/**
 * @mixin EloquentBuilder<DummyChild>
 *
 * @property int $id
 * @property int $dummy_id
 * @property string $label
 */
class DummyChild extends Model
{
    /** @use HasFactory<DummyChildFactory> */
    use HasFactory;

    protected $table = 'dummy_children';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dummy_id',
        'label',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * @return BelongsTo<Dummy, $this>
     */
    public function dummy(): BelongsTo
    {
        return $this->belongsTo(Dummy::class);
    }

    /**
     * @return DummyChildFactory<DummyChild>
     */
    protected static function newFactory(): DummyChildFactory
    {
        return DummyChildFactory::new();
    }
}
