<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Address;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use function is_array;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'address' => new Address([
                'streetAddress' => $this->faker->streetAddress,
                'postcode' => $this->faker->postcode,
                'city' => $this->faker->city,
            ]),
            'remember_token' => Str::random(10),
        ];
    }

    public function hasLaravelPosts($count = 1, $attributes = []): self
    {
        if (is_array($count)) {
            [$count, $attributes] = [1, $count];
        }

        return $this->hasPosts(
            $count,
            array_merge($attributes, ['title' => 'Laravel : ' . $this->faker->sentence])
        );
    }
}
