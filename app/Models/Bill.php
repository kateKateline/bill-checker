<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeOcrCompleted(Builder $query): Builder
    {
        return $query->where('status', 'ocr_completed');
    }

    public function scopeAnalyzed(Builder $query): Builder
    {
        return $query->where('status', 'analyzed');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopeBySession(Builder $query, string $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    // ==========================================
    // STATUS CHECKERS
    // ==========================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOcrCompleted(): bool
    {
        return $this->status === 'ocr_completed';
    }

    public function isAnalyzed(): bool
    {
        return $this->status === 'analyzed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function hasRawText(): bool
    {
        return !empty($this->raw_text);
    }

    public function hasItems(): bool
    {
        return $this->items()->exists();
    }

    // ==========================================
    // STATUS UPDATERS
    // ==========================================

    public function markAsOcrCompleted(string $rawText): void
    {
        $this->update([
            'raw_text' => $rawText,
            'status' => 'ocr_completed',
            'ocr_completed_at' => now(),
        ]);
    }

    public function markAsAnalyzed(float $totalPrice, ?string $hospitalName = null): void
    {
        $this->update([
            'status' => 'analyzed',
            'total_price' => $totalPrice,
            'hospital_name' => $hospitalName ?? $this->hospital_name,
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
        ]);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price ?? 0, 0, ',', '.');
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

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

    public function getDangerItemsCountAttribute(): int
    {
        return $this->items()->where('status', 'danger')->count();
    }

    public function getReviewItemsCountAttribute(): int
    {
        return $this->items()->where('status', 'review')->count();
    }

    public function getSafeItemsCountAttribute(): int
    {
        return $this->items()->where('status', 'safe')->count();
    }
}