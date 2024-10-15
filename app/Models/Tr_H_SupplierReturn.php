<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tr_H_SupplierReturn extends Model
{
    protected $table = 'tr_h_supplier_returns';
    protected $guarded = [];
    use HasFactory;
    public function details()
    {
        return $this->hasMany(Tr_D_SupplierReturn::class, 'tr_h_supplier_returns');
    }

    public function purchase()
    {
        return $this->hasOne(Tr_H_Purchase::class, 'id');
    }
}
