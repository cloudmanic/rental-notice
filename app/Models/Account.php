<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notice_type_plan_date',
    ];

    protected $casts = [
        'notice_type_plan_date' => 'date',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'account_to_user')
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'account_to_user')
            ->withPivot('is_owner')
            ->withTimestamps()
            ->wherePivot('is_owner', true);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the referral for this account.
     */
    public function referral(): HasOne
    {
        return $this->hasOne(Referral::class);
    }

    public static function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'notice_type_plan_date' => ['nullable', 'date'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'The company name is required',
        ];
    }
}
