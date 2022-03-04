<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Comment;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->text,
        ];
    }
}
