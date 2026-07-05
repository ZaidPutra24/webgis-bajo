@extends('layouts.admin')

@section('title', 'Edit Data Jarak')
@section('page-title', 'Edit Matriks Jarak & Rute')

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
    .mode-toggle-wrap { display:flex; gap:.75rem; }
    .mode-toggle-btn { flex:1; padding:.6rem 1rem; border:2px solid #e2e8f0; border-radius:.75rem;
        background:#f8fafc; color:#64748b; font-weight:600; font-size:.88rem;
        cursor:pointer; transition:all .2s; text-align:center; }
    .mode-toggle-btn.selected-darat     { border-color:#16a34a; background:#dcfce7; color:#166534; }
    .mode-toggle-btn.selected-multimoda { border-color:#ca8a04; background:#fef9c3; color:#92400e; }
    .btn-action-save { background:#4F46E5; border:2px solid #4F46E5; color:#fff; font-weight:600;
        font-size:.9rem; transition:all .2s; box-shadow:0 4px 12px rgba(79,70,229,.15); }
    .btn-action-save:hover { background:#4338ca; border-color:#4338ca; color:#fff; transform:translateY(-1px); }
    .btn-action-cancel { border:2px solid #e2e8f0; color:#64748b; font-weight:600; font-size:.9rem; transition:all .2s; background:transparent; }
    .btn-action-cancel:hover { background:#f1f5f9; border-color:#cbd5e1; color:#334155; }

    /* ── Info badge ── */
    .current-info-badge {
        background:linear-gradient(135deg,#f0f4ff,#ede9fe); border:1.5px solid #c7d2fe;
        border-radius:.875rem; padding:.875rem 1.25rem;
        display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
    .cib-item { font-size:.8rem; color:#475569; }
    .cib-item strong { color:#001e40; }

    /* ── Input GeoJSON rute ── */
    .geojson-textarea {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.85rem;
        line-height: 1.6;
        color: #475569;
        background-color: #f8fafc;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 mb-5">
<div class="row justify-content-center">
<div class="col-lg-10">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Edit Data Jarak & Rute</h4>
            <p class="text-muted small mb-0">Ubah jarak, waktu tempuh, dan data GeoJSON rute.</p>
        </div>
        <a href="{{ route('jarak.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
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

    {{-- Info rekaman saat ini --}}
    <div class="current-info-badge mb-4">
        <div class="cib-item">
            <i class="bi bi-building-fill-check me-1 text-primary"></i>
            <strong>{{ $jarak->sekolah->nama_sekolah ?? '—' }}</strong>
        </div>
        <div class="cib-item" style="color:#94a3b8;">→</div>
        <div class="cib-item">
            <i class="bi bi-geo-alt-fill me-1 text-success"></i>
            <strong>{{ $jarak->wilayahDesa->nama_wilayah ?? '—' }}</strong>
        </div>
        <div class="cib-item ms-auto" style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <span><i class="bi bi-rulers me-1"></i><strong>{{ number_format($jarak->jarak, 2) }} km</strong></span>
            @if($jarak->walk_mnt !== null)
                <span><i class="bi bi-person-walking me-1"></i>{{ $jarak->walk_label }}</span>
            @endif
            @if($jarak->drive_mnt !== null)
                <span><i class="bi bi-car-front me-1"></i>{{ $jarak->drive_label }}</span>
            @endif
            @if($jarak->boat_mnt !== null)
                <span><i class="bi bi-water me-1"></i>{{ $jarak->boat_label }}</span>
            @endif
            @if($jarak->route_geojson)
                <span style="color:#16a34a;"><i class="bi bi-check-circle-fill me-1"></i>Ada rute</span>
            @else
                <span style="color:#94a3b8;"><i class="bi bi-x-circle me-1"></i>Belum ada rute</span>
            @endif
        </div>
    </div>

    <div class="card modern-card shadow-sm">
        <div class="card-header bg-white py-4 border-0 rounded-top-4">
            <h5 class="form-header-title fw-bold mb-0">Ubah Nilai Matriks Jarak</h5>
        </div>
        <div class="card-body p-4 pt-2">
        <form action="{{ route('jarak.update', $jarak->id) }}" method="POST" id="form-jarak">
            @csrf
            @method('PUT')

            {{-- ══ SEKSI 1: Relasi ══ --}}
            <p class="section-label mt-2"><i class="bi bi-link-45deg"></i> Data Relasi</p>
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label class="form-label form-label-custom mb-2">Nama Sekolah</label>
                    <select class="form-select form-select-custom @error('sekolah_id') is-invalid @enderror"
                            name="sekolah_id" id="sekolah_id" required>
                        @foreach($sekolahs as $s)
                            <option value="{{ $s->id }}"
                                {{ old('sekolah_id', $jarak->sekolah_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_sekolah }}
                            </option>
                        @endforeach
                    </select>
                    @error('sekolah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-custom mb-2">ROI Area / Desa</label>
                    <select class="form-select form-select-custom @error('wilayah_id') is-invalid @enderror"
                            name="wilayah_id" id="wilayah_id" required>
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id }}"
                                {{ old('wilayah_id', $jarak->wilayah_id) == $w->id ? 'selected' : '' }}>
                                {{ $w->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>
                    @error('wilayah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- ══ SEKSI 2: Mode ══ --}}
            <div class="section-sep"></div>
            <p class="section-label"><i class="bi bi-signpost-split"></i> Mode Transportasi</p>
            @php $currentMode = old('mode_transport', $jarak->mode_transport ?? 'darat'); @endphp
            <input type="hidden" name="mode_transport" id="mode_transport" value="{{ $currentMode }}">
            <div class="mode-toggle-wrap mb-1">
                <button type="button"
                    class="mode-toggle-btn {{ $currentMode==='darat'?'selected-darat':'' }}"
                    data-mode="darat" onclick="setMode('darat')">
                    <i class="bi bi-truck me-1"></i> Darat
                </button>
                <button type="button"
                    class="mode-toggle-btn {{ $currentMode==='multimoda'?'selected-multimoda':'' }}"
                    data-mode="multimoda" onclick="setMode('multimoda')">
                    <i class="bi bi-water me-1"></i> Multimoda (Darat + Perahu)
                </button>
            </div>

            {{-- ══ SEKSI 3: Jarak ══ --}}
            <div class="section-sep"></div>
            <p class="section-label"><i class="bi bi-rulers"></i> Data Jarak</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-custom mb-2">Jarak Total</label>
                    <div class="input-group">
                        <input type="number" step="0.001" min="0" id="jarak" name="jarak"
                               class="form-control form-control-custom @error('jarak') is-invalid @enderror"
                               value="{{ old('jarak', $jarak->jarak) }}"
                               style="border-top-right-radius:0;border-bottom-right-radius:0;"
                               required oninput="autoCalcIfEmpty()">
                        <span class="input-group-text input-group-text-custom">km</span>
                        @error('jarak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6" id="panel-jarak-laut"
                     style="display:{{ $currentMode==='multimoda'?'':'none' }};">
                    <label class="form-label form-label-custom mb-2">Jarak Segmen Laut</label>
                    <div class="input-group">
                        <input type="number" step="0.001" min="0" id="jarak_laut" name="jarak_laut"
                               class="form-control form-control-custom"
                               value="{{ old('jarak_laut', $jarak->jarak_laut) }}"
                               style="border-top-right-radius:0;border-bottom-right-radius:0;"
                               oninput="autoCalcIfEmpty()">
                        <span class="input-group-text input-group-text-custom">km</span>
                    </div>
                    <p class="calc-hint">Segmen yang ditempuh dengan perahu saja.</p>
                </div>
            </div>

            {{-- ══ SEKSI 4: Waktu Tempuh ══ --}}
            <div class="section-sep"></div>
            <p class="section-label"><i class="bi bi-stopwatch"></i> Estimasi Waktu Tempuh
                <span style="font-size:.68rem;font-weight:500;text-transform:none;letter-spacing:0;">(edit manual jika perlu override)</span>
            </p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label form-label-custom mb-2"><i class="bi bi-person-walking me-1"></i>Jalan Kaki</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" id="walk_mnt" name="walk_mnt"
                               class="form-control form-control-custom"
                               value="{{ old('walk_mnt', $jarak->walk_mnt) }}" placeholder="—"
                               style="border-top-right-radius:0;border-bottom-right-radius:0;">
                        <span class="input-group-text input-group-text-custom">mnt</span>
                    </div>
                    <p class="calc-hint">÷ 5 km/jam × 60</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-custom mb-2"><i class="bi bi-car-front me-1"></i>Berkendara</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" id="drive_mnt" name="drive_mnt"
                               class="form-control form-control-custom"
                               value="{{ old('drive_mnt', $jarak->drive_mnt) }}" placeholder="—"
                               style="border-top-right-radius:0;border-bottom-right-radius:0;">
                        <span class="input-group-text input-group-text-custom">mnt</span>
                    </div>
                    <p class="calc-hint">÷ 30 km/jam × 60</p>
                </div>
                <div class="col-md-4" id="panel-multimoda"
                     style="display:{{ $currentMode==='multimoda'?'':'none' }};">
                    <label class="form-label form-label-custom mb-2"><i class="bi bi-water me-1"></i>Perahu</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" id="boat_mnt" name="boat_mnt"
                               class="form-control form-control-custom"
                               value="{{ old('boat_mnt', $jarak->boat_mnt) }}" placeholder="—"
                               style="border-top-right-radius:0;border-bottom-right-radius:0;">
                        <span class="input-group-text input-group-text-custom">mnt</span>
                    </div>
                    <p class="calc-hint">÷ 25 km/jam × 60</p>
                </div>
            </div>

            {{-- ══ SEKSI 5: Rute GeoJSON ══ --}}
            <div class="section-sep"></div>
            <p class="section-label"><i class="bi bi-map"></i> Rute GeoJSON
                <span style="font-size:.68rem;font-weight:500;text-transform:none;letter-spacing:0;">
                    (opsional — tempel teks GeoJSON jalur rute, misalnya hasil export dari QGIS)
                </span>
            </p>

            <textarea class="form-control form-control-custom geojson-textarea @error('route_geojson') is-invalid @enderror"
                      id="route_geojson" name="route_geojson" rows="8"
                      placeholder='Contoh: {"type": "LineString", "coordinates": [[122.65, -3.93], [122.66, -3.94]]}'>{{ old('route_geojson', $jarak->route_geojson) }}</textarea>
            @error('route_geojson') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <p class="calc-hint mt-2">
                <i class="bi bi-lightbulb me-1"></i>
                Format geometry GeoJSON standar (LineString/MultiLineString). Kosongkan untuk menghapus rute.
            </p>

            {{-- ══ Footer ══ --}}
            <div class="d-flex gap-2 justify-content-end pt-4 mt-2 border-top border-light">
                <a href="{{ route('jarak.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill">Batal</a>
                <button type="submit" class="btn btn-action-save px-4 py-2 rounded-pill">
                    <i class="bi bi-check-lg me-1"></i> Update Data
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
var currentMode = '{{ $currentMode }}';

// ─── Mode Toggle ─────────────────────────────────────────────────────────────
function setMode(mode) {
    document.getElementById('mode_transport').value = mode;
    document.querySelectorAll('.mode-toggle-btn').forEach(function(b) {
        b.classList.remove('selected-darat','selected-multimoda');
    });
    var sel = document.querySelector('.mode-toggle-btn[data-mode="'+mode+'"]');
    if (sel) sel.classList.add('selected-'+mode);
    var isMulti = mode === 'multimoda';
    document.getElementById('panel-multimoda').style.display  = isMulti ? '' : 'none';
    document.getElementById('panel-jarak-laut').style.display = isMulti ? '' : 'none';
    if (!isMulti) {
        document.getElementById('boat_mnt').value   = '';
        document.getElementById('jarak_laut').value = '';
    }
}

// ─── Auto-calc (hanya jika field kosong) ─────────────────────────────────────
function autoCalcIfEmpty() {
    var jarak     = parseFloat(document.getElementById('jarak').value)      || 0;
    var jarakLaut = parseFloat(document.getElementById('jarak_laut').value) || 0;
    var mode      = document.getElementById('mode_transport').value;
    var wF = document.getElementById('walk_mnt');
    var dF = document.getElementById('drive_mnt');
    var bF = document.getElementById('boat_mnt');
    if (wF.value === '' && jarak > 0)
        wF.value = Math.round((jarak / 5) * 60 * 100) / 100;
    if (dF.value === '' && jarak > 0)
        dF.value = Math.round((jarak / 30) * 60 * 100) / 100;
    if (mode === 'multimoda' && bF.value === '' && jarakLaut > 0)
        bF.value = Math.round((jarakLaut / 25) * 60 * 100) / 100;
}
</script>
@endpush