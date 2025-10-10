<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class ActionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

                // Menonaktfikan pengecekan Foreign Key
        Schema::disableForeignKeyConstraints();


        DB::table('action_items')->insert([
            [
                'action_id' => 6,
                'mom_id' => 213129,
                'item' => 'Menyusun laporan akhir progres proyek jaringan wilayah barat',
                'due' => '2025-10-31',
                'status' => 'mendatang',
                'created_at' => Carbon::parse('2025-10-07 06:45:04'),
                'updated_at' => Carbon::parse('2025-10-07 06:45:04'),
            ],
            [
                'action_id' => 7,
                'mom_id' => 213129,
                'item' => 'Melakukan integrasi API CRM baru dengan sistem backend',
                'due' => '2025-10-25',
                'status' => 'mendatang',
                'created_at' => Carbon::parse('2025-10-07 06:45:04'),
                'updated_at' => Carbon::parse('2025-10-07 06:45:04'),
            ],
            [
                'action_id' => 3,
                'mom_id' => 213129,
                'item' => 'Menyelesaikan testing modul notifikasi dan laporan otomatis',
                'due' => '2025-10-20',
                'status' => 'selesai',
                'created_at' => Carbon::parse('2025-10-07 06:45:04'),
                'updated_at' => Carbon::parse('2025-10-07 06:45:04'),
            ],
            [
                'action_id' => 4,
                'mom_id' => 213129,
                'item' => 'Review performa vendor dan update kontrak baru',
                'due' => '2025-10-29',
                'status' => 'mendatang',
                'created_at' => Carbon::parse('2025-10-07 06:45:04'),
                'updated_at' => Carbon::parse('2025-10-07 06:45:04'),
            ],
            [
                'action_id' => 5,
                'mom_id' => 213129,
                'item' => 'Pembuatan dokumentasi teknis hasil implementasi sistem IoT',
                'due' => '2025-10-31',
                'status' => 'selesai',
                'created_at' => Carbon::parse('2025-10-07 06:45:04'),
                'updated_at' => Carbon::parse('2025-10-07 06:45:04'),
            ],
        ]);
    }
}
