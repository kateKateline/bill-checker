<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'file_path',
        'hospital_name',
        'total_price',
        'status',
        'session_id',
    ];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }
}
