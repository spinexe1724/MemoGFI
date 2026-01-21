<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'approver_id', 
        'reference_no', 
        'recipient', 
        'sender', 
        'subject', 
        'body_text', 
        'valid_until', 
        'cc_list', 
        'is_draft', 
        'is_rejected'
    ];

    /**
     * Konversi otomatis data JSON dari database menjadi Array PHP.
     */
    protected $casts = [
        'cc_list' => 'array',
        'is_draft' => 'boolean',
        'is_rejected' => 'boolean',
    ];

    /**
     * RELASI UTAMA: Menghubungkan memo dengan pembuatnya (User).
     * Ini adalah bagian yang menyebabkan error jika tidak ada.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * RELASI APPROVER: Menghubungkan dengan Manager yang ditunjuk untuk menyetujui.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * RELASI APPROVALS: Riwayat tanda tangan digital (Pivot Table).
     */
    public function approvals()
    {
        return $this->belongsToMany(User::class, 'memo_approvals')
                    ->withPivot('note', 'created_at')
                    ->withTimestamps();
    }

    /**
     * LOGIKA THRESHOLD APPROVAL (HO vs Non-HO)
     * Menggunakan Accessor: $memo->is_final
     */
    public function getIsFinalAttribute()
    {
        if ($this->is_draft || $this->is_rejected) return false;

        $count = $this->approvals()->count();
        $creator = $this->user;
        
        if (!$creator) return false;

        $role = strtolower($creator->role);
        $isHO = strtoupper($creator->branch ?? '') === 'HO';

        // Alur CABANG NON-HO (Admin/Supervisor)
        // Jalur: Creator -> BM -> GA -> Dir 1 -> Dir 2 (Total 5)
        if (!$isHO && in_array($role, ['admin', 'supervisor'])) {
            return $count >= 5;
        }

        // Alur CABANG HO atau Role Lainnya
        if (in_array($role, ['supervisor', 'admin'])) return $count >= 5;
        if ($role === 'manager') return $count >= 4;
        if (in_array($role, ['gm', 'direksi'])) return $count >= 2;

        return $count >= 5;
    }
}