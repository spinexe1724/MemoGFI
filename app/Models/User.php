<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'division',
            'level',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class);
    }

    public function approvedMemos(): BelongsToMany
    {
        return $this->belongsToMany(Memo::class, 'memo_approvals');
    }
}