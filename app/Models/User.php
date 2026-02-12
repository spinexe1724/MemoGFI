<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import class SoftDeletes

class User extends Authenticatable
{
    use Notifiable, SoftDeletes; // 2. Tambahkan SoftDeletes di sini agar fungsi trashed() aktif

    protected $fillable = [
        'name',
        'email',
        'password',
        'division',
        'level',
        'branch',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi: User memiliki banyak Memo yang dibuat.
     */
    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class);
    }

    /**
     * Relasi: User memiliki banyak Memo yang disetujui (Tanda Tangan).
     */
    public function approvedMemos(): BelongsToMany
    {
        return $this->belongsToMany(Memo::class, 'memo_approvals')
                    ->withPivot('note', 'created_at')
                    ->withTimestamps();
    }
}