<?php

namespace Database\Seeders;

use App\Models\NoticeType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NoticeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $noticeTypes = [
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 15.00,
                'plan_date' => Carbon::parse('2025-01-01'),
                'stripe_price_id' => 'price_1REESmQFuE0lkXsTfkP7eaDu',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 15.00,
                'plan_date' => Carbon::parse('2025-01-01'),
                'stripe_price_id' => 'price_1REEZIQFuE0lkXsTZoOVfsAY',
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 20.00,
                'plan_date' => Carbon::parse('2025-02-15'),
                'stripe_price_id' => 'price_1REET2QFuE0lkXsTgr51vGKA',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 20.00,
                'plan_date' => Carbon::parse('2025-02-15'),
                'stripe_price_id' => 'price_1REEZeQFuE0lkXsTB7uHBcfa',
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 25.00,
                'plan_date' => Carbon::parse('2025-04-01'),
                'stripe_price_id' => 'price_1REETXQFuE0lkXsTKT7ikl8v',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 25.00,
                'plan_date' => Carbon::parse('2025-04-01'),
                'stripe_price_id' => 'price_1REEaAQFuE0lkXsTN2P0NNHA',
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 0.00,
                'plan_date' => Carbon::parse('2024-01-02'),
                'stripe_price_id' => 'price_1REERqQFuE0lkXsTYpDUVzXJ',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 0.00,
                'plan_date' => Carbon::parse('2024-01-02'),
                'stripe_price_id' => 'price_1REEY9QFuE0lkXsTjwVdz07V',
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 10.00,
                'plan_date' => Carbon::parse('2024-01-03'),
                'stripe_price_id' => 'price_1REESRQFuE0lkXsT6J9nQ2Xd',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 10.00,
                'plan_date' => Carbon::parse('2024-01-03'),
                'stripe_price_id' => 'price_1REEYwQFuE0lkXsTgpO9T3mt',
            ],
        ];

        foreach ($noticeTypes as $noticeType) {
            NoticeType::create($noticeType);
        }
    }
}
