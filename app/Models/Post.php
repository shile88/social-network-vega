<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Termwind\Components\Li;

class Post extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes() : HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reportable_id');
    }

    //Check post status inside reports table
    public function scopeNotBanned($query)
    {
        return $query->whereDoesntHave('reports', function ($subquery) {
            $subquery->where('status', 'accepted');
        });
    }

}
