<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rules\Password;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address_1',
        'address_2',
        'city',
        'state',
        'zip',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function notices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Notice::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullAddressAttribute(): string
    {
        $address = $this->address_1;
        if ($this->address_2) {
            $address .= ", {$this->address_2}";
        }
        return "{$address}, {$this->city}, {$this->state} {$this->zip}";
    }

    public static function validationRules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_1' => ['required', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
            'zip' => ['required', 'string', 'max:10', 'regex:/^\d{5}(-\d{4})?$/'],
        ];
    }

    public static function messages(): array
    {
        return [
            'zip.regex' => 'The zip code must be in the format 12345 or 12345-6789',
            'state.size' => 'The state must be a 2-letter code',
        ];
    }
}
