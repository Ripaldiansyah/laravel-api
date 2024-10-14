<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tr_H_Sale extends Model
{
    use HasFactory;

    protected $table = 'tr_h_sales';
    protected $guarded = [];
    public function details()
    {
        return $this->hasMany(Tr_D_Sale::class, 'tr_h_sales');
    }
}