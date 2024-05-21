<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'table_order';

    protected $guarded = [];

    public function customer()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'nomor_po', 'nomor_po');
    }
}
