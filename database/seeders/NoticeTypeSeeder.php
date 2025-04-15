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
                'stripe_price_id' => 'price_0RBsSeRis6D3eYxt0w6lokfi',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 15.00,
                'plan_date' => Carbon::parse('2025-01-01'),
                'stripe_price_id' => 'price_0RDzU4Ris6D3eYxtHr3xsIEh',
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 20.00,
                'plan_date' => Carbon::parse('2025-02-15'),
                'stripe_price_id' => 'price_0RDzWlRis6D3eYxt0wXt7Iqc',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 20.00,
                'plan_date' => Carbon::parse('2025-02-15'),
                'stripe_price_id' => 'price_0RDzVORis6D3eYxtPUhryGAQ',
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 25.00,
                'plan_date' => Carbon::parse('2025-04-01'),
                'stripe_price_id' => 'price_0RDzX2Ris6D3eYxtAq6dOrWI',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 25.00,
                'plan_date' => Carbon::parse('2025-04-01'),
                'stripe_price_id' => 'price_0RDzW4Ris6D3eYxtWd1530bA',
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 0.00,
                'plan_date' => Carbon::parse('2024-01-01'),
                'stripe_price_id' => 'price_0RDzXaRis6D3eYxtSIQtOeg4',
            ],
            [
                'name' => '13-Day Notice of Termination for Nonpayment of Rent',
                'price' => 0.00,
                'plan_date' => Carbon::parse('2024-01-01'),
                'stripe_price_id' => 'price_0RDzXmRis6D3eYxtchUscdfR',
            ],
        ];

        foreach ($noticeTypes as $noticeType) {
            NoticeType::create($noticeType);
        }
    }
}
