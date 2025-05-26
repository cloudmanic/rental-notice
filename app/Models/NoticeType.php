<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'plan_date',
        'template',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'plan_date' => 'date',
    ];

    public static function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:notice_types'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'plan_date' => ['required', 'date'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.unique' => 'This notice type already exists',
            'price.min' => 'The price cannot be negative',
            'price.max' => 'The price cannot exceed $99,999.99',
            'plan_date.required' => 'The plan date is required',
        ];
    }

    /**
     * Get the most recent plan date from all notice types
     *
     * @return string|null The most recent plan date
     */
    public static function getMostRecentPlanDate()
    {
        return self::orderBy('plan_date', 'desc')->value('plan_date');
    }
}
