<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notice extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_SERVICE_PENDING = 'service_pending';
    const STATUS_SERVED = 'served';
    const STATUS_ERROR = 'error';

    protected $fillable = [
        'account_id',
        'user_id',
        'notice_type_id',
        'agent_id',
        'price',
        'past_due_rent',
        'late_charges',
        'other_1_title',
        'other_1_price',
        'other_2_title',
        'other_2_price',
        'other_3_title',
        'other_3_price',
        'other_4_title',
        'other_4_price',
        'other_5_title',
        'other_5_price',
        'payment_other_means',
        'include_all_other_occupents',
        'status',
        'error_message',
        'draft_pdf',
        'final_pdf',
        'certificate_pdf',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'past_due_rent' => 'decimal:2',
        'late_charges' => 'decimal:2',
        'other_1_price' => 'decimal:2',
        'other_2_price' => 'decimal:2',
        'other_3_price' => 'decimal:2',
        'other_4_price' => 'decimal:2',
        'other_5_price' => 'decimal:2',
        'payment_other_means' => 'boolean',
        'include_all_other_occupents' => 'boolean',
        'status' => 'string',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function noticeType(): BelongsTo
    {
        return $this->belongsTo(NoticeType::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'notice_tenant')
            ->withTimestamps();
    }

    public function getAgentFullAddressAttribute(): string
    {
        $address = $this->agent_address_1;
        if ($this->agent_address_2) {
            $address .= ", {$this->agent_address_2}";
        }
        return "{$address}, {$this->agent_city}, {$this->agent_state} {$this->agent_zip}";
    }

    public function getTotalChargesAttribute(): float
    {
        $total = $this->past_due_rent + $this->late_charges;

        for ($i = 1; $i <= 5; $i++) {
            $priceField = "other_{$i}_price";
            $total += $this->$priceField ?? 0;
        }

        return $total;
    }

    public static function validationRules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'user_id' => ['required', 'exists:users,id'],
            'agent_id' => ['required', 'exists:agents,id'],
            'tenants' => ['required', 'array', 'min:1'],
            'tenants.*' => ['exists:tenants,id'],
            'notice_type_id' => ['required', 'exists:notice_types,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'past_due_rent' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'late_charges' => ['required', 'numeric', 'min:0', 'max:99999.99'],

            // Other charges validation
            'other_1_title' => ['nullable', 'string', 'max:255'],
            'other_1_price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'other_2_title' => ['nullable', 'string', 'max:255'],
            'other_2_price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'other_3_title' => ['nullable', 'string', 'max:255'],
            'other_3_price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'other_4_title' => ['nullable', 'string', 'max:255'],
            'other_4_price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'other_5_title' => ['nullable', 'string', 'max:255'],
            'other_5_price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],

            // Flags validation
            'payment_other_means' => ['boolean'],
            'include_all_other_occupents' => ['boolean'],

            // PDF files validation
            'draft_pdf' => ['nullable', 'string'],
            'final_pdf' => ['nullable', 'string'],
            'certificate_pdf' => ['nullable', 'string'],

            'status' => ['required', 'string', 'in:' . implode(',', self::statuses())],
            'error_message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'agent_zip.regex' => 'The agent zip code must be in the format 12345 or 12345-6789',
            'agent_state.size' => 'The agent state must be a 2-letter code',
            'price.min' => 'The price cannot be negative',
            'price.max' => 'The price cannot exceed $99,999.99',
            'past_due_rent.min' => 'The past due rent cannot be negative',
            'past_due_rent.max' => 'The past due rent cannot exceed $99,999.99',
            'late_charges.min' => 'The late charges cannot be negative',
            'late_charges.max' => 'The late charges cannot exceed $99,999.99',
            'status.in' => 'The selected status is invalid.',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_SERVICE_PENDING,
            self::STATUS_SERVED,
            self::STATUS_ERROR,
        ];
    }
}
