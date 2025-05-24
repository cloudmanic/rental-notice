<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColdOutReachList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'street_1',
        'street_2',
        'city',
        'state',
        'zip',
        'expiration',
        'license_number',
        'status',
        'company_name',
        'phone',
        'email',
        'domain',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expiration' => 'date',
    ];

    /**
     * Get the full name of the contact
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the full address of the contact
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->street_1;

        if (! empty($this->street_2)) {
            $address .= ", {$this->street_2}";
        }

        $address .= ", {$this->city}, {$this->state} {$this->zip}";

        return $address;
    }
}
