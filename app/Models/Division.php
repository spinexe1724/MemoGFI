<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['name','initial'];

    // Relasi ke User
    public function users()
    {
        return $this->hasMany(User::class, 'division', 'name');
    }
}