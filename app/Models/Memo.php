<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Memo extends Model
{
    protected $fillable = [
    'user_id', 'reference_no', 'recipient', 'sender', 
    'cc_list', 'subject', 'body_text', 'valid_until', 'is_fully_approved', 'is_rejected'
];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvals(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memo_approvals')
                                                ->withTimestamps()
                 
                                                ->withPivot('note');
    }
    protected $casts = [
    'cc_list' => 'array',
];
}