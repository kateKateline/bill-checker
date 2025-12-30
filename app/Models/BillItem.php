<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'item_name',
        'category',
        'price',
        'status',
        'label',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function scopeSafe(Builder $query): Builder
    {
        return $query->where('status', 'safe');
    }

    public function scopeReview(Builder $query): Builder
    {
        return $query->where('status', 'review');
    }

    public function scopeDanger(Builder $query): Builder
    {
        return $query->where('status', 'danger');
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function isSafe(): bool
    {
        return $this->status === 'safe';
    }

    public function needsReview(): bool
    {
        return $this->status === 'review';
    }

    public function isDangerous(): bool
    {
        return $this->status === 'danger';
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'safe' => 'emerald',
            'review' => 'amber',
            'danger' => 'rose',
            default => 'slate'
        };
    }
}