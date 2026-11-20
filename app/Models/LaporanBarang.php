<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBarang extends Model
{
    protected $table = 'laporan_barang';
    protected $primaryKey = 'id_laporan_barang';
    public $timestamps = false;
    protected $fillable = ['id_barang', 'periode', 'total_barang_masuk', 'total_barang_keluar', 'hasil_stock_opname'];
}
