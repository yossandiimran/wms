<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'table_order_detail';

    protected $guarded = [];

    public function order()
    {
        return $this->hasOne(Order::class, 'nomor_po', 'nomor_po');
    }

    public function barang()
    {
        return $this->hasOne(MasterBarang::class, 'kode_barang', 'kode_barang');
    }
}
