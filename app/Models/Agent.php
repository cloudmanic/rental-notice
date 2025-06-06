<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'name',
        'first_name',
        'last_name',
        'address_1',
        'address_2',
        'city',
        'state',
        'zip',
        'phone',
        'email',
    ];

    /**
     * Get the account that owns the agent.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the notices associated with this agent.
     */
    public function notices()
    {
        return $this->hasMany(Notice::class);
    }
}
