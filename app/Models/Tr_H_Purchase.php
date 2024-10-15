<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tr_H_Purchase extends Model
{
    protected $table = 'tr_h_purchases';
    protected $guarded = [];
    use HasFactory;
    public function details()
    {
        return $this->hasMany(Tr_D_Purchase::class, 'tr_h_purchase');
    }
}