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
            ],
            [
                'name' => '13-Day Termination for Nonpayment of Rent',
                'price' => 15.00,
                'plan_date' => Carbon::parse('2025-01-01'),
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 20.00,
                'plan_date' => Carbon::parse('2025-02-15'),
            ],
            [
                'name' => '13-Day Termination for Nonpayment of Rent',
                'price' => 20.00,
                'plan_date' => Carbon::parse('2025-02-15'),
            ],
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 25.00,
                'plan_date' => Carbon::parse('2025-04-01'),
            ],
            [
                'name' => '13-Day Termination for Nonpayment of Rent',
                'price' => 25.00,
                'plan_date' => Carbon::parse('2025-04-01'),
            ],
        ];

        foreach ($noticeTypes as $noticeType) {
            NoticeType::create($noticeType);
        }
    }
}
