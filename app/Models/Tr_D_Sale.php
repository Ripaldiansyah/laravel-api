<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tr_D_Sale extends Model
{
    use HasFactory;

    protected $table = 'tr_d_sales';
    protected $guarded = [];

    public function header()
    {
        return $this->hasMany(Tr_H_Sale::class);
    }
}