<?php

namespace Database\Seeders;

use App\Models\NoticeType;
use Illuminate\Database\Seeder;

class NoticeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $noticeTypes = [
            [
                'name' => '10-Day Notice of Termination for Nonpayment of Rent',
                'price' => 50.00,
            ],
            [
                'name' => '13-Day Termination for Nonpayment of Rent',
                'price' => 50.00,
            ],
        ];

        foreach ($noticeTypes as $noticeType) {
            NoticeType::create($noticeType);
        }
    }
}