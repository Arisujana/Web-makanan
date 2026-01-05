<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makanan extends Model
{
    protected $fillable = [
        'nama',
        'asal',
        'harga',
        'rating',
        'foto',
        'resep'
    ];
}
