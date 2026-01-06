<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_no',
        'recipient',
        'sender',
        'cc_list',
        'subject',
        'body_text',
        'gm_name',
    ];
}