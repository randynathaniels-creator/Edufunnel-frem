<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/data_master.json');
        if (file_exists($path)) {
            $jsonString = file_get_contents($path);
            
            DB::table('dashboard_data')->updateOrInsert(
                ['key' => 'master_data'],
                ['value' => $jsonString, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
