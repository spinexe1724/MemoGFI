<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Memo extends Model
{
    protected $fillable = [
        'user_id', 
        'reference_no', 
        'recipient', 
        'sender', 
        'cc_list', 
        'subject', 
        'body_text', 
        'is_fully_approved'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvals(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memo_approvals')->withTimestamps();
    }
}