<?php

namespace Parallax\FilamentComments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilamentCommentAttachment extends Model
{
    protected $fillable = [
        'comment_id',
        'path',
        'name',
        'mime_type',
        'size'
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(FilamentComment::class);
    }
} 