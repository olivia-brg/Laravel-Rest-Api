<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'isbn' => fake()->isbn13(),
            'description' => fake()->paragraph(),
            'author_id' => Author::inRandomOrder()->first()->id ?? Author::factory(),
            'genre' => fake()->randomElement(['Science-fiction', 'Aventure', 'Romance', 'Fantastique', 'Histoire', 'Autre']),
            'publication_date' => fake()->date(),
            'total_copies' => fake()->numberBetween(1, 100),
            'available_copies' => fake()->numberBetween(0, 100),
            'price' => fake()->randomFloat(2, 5, 200),
            'cover_image' => fake()->imageUrl(200, 200, 'books', true, 'Faker', true, 'jpg'),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
