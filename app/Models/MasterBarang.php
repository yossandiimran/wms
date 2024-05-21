<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBarang extends Model
{
    protected $table = 'master_barang';

    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(MasterSupplier::class, 'id_supplier');
    }

    public function gudang()
    {
        return $this->belongsTo(MasterGudang::class, 'id_gudang');
    }
    
}
