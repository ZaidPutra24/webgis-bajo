@extends('layouts.admin')

@section('title', 'Add Distance Data')
@section('page-title', 'Add Distance & Route Data')

@push('styles')
<style>
    .modern-card { border-radius:1.25rem; border:none; box-shadow:0 8px 30px rgba(0,0,0,.04); background:#fff; }
    .form-header-title { position:relative; padding-left:1rem; color:#2b3445; }
    .form-header-title::before { content:''; position:absolute; left:0; top:20%; height:60%; width:5px;
        background:linear-gradient(135deg,#4F46E5 0%,#06b6d4 100%); border-radius:5px; }
    .form-label-custom { font-weight:600; font-size:.83rem; color:#475569; text-transform:uppercase; letter-spacing:.05em; }
    .form-control-custom, .form-select-custom {
        border:2px solid #e2e8f0; border-radius:.75rem; padding:.65rem 1rem;
        font-size:.95rem; color:#334155; transition:all .2s; background:#f8fafc; }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color:#4F46E5; background:#fff; box-shadow:0 0 0 4px rgba(79,70,229,.1); color:#1e293b; }
    .input-group-text-custom {
        border:2px solid #e2e8f0; border-left:none; background:#f1f5f9; color:#64748b;
        font-weight:600; padding:0 1.1rem;
        border-top-right-radius:.75rem !important; border-bottom-right-radius:.75rem !important; }
    .section-sep { border-top:2px dashed #e8ecf2; margin:1.5rem 0 1.25rem; }
    .section-label { font-size:.72rem; font-weight:700; color:#94a3b8; text-transform:uppercase;
        letter-spacing:.1em; margin-bottom:1rem; display:flex; align-items:center; gap:.5rem; }
    .section-label::after { content:''; flex:1; height:1px; background:#e8ecf2; }
    .calc-hint { font-size:.72rem; color:#94a3b8; margin-top:.3rem; }
    .moda-toggle-wrap { display:flex; gap:.75rem; }
    .moda-toggle-btn { flex:1; padding:.6rem 1rem; border:2px solid #e2e8f0; border-radius:.75rem;
        background:#f8fafc; color:#64748b; font-weight:600; font-size:.88rem;
        cursor:pointer; transition:all .2s; text-align:center; }
    .moda-toggle-btn.selected-jalan_kaki { border-color:#16a34a; background:#dcfce7; color:#166534; }
    .moda-toggle-btn.selected-kendaraan  { border-color:#2563eb; background:#dbeafe; color:#1d4ed8; }
    .moda-toggle-btn.selected-perahu     { border-color:#ea580c; background:#ffedd5; color:#9a3412; }
    .btn-action-save { background:#4F46E5; border:2px solid #4F46E5; color:#fff; font-weight:600;
        font-size:.9rem; transition:all .2s; box-shadow:0 4px 12px rgba(79,70,229,.15); }
    .btn-action-save:hover { background:#4338ca; border-color:#4338ca; color:#fff; transform:translateY(-1px); }
    .btn-action-cancel { border:2px solid #e2e8f0; color:#64748b; font-weight:600; font-size:.9rem; transition:all .2s; background:transparent; }
    .btn-action-cancel:hover { background:#f1f5f9; border-color:#cbd5e1; color:#334155; }

    /* ── Input GeoJSON rute ── */
    .geojson-textarea {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.85rem;
        line-height: 1.6;
        color: #475569;
        background-color: #f8fafc;
    }
    #panel-sekolah { display: block; }
    #panel-tujuan-label { display: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 mb-5">
<div class="row justify-content-center">
<div class="col-lg-10">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Add Distance & Route Data</h4>
            <p class="text-muted small mb-0">Input distance, travel mode, travel time, and GeoJSON route data (optional). One row = one travel mode.</p>
        </div>
        <a href="{{ route('jarak.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-medium text-danger">{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <div class="card modern-card shadow-sm">
        <div class="card-header bg-white py-4 border-0 rounded-top-4">
            <h5 class="form-header-title fw-bold mb-0">Input New Distance & Route Data</h5>
        </div>
        <div class="card-body p-4 pt-2">
        <form action="{{ route('jarak.store') }}" method="POST" id="form-jarak">
            @csrf

            {{-- ══ SEKSI 1: Moda ══ --}}
            <p class="section-label mt-2"><i class="bi bi-signpost-split"></i> Travel Mode</p>
            <input type="hidden" name="moda" id="moda" value="{{ old('moda','jalan_kaki') }}">
            <div class="moda-toggle-wrap mb-1">
                <button type="button" class="moda-toggle-btn {{ old('moda','jalan_kaki')==='jalan_kaki'?'selected-jalan_kaki':'' }}"
                        data-moda="jalan_kaki" onclick="setModa('jalan_kaki')">
                    <i class="bi bi-person-walking me-1"></i> Walking
                </button>
                <button type="button" class="moda-toggle-btn {{ old('moda')==='kendaraan'?'selected-kendaraan':'' }}"
                        data-moda="kendaraan" onclick="setModa('kendaraan')">
                    <i class="bi bi-car-front me-1"></i> Vehicle
                </button>
                <button type="button" class="moda-toggle-btn {{ old('moda')==='perahu'?'selected-perahu':'' }}"
                        data-moda="perahu" onclick="setModa('perahu')">
                    <i class="bi bi-water me-1"></i> Boat
                </button>
            </div>
            @error('moda') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

            {{-- ══ SEKSI 2: Segmen ══ --}}
            <div class="section-sep"></div>
            <p class="section-label"><i class="bi bi-signpost"></i> Journey Segment</p>
            <select class="form-select form-select-custom @error('segmen') is-invalid @enderror"
                    name="segmen" id="segmen" onchange="onSegmenChange()">
                <option value="langsung" {{ old('segmen','langsung')==='langsung'?'selected':'' }}>Direct (village &rarr; school, full leg)</option>
                <option value="ke_dermaga" {{ old('segmen')==='ke_dermaga'?'selected':'' }}>To Harbor (village &rarr; harbor)</option>
                <option value="penyeberangan" {{ old('segmen')==='penyeberangan'?'selected':'' }}>Sea Crossing (harbor &rarr; harbor, boat)</option>
                <option value="dermaga_ke_sekolah" {{ old('segmen')==='dermaga_ke_sekolah'?'selected':'' }}>Harbor &rarr; School (after crossing)</option>
            </select>
            @error('segmen') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <p class="calc-hint">Select "Direct" for standard routes. Select other segments for multi-leg island routes (e.g., Bungin/Saponda).</p>

            {{-- ══ SEKSI 3: Relasi ══ --}}
            <div class="section-sep"></div>
            <p class="section-label"><i class="bi bi-link-45deg"></i> Relation Data</p>
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label class="form-label form-label-custom mb-2">Select Village / Region</label>
                    <select class="form-select form-select-custom @error('wilayah_id') is-invalid @enderror"
                            name="wilayah_id" id="wilayah_id" required>
                        <option value="">-- Select Village --</option>
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id }}" {{ old('wilayah_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>
                    @error('wilayah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6" id="panel-sekolah">
                    <label class="form-label form-label-custom mb-2">Select School</label>
                    <select class="form-select form-select-custom @error('sekolah_id') is-invalid @enderror"
                            name="sekolah_id" id="sekolah_id">
                        <option value="">-- Select School --</option>
                        @foreach($sekolahs as $s)
                            <option value="{{ $s->id }}"
                                {{ old('sekolah_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_sekolah }}
                            </option>
                        @endforeach
                    </select>
                    @error('sekolah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6" id="panel-tujuan-label">
                    <label class="form-label form-label-custom mb-2">Destination Label</label>
                    <input type="text" class="form-control form-control-custom @error('tujuan_label') is-invalid @enderror"
                           name="tujuan_label" id="tujuan_label" value="{{ old('tujuan_label') }}"
                           placeholder='Example: "Dermaga Pulau Bungin"'>
                    @error('tujuan_label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <p class="calc-hint">Used when this leg is not the final destination school (e.g. intermediate leg to harbor).</p>
                </div>
            </div>

            {{-- ══ SEKSI 4: Jarak & Waktu ══ --}}
            <div class="section-sep"></div>
            <p class="section-label"><i class="bi bi-rulers"></i> Distance & Travel Time</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-custom mb-2">Distance for this mode</label>
                    <div class="input-group">
                        <input type="number" step="0.001" min="0" id="jarak" name="jarak"
                               class="form-control form-control-custom @error('jarak') is-invalid @enderror"
                               value="{{ old('jarak') }}" placeholder="Example: 17.58"
                               style="border-top-right-radius:0;border-bottom-right-radius:0;"
                               required oninput="autoCalc()">
                        <span class="input-group-text input-group-text-custom">km</span>
                        @error('jarak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-custom mb-2">Estimated Travel Time
                        <span style="font-size:.68rem;font-weight:500;text-transform:none;letter-spacing:0;">(auto — can be overridden)</span>
                    </label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" id="waktu_tempuh_mnt" name="waktu_tempuh_mnt"
                               class="form-control form-control-custom @error('waktu_tempuh_mnt') is-invalid @enderror"
                               value="{{ old('waktu_tempuh_mnt') }}" placeholder="auto"
                               style="border-top-right-radius:0;border-bottom-right-radius:0;">
                        <span class="input-group-text input-group-text-custom">min</span>
                        @error('waktu_tempuh_mnt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <p class="calc-hint" id="calc-hint-text">÷ 5 km/h × 60 (walking)</p>
                </div>
            </div>

            {{-- ══ SEKSI 5: Rute GeoJSON ══ --}}
            <div class="section-sep"></div>
            <p class="section-label"><i class="bi bi-map"></i> GeoJSON Route
                <span style="font-size:.68rem;font-weight:500;text-transform:none;letter-spacing:0;">
                    (optional — paste the route path GeoJSON text, e.g., an export from QGIS)
                </span>
            </p>

            <textarea class="form-control form-control-custom geojson-textarea @error('route_geojson') is-invalid @enderror"
                      id="route_geojson" name="route_geojson" rows="8"
                      placeholder='Example: {"type": "LineString", "coordinates": [[122.65, -3.93], [122.66, -3.94]]}'>{{ old('route_geojson') }}</textarea>
            @error('route_geojson') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <p class="calc-hint mt-2">
                <i class="bi bi-lightbulb me-1"></i>
                Standard GeoJSON geometry format (LineString/MultiLineString) specifically for the selected mode &amp; segment. Leave blank if there is no route data yet.
            </p>

            {{-- ══ Footer ══ --}}
            <div class="d-flex gap-2 justify-content-end pt-4 mt-2 border-top border-light">
                <a href="{{ route('jarak.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill">Cancel</a>
                <button type="submit" class="btn btn-action-save px-4 py-2 rounded-pill">
                    <i class="bi bi-check-lg me-1"></i> Save Data
                </button>
            </div>

        </form>
        </div>
    </div>

</div>
</div>
</div>
@endsection

@push('scripts')
<script>
// ─── Kecepatan rata-rata per moda (km/jam), harus konsisten dengan Model::hitungXxxMnt() ───
var MODA_SPEED = { jalan_kaki: 5, kendaraan: 30, perahu: 25 };
var MODA_HINT  = {
    jalan_kaki: '÷ 5 km/h × 60 (walking)',
    kendaraan:  '÷ 30 km/h × 60 (vehicle)',
    perahu:     '÷ 25 km/h × 60 (boat)'
};

// ─── Moda Toggle ─────────────────────────────────────────────────────────────
function setModa(moda) {
    document.getElementById('moda').value = moda;
    document.querySelectorAll('.moda-toggle-btn').forEach(function(b) {
        b.classList.remove('selected-jalan_kaki', 'selected-kendaraan', 'selected-perahu');
    });
    var sel = document.querySelector('.moda-toggle-btn[data-moda="' + moda + '"]');
    if (sel) sel.classList.add('selected-' + moda);
    document.getElementById('calc-hint-text').textContent = MODA_HINT[moda] || '';
    autoCalc();
}

// ─── Segmen: sekolah wajib untuk 'langsung'/'dermaga_ke_sekolah', tujuan_label untuk sisanya ───
function onSegmenChange() {
    var segmen = document.getElementById('segmen').value;
    var needsSekolah = (segmen === 'langsung' || segmen === 'dermaga_ke_sekolah');
    document.getElementById('panel-sekolah').style.display = needsSekolah ? '' : 'none';
    document.getElementById('panel-tujuan-label').style.display = needsSekolah ? 'none' : '';
    document.getElementById('sekolah_id').required = needsSekolah;
}

// ─── Auto-calc Waktu Tempuh ───────────────────────────────────────────────────
function autoCalc() {
    var jarak = parseFloat(document.getElementById('jarak').value) || 0;
    var moda  = document.getElementById('moda').value;
    var wF    = document.getElementById('waktu_tempuh_mnt');
    var speed = MODA_SPEED[moda] || 5;
    if (!wF.dataset.manual && jarak > 0) {
        wF.value = Math.round((jarak / speed) * 60 * 100) / 100;
    }
}
document.getElementById('waktu_tempuh_mnt').addEventListener('input', function() { this.dataset.manual = '1'; });

// Init
(function() {
    setModa(document.getElementById('moda').value || 'jalan_kaki');
    onSegmenChange();
    if (document.getElementById('jarak').value) autoCalc();
})();
</script>
@endpush
