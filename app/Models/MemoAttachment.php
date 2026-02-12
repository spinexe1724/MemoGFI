<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoAttachment extends Model
{
    protected $fillable = ['memo_id', 'file_path', 'file_name', 'file_type'];

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }
}