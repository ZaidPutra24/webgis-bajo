@extends('layouts.admin')

@section('title', 'School Accessibility Dashboard')
@section('page-title', 'School Accessibility Dashboard')

@section('content')
@php
    // ── Helpers tampilan ──
    $fmt = function ($m) {
        if ($m === null) return '–';
        $m = (float) $m;
        if ($m >= 90) {
            $total = (int) round($m);
            return intdiv($total, 60) . ' h ' . ($total % 60) . ' m';
        }
        return rtrim(rtrim(number_format($m, 1), '0'), '.') . ' min';
    };
    $basisWaktu = fn ($c) => $moda === 'kendaraan'
        ? ($c['drive_mnt'] ?? $c['walk_mnt'])
        : ($c['walk_mnt'] ?? $c['drive_mnt']);
    $tone = function ($c) use ($ambang, $basisWaktu) {
        if (!$c) return 'nodata';
        $w = $basisWaktu($c);
        if ($w === null) return 'nodata';
        return $w > $ambang ? 'critical' : ($w > $ambang / 2 ? 'moderate' : 'good');
    };
    $modaLabel = $moda === 'kendaraan' ? 'motor vehicle' : 'walking';
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    .modern-card { border-radius: 1.25rem; border: none; box-shadow: 0 8px 30px rgba(0,0,0,0.04); background: #ffffff; }
    .table-header-title { position: relative; padding-left: 1rem; color: #2b3445; font-size: 1.1rem; }
    .table-header-title::before {
        content: ''; position: absolute; left: 0; top: 20%; height: 60%; width: 5px;
        background: linear-gradient(135deg, #4F46E5 0%, #06b6d4 100%); border-radius: 5px;
    }
    .stat-card { border-radius: 1.1rem; border: none; background: #fff; box-shadow: 0 6px 20px rgba(0,0,0,.04); padding: 1.15rem 1.3rem; display: flex; align-items: center; gap: 14px; height: 100%; }
    .stat-icon { width: 44px; height: 44px; border-radius: .85rem; display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0; }
    .stat-value { font-size: 1.32rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
    .stat-label { font-size: .74rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .stat-sub { font-size: .74rem; color: #64748b; }

    .ctrl-form { display: flex; align-items: flex-end; gap: .9rem; flex-wrap: wrap; }
    .ctrl-form label { display: block; font-size: .74rem; font-weight: 600; color: #64748b; margin-bottom: .25rem; }
    .ctrl-form input, .ctrl-form select { border: 2px solid #e2e8f0; border-radius: .65rem; padding: .45rem .8rem; font-size: .88rem; font-weight: 600; color: #334155; background: #f8fafc; }
    .ctrl-form input { width: 110px; }
    .ctrl-form input:focus, .ctrl-form select:focus { outline: none; border-color: #4F46E5; }

    .filter-pills { display: flex; gap: .5rem; flex-wrap: wrap; }
    .filter-pill { border: 1.5px solid #e2e8f0; border-radius: 2rem; padding: .25rem .85rem; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s; background: transparent; color: #64748b; }
    .filter-pill:hover, .filter-pill.active { background: #4F46E5; border-color: #4F46E5; color: #fff; }

    /* ── Travel-time matrix ── */
    .matrix-table { border-collapse: separate; border-spacing: 0 6px; width: 100%; }
    .matrix-table thead th { font-size: .74rem; font-weight: 600; color: #64748b; padding: .3rem .5rem; text-align: center; white-space: nowrap; }
    .matrix-table thead th:first-child { text-align: left; }
    .matrix-table td { padding: 0 4px; vertical-align: middle; }
    .matrix-table td:first-child { padding-right: 12px; min-width: 170px; }
    .vname { font-weight: 700; color: #1e293b; font-size: .92rem; }
    .vsub  { font-size: .74rem; color: #94a3b8; }
    .tile { border-radius: .8rem; padding: .5rem .55rem; text-align: center; min-width: 108px; border: 1.5px solid transparent; line-height: 1.25; }
    .tile .t-main { font-weight: 700; font-size: .95rem; }
    .tile .t-sub  { font-size: .72rem; opacity: .85; }
    .tile .t-school { font-size: .68rem; opacity: .75; max-width: 120px; margin: 2px auto 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .tile-good     { background: #ecfdf3; color: #166534; border-color: #bbf7d0; }
    .tile-moderate { background: #fffbeb; color: #92400e; border-color: #fde68a; }
    .tile-critical { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .tile-nodata   { background: #f8fafc; color: #94a3b8; border: 1.5px dashed #cbd5e1; }
    .tile .bi-water { color: #2563eb; }

    .badge-status { border-radius: .5rem; padding: .2rem .6rem; font-size: .72rem; font-weight: 700; display: inline-block; white-space: nowrap; }
    .badge-status.critical { background: rgba(220,38,38,.1); color: #dc2626; border: 1px solid #dc2626; }
    .badge-status.moderate { background: rgba(217,119,6,.1); color: #b45309; border: 1px solid #d97706; }
    .badge-status.good     { background: rgba(34,197,94,.1); color: #15803d; border: 1px solid #22c55e; }
    .badge-status.nodata   { background: #f1f5f9; color: #64748b; border: 1px dashed #94a3b8; }
    .note-chip { display: inline-block; border-radius: .45rem; padding: .12rem .5rem; font-size: .7rem; font-weight: 600; margin: 1px 3px 1px 0; }
    .note-chip.boat { background: #eff6ff; color: #1d4ed8; }
    .note-chip.far  { background: #fef2f2; color: #b91c1c; }
    .note-chip.gap  { background: #f1f5f9; color: #64748b; }

    #accessMap { height: 520px; width: 100%; border-radius: 0 0 1.25rem 1.25rem; }
    .map-legend { position: absolute; bottom: 14px; left: 14px; z-index: 999; background: rgba(255,255,255,.96); border-radius: .85rem; padding: .75rem .9rem; box-shadow: 0 6px 20px rgba(0,0,0,.12); font-size: .78rem; }
    .map-legend-title { font-weight: 700; color: #1e293b; margin-bottom: .4rem; font-size: .74rem; }
    .map-legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: .28rem; color: #475569; }
    .legend-dot { width: 11px; height: 11px; border-radius: 50%; flex-shrink: 0; border: 1.5px solid rgba(0,0,0,.15); }
    .legend-line { width: 22px; height: 0; flex-shrink: 0; border-top: 3px solid; }
    .legend-line.dashed { border-top-style: dashed; }

    .alert-list { list-style: none; padding: 0; margin: 0; }
    .alert-list li { padding: .6rem 0; border-bottom: 1px solid #f1f5f9; font-size: .88rem; color: #334155; }
    .alert-list li:last-child { border-bottom: 0; }
    .alert-list .a-meta { font-size: .76rem; color: #94a3b8; }
    .chart-wrap { position: relative; height: 320px; }
    .method-note { font-size: .82rem; color: #64748b; line-height: 1.6; }
    .method-note li { margin-bottom: .35rem; }
</style>

{{-- ── Controls ── --}}
<div class="modern-card p-3 p-md-4 mb-4">
    <form method="GET" action="{{ route('aksesibilitas.index') }}" class="ctrl-form">
        <div>
            <label for="ambang">"Too far" threshold (minutes)</label>
            <input type="number" id="ambang" name="ambang" min="5" max="240" step="5" value="{{ rtrim(rtrim(number_format($ambang, 1), '0'), '.') }}">
        </div>
        <div>
            <label for="moda">Judge travel time by</label>
            <select id="moda" name="moda">
                <option value="jalan_kaki" @selected($moda === 'jalan_kaki')>Walking (no vehicle)</option>
                <option value="kendaraan"  @selected($moda === 'kendaraan')>Motor vehicle</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="border-radius:.65rem; font-weight:600;">
            <i class="bi bi-funnel-fill me-1"></i> Apply
        </button>
        <div class="text-muted small ms-md-auto" style="max-width: 420px;">
            A village is <b>Critical</b> when its worst school level takes more than {{ $fmt($ambang) }} by {{ $modaLabel }};
            <b>Moderate</b> above {{ $fmt($ambang / 2) }}.
        </div>
    </form>
</div>

{{-- ── Summary cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2;color:#dc2626;"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="stat-value">{{ $ringkasan['desa_lebih_ambang'] }} <span class="fs-6 text-muted">/ {{ $ringkasan['total_desa'] }}</span></div>
                <div class="stat-label">Villages &gt; {{ $fmt($ambang) }}</div>
                <div class="stat-sub">{{ number_format($ringkasan['anak_terdampak']) }} children in affected age groups</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-water"></i></div>
            <div>
                <div class="stat-value">{{ $ringkasan['desa_butuh_perahu'] }}</div>
                <div class="stat-label">Villages needing a boat</div>
                <div class="stat-sub">for at least one school level</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eef2ff;color:#4F46E5;"><i class="bi bi-signpost-split-fill"></i></div>
            <div>
                <div class="stat-value">{{ $ringkasan['desa_tanpa_darat'] }}</div>
                <div class="stat-label">No land-only route</div>
                <div class="stat-sub">for at least one school level</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f1f5f9;color:#64748b;"><i class="bi bi-question-diamond-fill"></i></div>
            <div>
                <div class="stat-value">{{ $ringkasan['desa_data_kurang'] }}</div>
                <div class="stat-label">Villages with data gaps</div>
                <div class="stat-sub">{{ number_format($totalRute) }} route legs recorded</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Travel-time matrix ── --}}
<div class="modern-card p-3 p-md-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="table-header-title fw-bold mb-0">Travel time to the nearest school, by level</h5>
        <div class="filter-pills" id="statusPills">
            <button type="button" class="filter-pill active" data-status="all">All</button>
            <button type="button" class="filter-pill" data-status="critical">Critical</button>
            <button type="button" class="filter-pill" data-status="moderate">Moderate</button>
            <button type="button" class="filter-pill" data-status="good">Good</button>
            <button type="button" class="filter-pill" data-status="no_data">No route data</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="matrix-table" id="matrixTable">
            <thead>
                <tr>
                    <th>Village</th>
                    @foreach($jenjangList as $j)
                        <th>{{ $j }}</th>
                    @endforeach
                    <th style="text-align:left;">Flags</th>
                </tr>
            </thead>
            <tbody>
            @foreach($desa as $d)
                <tr class="matrix-row" data-status="{{ $d['status'] }}">
                    <td>
                        <div class="vname">{{ $d['nama_wilayah'] }}</div>
                        <span class="badge-status {{ $d['status'] === 'no_data' ? 'nodata' : $d['status'] }}">{{ $d['status_label'] }}</span>
                        @if($d['status'] !== 'no_data' && !empty($d['jenjang_tanpa_data']))
                            <div class="vsub mt-1"><i class="bi bi-exclamation-circle"></i> incomplete data</div>
                        @endif
                        @if($d['anak_terdampak'] > 0)
                            <div class="vsub mt-1">{{ number_format($d['anak_terdampak']) }} children affected</div>
                        @endif
                    </td>
                    @foreach($jenjangList as $j)
                        @php $c = $d['jenjang'][$j] ?? null; $t = $tone($c); @endphp
                        <td>
                            <div class="tile tile-{{ $t }}"
                                 @if($c) title="{{ $c['nama_sekolah'] }} — {{ number_format($c['jarak_km'], 2) }} km" @else title="No recorded route to a {{ $j }} school" @endif>
                                @if($c)
                                    <div class="t-main">{{ $fmt($basisWaktu($c)) }}</div>
                                    <div class="t-sub">
                                        @if($moda === 'kendaraan')
                                            <i class="bi bi-person-walking"></i> {{ $fmt($c['walk_mnt']) }}
                                        @else
                                            <i class="bi bi-car-front-fill"></i> {{ $fmt($c['drive_mnt']) }}
                                        @endif
                                        @if($c['butuh_perahu'])
                                            &nbsp;<i class="bi bi-water"></i> {{ $fmt($c['boat_mnt']) }}
                                        @endif
                                    </div>
                                    <div class="t-school">{{ $c['nama_sekolah'] }}</div>
                                @else
                                    <div class="t-main">–</div>
                                    <div class="t-sub">no route data</div>
                                @endif
                            </div>
                        </td>
                    @endforeach
                    <td style="min-width:180px;">
                        @foreach($d['jenjang_lebih_ambang'] as $j)
                            <span class="note-chip far">{{ $j }} &gt; {{ $fmt($ambang) }}</span>
                        @endforeach
                        @foreach($d['jenjang_butuh_perahu'] as $j)
                            <span class="note-chip boat"><i class="bi bi-water"></i> {{ $j }} by boat</span>
                        @endforeach
                        @foreach($d['jenjang_tanpa_data'] as $j)
                            <span class="note-chip gap">{{ $j }}: no data</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="text-muted small mt-2" id="matrixCount"></div>
    <div class="text-muted small mt-1">
        Large figure = {{ $modaLabel }} time; small line = the other mode ({{ $moda === 'kendaraan' ? 'walking' : 'vehicle' }}) to the same school, plus the boat leg where a crossing is needed. Hover a tile for the school name and distance.
    </div>
</div>

{{-- ── Map ── --}}
<div class="modern-card mb-4" style="overflow:hidden;">
    <div class="p-3 p-md-4 pb-2">
        <h5 class="table-header-title fw-bold mb-1">Critical route per village</h5>
        <div class="text-muted small">The route drawn is the one to each village's hardest-to-reach school level (the level behind its status colour).</div>
    </div>
    <div style="position:relative;">
        <div id="accessMap"></div>
        <div class="map-legend">
            <div class="map-legend-title">Village status</div>
            <div class="map-legend-item"><span class="legend-dot" style="background:#dc2626"></span> Critical</div>
            <div class="map-legend-item"><span class="legend-dot" style="background:#d97706"></span> Moderate</div>
            <div class="map-legend-item"><span class="legend-dot" style="background:#16a34a"></span> Good</div>
            <div class="map-legend-item"><span class="legend-dot" style="background:#94a3b8"></span> No route data</div>
            <div class="map-legend-title mt-2">Route</div>
            <div class="map-legend-item"><span class="legend-line" style="border-color:#4F46E5"></span> On foot / road</div>
            <div class="map-legend-item"><span class="legend-line dashed" style="border-color:#2563eb"></span> Boat crossing</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- ── Chart ── --}}
    <div class="col-lg-7">
        <div class="modern-card p-3 p-md-4 h-100">
            <h5 class="table-header-title fw-bold mb-3">Worst school level per village</h5>
            <div class="chart-wrap"><canvas id="timeChart"></canvas></div>
            <div class="text-muted small mt-2">Bars show travel time to the school level with the longest trip; the dashed line is your threshold.</div>
        </div>
    </div>

    {{-- ── Per-mode summary ── --}}
    <div class="col-lg-5">
        <div class="modern-card p-3 p-md-4 h-100">
            <h5 class="table-header-title fw-bold mb-3">Summary by mode</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-2">
                    <thead>
                        <tr class="text-muted small"><th>Mode</th><th class="text-end">Trips</th><th class="text-end">Average</th><th class="text-end">Median</th><th class="text-end">Longest</th></tr>
                    </thead>
                    <tbody>
                    @foreach([['jalan_kaki','Walking','bi-person-walking'],['kendaraan','Motor vehicle','bi-car-front-fill'],['perahu','Boat leg','bi-water']] as [$key,$label,$icon])
                        @php $m = $perModa[$key]; @endphp
                        <tr>
                            <td><i class="bi {{ $icon }} me-1 text-muted"></i>{{ $label }}</td>
                            <td class="text-end">{{ $m['n'] }}</td>
                            <td class="text-end">{{ $fmt($m['rata']) }}</td>
                            <td class="text-end">{{ $fmt($m['median']) }}</td>
                            <td class="text-end fw-semibold">{{ $fmt($m['maks']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-muted small">
                Each trip is one village × school-level pair (nearest school). "Boat leg" counts only trips that include a crossing and shows the crossing time alone.
            </div>
        </div>
    </div>
</div>

{{-- ── Attention lists ── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="modern-card p-3 p-md-4 h-100">
            <h5 class="table-header-title fw-bold mb-3">Beyond {{ $fmt($ambang) }} by {{ $modaLabel }}</h5>
            <ul class="alert-list">
                @forelse($desa->filter(fn ($d) => !empty($d['jenjang_lebih_ambang'])) as $d)
                    @foreach($d['jenjang_lebih_ambang'] as $j)
                        @php $c = $d['jenjang'][$j]; @endphp
                        <li>
                            <b>{{ $d['nama_wilayah'] }}</b> → {{ $j }}: {{ $fmt($basisWaktu($c)) }}
                            <div class="a-meta">{{ $c['nama_sekolah'] }} · {{ number_format($c['jarak_km'], 1) }} km
                                @if(($d['penduduk_jenjang'][$j]['penduduk'] ?? null) !== null)
                                    · {{ $d['penduduk_jenjang'][$j]['penduduk'] }} children of {{ $j }} age
                                @endif
                            </div>
                        </li>
                    @endforeach
                @empty
                    <li class="text-muted">No village exceeds this threshold by {{ $modaLabel }} at the moment.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="modern-card p-3 p-md-4 h-100">
            <h5 class="table-header-title fw-bold mb-3">Boat-dependent access</h5>
            <ul class="alert-list">
                @forelse($desa->filter(fn ($d) => !empty($d['jenjang_butuh_perahu'])) as $d)
                    @foreach($d['jenjang_butuh_perahu'] as $j)
                        @php $c = $d['jenjang'][$j]; $tanpaDarat = in_array($j, $d['jenjang_tanpa_darat'], true); @endphp
                        <li>
                            <b>{{ $d['nama_wilayah'] }}</b> → {{ $j }}: {{ $fmt($c['walk_mnt']) }} on foot incl. {{ $fmt($c['boat_mnt']) }} crossing
                            <div class="a-meta">{{ $c['nama_sekolah'] }}
                                · {{ $tanpaDarat ? 'no land-only route recorded' : 'a land-only alternative exists' }}
                            </div>
                        </li>
                    @endforeach
                @empty
                    <li class="text-muted">No village depends on a boat crossing.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

{{-- ── Method & caveats ── --}}
<div class="modern-card p-3 p-md-4 mb-4">
    <h5 class="table-header-title fw-bold mb-3">How to read this</h5>
    <ul class="method-note mb-0">
        <li>Times come from the recorded field routes in <b>Distance Analysis</b> (one row per leg and mode). Island routes are chained: village → jetty, boat crossing, jetty → school.</li>
        <li>The nearest school of each level (PAUD, SD, SMP, SMA/MA/SMK) is the one with the shortest <b>walking</b> time; the vehicle time shown is for that same school.</li>
        <li><b>Boat waiting time and sailing schedules are not included</b> — a crossing that shows a few minutes can still cost much more in practice.</li>
        <li>There is deliberately <b>no straight-line fallback</b> here: for coastal and island villages a straight line crosses open water and would understate the real trip. Levels without a recorded route show "no route data" instead of a guess.</li>
        <li>Walking time to a school 10+ km away is a theoretical worst case (children without any transport), not a realistic daily commute — switch to <b>Motor vehicle</b> above to compare.</li>
    </ul>
</div>
@endsection

@push('scripts')
<script crossorigin="" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const villages  = @json($desa->values());
    const ambang    = @json($ambang);
    const moda      = @json($moda);
    const statusCol = { kritis: '#dc2626', sedang: '#d97706', baik: '#16a34a', no_data: '#94a3b8' };
    const shortName = n => n.replace(/^Desa\s+/i, '');
    const fmtMin = m => {
        if (m === null || m === undefined) return '–';
        if (m >= 90) { const t = Math.round(m); return Math.floor(t / 60) + ' h ' + (t % 60) + ' m'; }
        return (Math.round(m * 10) / 10) + ' min';
    };

    // ── Status filter on matrix ──
    const rows = Array.from(document.querySelectorAll('#matrixTable .matrix-row'));
    const countEl = document.getElementById('matrixCount');
    const statusMap = { critical: 'kritis', moderate: 'sedang', good: 'baik', no_data: 'no_data' };
    function applyFilter(key) {
        let visible = 0;
        rows.forEach(r => {
            const show = key === 'all' || r.dataset.status === statusMap[key];
            r.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        countEl.textContent = visible + ' of ' + rows.length + ' villages';
    }
    document.querySelectorAll('#statusPills .filter-pill').forEach(p => {
        p.addEventListener('click', () => {
            document.querySelectorAll('#statusPills .filter-pill').forEach(x => x.classList.remove('active'));
            p.classList.add('active');
            applyFilter(p.dataset.status);
        });
    });
    applyFilter('all');

    // ── Map ──
    const mapEl = document.getElementById('accessMap');
    if (mapEl && window.L) {
        // View awal WAJIB diset sebelum layer vektor apa pun ditambahkan. Tanpa ini,
        // Leaflet menunda semua layer sampai fitBounds() dan L.geoJSON() (yang punya
        // anak Polyline) dieksekusi SEBELUM renderer SVG-nya siap -> TypeError
        // "reading 'min'" dan seluruh handler berhenti (peta kosong, chart kosong).
        const map = L.map('accessMap').setView([-4.0, 122.5], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        const bounds = [];

        villages.forEach(v => {
            (v.rute || []).forEach(leg => {
                const boat = leg.moda === 'perahu';
                const layer = L.geoJSON({ type: 'Feature', geometry: leg.geojson }, {
                    style: { color: boat ? '#2563eb' : '#4F46E5', weight: boat ? 4 : 3, opacity: .85, dashArray: boat ? '8,8' : null }
                }).addTo(map);
                bounds.push(layer.getBounds().getSouthWest(), layer.getBounds().getNorthEast());
            });

            if (v.sekolah_kritis) {
                L.circleMarker([v.sekolah_kritis.lat, v.sekolah_kritis.lng], {
                    radius: 5, color: '#334155', weight: 1.5, fillColor: '#f8fafc', fillOpacity: 1
                }).addTo(map).bindPopup('<b>' + v.sekolah_kritis.nama + '</b><br>Destination for ' + v.nama_wilayah);
                bounds.push([v.sekolah_kritis.lat, v.sekolah_kritis.lng]);
            }

            if (v.lat !== null && v.lng !== null) {
                let html = '<b>' + v.nama_wilayah + '</b><br>' + v.status_label;
                if (v.terburuk_jenjang) {
                    html += '<br>Hardest level: ' + v.terburuk_jenjang +
                            '<br>Walking ' + fmtMin(v.terburuk_walk_mnt) +
                            ' · Vehicle ' + fmtMin(v.terburuk_drive_mnt);
                    if (v.terburuk_boat_mnt !== null) html += ' · Boat leg ' + fmtMin(v.terburuk_boat_mnt);
                }
                if (v.jenjang_tanpa_data.length) html += '<br><span style="color:#64748b">No data: ' + v.jenjang_tanpa_data.join(', ') + '</span>';
                L.circleMarker([v.lat, v.lng], {
                    radius: 9, color: '#1e293b', weight: 1.5, fillColor: statusCol[v.status], fillOpacity: .95
                }).addTo(map).bindPopup(html);
                bounds.push([v.lat, v.lng]);
            }
        });

        if (bounds.length) map.fitBounds(bounds, { padding: [30, 30] });
        else map.setView([-4.0, 122.5], 8);
    }

    // ── Chart ──
    const chartEl = document.getElementById('timeChart');
    const withData = villages.filter(v => v.status !== 'no_data');
    if (chartEl && window.Chart && withData.length) {
        const labels = withData.map(v => shortName(v.nama_wilayah) + ' (' + v.terburuk_jenjang + ')');
        new Chart(chartEl, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Walking', data: withData.map(v => v.terburuk_walk_mnt), backgroundColor: '#4F46E5', borderRadius: 6 },
                    { label: 'Motor vehicle', data: withData.map(v => v.terburuk_drive_mnt), backgroundColor: '#06b6d4', borderRadius: 6 },
                    { type: 'line', label: 'Threshold (' + ambang + ' min)', data: withData.map(() => ambang),
                      borderColor: '#dc2626', borderDash: [6, 6], borderWidth: 2, pointRadius: 0, fill: false }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: c => c.dataset.label + ': ' + fmtMin(c.parsed.y) } } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Minutes' } },
                          x: { ticks: { maxRotation: 40, minRotation: 0 } } }
            }
        });
    }
});
</script>
@endpush