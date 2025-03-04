<?php

namespace Parallax\FilamentComments\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Parallax\FilamentComments\Database\Factories\FilamentCommentFactory;

class FilamentComment extends Model
{
    use MassPrunable;
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'subject_type',
        'subject_id',
        'comment',
    ];

    public function __construct(array $attributes = [])
    {
        $config = Config::get('filament-comments');

        if (isset($config['table_name'])) {
            $this->setTable($config['table_name']);
        }

        parent::__construct($attributes);
    }

    public function user(): BelongsTo
    {
        $authenticatable = config('filament-comments.authenticatable');

        return $this->belongsTo($authenticatable, 'user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->morphTo();
    }

    public function prunable(): Builder
    {
        $days = config('filament-comments.prune_after_days');

        return static::onlyTrashed()->where('created_at', '<=', now()->subDays($days));
    }

    public function replies(): HasMany
    {
        return $this->hasMany(config('filament-comments.comment_model'), 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(config('filament-comments.comment_model'), 'parent_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(FilamentCommentRead::class, 'comment_id');
    }

    public function isReadByUser(?int $userId = null): bool
    {
        if (!$userId) {
            $userId = auth()->id();
        }

        return $this->reads()->where('user_id', $userId)->exists();
    }

    protected static function newFactory(): FilamentCommentFactory
    {
        return FilamentCommentFactory::new();
    }
}
