<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Dummy;
use Workbench\App\Models\DummyChild;

/**
 * @template TModel of DummyChild
 *
 * @extends Factory<TModel>
 */
class DummyChildFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = DummyChild::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->text(20),
            'dummy_id' => Dummy::factory(),
        ];
    }
}
