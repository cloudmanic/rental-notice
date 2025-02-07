<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeType extends Model
{
    protected $fillable = [
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public static function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:notice_types'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.unique' => 'This notice type already exists',
            'price.min' => 'The price cannot be negative',
            'price.max' => 'The price cannot exceed $99,999.99',
        ];
    }
}
