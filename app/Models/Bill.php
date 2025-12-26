<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bill extends Model
{
    protected $fillable = [
        'uuid',
        'file_path',
        'hospital_name',
        'total_price',
        'status',
        'session_id',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }
}