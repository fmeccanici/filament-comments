<?php

namespace Parallax\FilamentComments\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Parallax\FilamentComments\Models\FilamentComment;

class FilamentCommentFactory extends Factory
{
    protected $model = FilamentComment::class;

    public function definition(): array
    {
        return [
            'user_id' => 1,
            'comment' => $this->faker->paragraph(),
            'subject_type' => 'App\\Models\\User',
            'subject_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 