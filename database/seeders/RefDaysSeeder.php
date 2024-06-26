<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('ref_days')->insert([
            ['day_name' => 'Sunday', 'created_at' => $now, 'updated_at' => $now],
            ['day_name' => 'Monday', 'created_at' => $now, 'updated_at' => $now],
            ['day_name' => 'Tuesday', 'created_at' => $now, 'updated_at' => $now],
            ['day_name' => 'Wednesday', 'created_at' => $now, 'updated_at' => $now],
            ['day_name' => 'Thursday', 'created_at' => $now, 'updated_at' => $now],
            ['day_name' => 'Friday', 'created_at' => $now, 'updated_at' => $now],
            ['day_name' => 'Saturday', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
