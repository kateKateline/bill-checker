<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Bill Model
 * 
 * Represents a hospital bill with OCR text and AI analysis results.
 */
class Bill extends Model
{
    protected $fillable = [
        'uuid',
        'file_path',
        'raw_text',
        'ocr_engine',
        'ocr_completed_at',
        'hospital_name',
        'total_price',
        'status',
        'session_id',
    ];

    protected $casts = [
        'ocr_completed_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Boot the model
     * 
     * Automatically generates UUID when creating a new bill.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model
     * 
     * Uses UUID instead of ID for routing.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get all bill items
     * 
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope for pending bills
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for OCR completed bills
     */
    public function scopeOcrCompleted(Builder $query): Builder
    {
        return $query->where('status', 'ocr_completed');
    }

    /**
     * Scope for analyzed bills
     */
    public function scopeAnalyzed(Builder $query): Builder
    {
        return $query->where('status', 'analyzed');
    }

    /**
     * Scope for failed bills
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for bills by session ID
     */
    public function scopeBySession(Builder $query, string $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    // ==========================================
    // STATUS CHECKERS
    // ==========================================

    /**
     * Check if bill is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if OCR is completed
     */
    public function isOcrCompleted(): bool
    {
        return $this->status === 'ocr_completed';
    }

    /**
     * Check if bill is analyzed
     */
    public function isAnalyzed(): bool
    {
        return $this->status === 'analyzed';
    }

    /**
     * Check if bill processing failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if bill has raw text
     */
    public function hasRawText(): bool
    {
        return !empty($this->raw_text);
    }

    /**
     * Check if bill has items
     */
    public function hasItems(): bool
    {
        return $this->items()->exists();
    }

    // ==========================================
    // STATUS UPDATERS
    // ==========================================

    /**
     * Mark bill as OCR completed
     * 
     * @param string $rawText
     * @return void
     */
    public function markAsOcrCompleted(string $rawText): void
    {
        $this->update([
            'raw_text' => $rawText,
            'status' => 'ocr_completed',
            'ocr_completed_at' => now(),
        ]);
    }

    /**
     * Mark bill as analyzed
     * 
     * @param float $totalPrice
     * @param string|null $hospitalName
     * @return void
     */
    public function markAsAnalyzed(float $totalPrice, ?string $hospitalName = null): void
    {
        $this->update([
            'status' => 'analyzed',
            'total_price' => $totalPrice,
            'hospital_name' => $hospitalName ?? $this->hospital_name,
        ]);
    }

    /**
     * Mark bill as failed
     * 
     * @return void
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
        ]);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    /**
     * Get formatted total price
     * 
     * @return string
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price ?? 0, 0, ',', '.');
    }

    /**
     * Get file URL
     * 
     * @return string
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get status badge information
     * 
     * @return array
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['label' => 'Pending', 'color' => 'slate'],
            'ocr_completed' => ['label' => 'OCR Done', 'color' => 'blue'],
            'analyzed' => ['label' => 'Analyzed', 'color' => 'green'],
            'failed' => ['label' => 'Failed', 'color' => 'red'],
            default => ['label' => 'Unknown', 'color' => 'gray']
        };
    }

    /**
     * Get count of danger items
     * 
     * @return int
     */
    public function getDangerItemsCountAttribute(): int
    {
        return $this->items()->where('status', 'danger')->count();
    }

    /**
     * Get count of review items
     * 
     * @return int
     */
    public function getReviewItemsCountAttribute(): int
    {
        return $this->items()->where('status', 'review')->count();
    }

    /**
     * Get count of safe items
     * 
     * @return int
     */
    public function getSafeItemsCountAttribute(): int
    {
        return $this->items()->where('status', 'safe')->count();
    }
}
