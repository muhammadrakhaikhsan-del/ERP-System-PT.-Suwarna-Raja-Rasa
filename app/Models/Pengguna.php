<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $table = 'pengguna';
    // By default Laravel assumes $primaryKey is 'id'. Table has 'id'.
    protected $fillable = ['username', 'password', 'role', 'nama'];
}
