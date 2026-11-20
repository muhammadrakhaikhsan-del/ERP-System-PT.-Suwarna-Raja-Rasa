<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $table = 'stock_opname';
    protected $primaryKey = 'id_so';
    public $timestamps = false;
    protected $fillable = ['id_barang', 'stok_sistem', 'stok_fisik', 'selisih', 'tgl'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
