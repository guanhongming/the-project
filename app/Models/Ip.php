<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ip extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'ip_address'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'name', 'name');
    }
}
