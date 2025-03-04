<?php

namespace Parallax\FilamentComments\Tests\Unit\QueryBuilders;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Parallax\FilamentComments\Models\FilamentComment;
use Parallax\FilamentComments\Models\FilamentCommentRead;
use PHPUnit\Framework\TestCase;

class FilamentCommentQueryBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_unread_count_returns_correct_number(): void
    {
        // Given
        $comment1 = FilamentComment::factory()->create();
        $comment2 = FilamentComment::factory()->create();
        $userId = 1;

        FilamentCommentRead::factory()->create([
            'comment_id' => $comment1->id,
            'user_id' => $userId,
        ]);

        // When
        $unreadCount = FilamentComment::query()->unreadCount($userId);

        // Then
        $this->assertEquals(1, $unreadCount);
    }

    public function test_unread_scope_returns_only_unread_comments(): void
    {
        // Given
        $comment1 = FilamentComment::factory()->create();
        $comment2 = FilamentComment::factory()->create();
        $userId = 1;

        FilamentCommentRead::factory()->create([
            'comment_id' => $comment1->id,
            'user_id' => $userId,
        ]);

        // When
        $unreadComments = FilamentComment::query()->unread($userId)->get();

        // Then
        $this->assertCount(1, $unreadComments);
        $this->assertEquals($comment2->id, $unreadComments->first()->id);
    }

    public function test_unread_scope_includes_comments_read_by_other_users(): void
    {
        // Given
        $comment = FilamentComment::factory()->create();
        $userId1 = 1;
        $userId2 = 2;

        FilamentCommentRead::factory()->create([
            'comment_id' => $comment->id,
            'user_id' => $userId1,
        ]);

        // When
        $unreadComments = FilamentComment::query()->unread($userId2)->get();

        // Then
        $this->assertCount(1, $unreadComments);
        $this->assertEquals($comment->id, $unreadComments->first()->id);
    }
}
