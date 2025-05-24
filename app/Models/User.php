<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rules\Password;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use CanResetPassword, HasFactory, Notifiable;

    // User types
    public const TYPE_SUPER_ADMIN = 'Super Admin';

    public const TYPE_ADMIN = 'Admin';

    public const TYPE_CONTRIBUTOR = 'Contributor';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'account_id',
        'is_owner',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_owner' => 'boolean',
        ];
    }

    /*
    * Get the account that the user belongs to.
    * For now we just return the first account as there will only be one account per user.
    */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id')
            ->withDefault(function () {
                return $this->accounts()->first();
            });
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_to_user')
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    public function ownedAccounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_to_user')
            ->withPivot('is_owner')
            ->withTimestamps()
            ->wherePivot('is_owner', true);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user is a Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->type === self::TYPE_SUPER_ADMIN;
    }

    /**
     * Check if user is an Admin
     */
    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * Check if user is a Contributor
     */
    public function isContributor(): bool
    {
        return $this->type === self::TYPE_CONTRIBUTOR;
    }

    /**
     * Check if user has at least admin privileges
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->type, [self::TYPE_SUPER_ADMIN, self::TYPE_ADMIN]);
    }

    public static function validationRules(bool $isUpdate = false): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                $isUpdate ? 'unique:users,email,'.request()->user()->id : 'unique:users',
            ],
            'password' => $isUpdate ? ['nullable'] : ['required', Password::defaults()],
        ];
    }

    public static function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered',
        ];
    }
}
