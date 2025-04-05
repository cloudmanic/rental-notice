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
                'price' => 50.00,
                'plan_date' => Carbon::parse('2025-01-01'),
            ],
            [
                'name' => '13-Day Termination for Nonpayment of Rent',
                'price' => 50.00,
                'plan_date' => Carbon::parse('2025-01-01'),
            ],
        ];

        foreach ($noticeTypes as $noticeType) {
            NoticeType::create($noticeType);
        }
    }
}
