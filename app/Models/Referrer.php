<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Referrer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'slug',
        'plan_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'plan_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the referrals for the referrer.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Get the full name of the referrer.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the referral URL for this referrer.
     */
    public function getReferralUrlAttribute(): string
    {
        return url("/r/{$this->slug}");
    }

    /**
     * Generate a unique slug for the referrer.
     */
    public static function generateUniqueSlug(string $firstName, string $lastName): string
    {
        $baseSlug = Str::slug($firstName.'-'.$lastName);
        $slug = $baseSlug;
        $count = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get the price for this referrer's plan date.
     */
    public function getReferrerPriceAttribute(): float
    {
        // Get the 10-day notice type for this referrer's plan date (they're all the same price)
        $noticeType = \App\Models\NoticeType::where('plan_date', '=', $this->plan_date)
            ->where('name', 'like', '%10%')
            ->first();

        return $noticeType ? (float) $noticeType->price : 15.00;
    }

    /**
     * Get the discount amount based on the difference from standard price.
     */
    public function getDiscountAmountAttribute(): float
    {
        $standardPrice = 15.00; // Standard price from PricingService

        return max(0, $standardPrice - $this->referrer_price);
    }

    /**
     * Get the discounted price for referrals (same as referrer's price).
     */
    public function getDiscountedPriceAttribute(): float
    {
        return $this->referrer_price;
    }

    /**
     * Scope a query to only include active referrers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
