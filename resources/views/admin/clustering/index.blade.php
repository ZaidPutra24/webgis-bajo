@extends('layouts.admin')

@section('title', 'K-Means Village Clustering')
@section('page-title', 'Village Clustering & New School Location Suggestions')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    .modern-card { border-radius: 1.25rem; border: none; box-shadow: 0 8px 30px rgba(0,0,0,0.04); background: #ffffff; }
    .table-header-title { position: relative; padding-left: 1rem; color: #2b3445; font-size: 1.1rem; }
    .table-header-title::before {
        content: ''; position: absolute; left: 0; top: 20%; height: 60%; width: 5px;
        background: linear-gradient(135deg, #4F46E5 0%, #06b6d4 100%); border-radius: 5px;
    }
    .custom-table thead th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding-top: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0; }
    .custom-table tbody td { padding-top: .9rem; padding-bottom: .9rem; color: #334155; }

    .search-wrapper { position: relative; }
    .search-wrapper .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .search-input { border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 0.55rem 1rem 0.55rem 2.75rem; font-size: 0.9rem; color: #334155; background-color: #f8fafc; transition: all 0.2s; width: 260px; }
    .search-input:focus { border-color: #4F46E5; background-color: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }
    .search-count { font-size: 0.8rem; color: #94a3b8; }
    .filter-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .filter-pill { border: 1.5px solid #e2e8f0; border-radius: 2rem; padding: 0.25rem 0.85rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.15s; background: transparent; color: #64748b; }
    .filter-pill:hover, .filter-pill.active { background: #4F46E5; border-color: #4F46E5; color: #fff; }

    .stat-card { border-radius: 1.1rem; border: none; background: #fff; box-shadow: 0 6px 20px rgba(0,0,0,.04); padding: 1.15rem 1.3rem; display: flex; align-items: center; gap: 14px; height: 100%; }
    .stat-icon { width: 44px; height: 44px; border-radius: .85rem; display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0; }
    .stat-value { font-size: 1.32rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
    .stat-label { font-size: .74rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }

    .badge-prioritas-tinggi { background: rgba(220,38,38,.1); color: #dc2626; border: 1px solid #dc2626; }
    .badge-prioritas-sedang { background: rgba(217,119,6,.1); color: #b45309; border: 1px solid #d97706; }
    .badge-prioritas-rendah { background: rgba(34,197,94,.1); color: #15803d; border: 1px solid #22c55e; }

    #clusterMap { height: 560px; width: 100%; border-radius: 0 0 1.25rem 1.25rem; }
    .map-legend {
        position: absolute; bottom: 14px; left: 14px; z-index: 999; background: rgba(255,255,255,.96);
        border-radius: .85rem; padding: .75rem .9rem; box-shadow: 0 6px 20px rgba(0,0,0,.12); font-size: .78rem; max-width: 220px;
    }
    .map-legend-title { font-weight: 700; color: #1e293b; margin-bottom: .4rem; font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; }
    .map-legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: .28rem; color: #475569; }
    .legend-dot { width: 11px; height: 11px; border-radius: 50%; flex-shrink: 0; border: 1.5px solid rgba(0,0,0,.15); }
    .legend-star { width: 11px; height: 11px; flex-shrink: 0; color: #111827; }

    .k-select-form { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
    .k-select-form select { border: 2px solid #e2e8f0; border-radius: .65rem; padding: .45rem .8rem; font-size: .88rem; font-weight: 600; color: #334155; background: #f8fafc; }
    .k-select-form select:focus { outline: none; border-color: #4F46E5; }

    .cluster-swatch-card { border-radius: 1rem; border: 1px solid #eef0f4; padding: 1rem 1.1rem; height: 100%; background: #fff; }
    .cluster-swatch-dot { width: 14px; height: 14px; border-radius: 50%; display: inline-block; margin-right: 8px; }

    .rekom-card { border-radius: 1rem; border: 1px solid #eef0f4; padding: 1.1rem 1.2rem; background: linear-gradient(180deg,#fff, #fbfbff); height: 100%; }
    .rekom-badge-desa { background: #eef2ff; color: #4338ca; border-radius: .5rem; padding: .18rem .55rem; font-size: .72rem; font-weight: 600; display: inline-block; margin: 2px 3px 2px 0; }

    .elbow-bar-row { display: flex; align-items: center; gap: .6rem; margin-bottom: .5rem; }
    .elbow-bar-label { width: 46px; font-size: .78rem; font-weight: 700; color: #475569; flex-shrink: 0; }
    .elbow-bar-track { flex: 1; background: #f1f5f9; border-radius: .4rem; height: 14px; overflow: hidden; }
    .elbow-bar-fill { height: 100%; background: linear-gradient(90deg,#4F46E5,#06b6d4); border-radius: .4rem; }
    .elbow-bar-value { width: 70px; text-align: right; font-size: .74rem; color: #94a3b8; flex-shrink: 0; }
</style>

<div class="container-fluid px-0 mb-5">

    <div class="mb-4">
        <h4 class="mb-1 text-dark fw-bold">Village Clustering (K-Means) &amp; New School Location Suggestions</h4>
        <p class="text-muted small mb-0" style="max-width: 900px;">
            Each village is represented as a data point built from three inputs: <strong>village coordinates</strong>
            (village hall point), <strong>coordinates of its nearest school</strong> (any level), and the
            <strong>number of students</strong> currently enrolled at that nearest school. K-Means groups villages that
            are geographically close to each other <em>and</em> share a similar school-distance / student-load pattern.
            Clusters whose villages are, on average, far from a school <em>and</em> whose nearest school is already
            crowded are flagged <span class="fw-semibold text-danger">Prioritas Tinggi</span> — these become the basis
            for the new-school-location suggestions below.
        </p>
    </div>

    {{-- ── K selector ── --}}
    <div class="card modern-card shadow-sm mb-4">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
            <form method="GET" action="{{ route('clustering.index') }}" class="k-select-form">
                <label for="kSelect" class="fw-semibold text-dark small mb-0">Number of clusters (k):</label>
                <select name="k" id="kSelect" onchange="this.form.submit()">
                    @for($opt = 2; $opt <= 8; $opt++)
                        <option value="{{ $opt }}" {{ $k == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endfor
                </select>
                <span class="text-muted small">
                    <i class="bi bi-info-circle"></i>
                    Not sure which k to pick? Check the elbow chart below — choose k where the curve starts to flatten.
                </span>
            </form>
            <div class="text-muted small">
                <i class="bi bi-cpu"></i> Algorithm: K-Means++ init &middot; z-score standardized features &middot; deterministic seed
            </div>
        </div>
    </div>

    @if($totalDianalisis < 2)
        <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4 p-4">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Not enough data to cluster</h6>
            <p class="mb-0 small">
                At least 2 villages need a village-hall coordinate and at least 1 school needs coordinates before
                clustering can run. Please complete the <strong>Village Areas</strong> and <strong>Schools</strong>
                data first.
            </p>
        </div>
    @else

    {{-- ── Stat cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(79,70,229,.1); color:#4F46E5;"><i class="bi bi-map"></i></div>
                <div>
                    <div class="stat-value">{{ $totalDianalisis }} / {{ $totalDesaSemua }}</div>
                    <div class="stat-label">Villages Analyzed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(6,182,212,.1); color:#06b6d4;"><i class="bi bi-diagram-3-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $clusterStats->count() }}</div>
                    <div class="stat-label">Clusters Formed (k={{ $k }})</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(220,38,38,.1); color:#dc2626;"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $clusterStats->where('kategori','Prioritas Tinggi')->sum('jumlah_desa') }}</div>
                    <div class="stat-label">High-Priority Villages</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(217,119,6,.12); color:#b45309;"><i class="bi bi-geo-alt-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $rekomendasi->count() }}</div>
                    <div class="stat-label">New School Site Suggestions</div>
                </div>
            </div>
        </div>
    </div>

    @if($desaTanpaData->isNotEmpty())
        <div class="alert alert-secondary border-0 shadow-sm rounded-3 mb-4 p-3 small">
            <i class="bi bi-info-circle me-1"></i>
            <strong>{{ $desaTanpaData->count() }} village(s) excluded</strong> from clustering (missing village-hall
            coordinates): {{ $desaTanpaData->implode(', ') }}.
        </div>
    @endif

    <div class="row g-4 mb-4">
        {{-- ── Map ── --}}
        <div class="col-lg-8">
            <div class="card modern-card shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 rounded-top-4">
                    <h5 class="table-header-title fw-bold mb-0">Cluster Map</h5>
                    <p class="text-muted small mb-0 ps-1 mt-1">
                        Colored dots = villages by cluster &middot; grey dots = schools &middot; dashed line = link to
                        nearest school &middot; gold stars = suggested new school locations.
                    </p>
                </div>
                <div class="card-body p-0 position-relative">
                    <div id="clusterMap"></div>
                    <div class="map-legend" id="mapLegend"></div>
                </div>
            </div>
        </div>

        {{-- ── Elbow chart ── --}}
        <div class="col-lg-4">
            <div class="card modern-card shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 rounded-top-4">
                    <h5 class="table-header-title fw-bold mb-0">Elbow Method</h5>
                    <p class="text-muted small mb-0 ps-1 mt-1">Inertia (WCSS) per k — pick k near the bend.</p>
                </div>
                <div class="card-body">
                    @php $maxInertia = $inertiaPerK->max('inertia') ?: 1; @endphp
                    @foreach($inertiaPerK as $row)
                        <div class="elbow-bar-row">
                            <div class="elbow-bar-label">k={{ $row['k'] }}</div>
                            <div class="elbow-bar-track">
                                <div class="elbow-bar-fill" style="width: {{ max(4, round($row['inertia']/$maxInertia*100)) }}%; {{ $row['k'] == $k ? 'background: linear-gradient(90deg,#dc2626,#f59e0b);' : '' }}"></div>
                            </div>
                            <div class="elbow-bar-value">{{ number_format($row['inertia'], 2) }}</div>
                        </div>
                    @endforeach
                    <p class="text-muted small mb-0 mt-2"><i class="bi bi-lightbulb"></i> Highlighted bar = currently selected k.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Cluster summary cards ── --}}
    <div class="mb-4">
        <h6 class="fw-bold text-dark mb-3">Cluster Summary</h6>
        <div class="row g-3">
            @foreach($clusterStats as $stat)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="cluster-swatch-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold text-dark">
                                <span class="cluster-swatch-dot" style="background: {{ $stat['warna'] }};"></span>
                                Cluster {{ $stat['cluster'] + 1 }}
                            </div>
                            <span class="badge rounded-pill px-2 py-1
                                {{ $stat['kategori'] === 'Prioritas Tinggi' ? 'badge-prioritas-tinggi' : ($stat['kategori'] === 'Prioritas Sedang' ? 'badge-prioritas-sedang' : 'badge-prioritas-rendah') }}">
                                {{ $stat['kategori'] }}
                            </span>
                        </div>
                        <div class="small text-muted mb-1">{{ $stat['jumlah_desa'] }} village(s)</div>
                        <div class="small text-muted mb-1">Avg. distance to school: <strong class="text-dark">{{ $stat['avg_jarak_km'] }} km</strong></div>
                        <div class="small text-muted mb-1">Avg. students at nearest school: <strong class="text-dark">{{ number_format($stat['avg_siswa']) }}</strong></div>
                        <div class="small text-muted">Priority score: <strong class="text-dark">{{ $stat['skor_prioritas'] }}</strong></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Recommendations ── --}}
    @if($rekomendasi->isNotEmpty())
    <div class="mb-4">
        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-geo-alt-fill text-danger"></i> Suggested New School Locations</h6>
        <p class="text-muted small mb-3">
            Computed by sub-clustering villages inside high-priority clusters purely by geographic position, so each
            suggested point sits close to the villages it would actually serve.
        </p>
        <div class="row g-3">
            @foreach($rekomendasi as $i => $r)
                <div class="col-md-6 col-lg-4">
                    <div class="rekom-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="bi bi-star-fill"></i> Suggestion {{ $i + 1 }}</span>
                        </div>
                        <div class="small text-muted mb-1">Coordinates</div>
                        <div class="fw-bold text-dark mb-2">{{ $r['lat'] }}, {{ $r['lng'] }}</div>
                        <div class="small text-muted mb-1">Would serve {{ $r['jumlah_desa'] }} village(s), current avg. distance {{ $r['avg_jarak_saat_ini_km'] }} km:</div>
                        <div class="mb-2">
                            @foreach($r['desa_terlayani'] as $nama)
                                <span class="rekom-badge-desa">{{ $nama }}</span>
                            @endforeach
                        </div>
                        <div class="small text-muted">Students currently affected (at overloaded nearest school): <strong class="text-dark">{{ number_format($r['total_siswa_terdampak']) }}</strong></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Table ── --}}
    <div class="card modern-card shadow-sm">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
                <h5 class="table-header-title fw-bold mb-0">Village Clustering Results</h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="search-count" id="searchCount"></span>
                    <div class="search-wrapper">
                        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" class="search-input" id="tableSearch" placeholder="Search village name..." autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="filter-pills ps-1">
                <button class="filter-pill active" data-filter-kategori="all">All</button>
                <button class="filter-pill" data-filter-kategori="Prioritas Tinggi">High Priority</button>
                <button class="filter-pill" data-filter-kategori="Prioritas Sedang">Medium Priority</button>
                <button class="filter-pill" data-filter-kategori="Prioritas Rendah">Low Priority</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width: 1150px;">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="3%" class="ps-4 text-center">No.</th>
                            <th width="14%">Village</th>
                            <th width="6%" class="text-center">Cluster</th>
                            <th width="12%" class="text-center">Priority</th>
                            <th width="17%">Nearest School</th>
                            <th width="10%" class="text-center">Distance (km)</th>
                            <th width="10%" class="text-center">Students at Nearest School</th>
                            <th width="10%" class="text-center">Schools in Village (ROI)</th>
                        </tr>
                    </thead>
                    <tbody id="clusterTableBody">
                        @forelse($desaHasil as $index => $p)
                        <tr class="searchable-row" data-kategori="{{ $p['kategori'] }}">
                            <td class="ps-4 text-center fw-semibold text-muted row-number">{{ $index + 1 }}</td>
                            <td><span class="fw-bold text-dark">{{ $p['nama_wilayah'] }}</span></td>
                            <td class="text-center">
                                <span class="cluster-swatch-dot" style="background: {{ $p['warna'] }};"></span>
                                {{ $p['cluster'] + 1 }}
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-2 py-1
                                    {{ $p['kategori'] === 'Prioritas Tinggi' ? 'badge-prioritas-tinggi' : ($p['kategori'] === 'Prioritas Sedang' ? 'badge-prioritas-sedang' : 'badge-prioritas-rendah') }}">
                                    {{ $p['kategori'] }}
                                </span>
                            </td>
                            <td>
                                <span class="text-dark">{{ $p['nama_sekolah'] }}</span>
                                <div class="text-muted small">{{ $p['jenjang_sekolah'] ?? '–' }}</div>
                            </td>
                            <td class="text-center">{{ number_format($p['jarak_km'], 2) }}</td>
                            <td class="text-center">{{ number_format($p['jumlah_siswa']) }}</td>
                            <td class="text-center">{{ $p['jumlah_sekolah_roi'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @endif
</div>

@push('scripts')
<script crossorigin="" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Table search + filter pills ──
    const searchInput = document.getElementById('tableSearch');
    const rows = Array.from(document.querySelectorAll('#clusterTableBody .searchable-row'));
    const searchCount = document.getElementById('searchCount');
    const pills = document.querySelectorAll('.filter-pill');
    let activeKategori = 'all';

    function applyFilters() {
        const term = (searchInput?.value || '').toLowerCase().trim();
        let visible = 0;
        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            const name = nameCell ? nameCell.textContent.toLowerCase() : '';
            const kategori = row.dataset.kategori;
            const matchSearch = !term || name.includes(term);
            const matchKategori = activeKategori === 'all' || kategori === activeKategori;
            const show = matchSearch && matchKategori;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (searchCount) searchCount.textContent = visible + ' of ' + rows.length + ' villages';
    }

    searchInput?.addEventListener('input', applyFilters);
    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            activeKategori = pill.dataset.filterKategori ?? 'all';
            applyFilters();
        });
    });
    applyFilters();

    // ── Map ──
    @if($totalDianalisis >= 2)
    const villages = @json($desaHasil->values());
    const recommendations = @json($rekomendasi->values());
    const clusterStats = @json($clusterStats->values());

    const mapEl = document.getElementById('clusterMap');
    if (mapEl && window.L) {
        const map = L.map('clusterMap');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const bounds = [];
        const schoolSeen = {};

        villages.forEach(v => {
            // Link line: village -> its nearest school
            const line = L.polyline(
                [[v.lat_desa, v.lng_desa], [v.lat_sekolah, v.lng_sekolah]],
                { color: v.warna, weight: 2, dashArray: '4,5', opacity: 0.55 }
            ).addTo(map);

            const villageMarker = L.circleMarker([v.lat_desa, v.lng_desa], {
                radius: 8, color: '#1e293b', weight: 1.5, fillColor: v.warna, fillOpacity: 0.9
            }).addTo(map);
            villageMarker.bindPopup(
                '<b>' + v.nama_wilayah + '</b><br>' +
                'Cluster ' + (v.cluster + 1) + ' &middot; ' + v.kategori + '<br>' +
                'Nearest school: ' + v.nama_sekolah + ' (' + (v.jenjang_sekolah ?? '-') + ')<br>' +
                'Distance: ' + v.jarak_km.toFixed(2) + ' km &middot; Students there: ' + v.jumlah_siswa
            );
            bounds.push([v.lat_desa, v.lng_desa]);

            if (!schoolSeen[v.sekolah_id]) {
                schoolSeen[v.sekolah_id] = true;
                const schoolMarker = L.circleMarker([v.lat_sekolah, v.lng_sekolah], {
                    radius: 5, color: '#334155', weight: 1, fillColor: '#94a3b8', fillOpacity: 0.9
                }).addTo(map);
                schoolMarker.bindPopup('<b>' + v.nama_sekolah + '</b><br>' + (v.jenjang_sekolah ?? '-'));
                bounds.push([v.lat_sekolah, v.lng_sekolah]);
            }
        });

        const starIcon = L.divIcon({
            html: '<i class="bi bi-star-fill" style="color:#f59e0b; font-size:22px; text-shadow: 0 0 3px #000, 0 0 3px #000;"></i>',
            className: '', iconSize: [22, 22], iconAnchor: [11, 11]
        });

        recommendations.forEach((r, i) => {
            const marker = L.marker([r.lat, r.lng], { icon: starIcon }).addTo(map);
            marker.bindPopup(
                '<b>Suggested Location ' + (i + 1) + '</b><br>' +
                r.lat + ', ' + r.lng + '<br>' +
                'Serves ' + r.jumlah_desa + ' village(s): ' + r.desa_terlayani.join(', ') + '<br>' +
                'Students currently affected: ' + r.total_siswa_terdampak
            );
            bounds.push([r.lat, r.lng]);
        });

        if (bounds.length) {
            map.fitBounds(bounds, { padding: [30, 30] });
        } else {
            map.setView([-5.1, 119.4], 10);
        }

        // Legend
        const legend = document.getElementById('mapLegend');
        if (legend) {
            let html = '<div class="map-legend-title">Clusters</div>';
            clusterStats.forEach(s => {
                html += '<div class="map-legend-item"><span class="legend-dot" style="background:' + s.warna + '"></span>' +
                        'Cluster ' + (s.cluster + 1) + ' (' + s.kategori + ') &middot; ' + s.jumlah_desa + ' villages</div>';
            });
            if (recommendations.length) {
                html += '<div class="map-legend-item"><i class="bi bi-star-fill legend-star" style="color:#f59e0b;"></i> Suggested new school site</div>';
            }
            html += '<div class="map-legend-item"><span class="legend-dot" style="background:#94a3b8"></span> Existing school</div>';
            legend.innerHTML = html;
        }
    }
    @endif
});
</script>
@endpush
@endsection
