<?php

namespace Parallax\FilamentComments\Tests\Unit\Models;

use Illuminate\Foundation\Auth\User;
use Parallax\FilamentComments\Models\FilamentComment;
use Parallax\FilamentComments\Models\FilamentCommentRead;
use Parallax\FilamentComments\Tests\TestCase;

class FilamentCommentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('filament-comments.authenticatable', User::class);
        config()->set('filament-comments.comment_model', FilamentComment::class);
        config()->set('filament-comments.prune_after_days', 30);
    }

    public function test_comment_belongs_to_user(): void
    {
        // Given
        $comment = new FilamentComment();

        // When
        $relation = $comment->user();

        // Then
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertInstanceOf(User::class, $relation->getRelated());
    }

    public function test_comment_can_have_replies(): void
    {
        // Given
        $comment = new FilamentComment();

        // When
        $relation = $comment->replies();

        // Then
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertInstanceOf(FilamentComment::class, $relation->getRelated());
    }

    public function test_comment_can_have_parent(): void
    {
        // Given
        $comment = new FilamentComment();

        // When
        $relation = $comment->parent();

        // Then
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertInstanceOf(FilamentComment::class, $relation->getRelated());
    }

    public function test_comment_can_have_reads(): void
    {
        // Given
        $comment = new FilamentComment();

        // When
        $relation = $comment->reads();

        // Then
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertInstanceOf(FilamentCommentRead::class, $relation->getRelated());
    }

    public function test_can_check_if_comment_is_read_by_user(): void
    {
        // Given
        $comment = FilamentComment::factory()->create();
        $userId = 1;
        FilamentCommentRead::factory()->create([
            'comment_id' => $comment->id,
            'user_id' => $userId,
        ]);

        // When
        $isRead = $comment->isReadByUser($userId);

        // Then
        $this->assertTrue($isRead);
    }

    public function test_prunable_scope_returns_old_trashed_comments(): void
    {
        // Given
        $comment = FilamentComment::factory()->create([
            'deleted_at' => now()->subDays(31),
        ]);

        // When
        $prunableComments = $comment->prunable();

        // Then
        $this->assertStringContainsString('deleted_at is not null', $prunableComments->toSql());
        $this->assertStringContainsString('created_at <=', $prunableComments->toSql());
    }

    public function test_comment_can_use_custom_table_name_from_config(): void
    {
        // Given
        config()->set('filament-comments.table_name', 'custom_comments');

        // When
        $comment = new FilamentComment();

        // Then
        $this->assertEquals('custom_comments', $comment->getTable());
    }

    public function test_query_builder_returns_unread_comments(): void
    {
        // Given
        $comment = FilamentComment::factory()->create();
        $userId = 1;

        // When
        $unreadCount = $comment->newQuery()->unreadCount($userId);

        // Then
        $this->assertEquals(1, $unreadCount);
    }
}
