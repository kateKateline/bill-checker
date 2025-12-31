<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Bill Item Model
 * 
 * Represents an individual item from a bill analysis.
 */
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

    /**
     * Get the bill that owns this item
     * 
     * @return BelongsTo
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Scope for safe items
     */
    public function scopeSafe(Builder $query): Builder
    {
        return $query->where('status', 'safe');
    }

    /**
     * Scope for review items
     */
    public function scopeReview(Builder $query): Builder
    {
        return $query->where('status', 'review');
    }

    /**
     * Scope for danger items
     */
    public function scopeDanger(Builder $query): Builder
    {
        return $query->where('status', 'danger');
    }

    /**
     * Scope for items by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Check if item is safe
     */
    public function isSafe(): bool
    {
        return $this->status === 'safe';
    }

    /**
     * Check if item needs review
     */
    public function needsReview(): bool
    {
        return $this->status === 'review';
    }

    /**
     * Check if item is dangerous
     */
    public function isDangerous(): bool
    {
        return $this->status === 'danger';
    }

    /**
     * Get formatted price
     * 
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get status badge color
     * 
     * @return string
     */
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
