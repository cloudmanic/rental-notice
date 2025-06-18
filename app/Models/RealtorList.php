<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealtorList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'realtor_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'csv_id',
        'email',
        'full_name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'office_name',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'county',
        'phone',
        'fax',
        'mobile',
        'license_type',
        'license_number',
        'original_issue_date',
        'expiration_date',
        'association',
        'agency',
        'listings',
        'listings_volume',
        'sold',
        'sold_volume',
        'email_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'csv_id' => 'integer',
        'original_issue_date' => 'date',
        'expiration_date' => 'date',
        'listings' => 'integer',
        'listings_volume' => 'decimal:2',
        'sold' => 'integer',
        'sold_volume' => 'decimal:2',
    ];
}
