<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function post() : BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies() : hasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function comment() : BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}
