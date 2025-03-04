<?php

namespace Parallax\FilamentComments\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class FilamentCommentQueryBuilder extends Builder
{
    public function whereUnread(int $userId): Builder
    {
        return $this->whereDoesntHave('reads', function (Builder $query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('user_id', '!=', $userId);
    }
}
