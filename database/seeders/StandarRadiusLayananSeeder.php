<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Standar radius pelayanan sekolah per jenjang & penduduk pendukung ideal.
 *
 * Sumber default: SNI 03-1733-2004 (Tata Cara Perencanaan Lingkungan
 * Perumahan Perkotaan, Kementerian PU). Ini soal radius pelayanan ideal
 * fasilitas pendidikan, BUKAN radius zonasi PPDB.
 *
 * CATATAN: belum diverifikasi ulang secara independen. Disimpan sebagai
 * tabel "Pengaturan Standar" yang bisa diedit admin — begitu ada SK/Perbup/
 * data BAPPEDA yang lebih akurat untuk wilayah Bajo (Soropia/Tinanggea),
 * tinggal diubah tanpa perlu perubahan kode. Untuk Paket A/B/C (kesetaraan),
 * radius memakai acuan jenjang formal yang setara (SD/SMP/SMA) karena SNI
 * tidak mengatur kesetaraan secara eksplisit.
 */
class StandarRadiusLayananSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('standar_radius_layanan')->truncate();

        $sniSumber = 'SNI 03-1733-2004';
        $catatanSni = 'Radius garis lurus (km) dipakai sebagai acuan/cross-check sekunder. Untuk desa pesisir/kepulauan (akses via laut), status terlayani/tidak diprioritaskan dari data waktu tempuh riil (walk_mnt/drive_mnt/boat_mnt) di jaraksekolahlokasi bila tersedia.';
        $catatanSetara = 'Radius mengacu ke jenjang formal setara (SNI tidak mengatur kesetaraan secara eksplisit).';

        // jenjang_id sesuai JenjangSeeder: PAUD=10, SD=3, SMP=5, SMA=7, Paket A=16, Paket B=17, Paket C=18
        $data = [
            ['jenjang_id' => 10, 'radius_meter' => 500,  'penduduk_pendukung_ideal' => 1250, 'sumber' => $sniSumber, 'keterangan' => $catatanSni],
            ['jenjang_id' => 3,  'radius_meter' => 1000, 'penduduk_pendukung_ideal' => 1600, 'sumber' => $sniSumber, 'keterangan' => $catatanSni],
            ['jenjang_id' => 5,  'radius_meter' => 1000, 'penduduk_pendukung_ideal' => 4800, 'sumber' => $sniSumber, 'keterangan' => $catatanSni],
            ['jenjang_id' => 7,  'radius_meter' => 3000, 'penduduk_pendukung_ideal' => 4800, 'sumber' => $sniSumber, 'keterangan' => $catatanSni],
       ];

        foreach ($data as $row) {
            DB::table('standar_radius_layanan')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Sukses! Standar radius layanan 7 jenjang (SNI 03-1733-2004) berhasil dimasukkan.');
    }
}
