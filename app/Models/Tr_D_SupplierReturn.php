<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tr_D_SupplierReturn extends Model
{
    use HasFactory;
    protected $table = 'tr_d_supplier_returns';
    protected $guarded = [];
    public function header()
    {
        return $this->hasMany(Tr_H_SupplierReturn::class);
    }
}
