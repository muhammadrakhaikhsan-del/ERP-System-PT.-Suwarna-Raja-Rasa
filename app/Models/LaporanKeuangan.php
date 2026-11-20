<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKeuangan extends Model
{
    protected $table = 'laporan_keuangan';
    protected $primaryKey = 'id_laporan_keuangan';
    public $timestamps = false;
    protected $fillable = ['id_kh', 'periode', 'total_penjualan'];
}
