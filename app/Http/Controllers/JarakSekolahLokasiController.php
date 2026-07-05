<?php

namespace App\Http\Controllers;

use App\Models\JarakSekolahLokasi;
use App\Models\Sekolah;
use App\Models\WilayahDesa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JarakSekolahLokasiController extends Controller
{
    // =========================================================================
    // ADMIN CRUD
    // =========================================================================

    public function index()
    {
        $matriksJarak = JarakSekolahLokasi::with(['sekolah', 'wilayahDesa'])
            ->latest()
            ->get();

        return view('admin.jarak.index', compact('matriksJarak'));
    }

    public function create()
    {
        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        $wilayahs = WilayahDesa::orderBy('nama_wilayah')->get();
        return view('admin.jarak.create', compact('sekolahs', 'wilayahs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sekolah_id'     => 'required|exists:sekolah,id',
            'wilayah_id'     => 'required|exists:wilayah_desa,id',
            'jarak'          => 'required|numeric|min:0',
            'walk_mnt'       => 'nullable|numeric|min:0',
            'drive_mnt'      => 'nullable|numeric|min:0',
            'boat_mnt'       => 'nullable|numeric|min:0',
            'jarak_laut'     => 'nullable|numeric|min:0',
            'mode_transport' => 'nullable|in:darat,multimoda',
            'route_geojson'  => 'nullable|string',
        ]);

        $exists = JarakSekolahLokasi::where('sekolah_id', $validated['sekolah_id'])
                                    ->where('wilayah_id', $validated['wilayah_id'])
                                    ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data jarak untuk sekolah ke desa ini sudah ada!');
        }

        // Auto-hitung waktu tempuh jika tidak diisi manual
        $validated = $this->autoHitungWaktu($validated);

        JarakSekolahLokasi::create($validated);

        return redirect()->route('jarak.index')
            ->with('success', 'Data Matriks Jarak Berhasil Ditambahkan!');
    }

    public function show($id)
    {
        return redirect()->route('jarak.edit', $id);
    }

    public function edit($id)
    {
        $jarak    = JarakSekolahLokasi::findOrFail($id);
        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        $wilayahs = WilayahDesa::orderBy('nama_wilayah')->get();
        return view('admin.jarak.edit', compact('jarak', 'sekolahs', 'wilayahs'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sekolah_id'     => 'required|exists:sekolah,id',
            'wilayah_id'     => 'required|exists:wilayah_desa,id',
            'jarak'          => 'required|numeric|min:0',
            'walk_mnt'       => 'nullable|numeric|min:0',
            'drive_mnt'      => 'nullable|numeric|min:0',
            'boat_mnt'       => 'nullable|numeric|min:0',
            'jarak_laut'     => 'nullable|numeric|min:0',
            'mode_transport' => 'nullable|in:darat,multimoda',
            'route_geojson'  => 'nullable|string',
        ]);

        $jarak = JarakSekolahLokasi::findOrFail($id);

        // BUG FIX: Cek duplikat pasangan sekolah+wilayah saat UPDATE — exclude id saat ini
        $duplicate = JarakSekolahLokasi::where('sekolah_id', $validated['sekolah_id'])
                                        ->where('wilayah_id', $validated['wilayah_id'])
                                        ->where('id', '!=', $id)
                                        ->exists();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kombinasi Sekolah dan Wilayah tersebut sudah ada pada data lain!');
        }

        // Auto-hitung waktu tempuh jika tidak diisi manual
        $validated = $this->autoHitungWaktu($validated);

        $jarak->update($validated);

        return redirect()->route('jarak.index')
            ->with('success', 'Data Jarak berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jarak = JarakSekolahLokasi::findOrFail($id);
        $jarak->delete();

        return redirect()->route('jarak.index')
            ->with('success', 'Data Jarak berhasil dihapus!');
    }

    // =========================================================================
    // API ENDPOINTS UNTUK PETA LEAFLET
    // =========================================================================

    /**
     * GET /api/jarak/routes
     *
     * Kembalikan semua rute GeoJSON yang tersedia untuk ditampilkan di peta Leaflet.
     * Filter opsional: ?wilayah_id=1 atau ?sekolah_id=2 atau ?mode=darat
     *
     * Contoh pemakaian di Leaflet:
     *   fetch('/api/jarak/routes?wilayah_id=1')
     *     .then(r => r.json())
     *     .then(data => {
     *       data.forEach(row => {
     *         L.geoJSON(JSON.parse(row.route_geojson)).addTo(map);
     *       });
     *     });
     */
    public function apiRoutes(Request $request): JsonResponse
    {
        $query = JarakSekolahLokasi::with(['sekolah:id,nama_sekolah,latitude,longitude',
                                           'wilayahDesa:id,nama_wilayah'])
            ->whereNotNull('route_geojson');

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->integer('wilayah_id'));
        }

        if ($request->filled('sekolah_id')) {
            $query->where('sekolah_id', $request->integer('sekolah_id'));
        }

        if ($request->filled('mode')) {
            $query->where('mode_transport', $request->string('mode'));
        }

        $rows = $query->get()->map(function ($row) {
            return [
                'id'             => $row->id,
                'sekolah_id'     => $row->sekolah_id,
                'nama_sekolah'   => $row->sekolah?->nama_sekolah,
                'wilayah_id'     => $row->wilayah_id,
                'nama_wilayah'   => $row->wilayahDesa?->nama_wilayah,
                'jarak'          => (float) $row->jarak,
                'walk_mnt'       => $row->walk_mnt !== null ? (float) $row->walk_mnt : null,
                'drive_mnt'      => $row->drive_mnt !== null ? (float) $row->drive_mnt : null,
                'boat_mnt'       => $row->boat_mnt !== null ? (float) $row->boat_mnt : null,
                'jarak_laut'     => $row->jarak_laut !== null ? (float) $row->jarak_laut : null,
                'mode_transport' => $row->mode_transport,
                'mode_label'     => $row->mode_label,
                'walk_label'     => $row->walk_label,
                'drive_label'    => $row->drive_label,
                'boat_label'     => $row->boat_label,
                // route_geojson dikirim sebagai string — client tinggal JSON.parse()
                'route_geojson'  => $row->route_geojson,
            ];
        });

        return response()->json($rows);
    }

    /**
     * GET /api/jarak/matriks
     *
     * Kembalikan matriks jarak (tanpa route_geojson) untuk semua kombinasi.
     * Berguna untuk tabel dan statistik.
     * Filter opsional: ?wilayah_id=1 atau ?sekolah_id=2
     */
    public function apiMatriks(Request $request): JsonResponse
    {
        $query = JarakSekolahLokasi::with([
            'sekolah:id,nama_sekolah,latitude,longitude,akreditasi',
            'wilayahDesa:id,nama_wilayah',
        ]);

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->integer('wilayah_id'));
        }

        if ($request->filled('sekolah_id')) {
            $query->where('sekolah_id', $request->integer('sekolah_id'));
        }

        $rows = $query->orderBy('wilayah_id')->orderBy('jarak')->get()
            ->map(function ($row) {
                return [
                    'id'             => $row->id,
                    'sekolah_id'     => $row->sekolah_id,
                    'nama_sekolah'   => $row->sekolah?->nama_sekolah,
                    'lat_sekolah'    => $row->sekolah?->latitude,
                    'lon_sekolah'    => $row->sekolah?->longitude,
                    'wilayah_id'     => $row->wilayah_id,
                    'nama_wilayah'   => $row->wilayahDesa?->nama_wilayah,
                    'jarak'          => (float) $row->jarak,
                    'walk_mnt'       => $row->walk_mnt !== null ? (float) $row->walk_mnt : null,
                    'drive_mnt'      => $row->drive_mnt !== null ? (float) $row->drive_mnt : null,
                    'boat_mnt'       => $row->boat_mnt !== null ? (float) $row->boat_mnt : null,
                    'jarak_laut'     => $row->jarak_laut !== null ? (float) $row->jarak_laut : null,
                    'mode_transport' => $row->mode_transport,
                    'mode_label'     => $row->mode_label,
                    'walk_label'     => $row->walk_label,
                    'drive_label'    => $row->drive_label,
                    'boat_label'     => $row->boat_label,
                    'has_route'      => $row->route_geojson !== null,
                ];
            });

        return response()->json($rows);
    }

    /**
     * GET /api/jarak/sekolah-terdekat?wilayah_id=1&limit=5
     *
     * Kembalikan daftar sekolah terdekat dari suatu wilayah, diurutkan dari terdekat.
     * Berguna untuk panel info di peta.
     */
    public function apiSekolahTerdekat(Request $request): JsonResponse
    {
        $request->validate([
            'wilayah_id' => 'required|integer|exists:wilayah_desa,id',
            'limit'      => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $request->integer('limit', 10);

        $rows = JarakSekolahLokasi::with([
                'sekolah:id,nama_sekolah,latitude,longitude,akreditasi,jenjang_id',
                'sekolah.jenjang:id,nama_jenjang',
                'wilayahDesa:id,nama_wilayah',
            ])
            ->where('wilayah_id', $request->integer('wilayah_id'))
            ->orderBy('jarak')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'id'             => $row->id,
                    'sekolah_id'     => $row->sekolah_id,
                    'nama_sekolah'   => $row->sekolah?->nama_sekolah,
                    'jenjang'        => $row->sekolah?->jenjang?->nama_jenjang,
                    'akreditasi'     => $row->sekolah?->akreditasi,
                    'lat_sekolah'    => $row->sekolah?->latitude,
                    'lon_sekolah'    => $row->sekolah?->longitude,
                    'nama_wilayah'   => $row->wilayahDesa?->nama_wilayah,
                    'jarak'          => (float) $row->jarak,
                    'walk_mnt'       => $row->walk_mnt !== null ? (float) $row->walk_mnt : null,
                    'drive_mnt'      => $row->drive_mnt !== null ? (float) $row->drive_mnt : null,
                    'boat_mnt'       => $row->boat_mnt !== null ? (float) $row->boat_mnt : null,
                    'mode_transport' => $row->mode_transport,
                    'mode_label'     => $row->mode_label,
                    'walk_label'     => $row->walk_label,
                    'drive_label'    => $row->drive_label,
                    'boat_label'     => $row->boat_label,
                    'has_route'      => $row->route_geojson !== null,
                ];
            });

        return response()->json($rows);
    }

    // =========================================================================
    // HELPER PRIVAT
    // =========================================================================

    /**
     * Auto-hitung waktu tempuh dari jarak jika kolom tidak diisi secara manual.
     * walk_mnt  = jarak / 5 km/jam * 60
     * drive_mnt = jarak / 30 km/jam * 60
     * boat_mnt  = jarak_laut / 25 km/jam * 60 (hanya jika ada jarak_laut)
     */
    private function autoHitungWaktu(array $data): array
    {
        $jarak = (float) ($data['jarak'] ?? 0);

        if (empty($data['walk_mnt']) && $jarak > 0) {
            $data['walk_mnt'] = JarakSekolahLokasi::hitungWalkMnt($jarak);
        }

        if (empty($data['drive_mnt']) && $jarak > 0) {
            $data['drive_mnt'] = JarakSekolahLokasi::hitungDriveMnt($jarak);
        }

        if (!empty($data['jarak_laut']) && empty($data['boat_mnt'])) {
            $data['boat_mnt'] = JarakSekolahLokasi::hitungBoatMnt((float) $data['jarak_laut']);
        }

        // Set mode_transport berdasarkan ada tidaknya jarak_laut
        if (empty($data['mode_transport'])) {
            $data['mode_transport'] = !empty($data['jarak_laut']) ? 'multimoda' : 'darat';
        }

        return $data;
    }
}