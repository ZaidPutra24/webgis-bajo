<?php

namespace App\Support;

use App\Models\JarakSekolahLokasi;
use App\Models\Sekolah;
use Illuminate\Support\Collection;

/**
 * Analisis hubungan jarak/keterpencilan dengan kondisi sekolah (poin 4).
 *
 * DASAR RUJUKAN (dikonfirmasi ke pengguna sebelum implementasi, bukan
 * asumsi bebas):
 *
 *  - Keterpencilan (akses transportasi & keterbatasan listrik/komunikasi)
 *    -> Permendikbud No. 34/2012 jo. No. 13/2015 tentang Kriteria Daerah
 *       Khusus, Pasal 2: akses transportasi sulit/tergantung cuaca/hanya
 *       jalan kaki, serta keterbatasan fasilitas listrik & informasi-
 *       komunikasi, adalah SATU definisi yang sama ("daerah khusus") —
 *       bukan dua hal independen. Karena itu status listrik/internet di
 *       sini ditabulasi-silang untuk MENGUJI apakah data lapangan cocok
 *       dengan definisi resmi, bukan diperlakukan sebagai variabel bebas.
 *  - Rasio siswa:guru maksimal 20:1 (SD-SMA) / 15:1 (SMK)
 *    -> PP No. 74/2008 tentang Guru, Pasal 17.
 *  - Kecukupan ruang kelas per rombel
 *    -> Kepmendikdasmen No. 14/2026 (turunan Permendikdasmen No. 26/2025
 *       tentang Standar Pengelolaan).
 *  - Status akreditasi A/B/C/Tidak Terakreditasi
 *    -> Permendikbudristek No. 38/2023 tentang Akreditasi PAUD/Dikdasmen
 *       (turunan PP No. 57/2021 tentang Standar Nasional Pendidikan).
 *
 * METODE: deskriptif (tabulasi silang per kelompok keterpencilan), BUKAN
 * korelasi/regresi formal — jumlah sekolah dalam studi kasus ini (wilayah
 * Bajo, 9 desa) terlalu kecil untuk klaim signifikansi statistik. Hasilnya
 * disajikan sebagai pola yang layak dicermati, bukan "terbukti secara
 * statistik".
 */
class JarakKondisiAnalyzer
{
    /** PP No. 74/2008 tentang Guru, Pasal 17. */
    public const AMBANG_RASIO_SISWA_GURU_UMUM = 20;
    public const AMBANG_RASIO_SISWA_GURU_SMK = 15;

    /** Konsisten dengan ambang di Dashboard Aksesibilitas (poin 3). */
    public const AMBANG_JAUH_MENIT = 60;

    /**
     * @return array{
     *   sekolah: Collection,
     *   ringkasanKelompok: Collection,
     *   totalSekolah: int,
     *   totalBebanGanda: int,
     * }
     */
    public function analisis(?float $ambangJauh = null): array
    {
        $ambangJauh = $ambangJauh ?? self::AMBANG_JAUH_MENIT;

        $sekolahs = Sekolah::with(['jenjang:id,nama_jenjang', 'statistik', 'utilitas'])
            ->orderBy('nama_sekolah')
            ->get();

        [$opsiTerpilihPerSekolah, $opsiMentahPerSekolah] = $this->kumpulkanOpsiPerSekolah();

        $hasil = $sekolahs->map(function (Sekolah $sekolah) use ($opsiTerpilihPerSekolah, $opsiMentahPerSekolah, $ambangJauh) {
            return $this->analisisSatuSekolah(
                $sekolah,
                $opsiTerpilihPerSekolah->get($sekolah->id, collect()),
                $opsiMentahPerSekolah[$sekolah->id] ?? [],
                $ambangJauh
            );
        });

        $ringkasanKelompok = collect(['mudah', 'jauh', 'kepulauan', 'tidak_ada_data'])
            ->map(fn ($kelompok) => $this->ringkasKelompok($kelompok, $hasil->where('kelompok_keterpencilan', $kelompok)));

        return [
            'sekolah'           => $hasil,
            'ringkasanKelompok' => $ringkasanKelompok,
            'totalSekolah'      => $hasil->count(),
            'totalBebanGanda'   => $hasil->where('beban_ganda', true)->count(),
            'ambangJauh'        => $ambangJauh,
        ];
    }

    /**
     * Balik arah pandang dari "per desa" (bagaimana data disimpan &
     * disusun oleh JarakSekolahLokasi::susunOpsiRute) menjadi "per sekolah":
     * kumpulkan, untuk tiap sekolah, opsi rute dari SEMUA desa yang punya
     * jalur ke sana.
     *
     * @return array{0: Collection, 1: array} [sekolah_id => Collection opsi
     *   terpilih (1 per desa, prioritas jalan kaki), sekolah_id => array
     *   SEMUA opsi mentah (segala moda/tipe, dari semua desa) untuk cek
     *   ada-tidaknya akses darat murni]
     */
    protected function kumpulkanOpsiPerSekolah(): array
    {
        $opsiTerpilih = collect();
        $opsiMentah = [];

        $barisPerDesa = JarakSekolahLokasi::all()->groupBy('wilayah_id');

        foreach ($barisPerDesa as $wilayahId => $rows) {
            $petaSekolah = JarakSekolahLokasi::susunOpsiRute($rows);

            foreach ($petaSekolah as $sekolahId => $opsiList) {
                $opsiMentah[$sekolahId] = array_merge($opsiMentah[$sekolahId] ?? [], $opsiList);

                $terpilih = JarakSekolahLokasi::pilihOpsi($opsiList, 'jalan_kaki');
                if ($terpilih) {
                    $list = $opsiTerpilih->get($sekolahId, collect());
                    $list->push(['wilayah_id' => $wilayahId, 'opsi' => $terpilih]);
                    $opsiTerpilih->put($sekolahId, $list);
                }
            }
        }

        return [$opsiTerpilih, $opsiMentah];
    }

    protected function analisisSatuSekolah(Sekolah $sekolah, Collection $opsiPerDesa, array $opsiMentah, float $ambangJauh): array
    {
        // ── Keterpencilan ──
        $adaAksesDaratMurni = collect($opsiMentah)->contains(fn ($o) => $o['pakai_perahu'] === false);
        $waktuSemuaDesa = $opsiPerDesa->pluck('opsi.waktu_mnt');

        $keterpencilanMinMnt = $waktuSemuaDesa->isNotEmpty() ? round($waktuSemuaDesa->min(), 1) : null;
        $keterpencilanRataMnt = $waktuSemuaDesa->isNotEmpty() ? round($waktuSemuaDesa->avg(), 1) : null;
        $jumlahDesaTerhubung = $opsiPerDesa->count();

        if ($keterpencilanMinMnt === null) {
            $kelompok = 'tidak_ada_data';
        } elseif (!$adaAksesDaratMurni) {
            $kelompok = 'kepulauan';
        } elseif ($keterpencilanMinMnt > $ambangJauh) {
            $kelompok = 'jauh';
        } else {
            $kelompok = 'mudah';
        }

        // ── Indikator kondisi (poin 4) ──
        $akreditasi = $this->nilaiAkreditasi($sekolah->akreditasi);
        $rasio = $this->nilaiRasioSiswaGuru($sekolah);
        $ruangKelas = $this->nilaiRuangKelas($sekolah);
        $listrik = $this->nilaiListrik($sekolah);
        $internet = $this->nilaiInternet($sekolah);

        $masalahTerkonfirmasi = collect([$akreditasi, $rasio, $ruangKelas, $listrik, $internet])
            ->filter(fn ($i) => $i['status'] === 'kurang')
            ->count();

        $bebanGanda = in_array($kelompok, ['jauh', 'kepulauan'], true) && $masalahTerkonfirmasi > 0;

        return [
            'sekolah_id'              => $sekolah->id,
            'nama_sekolah'            => $sekolah->nama_sekolah,
            'jenjang'                 => optional($sekolah->jenjang)->nama_jenjang,
            'status_sekolah'          => $sekolah->status,
            'kelompok_keterpencilan'  => $kelompok,
            'ada_akses_darat_murni'   => $adaAksesDaratMurni,
            'keterpencilan_min_mnt'   => $keterpencilanMinMnt,
            'keterpencilan_rata_mnt'  => $keterpencilanRataMnt,
            'jumlah_desa_terhubung'   => $jumlahDesaTerhubung,
            'akreditasi'              => $akreditasi,
            'rasio_siswa_guru'        => $rasio,
            'ruang_kelas'             => $ruangKelas,
            'listrik'                 => $listrik,
            'internet'                => $internet,
            'jumlah_masalah'          => $masalahTerkonfirmasi,
            'beban_ganda'             => $bebanGanda,
        ];
    }

    /** Permendikbudristek No. 38/2023: status A/B/C atau Tidak Terakreditasi. */
    protected function nilaiAkreditasi(?string $akreditasi): array
    {
        $akreditasi = $akreditasi ? strtoupper(trim($akreditasi)) : null;

        if ($akreditasi === null || $akreditasi === '') {
            return ['nilai' => null, 'label' => 'No data', 'status' => 'tidak_ada_data'];
        }

        $skor = match ($akreditasi) {
            'A' => 4, 'B' => 3, 'C' => 2,
            default => 1, // mis. "TT" / Tidak Terakreditasi
        };

        return [
            'nilai'  => $akreditasi,
            'skor'   => $skor,
            'label'  => $akreditasi === 'A' || $akreditasi === 'B' || $akreditasi === 'C' ? "Accredited {$akreditasi}" : 'Unaccredited',
            // C dan Tidak Terakreditasi sama-sama di bawah kriteria minimal
            // yang diharapkan (Permendikbudristek 38/2023 Ps. 6-7).
            'status' => in_array($akreditasi, ['A', 'B'], true) ? 'baik' : 'kurang',
        ];
    }

    /** PP No. 74/2008 Pasal 17: maksimal 20:1 (SD-SMA), 15:1 (SMK). */
    protected function nilaiRasioSiswaGuru(Sekolah $sekolah): array
    {
        $statistik = $sekolah->statistik;
        if (!$statistik || !$statistik->jumlah_guru || !$statistik->jumlah_siswa) {
            return ['nilai' => null, 'label' => 'No data', 'status' => 'tidak_ada_data'];
        }

        $rasio = round($statistik->jumlah_siswa / $statistik->jumlah_guru, 1);
        $isSmk = optional($sekolah->jenjang)->nama_jenjang && str_contains(strtoupper($sekolah->jenjang->nama_jenjang), 'SMK');
        $ambang = $isSmk ? self::AMBANG_RASIO_SISWA_GURU_SMK : self::AMBANG_RASIO_SISWA_GURU_UMUM;

        return [
            'nilai'  => $rasio,
            'ambang' => $ambang,
            'label'  => $rasio . ' students/teacher',
            'status' => $rasio > $ambang ? 'kurang' : 'baik',
        ];
    }

    /** Kepmendikdasmen No. 14/2026: ruang kelas idealnya >= jumlah rombel. */
    protected function nilaiRuangKelas(Sekolah $sekolah): array
    {
        $statistik = $sekolah->statistik;
        if (!$statistik || $statistik->ruang_kelas === null || !$statistik->jumlah_rombel) {
            return ['nilai' => null, 'label' => 'No data', 'status' => 'tidak_ada_data'];
        }

        $selisih = $statistik->ruang_kelas - $statistik->jumlah_rombel;

        return [
            'nilai'  => $statistik->ruang_kelas,
            'rombel' => $statistik->jumlah_rombel,
            'label'  => "{$statistik->ruang_kelas} rooms / {$statistik->jumlah_rombel} classes",
            'status' => $selisih < 0 ? 'kurang' : 'baik',
        ];
    }

    /** Bagian dari kriteria "daerah khusus" pada Permendikbud 34/2012. */
    protected function nilaiListrik(Sekolah $sekolah): array
    {
        $sumber = optional($sekolah->utilitas)->sumber_listrik;
        if (!$sumber || trim($sumber) === '') {
            return ['nilai' => null, 'label' => 'No data', 'status' => 'tidak_ada_data'];
        }

        $adaPln = strcasecmp(trim($sumber), 'PLN') === 0;

        return [
            'nilai'  => $sumber,
            'label'  => $sumber,
            'status' => $adaPln ? 'baik' : 'kurang',
        ];
    }

    /** Bagian dari kriteria "daerah khusus" pada Permendikbud 34/2012. */
    protected function nilaiInternet(Sekolah $sekolah): array
    {
        $akses = optional($sekolah->utilitas)->akses_internet;
        if (!$akses || trim($akses) === '') {
            return ['nilai' => null, 'label' => 'No data', 'status' => 'tidak_ada_data'];
        }

        return [
            'nilai'  => $akses,
            'label'  => $akses,
            'status' => $this->isKosong($akses) ? 'kurang' : 'baik',
        ];
    }

    protected function isKosong(string $nilai): bool
    {
        return in_array(strtolower(trim($nilai)), ['tidak ada', 'tidak tersedia', 'none', '-'], true);
    }

    protected function ringkasKelompok(string $kelompok, Collection $sekolahs): array
    {
        $labelKelompok = match ($kelompok) {
            'mudah'          => 'Accessible',
            'jauh'           => 'Far (Land > threshold)',
            'kepulauan'      => 'Islands (Boat Required)',
            default          => 'No Distance Data',
        };

        $ringkasIndikator = function (string $key) use ($sekolahs) {
            $adaData = $sekolahs->pluck($key)->filter(fn ($i) => $i['status'] !== 'tidak_ada_data');
            $kurang = $adaData->filter(fn ($i) => $i['status'] === 'kurang');

            return [
                'n_data_ada' => $adaData->count(),
                'n_kurang'   => $kurang->count(),
                'persen_kurang' => $adaData->count() > 0 ? round($kurang->count() / $adaData->count() * 100, 1) : null,
            ];
        };

        return [
            'kelompok'          => $kelompok,
            'label'             => $labelKelompok,
            'jumlah_sekolah'    => $sekolahs->count(),
            'jumlah_beban_ganda' => $sekolahs->where('beban_ganda', true)->count(),
            'akreditasi'        => $ringkasIndikator('akreditasi'),
            'rasio_siswa_guru'  => $ringkasIndikator('rasio_siswa_guru'),
            'ruang_kelas'       => $ringkasIndikator('ruang_kelas'),
            'listrik'           => $ringkasIndikator('listrik'),
            'internet'          => $ringkasIndikator('internet'),
        ];
    }
}
