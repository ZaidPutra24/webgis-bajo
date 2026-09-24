<?php

namespace App\Http\Controllers;

use App\Models\JarakSekolahLokasi;
use App\Models\PendudukJenjang;
use App\Models\Sekolah;
use App\Models\WilayahDesa;
use App\Support\AksesibilitasAnalyzer;
use Illuminate\Http\Request;

/**
 * Dashboard Aksesibilitas Sekolah (poin 3).
 *
 * Memvisualisasikan waktu tempuh jalan kaki / kendaraan / perahu dari tiap desa
 * ke sekolah terdekat per jenjang, menandai desa dengan waktu tempuh melebihi
 * ambang (default 60 menit) dan jenjang yang hanya bisa dicapai lewat perahu
 * (tidak ada jalur darat murni). Seluruh perhitungan ada di
 * App\Support\AksesibilitasAnalyzer; controller ini hanya menyiapkan input,
 * urutan tampilan, dan geometri rute untuk peta.
 */
class AksesibilitasController extends Controller
{
    public function index(Request $request)
    {
        $ambang = (float) max(5, min(240, (float) $request->query('ambang', 60)));
        $moda = $request->query('moda') === 'kendaraan' ? 'kendaraan' : 'jalan_kaki';

        $wilayahs = WilayahDesa::orderBy('nama_wilayah')->get();
        $sekolahs = Sekolah::whereNotNull('latitude')->whereNotNull('longitude')->get();
        // Tanpa route_geojson (besar); geometri hanya diambil untuk rute kritis di bawah.
        $jarakRows = JarakSekolahLokasi::select(JarakSekolahLokasi::KOLOM_RINGAN)->get();
        $penduduk = PendudukJenjang::whereIn('jenjang_id', array_values(AksesibilitasAnalyzer::PENDUDUK_JENJANG_ID))->get();

        $hasil = AksesibilitasAnalyzer::analisis($wilayahs, $sekolahs, $jarakRows, $ambang, $penduduk, $moda);

        // Geometri rute kritis (desa -> sekolah terjauh yang ditandai) untuk peta.
        $legIds = $hasil['desa']->pluck('leg_ids_kritis')->flatten()->unique()->values()->all();
        $legs = $legIds
            ? JarakSekolahLokasi::whereIn('id', $legIds)->get(['id', 'moda', 'segmen', 'route_geojson'])->keyBy('id')
            : collect();
        $sekolahById = $sekolahs->keyBy('id');

        $urutStatus = ['kritis' => 0, 'sedang' => 1, 'baik' => 2, 'no_data' => 3];

        $desa = $hasil['desa']->map(function (array $d) use ($legs, $sekolahById) {
            $d['rute'] = collect($d['leg_ids_kritis'])
                ->map(fn ($id) => $legs->get($id))
                ->filter()
                ->map(fn ($leg) => [
                    'moda'    => $leg->moda,
                    'segmen'  => $leg->segmen,
                    'geojson' => $this->sederhanakanGeoJson($leg->route_geojson),
                ])
                ->filter(fn ($r) => $r['geojson'] !== null)
                ->values()
                ->all();

            $kritis = $d['terburuk_jenjang'] ? ($d['jenjang'][$d['terburuk_jenjang']] ?? null) : null;
            $sek = $kritis ? $sekolahById->get($kritis['sekolah_id']) : null;
            $d['sekolah_kritis'] = $sek ? [
                'nama' => $sek->nama_sekolah,
                'lat'  => (float) $sek->latitude,
                'lng'  => (float) $sek->longitude,
            ] : null;

            unset($d['leg_ids_kritis']);

            return $d;
        })->sortBy([
            fn ($a, $b) => $urutStatus[$a['status']] <=> $urutStatus[$b['status']],
            fn ($a, $b) => ($b['terburuk_waktu_mnt'] ?? 0) <=> ($a['terburuk_waktu_mnt'] ?? 0),
        ])->values();

        return view('admin.aksesibilitas.index', [
            'desa'      => $desa,
            'ringkasan' => $hasil['ringkasan'],
            'perModa'   => $hasil['per_moda'],
            'jenjangList' => array_keys(AksesibilitasAnalyzer::JENJANG_INTI),
            'ambang'    => $ambang,
            'moda'      => $moda,
            'totalRute' => $jarakRows->count(),
        ]);
    }

    /**
     * Ringankan GeoJSON rute untuk dikirim ke browser: buang elevasi (elemen ke-3)
     * dan bulatkan koordinat ke 5 desimal (~1 m). Return null kalau tidak valid.
     */
    protected function sederhanakanGeoJson(?string $json): ?array
    {
        if (!$json) {
            return null;
        }
        $geo = json_decode($json, true);
        if (!is_array($geo) || !isset($geo['type'], $geo['coordinates'])) {
            return null;
        }

        $bulatkan = function ($node) use (&$bulatkan) {
            if (is_array($node) && isset($node[0]) && is_numeric($node[0])) {
                return [round((float) $node[0], 5), round((float) $node[1], 5)];
            }

            return is_array($node) ? array_map($bulatkan, $node) : $node;
        };

        return ['type' => $geo['type'], 'coordinates' => $bulatkan($geo['coordinates'])];
    }
}
