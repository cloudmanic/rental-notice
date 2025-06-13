<?php

namespace Database\Seeders;

use App\Models\NoticeType;
use App\Models\Referrer;
use Illuminate\Database\Seeder;

class ReferrerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Spicer Matthews referrer
        $referrer = Referrer::create([
            'first_name' => 'Spicer',
            'last_name' => 'Matthews',
            'email' => 'spicer@cloudmanic.com',
            'slug' => Referrer::generateUniqueSlug('Spicer', 'Matthews'),
            'plan_date' => NoticeType::getMostRecentPlanDate() ?? now()->format('Y-m-d'),
            'is_active' => true,
        ]);

        $this->command->info("Created referrer: {$referrer->full_name}");
        $this->command->info("Referral URL: {$referrer->referral_url}");
    }
}
