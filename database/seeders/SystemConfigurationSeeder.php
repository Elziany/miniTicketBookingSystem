<?php

namespace Database\Seeders;

use App\Models\SystemConfiguration;
use Illuminate\Database\Seeder;

class SystemConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        SystemConfiguration::query()->firstOrCreate(
            ['hold_duration_minutes' => '15'],
            ['value' => ['hold_duration_minutes' => 15]]
        );
    }
}
