<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tenant';
    protected $primaryKey = 'id_tenant';
    public $timestamps = false;
    protected $fillable = ['id_outlet', 'nama_tenant'];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_tenant', 'id_tenant');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }
}
