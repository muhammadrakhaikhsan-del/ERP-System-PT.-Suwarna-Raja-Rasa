<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanRekapitulasi extends Model
{
    protected $table = 'laporan_rekapitulasi';
    protected $primaryKey = 'id_laporan_rekapitulasi';
    public $timestamps = false;
    protected $fillable = ['id_laporan_barang', 'id_laporan_keuangan', 'periode'];
}
