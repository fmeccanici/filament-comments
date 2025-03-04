<?php

namespace Parallax\FilamentComments\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class FilamentCommentQueryBuilder extends Builder
{
    public function unreadCount(int $userId): int
    {
        return $this->unread($userId)->count();
    }

    public function unread(int $userId): Builder
    {
        return $this->whereDoesntHave('reads', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
}
