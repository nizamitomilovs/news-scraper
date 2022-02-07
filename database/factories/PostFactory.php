<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * @return array<string, string,int>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->randomNumber(),
            'title' => 'Amazing news',
            'link' => 'http://test.com',
            'points' => 0,
            'posted_at' => '2022-01-02'
        ];
    }
}
