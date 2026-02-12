<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'approver_id', 'reference_no', 'recipient', 'sender', 
        'subject', 'body_text', 'valid_until', 'cc_list', 'is_draft', 
        'is_rejected', 'target_approvers' // Kolom baru
    ];

    protected $casts = [
        'cc_list' => 'array',
        'target_approvers' => 'array', // Casting array untuk ID terpilih
        'is_draft' => 'boolean',
        'is_rejected' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function approver() { return $this->belongsTo(User::class, 'approver_id'); }
    public function approvals() { return $this->belongsToMany(User::class, 'memo_approvals')->withPivot('note', 'created_at')->withTimestamps(); }

    /**
     * LOGIKA FINAL BARU: 
     * Memo selesai jika Pembuat + Manager Tahap 1 + SEMUA Target terpilih sudah tanda tangan.
     */
    public function getIsFinalAttribute()
    {
        if ($this->is_draft || $this->is_rejected) return false;

        $signedIds = $this->approvals()->pluck('users.id')->toArray();
        $targetIds = $this->target_approvers ?? [];
        
        // 1. Cek tanda tangan Manager Tahap 1 (approver_id)
        if ($this->approver_id && !in_array($this->approver_id, $signedIds)) return false;

        // 2. Cek semua target tambahan (GM/Direksi pilihan)
        foreach ($targetIds as $id) {
            if (!in_array($id, $signedIds)) return false;
        }

        return true;
    }
    public function attachments()
{
    return $this->hasMany(MemoAttachment::class);
}
}