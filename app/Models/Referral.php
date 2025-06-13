<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'referrer_id',
        'account_id',
        'discount_amount',
        'discount_percentage',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    /**
     * Get the referrer that owns the referral.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Referrer::class);
    }

    /**
     * Get the account that owns the referral.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the actual discount amount applied.
     */
    public function getAppliedDiscountAttribute(): float
    {
        return $this->discount_amount ?? $this->referrer->discount_amount;
    }

    /**
     * Create a new referral from a referrer and account.
     */
    public static function createFromReferrer(Referrer $referrer, Account $account): self
    {
        return self::create([
            'referrer_id' => $referrer->id,
            'account_id' => $account->id,
            'discount_amount' => $referrer->discount_amount,
            'discount_percentage' => round(($referrer->discount_amount / 15.00) * 100, 2),
        ]);
    }
}
