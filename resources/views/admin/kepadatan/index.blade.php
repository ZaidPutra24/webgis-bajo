@extends('layouts.admin')

@section('title', 'School-Age Population Density')
@section('page-title', 'School-Age Density per Village')

@section('content')
<style>
    .modern-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    .table-header-title {
        position: relative;
        padding-left: 1rem;
        color: #2b3445;
        font-size: 1.1rem;
    }
    .table-header-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        height: 60%;
        width: 5px;
        background: linear-gradient(135deg, #4F46E5 0%, #06b6d4 100%);
        border-radius: 5px;
    }
    .custom-table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding-top: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .custom-table tbody td {
        padding-top: 1.1rem;
        padding-bottom: 1.1rem;
        color: #334155;
    }
    .search-wrapper { position: relative; }
    .search-wrapper .search-icon {
        position: absolute; left: 1rem; top: 50%;
        transform: translateY(-50%); color: #94a3b8; pointer-events: none;
    }
    .search-input {
        border: 2px solid #e2e8f0; border-radius: 0.75rem;
        padding: 0.55rem 1rem 0.55rem 2.75rem; font-size: 0.9rem;
        color: #334155; background-color: #f8fafc; transition: all 0.2s; width: 280px;
    }
    .search-input:focus {
        border-color: #4F46E5; background-color: #fff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none;
    }
    .search-count { font-size: 0.8rem; color: #94a3b8; }
    .filter-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .filter-pill { border: 1.5px solid #e2e8f0; border-radius: 2rem; padding: 0.25rem 0.85rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.15s; background: transparent; color: #64748b; }
    .filter-pill:hover, .filter-pill.active { background: #4F46E5; border-color: #4F46E5; color: #fff; }
    .no-results-row { display: none; }

    /* ─── STAT CARDS ─── */
    .stat-card {
        border-radius: 1.1rem; border: none; background: #fff;
        box-shadow: 0 6px 20px rgba(0,0,0,.04); padding: 1.25rem 1.4rem;
        display: flex; align-items: center; gap: 14px; height: 100%;
    }
    .stat-icon {
        width: 46px; height: 46px; border-radius: .85rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-value { font-size: 1.4rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
    .stat-label { font-size: .76rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }

    /* Category badges */
    .badge-kategori-tinggi { background: rgba(239,68,68,.1); color: #dc2626; border: 1px solid #ef4444; }
    .badge-kategori-sedang { background: rgba(245,158,11,.1); color: #b45309; border: 1px solid #f59e0b; }
    .badge-kategori-rendah { background: rgba(34,197,94,.1); color: #15803d; border: 1px solid #22c55e; }
    .badge-kategori-kosong { background: rgba(148,163,184,.1); color: #64748b; border: 1px solid #94a3b8; }
    .badge-prioritas { background: rgba(220,38,38,.1); color: #dc2626; border: 1px solid #dc2626; }

    /* Pagination */
    .pagination-wrapper { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 0.5rem; }
    .pagination-info { font-size: 0.8rem; color: #94a3b8; }
    .pagination-controls { display: flex; gap: 0.35rem; align-items: center; }
    .page-btn { border: 1.5px solid #e2e8f0; background: #fff; color: #64748b; border-radius: 0.5rem; padding: 0.3rem 0.65rem; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.15s; line-height: 1.4; }
    .page-btn:hover:not(:disabled) { border-color: #4F46E5; color: #4F46E5; }
    .page-btn.active { background: #4F46E5; border-color: #4F46E5; color: #fff; }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
</style>

<div class="container-fluid px-0 mb-5">

    <div class="mb-4">
        <h4 class="mb-1 text-dark fw-bold">School-Age Population Density per Village</h4>
        <p class="text-muted small mb-0">
            Density = school-age population ÷ land area (km²), calculated per village and
            <strong>per level</strong> (PAUD/SD/SMP/SMA). Each level is cross-checked against
            school service radius standards (SNI 03-1733-2004, distance from village hall point — using real land/sea travel time if entered)
            and classroom capacity standards (Kepmendikdasmen No. 14/2026) so the results represent field realities,
            including for Bajo coastal/island villages.
            Low/Medium/High categories are determined automatically using quartiles (Q1 = {{ $q1 ?? '–' }}, Q3 = {{ $q3 ?? '–' }} capita/km²) relative between villages.
            <span class="text-warning-emphasis">The standard figures below can be edited by admin</span> — replace them promptly as soon as official more accurate documents for the Bajo region are available.
        </p>
    </div>

    {{-- Tabel referensi standar (bisa diedit admin) --}}
    <div class="card modern-card shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <h5 class="table-header-title fw-bold mb-0">Reference Standards per Level</h5>
            <p class="text-muted small mb-0 ps-1 mt-1">
                Max. students/class: Kepmendikdasmen No. 14/2026 &middot; Service radius &amp; ideal supporting population: SNI 03-1733-2004 (secondary reference for coastal/island villages).
            </p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4">Level</th>
                            <th class="text-center">Max. Students/Class</th>
                            <th class="text-center">Service Radius</th>
                            <th class="text-center">Ideal Supporting Population</th>
                            <th class="pe-4">Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jenjangInti as $j)
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">{{ $j['label'] }}</td>
                            <td class="text-center">{{ $j['maks_murid_per_rombel'] ?? '–' }} students</td>
                            <td class="text-center">{{ $j['radius_meter'] ? number_format($j['radius_meter']/1000, 1).' km' : '–' }}</td>
                            <td class="text-center">{{ $j['penduduk_pendukung_ideal'] ? number_format($j['penduduk_pendukung_ideal']) . ' capita' : '–' }}</td>
                            <td class="pe-4 text-muted small">{{ $j['sumber_radius'] ?? '–' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(79,70,229,.1); color:#4F46E5;">
                    <i class="bi bi-map"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalDesa }}</div>
                    <div class="stat-label">Total Villages</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(6,182,212,.1); color:#06b6d4;">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $rataKepadatan }}</div>
                    <div class="stat-label">Avg. Density (capita/km²)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(239,68,68,.1); color:#dc2626;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalDesaTinggi }}</div>
                    <div class="stat-label">High Density Villages</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(220,38,38,.12); color:#dc2626;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalDesaPrioritas }}</div>
                    <div class="stat-label">Priority Villages (Dense, Low Schools)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(217,119,6,.12); color:#b45309;">
                    <i class="bi bi-signpost-split-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalDesaBermasalahJenjang }}</div>
                    <div class="stat-label">Villages with ≥1 Level Outside Radius/Capacity</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3" role="alert">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-medium text-success">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="card modern-card shadow-sm">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
                <h5 class="table-header-title fw-bold mb-0">School-Age Population Density List per Village</h5>
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
                <button class="filter-pill" data-filter-kategori="Tinggi">High</button>
                <button class="filter-pill" data-filter-kategori="Sedang">Medium</button>
                <button class="filter-pill" data-filter-kategori="Rendah">Low</button>
                <button class="filter-pill" data-filter-prioritas="1">⚠ Priority</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width: 1250px;">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="3%" class="ps-4 text-center">No.</th>
                            <th width="15%">Village</th>
                            <th width="9%" class="text-center">Area (km²)</th>
                            <th width="15%">School-Age Population</th>
                            <th width="11%" class="text-center">Density (capita/km²)</th>
                            <th width="9%" class="text-center">Category</th>
                            <th width="10%" class="text-center">Number of Schools</th>
                            <th width="14%" class="text-center">Status</th>
                            <th width="14%" class="text-center pe-4">Details per Level</th>
                        </tr>
                    </thead>
                    <tbody id="kepadatanTableBody">
                        @forelse($wilayahs as $index => $desa)
                        <tr class="searchable-row" data-kategori="{{ $desa->kategori_kepadatan }}" data-prioritas="{{ $desa->prioritas ? 1 : 0 }}">
                            <td class="ps-4 text-center fw-semibold text-muted row-number">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $desa->nama_wilayah }}</span>
                            </td>
                            <td class="text-center">
                                {{ $desa->luas_wilayah !== null ? number_format($desa->luas_wilayah, 2) : '–' }}
                            </td>
                            <td>
                                <div class="small">
                                    <div class="text-muted mb-1">Male: <strong class="text-dark">{{ $desa->penduduk_usia_sekolah_l ?? 0 }}</strong></div>
                                    <div class="text-muted mb-1">Female: <strong class="text-dark">{{ $desa->penduduk_usia_sekolah_p ?? 0 }}</strong></div>
                                    <div class="text-muted">Total: <strong class="text-primary fw-bold">{{ $desa->total_penduduk_usia_sekolah }}</strong></div>
                                    @if($desa->total_putus_sekolah > 0)
                                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>ATS/Dropouts: <strong>{{ $desa->total_putus_sekolah }}</strong></div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center fw-bold">
                                {{ $desa->kepadatan_usia_sekolah !== null ? number_format($desa->kepadatan_usia_sekolah, 2) : '–' }}
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($desa->kategori_kepadatan) {
                                        'Tinggi' => 'badge-kategori-tinggi',
                                        'Sedang' => 'badge-kategori-sedang',
                                        'Rendah' => 'badge-kategori-rendah',
                                        default  => 'badge-kategori-kosong',
                                    };
                                    $catLabel = match($desa->kategori_kepadatan) {
                                        'Tinggi' => 'High',
                                        'Sedang' => 'Medium',
                                        'Rendah' => 'Low',
                                        default  => $desa->kategori_kepadatan,
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill fw-semibold small">
                                    {{ $catLabel }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-dark">{{ $desa->jumlah_sekolah }}</span>
                                <div class="text-muted small">schools in village ROI</div>
                            </td>
                            <td class="text-center">
                                @if($desa->prioritas)
                                    <span class="badge badge-prioritas px-3 py-2 rounded-pill fw-semibold small">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Need New School
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-semibold small">
                                        Safe
                                    </span>
                                @endif
                                @if($desa->ada_jenjang_bermasalah)
                                    <div class="mt-1">
                                        <span class="badge badge-kategori-sedang px-2 py-1 rounded-pill fw-semibold" style="font-size:.68rem;">
                                            ⚠ Level outside radius/capacity
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 detail-toggle-btn" data-target="detail-{{ $desa->id }}">
                                    <i class="bi bi-list-ul me-1"></i>View per Level
                                </button>
                            </td>
                        </tr>
                        <tr class="detail-row" id="detail-{{ $desa->id }}" style="display:none;">
                            <td colspan="9" class="p-0 border-0">
                                <div class="p-3" style="background:#f8fafc;">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0" style="min-width: 1100px;">
                                            <thead>
                                                <tr class="text-secondary" style="font-size:.72rem; text-transform:uppercase; letter-spacing:.04em;">
                                                    <th>Level</th>
                                                    <th class="text-center">Level Age Population</th>
                                                    <th class="text-center">ATS/Dropouts</th>
                                                    <th class="text-center">Density (capita/km²)</th>
                                                    <th>Nearest School from Village Hall</th>
                                                    <th class="text-center">Distance / Standard Radius</th>
                                                    <th class="text-center">Served?</th>
                                                    <th class="text-center">Capacity vs Needs</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($desa->rincian_jenjang as $r)
                                                <tr style="font-size:.83rem;">
                                                    <td class="fw-semibold text-dark">{{ $r['label'] }}</td>
                                                    <td class="text-center">{{ $r['total_penduduk'] }}</td>
                                                    <td class="text-center">
                                                        {{ $r['total_putus_sekolah'] }}
                                                        @if($r['persen_putus_sekolah'] !== null)
                                                            <span class="text-muted">({{ $r['persen_putus_sekolah'] }}%)</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $r['kepadatan'] !== null ? number_format($r['kepadatan'], 2) : '–' }}</td>
                                                    <td>
                                                        @if($r['sekolah_terdekat'])
                                                            <div class="text-dark">{{ $r['sekolah_terdekat']['nama_sekolah'] }}</div>
                                                            <div class="text-muted" style="font-size:.75rem;">distance source: {{ $r['sekolah_terdekat']['sumber_jarak'] }} &middot; ~{{ $r['sekolah_terdekat']['waktu_tempuh_mnt'] }} mins</div>
                                                        @else
                                                            <span class="text-muted">No school recorded for this level</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($r['sekolah_terdekat'])
                                                            {{ number_format($r['sekolah_terdekat']['jarak_km'], 2) }} km
                                                            <span class="text-muted">/ {{ $r['radius_standar_km'] !== null ? number_format($r['radius_standar_km'], 1) : '–' }} km</span>
                                                        @else
                                                            –
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($r['terlayani'] === true)
                                                            <span class="badge badge-kategori-rendah px-2 py-1 rounded-pill fw-semibold" style="font-size:.72rem;">Yes</span>
                                                        @elseif($r['terlayani'] === false)
                                                            <span class="badge badge-kategori-tinggi px-2 py-1 rounded-pill fw-semibold" style="font-size:.72rem;">No</span>
                                                        @else
                                                            <span class="text-muted">–</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($r['daya_tampung'] === null)
                                                            <span class="text-muted">No school in ROI</span>
                                                        @elseif($r['kapasitas_cukup'])
                                                            <span class="badge badge-kategori-rendah px-2 py-1 rounded-pill fw-semibold" style="font-size:.72rem;">Sufficient ({{ $r['daya_tampung'] }})</span>
                                                        @else
                                                            <span class="badge badge-kategori-tinggi px-2 py-1 rounded-pill fw-semibold" style="font-size:.72rem;">Deficit {{ $r['kekurangan_daya_tampung'] }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted small">
                                No village area data available yet. Please add village data first in the "Village Areas" menu.
                            </td>
                        </tr>
                        @endforelse
                        <tr class="no-results-row" id="noResultsRow">
                            <td colspan="9" class="text-center py-5 text-muted small">
                                <svg width="40" height="40" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" class="mb-2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <div>No data found matching your search/filter.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-wrapper" id="paginationWrapper">
            <span class="pagination-info" id="paginationInfo"></span>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>
    </div>

</div>

<script>
(function () {
    const input = document.getElementById('tableSearch');
    const rows = Array.from(document.querySelectorAll('#kepadatanTableBody .searchable-row'));
    const noResults = document.getElementById('noResultsRow');
    const countEl = document.getElementById('searchCount');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const filterPills = document.querySelectorAll('.filter-pill');
    const PER_PAGE = 10;
    const total = rows.length;
    let activeKategori = 'all';
    let activePrioritas = false;
    let currentPage = 1;

    function getFilteredRows() {
        const q = input.value.toLowerCase().trim();
        return rows.filter(function(r) {
            const matchSearch = q === '' || r.textContent.toLowerCase().includes(q);
            const kategori = r.dataset.kategori || '';
            const matchKategori = activeKategori === 'all' || kategori === activeKategori;
            const matchPrioritas = !activePrioritas || r.dataset.prioritas === '1';
            return matchSearch && matchKategori && matchPrioritas;
        });
    }

    function renderPage() {
        const filteredRows = getFilteredRows();
        const totalFiltered = filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(totalFiltered / PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PER_PAGE;
        const end = start + PER_PAGE;

        rows.forEach(r => r.style.display = 'none');
        document.querySelectorAll('.detail-row').forEach(function (d) {
            d.style.display = 'none';
            const btn = document.querySelector('.detail-toggle-btn[data-target="' + d.id + '"]');
            if (btn) btn.innerHTML = '<i class="bi bi-list-ul me-1"></i>View per Level';
        });
        let displayNum = 1;
        filteredRows.forEach(function (row, idx) {
            if (idx >= start && idx < end) {
                row.style.display = '';
                row.querySelector('td.row-number').textContent = start + displayNum;
                displayNum++;
            }
        });

        noResults.style.display = (totalFiltered === 0 && (input.value.trim() !== '' || activeKategori !== 'all' || activePrioritas)) ? '' : 'none';
        countEl.textContent = (input.value.trim() !== '' || activeKategori !== 'all' || activePrioritas) ? totalFiltered + ' of ' + total + ' records' : total + ' records';

        if (totalFiltered > 0) {
            paginationInfo.textContent = 'Showing ' + (start + 1) + '–' + Math.min(end, totalFiltered) + ' of ' + totalFiltered + ' entries';
        } else { paginationInfo.textContent = ''; }

        paginationControls.innerHTML = '';
        const prevBtn = document.createElement('button');
        prevBtn.className = 'page-btn'; prevBtn.textContent = '‹'; prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', function () { currentPage--; renderPage(); });
        paginationControls.appendChild(prevBtn);

        const maxBtns = 5;
        let pageStart = Math.max(1, currentPage - Math.floor(maxBtns / 2));
        let pageEnd = Math.min(totalPages, pageStart + maxBtns - 1);
        if (pageEnd - pageStart < maxBtns - 1) pageStart = Math.max(1, pageEnd - maxBtns + 1);
        for (let i = pageStart; i <= pageEnd; i++) {
            const btn = document.createElement('button');
            btn.className = 'page-btn' + (i === currentPage ? ' active' : '');
            btn.textContent = i;
            btn.addEventListener('click', (function(p) { return function() { currentPage = p; renderPage(); }; })(i));
            paginationControls.appendChild(btn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'page-btn'; nextBtn.textContent = '›'; nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', function () { currentPage++; renderPage(); });
        paginationControls.appendChild(nextBtn);
    }

    renderPage();
    input.addEventListener('input', function () { currentPage = 1; renderPage(); });

    document.querySelectorAll('.detail-toggle-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(this.dataset.target);
            if (!target) return;
            const isOpen = target.style.display !== 'none';
            target.style.display = isOpen ? 'none' : '';
            this.innerHTML = isOpen
                ? '<i class="bi bi-list-ul me-1"></i>View per Level'
                : '<i class="bi bi-chevron-up me-1"></i>Hide';
        });
    });

    filterPills.forEach(function(pill) {
        pill.addEventListener('click', function() {
            if (this.dataset.filterPrioritas) {
                activePrioritas = !activePrioritas;
                this.classList.toggle('active', activePrioritas);
            } else {
                filterPills.forEach(p => { if (!p.dataset.filterPrioritas) p.classList.remove('active'); });
                this.classList.add('active');
                activeKategori = this.dataset.filterKategori;
            }
            currentPage = 1;
            renderPage();
        });
    });
})();
</script>
@endsection
