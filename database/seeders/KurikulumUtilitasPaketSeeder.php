<?php

namespace Database\Seeders;

use App\Models\KurikulumUtilitas;
use App\Models\Sekolah;
use Illuminate\Database\Seeder;

/**
 * Mengisi kolom `paket` ('A'/'B'/'C') untuk sekolah jenjang PKBM (id 12),
 * dengan cara membaca kata "Paket A/B/C" dari teks bebas kolom `kurikulum`
 * yang sudah ada (mis. "Kurikulum Paket C IPS 2013" -> paket = 'C').
 *
 * ⚠️ IDEMPOTENT & AMAN: seeder ini TIDAK PERNAH truncate/menimpa data
 * kurikulum_utilitas (tabel itu punya form edit admin sungguhan — lihat
 * KurikulumUtilitasController — jadi menimpanya akan berisiko sama seperti
 * masalah PendudukJenjangSeeder yang sudah diperbaiki sebelumnya). Seeder
 * ini HANYA mengisi kolom `paket` yang masih NULL, dan HANYA untuk baris
 * yang jenjang sekolahnya PKBM (id 12). Baris yang `paket`-nya sudah terisi
 * (baik oleh seeder ini sebelumnya maupun oleh admin manual) tidak disentuh.
 *
 * Sekolah PKBM yang teks kurikulumnya TIDAK menyebut "Paket A/B/C" secara
 * eksplisit (mis. cuma "Kurikulum 2013") akan dibiarkan `paket` = NULL dan
 * dicetak sebagai peringatan di akhir — perlu dicek & diisi manual oleh
 * admin lewat form Curriculum & Utilities, karena satu sekolah PKBM mungkin
 * menyelenggarakan lebih dari satu paket sekaligus, sementara struktur data
 * saat ini (1 baris kurikulum_utilitas per sekolah) hanya bisa menyimpan
 * SATU paket per sekolah — keterbatasan yang perlu diketahui admin.
 */
class KurikulumUtilitasPaketSeeder extends Seeder
{
    private const JENJANG_PKBM_ID = 12;

    public function run(): void
    {
        $sekolahPkbmIds = Sekolah::where('jenjang_id', self::JENJANG_PKBM_ID)->pluck('id');

        $rows = KurikulumUtilitas::whereIn('sekolah_id', $sekolahPkbmIds)
            ->whereNull('paket')
            ->get();

        $terisi = 0;
        $butuhCekManual = [];

        foreach ($rows as $row) {
            $teks = mb_strtolower((string) $row->kurikulum);
            $paket = null;

            if (str_contains($teks, 'paket a')) {
                $paket = 'A';
            } elseif (str_contains($teks, 'paket b')) {
                $paket = 'B';
            } elseif (str_contains($teks, 'paket c')) {
                $paket = 'C';
            }

            if ($paket !== null) {
                $row->paket = $paket;
                $row->save();
                $terisi++;
            } else {
                $sekolah = Sekolah::find($row->sekolah_id);
                $butuhCekManual[] = ($sekolah->nama_sekolah ?? "sekolah_id {$row->sekolah_id}")
                    . ' (kurikulum: "' . ($row->kurikulum ?? '-') . '")';
            }
        }

        $this->command->info("Sukses! {$terisi} sekolah PKBM berhasil diklasifikasikan paketnya otomatis dari teks kurikulum.");

        if (!empty($butuhCekManual)) {
            $this->command->warn('Perlu dicek & diisi manual lewat form Curriculum & Utilities (teks kurikulum tidak menyebut Paket A/B/C secara eksplisit, atau kemungkinan menyelenggarakan lebih dari 1 paket):');
            foreach ($butuhCekManual as $item) {
                $this->command->warn('  - ' . $item);
            }
        }
    }
}
