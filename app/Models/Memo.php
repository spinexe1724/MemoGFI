<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'approver_id', 'reference_no', 'recipient', 'sender', 
        'subject', 'body_text', 'valid_until', 'cc_list', 'is_draft', 'is_rejected'
    ];

    protected $casts = [
        'cc_list' => 'array',
        'is_draft' => 'boolean',
        'is_rejected' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }

    // Relasi ke Manager yang ditunjuk untuk menyetujui
    public function approver() { return $this->belongsTo(User::class, 'approver_id'); }

    public function approvals() {
        return $this->belongsToMany(User::class, 'memo_approvals')
                    ->withPivot('note', 'created_at')
                    ->withTimestamps();
    }

    public function getIsFinalAttribute()
    {
        if ($this->is_draft || $this->is_rejected) return false;

        $count = $this->approvals()->count();
        $role = strtolower($this->user->role);

        // Aturan Baru:
        // Supervisor: 5 sign (Pembuat, Manager Pilihan, GM, Direksi 1, Direksi 2)
        if ($role === 'supervisor') {
            return $count >= 5;
        } 
        // Manager: 4 sign (Pembuat, GM, Direksi 1, Direksi 2)
        elseif ($role === 'manager') {
            return $count >= 4;
        } 
        // GM/Direksi: 2 sign
        elseif (in_array($role, ['gm', 'direksi'])) {
            return $count >= 2;
        }

        return $count >= 6;
    }
}