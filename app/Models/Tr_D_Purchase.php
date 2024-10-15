<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tr_D_Purchase extends Model
{
    protected $table = 'tr_d_purchases';
    protected $guarded = [];
    use HasFactory;
    public function header()
    {
        return $this->hasMany(Tr_H_Purchase::class);
    }
}