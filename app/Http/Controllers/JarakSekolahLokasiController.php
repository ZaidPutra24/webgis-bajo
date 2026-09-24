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
        $validated = $this->validateJarak($request);

        $exists = JarakSekolahLokasi::where('sekolah_id', $validated['sekolah_id'] ?? null)
                                    ->where('wilayah_id', $validated['wilayah_id'])
                                    ->where('moda', $validated['moda'])
                                    ->where('segmen', $validated['segmen'] ?? 'langsung')
                                    ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data jarak untuk kombinasi sekolah/desa, moda, dan segmen ini sudah ada!');
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
        $validated = $this->validateJarak($request);

        $jarak = JarakSekolahLokasi::findOrFail($id);

        // Cek duplikat kombinasi sekolah+wilayah+moda+segmen saat UPDATE — exclude id saat ini
        $duplicate = JarakSekolahLokasi::where('sekolah_id', $validated['sekolah_id'] ?? null)
                                        ->where('wilayah_id', $validated['wilayah_id'])
                                        ->where('moda', $validated['moda'])
                                        ->where('segmen', $validated['segmen'] ?? 'langsung')
                                        ->where('id', '!=', $id)
                                        ->exists();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kombinasi Sekolah/Wilayah, Moda, dan Segmen tersebut sudah ada pada data lain!');
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
     * Filter opsional: ?wilayah_id=1 atau ?sekolah_id=2 atau ?moda=jalan_kaki
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

        if ($request->filled('moda')) {
            $query->where('moda', $request->string('moda'));
        }

        $rows = $query->get()->map(function ($row) {
            return [
                'id'               => $row->id,
                'sekolah_id'       => $row->sekolah_id,
                'nama_sekolah'     => $row->sekolah?->nama_sekolah,
                'wilayah_id'       => $row->wilayah_id,
                'nama_wilayah'     => $row->wilayahDesa?->nama_wilayah,
                'jarak'            => (float) $row->jarak,
                'moda'             => $row->moda,
                'moda_label'       => $row->moda_label,
                'segmen'           => $row->segmen,
                'waktu_tempuh_mnt' => $row->waktu_tempuh_mnt !== null ? (float) $row->waktu_tempuh_mnt : null,
                'waktu_label'      => $row->waktu_label,
                'tujuan'           => $row->tujuan,
                // route_geojson dikirim sebagai string — client tinggal JSON.parse()
                'route_geojson'    => $row->route_geojson,
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
                    'id'               => $row->id,
                    'sekolah_id'       => $row->sekolah_id,
                    'nama_sekolah'     => $row->sekolah?->nama_sekolah,
                    'lat_sekolah'      => $row->sekolah?->latitude,
                    'lon_sekolah'      => $row->sekolah?->longitude,
                    'wilayah_id'       => $row->wilayah_id,
                    'nama_wilayah'     => $row->wilayahDesa?->nama_wilayah,
                    'jarak'            => (float) $row->jarak,
                    'moda'             => $row->moda,
                    'moda_label'       => $row->moda_label,
                    'segmen'           => $row->segmen,
                    'waktu_tempuh_mnt' => $row->waktu_tempuh_mnt !== null ? (float) $row->waktu_tempuh_mnt : null,
                    'waktu_label'      => $row->waktu_label,
                    'tujuan'           => $row->tujuan,
                    'has_route'        => $row->route_geojson !== null,
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
            ->whereNotNull('sekolah_id')
            ->segmen('langsung')
            ->orderBy('jarak')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'id'               => $row->id,
                    'sekolah_id'       => $row->sekolah_id,
                    'nama_sekolah'     => $row->sekolah?->nama_sekolah,
                    'jenjang'          => $row->sekolah?->jenjang?->nama_jenjang,
                    'akreditasi'       => $row->sekolah?->akreditasi,
                    'lat_sekolah'      => $row->sekolah?->latitude,
                    'lon_sekolah'      => $row->sekolah?->longitude,
                    'nama_wilayah'     => $row->wilayahDesa?->nama_wilayah,
                    'jarak'            => (float) $row->jarak,
                    'moda'             => $row->moda,
                    'moda_label'       => $row->moda_label,
                    'waktu_tempuh_mnt' => $row->waktu_tempuh_mnt !== null ? (float) $row->waktu_tempuh_mnt : null,
                    'waktu_label'      => $row->waktu_label,
                    'has_route'        => $row->route_geojson !== null,
                ];
            });

        return response()->json($rows);
    }

    // =========================================================================
    // HELPER PRIVAT
    // =========================================================================

    /**
     * Validasi payload create/update terhadap skema BARU (satu baris = satu moda).
     * sekolah_id boleh kosong untuk leg antara (mis. desa -> dermaga, atau
     * penyeberangan perahu dermaga -> dermaga) — dalam hal ini tujuan_label wajib diisi.
     */
    private function validateJarak(Request $request): array
    {
        $validated = $request->validate([
            'sekolah_id'       => 'nullable|exists:sekolah,id',
            'wilayah_id'       => 'required|exists:wilayah_desa,id',
            'jarak'            => 'required|numeric|min:0',
            'moda'             => 'required|in:jalan_kaki,kendaraan,perahu',
            'segmen'           => 'nullable|in:langsung,ke_dermaga,penyeberangan,dermaga_ke_sekolah',
            'waktu_tempuh_mnt' => 'nullable|numeric|min:0',
            'tujuan_label'     => 'nullable|string|max:255',
            'route_geojson'    => 'nullable|string',
        ]);

        $validated['segmen'] = $validated['segmen'] ?? 'langsung';

        $request->validate([
            'sekolah_id' => $validated['segmen'] === 'langsung' || $validated['segmen'] === 'dermaga_ke_sekolah'
                ? 'required|exists:sekolah,id'
                : 'nullable',
        ]);

        if (empty($validated['sekolah_id']) && empty($validated['tujuan_label'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tujuan_label' => 'Label tujuan wajib diisi jika baris ini bukan tujuan akhir ke sekolah (mis. "Dermaga Pulau Bungin").',
            ]);
        }

        return $validated;
    }

    /**
     * Auto-hitung waktu tempuh dari jarak jika kolom tidak diisi secara manual.
     * jalan_kaki = jarak / 5  km/jam * 60
     * kendaraan  = jarak / 30 km/jam * 60
     * perahu     = jarak / 25 km/jam * 60
     */
    private function autoHitungWaktu(array $data): array
    {
        if (!empty($data['waktu_tempuh_mnt'])) {
            return $data;
        }

        $jarak = (float) ($data['jarak'] ?? 0);
        if ($jarak <= 0) {
            return $data;
        }

        $data['waktu_tempuh_mnt'] = match ($data['moda']) {
            'jalan_kaki' => JarakSekolahLokasi::hitungWalkMnt($jarak),
            'kendaraan'  => JarakSekolahLokasi::hitungDriveMnt($jarak),
            'perahu'     => JarakSekolahLokasi::hitungBoatMnt($jarak),
            default      => null,
        };

        return $data;
    }
}
