<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MomStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menonaktfikan pengecekan Foreign Key
        Schema::disableForeignKeyConstraints();
        
        // Truncate tabel
        DB::table('mom_status')->truncate(); 

        DB::table('mom_status')->insert([
            ['status' => 'Menunggu', 'created_at' => now(), 'updated_at' => now()],
            ['status' => 'Disetujui', 'created_at' => now(), 'updated_at' => now()],
            ['status' => 'Ditolak', 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        // Mengaktifkan kembali pengecekan Foreign Key
        Schema::enableForeignKeyConstraints();
    }
}
