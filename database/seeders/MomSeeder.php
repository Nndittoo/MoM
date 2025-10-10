<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class MomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menonaktfikan pengecekan Foreign Key
        Schema::disableForeignKeyConstraints();

        // Bersihkan tabel sebelum memasukkan data baru
        DB::table('moms')->truncate();

        // Data dummy untuk kolom 'manual_attendees' dan 'partner_attendees'
        $dummyManualAttendees = json_encode(['Denny', 'Hanif', 'Hardana']);
        $dummyPartnerAttendees = json_encode([
            ['name' => 'PT TIF', 'attendees' => ['Asido', 'Bambang', 'Aduhai']],
            ['name' => 'PT ZTE INDONESIA', 'attendees' => ['Lukman', 'Zulkairu']],
        ]);

        DB::table('moms')->insert([
            [
                'version_id' => 213129,
                'title' => 'Rapat Koordinasi Mingguan',
                'meeting_date' => '2025-10-07',
                'location' => 'Ruang Meeting 3A',
                'start_time' => '09:00',
                'end_time' => '10:30',
                
                'pimpinan_rapat' => 'Pak Denny', 
                'notulen' => 'Pak Hanif',
                
                'creator_id' => 2,
                'pembahasan' => '<p>Evaluasi progres proyek jaringan fiber optik minggu ini.</p><p>Diskusi kendala di lapangan.</p>',
                'status_id' => 1,

                // TAMBAH: Kolom JSON baru (disimpan sebagai string JSON)
                'nama_peserta' => $dummyManualAttendees,
                'nama_mitra' => $dummyPartnerAttendees,
                
                'created_at' => Carbon::parse('2025-10-07 03:55:02'),
                'updated_at' => Carbon::parse('2025-10-07 03:55:02'),
            ],
            [
                'version_id' => 213130,
                'title' => 'Kick Off Project IoT Monitoring',
                'meeting_date' => '2025-10-06',
                'location' => 'Online via Zoom',
                'start_time' => '13:00',
                'end_time' => '15:00',
                
                'pimpinan_rapat' => 'Pak Denny', 
                'notulen' => 'Pak Hanif',
                
                'creator_id' => 2,
                'pembahasan' => '<p>Pemaparan rencana awal proyek IoT monitoring sistem kelistrikan.</p><p>Pembagian tugas awal untuk tiap tim.</p>',
                'status_id' => 2,

                // TAMBAH: Kolom JSON baru
                'nama_peserta' => $dummyManualAttendees,
                'nama_mitra' => $dummyPartnerAttendees,
                
                'created_at' => Carbon::parse('2025-10-06 13:20:10'),
                'updated_at' => Carbon::parse('2025-10-06 13:20:10'),
            ],
            [
                'version_id' => 213131,
                'title' => 'Evaluasi Vendor Fiber Optik',
                'meeting_date' => '2025-10-05',
                'location' => 'Kantor Pusat Telkom Lt.2',
                'start_time' => '10:00',
                'end_time' => '12:00',
                
                'pimpinan_rapat' => 'Pak Denny', 
                'notulen' => 'Pak Hanif',
                
                'creator_id' => 1,
                'pembahasan' => '<p>Review performa vendor pemasangan kabel FO.</p><p>Rekomendasi penggantian vendor di beberapa area.</p>',
                'status_id' => 1,
                
                // TAMBAH: Kolom JSON baru
                'nama_peserta' => $dummyManualAttendees,
                'nama_mitra' => $dummyPartnerAttendees,
                
                'created_at' => Carbon::parse('2025-07-05 08:00:00'),
                'updated_at' => Carbon::parse('2025-10-05 08:00:00'),
            ],
            [
                'version_id' => 213127,
                'title' => 'Rapat Progress Deployment Backbone',
                'meeting_date' => '2025-10-04',
                'location' => 'Ruang Command Center',
                'start_time' => '08:30',
                'end_time' => '10:00',
                
                'pimpinan_rapat' => 'Pak Denny', 
                'notulen' => 'Pak Hanif',
                
                'creator_id' => 2,
                'pembahasan' => '<p>Update status instalasi backbone antar wilayah.</p><p>Penyusunan timeline revisi target minggu depan.</p>',
                'status_id' => 3,
                
                // TAMBAH: Kolom JSON baru
                'nama_peserta' => $dummyManualAttendees,
                'nama_mitra' => $dummyPartnerAttendees,
                
                'created_at' => Carbon::parse('2025-08-04 08:00:00'),
                'updated_at' => Carbon::parse('2025-10-04 08:00:00'),
            ],
            [
                'version_id' => 213128,
                'title' => 'Diskusi Integrasi Sistem CRM Baru',
                'meeting_date' => '2025-10-03',
                'location' => 'Ruang R&D 2',
                'start_time' => '14:00',
                'end_time' => '15:30',
                
                'pimpinan_rapat' => 'Pak Denny', 
                'notulen' => 'Pak Hanif',
                
                'creator_id' => 3,
                'pembahasan' => '<p>Analisis integrasi antara CRM lama dan platform baru.</p><p>Identifikasi kebutuhan API tambahan.</p>',
                'status_id' => 2,
                
                // TAMBAH: Kolom JSON baru
                'nama_peserta' => $dummyManualAttendees,
                'nama_mitra' => $dummyPartnerAttendees,
                
                'created_at' => Carbon::parse('2025-09-03 14:30:00'),
                'updated_at' => Carbon::parse('2025-10-03 14:30:00'),
            ],
        ]);
        
        // Mengaktifkan kembali pengecekan Foreign Key
        Schema::enableForeignKeyConstraints();
    }
}