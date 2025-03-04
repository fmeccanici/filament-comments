<?php

namespace Parallax\FilamentComments\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Parallax\FilamentComments\Models\FilamentCommentRead;

class FilamentCommentReadFactory extends Factory
{
    protected $model = FilamentCommentRead::class;

    public function definition(): array
    {
        return [
            'user_id' => 1,
            'comment_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 