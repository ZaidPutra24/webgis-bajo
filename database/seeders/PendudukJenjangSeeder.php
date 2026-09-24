<?php

namespace Database\Seeders;

use App\Models\WilayahDesa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * DATA DUMMY penduduk usia sekolah PER JENJANG + Anak Tidak Sekolah (ATS) /
 * putus sekolah per jenjang, per desa.
 *
 * ⚠️ Ini data dummy, bukan data riil ATS. Diturunkan (bukan diketik manual)
 * dari kolom agregat yang SUDAH ADA di wilayah_desa
 * (penduduk_usia_sekolah_l/p), dipecah ke 7 jenjang inti (PAUD, SD, SMP,
 * SMA, Paket A/B/C) memakai proporsi & angka putus-sekolah yang wajar untuk
 * karakteristik masyarakat pesisir/kepulauan Bajo — BUKAN hasil sensus.
 * Begitu data ATS riil dari Dinas Pendidikan tersedia, ganti lewat form
 * admin (menu "Penduduk & ATS per Jenjang") — kolom sumber_data dipakai
 * untuk menandai asalnya.
 *
 * ⚠️ IDEMPOTENT: seeder ini hanya mengisi kombinasi (wilayah_id, jenjang_id)
 * yang BELUM ADA baris-nya sama sekali. Ia TIDAK PERNAH truncate atau
 * menimpa baris yang sudah ada — baik dummy lama maupun data ATS asli yang
 * sudah diinput admin lewat form. Ini penting supaya `php artisan
 * migrate:fresh --seed` tidak menghapus data asli yang sudah diinput.
 *
 * Proporsi jenjang (total = 100% dari agregat usia sekolah desa):
 *   PAUD 12%, SD 33%, SMP 24%, SMA 21%, Paket A 4%, Paket B 4%, Paket C 2%
 *
 * Asumsi tingkat putus sekolah/ATS (persentase DARI populasi jenjang
 * tersebut, bukan tambahan di luar itu) — lebih tinggi di jenjang atas &
 * paket kesetaraan, mencerminkan pola umum wilayah kepulauan yang akses ke
 * SMP/SMA-nya jauh/lintas laut:
 *   PAUD 5%, SD 4%, SMP 8%, SMA 12%, Paket A 20%, Paket B 18%, Paket C 15%
 */
class PendudukJenjangSeeder extends Seeder
{
    // jenjang_id sesuai JenjangSeeder
    private const PROPORSI = [
        10 => 0.1333, // PAUD  (dulu 0.12)
        3  => 0.3667, // SD    (dulu 0.33)
        5  => 0.2667, // SMP   (dulu 0.24)
        7  => 0.2333, // SMA   (dulu 0.21)
    ];

    private const TINGKAT_PUTUS_SEKOLAH = [
        10 => 0.05,
        3  => 0.04,
        5  => 0.08,
        7  => 0.12,
    ];

    public function run(): void
    {
        // FIX (bug arsitektur serius): seeder ini TIDAK BOLEH LAGI truncate.
        // Sebelumnya seeder ini menghapus SELURUH isi tabel penduduk_jenjang
        // lalu menimpanya dengan dummy setiap kali dijalankan. Begitu form
        // admin sudah ada dan diisi data ATS asli, `php artisan migrate:fresh
        // --seed` (mis. saat deploy ulang) akan MENGHAPUS data asli itu tanpa
        // peringatan apa pun dan menggantinya dengan dummy lagi.
        //
        // Sekarang seeder ini idempotent, mengikuti pola WilayahAutoCalcSeeder:
        // hanya mengisi baris (wilayah_id, jenjang_id) yang BELUM ADA sama
        // sekali di tabel. Baris yang sudah ada — baik dummy lama maupun data
        // ATS asli yang sudah diinput admin lewat form — tidak pernah disentuh.
        $existing = DB::table('penduduk_jenjang')
            ->select('wilayah_id', 'jenjang_id')
            ->get()
            ->map(fn ($row) => $row->wilayah_id . '-' . $row->jenjang_id)
            ->flip();

        $desas = WilayahDesa::all();
        $baris = [];
        $sumberDummy = 'Dummy - diturunkan dari agregat WilayahSeeder (bukan data ATS riil, ganti saat data resmi tersedia)';

        foreach ($desas as $desa) {
            $aggL = $desa->penduduk_usia_sekolah_l;
            $aggP = $desa->penduduk_usia_sekolah_p;

            foreach (self::PROPORSI as $jenjangId => $proporsi) {
                if (isset($existing[$desa->id . '-' . $jenjangId])) {
                    // Sudah ada datanya (dummy lama atau ATS asli) — lewati,
                    // jangan pernah ditimpa oleh seeder ini.
                    continue;
                }

                $pendudukL = $aggL !== null ? (int) round($aggL * $proporsi) : 0;
                $pendudukP = $aggP !== null ? (int) round($aggP * $proporsi) : 0;

                $tingkatPutus = self::TINGKAT_PUTUS_SEKOLAH[$jenjangId];
                $putusL = (int) round($pendudukL * $tingkatPutus);
                $putusP = (int) round($pendudukP * $tingkatPutus);

                $baris[] = [
                    'wilayah_id'       => $desa->id,
                    'jenjang_id'       => $jenjangId,
                    'penduduk_l'       => $pendudukL,
                    'penduduk_p'       => $pendudukP,
                    'putus_sekolah_l'  => $putusL,
                    'putus_sekolah_p'  => $putusP,
                    'sumber_data'      => $sumberDummy,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }

        if (empty($baris)) {
            $this->command->info('Tidak ada baris baru — semua kombinasi desa/jenjang sudah punya data (dummy atau ATS asli). Tidak ada yang ditimpa.');
            return;
        }

        foreach (array_chunk($baris, 100) as $chunk) {
            DB::table('penduduk_jenjang')->insert($chunk);
        }

        $this->command->info('Sukses! ' . count($baris) . ' baris data dummy baru ditambahkan (baris yang sudah ada tidak disentuh) untuk ' . $desas->count() . ' desa.');
    }
}
