<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganHarian extends Model
{
    protected $table = 'keuangan_harian';
    protected $primaryKey = 'id_kh';
    public $timestamps = false;
    protected $fillable = ['id_tenant', 'tgl', 'total_penjualan'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }
}
