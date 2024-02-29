<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function likes() : HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }


    public function friendsOfMine()
    {
        return $this->belongsToMany(User::class,
            'connections',
            'user_id',
            'friend_id')
            ->wherePivot('status', 'accepted');
    }

    public function friendOf()
    {
        return $this->belongsToMany(User::class,
            'connections',
            'friend_id',
            'user_id')
            ->wherePivot('status', 'accepted');
    }

    public function receivedConnections()
    {
        return $this->belongsToMany(User::class,
            'connections',
            'friend_id',
            'user_id')
            ->withPivot(['status','id'])
            ->wherePivot('status', 'pending');
    }

    public function friends()
    {
        return $this->friendsOfMine->merge($this->friendOf);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        }
    }

    public function allSentConnection()
    {
        return $this->belongsToMany(User::class,
            'connections',
            'user_id',
            'friend_id');
    }

    public function allRecivedConnection()
    {
        return $this->belongsToMany(User::class,
            'connections',
            'friend_id',
            'user_id');
    }

    public function allConnections()
    {
        return $this->allSentConnection->merge($this->allRecivedConnection);
    }

}
