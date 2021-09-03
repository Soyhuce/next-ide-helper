<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->text,
        ];
    }
}
