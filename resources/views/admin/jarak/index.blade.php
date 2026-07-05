@extends('layouts.admin')

@section('title', 'Distance Matrix')
@section('page-title', 'School-to-Village Distance & Route Matrix')

@push('styles')
<style>
    .modern-card { border-radius: 1.25rem; border: none; box-shadow: 0 8px 30px rgba(0,0,0,.04); background:#fff; }
    .table-header-title { position:relative; padding-left:1rem; color:#2b3445; font-size:1.1rem; }
    .table-header-title::before { content:''; position:absolute; left:0; top:20%; height:60%; width:5px;
        background:linear-gradient(135deg,#4F46E5 0%,#06b6d4 100%); border-radius:5px; }

    .custom-table thead th { font-weight:600; text-transform:uppercase; font-size:.72rem;
        letter-spacing:.05em; padding:.9rem .75rem; border-bottom:1px solid #e2e8f0; color:#64748b; }
    .custom-table tbody td { padding:.95rem .75rem; color:#334155; vertical-align:middle; }
    .custom-table tbody tr:hover { background:#f8fafc; }

    /* Aksi tombol */
    .btn-icon { display:inline-flex; align-items:center; justify-content:center;
        width:32px; height:32px; padding:0; border-radius:.5rem; }
    .btn-icon svg { width:14px; height:14px; }
    .btn-act-view   { border:1.5px solid #e2e8f0; color:#0ea5e9; background:transparent; }
    .btn-act-view:hover   { background:#0ea5e9; border-color:#0ea5e9; color:#fff; }
    .btn-act-edit   { border:1.5px solid #e2e8f0; color:#4F46E5; background:transparent; }
    .btn-act-edit:hover   { background:#4F46E5; border-color:#4F46E5; color:#fff; }
    .btn-act-del    { border:1.5px solid #e2e8f0; color:#ef4444; background:transparent; }
    .btn-act-del:hover    { background:#ef4444; border-color:#ef4444; color:#fff; }

    /* Badge moda */
    .badge-darat    { background:#dcfce7; color:#16a34a; }
    .badge-multimoda{ background:#fef9c3; color:#ca8a04; }
    .badge-rute     { background:#ede9fe; color:#7c3aed; }
    .badge-norute   { background:#f1f5f9; color:#94a3b8; }

    /* Search */
    .search-wrapper { position:relative; }
    .search-wrapper .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; }
    .search-input { border:2px solid #e2e8f0; border-radius:.75rem; padding:.5rem 1rem .5rem 2.6rem;
        font-size:.88rem; color:#334155; background:#f8fafc; width:270px; transition:all .2s; }
    .search-input:focus { border-color:#4F46E5; background:#fff; box-shadow:0 0 0 4px rgba(79,70,229,.1); outline:none; }

    /* Filter chips */
    .filter-chip { border:1.5px solid #e2e8f0; background:#f8fafc; color:#64748b;
        font-size:.78rem; font-weight:600; border-radius:999px; padding:.3rem .9rem; cursor:pointer; transition:all .15s; }
    .filter-chip.active { border-color:#4F46E5; background:#ede9fe; color:#4F46E5; }

    /* Stat cards atas */
    .stat-mini { border-radius:.875rem; padding:1rem 1.25rem; display:flex; align-items:center; gap:.875rem; }
    .stat-mini .stat-icon { width:40px; height:40px; border-radius:.625rem; display:flex; align-items:center;
        justify-content:center; font-size:1.1rem; flex-shrink:0; }
    .stat-mini .stat-val { font-size:1.4rem; font-weight:700; line-height:1.1; }
    .stat-mini .stat-lbl { font-size:.75rem; color:#94a3b8; font-weight:500; }

    /* Pagination */
    .pg-wrapper { display:flex; align-items:center; justify-content:space-between; padding:.9rem 1.5rem;
        border-top:1px solid #e2e8f0; flex-wrap:wrap; gap:.5rem; }
    .pg-info { font-size:.78rem; color:#94a3b8; }
    .pg-ctrl { display:flex; gap:.3rem; align-items:center; }
    .pg-btn { border:1.5px solid #e2e8f0; background:#fff; color:#64748b; border-radius:.5rem;
        padding:.28rem .6rem; font-size:.8rem; font-weight:600; cursor:pointer; transition:all .15s; line-height:1.4; }
    .pg-btn:hover:not(:disabled) { border-color:#4F46E5; color:#4F46E5; }
    .pg-btn.active { background:#4F46E5; border-color:#4F46E5; color:#fff; }
    .pg-btn:disabled { opacity:.4; cursor:not-allowed; }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 mb-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Matriks Jarak & Rute</h4>
            <p class="text-muted small mb-0">Data jarak, estimasi waktu tempuh, dan rute GeoJSON setiap sekolah ke wilayah desa Bajo.</p>
        </div>
        <a href="{{ route('jarak.create') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data
        </a>
    </div>

    {{-- Stat mini cards --}}
    <div class="row g-3 mb-4">
        @php
            $total       = $matriksJarak->count();
            $totalDarat  = $matriksJarak->where('mode_transport','darat')->count();
            $totalMulti  = $matriksJarak->where('mode_transport','multimoda')->count();
            $totalRute   = $matriksJarak->whereNotNull('route_geojson')->count();
            $avgJarak    = $total ? round($matriksJarak->avg('jarak'),2) : 0;
        @endphp
        <div class="col-6 col-md-3">
            <div class="stat-mini bg-white shadow-sm border">
                <div class="stat-icon bg-indigo-100" style="background:#ede9fe;">
                    <i class="bi bi-table text-primary" style="font-size:1.2rem;"></i>
                </div>
                <div>
                    <div class="stat-val text-dark">{{ $total }}</div>
                    <div class="stat-lbl">Total Record</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-mini bg-white shadow-sm border">
                <div class="stat-icon" style="background:#dcfce7;">
                    <i class="bi bi-car-front text-success" style="font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:#16a34a;">{{ $totalDarat }}</div>
                    <div class="stat-lbl">Rute Darat</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-mini bg-white shadow-sm border">
                <div class="stat-icon" style="background:#fef9c3;">
                    <i class="bi bi-water text-warning" style="font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:#ca8a04;">{{ $totalMulti }}</div>
                    <div class="stat-lbl">Multimoda (Perahu)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-mini bg-white shadow-sm border">
                <div class="stat-icon" style="background:#ede9fe;">
                    <i class="bi bi-geo-alt text-purple" style="color:#7c3aed;font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:#7c3aed;">{{ $totalRute }}</div>
                    <div class="stat-lbl">Punya GeoJSON Rute</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel utama --}}
    <div class="card modern-card shadow-sm">

        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="table-header-title fw-bold mb-0">Daftar Matriks Jarak Spasial</h5>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Filter chips --}}
                    <button class="filter-chip active" data-filter="all">Semua</button>
                    <button class="filter-chip" data-filter="darat">Darat</button>
                    <button class="filter-chip" data-filter="multimoda">Multimoda</button>
                    <button class="filter-chip" data-filter="has-route">Punya Rute</button>

                    {{-- Search --}}
                    <div class="search-wrapper ms-1">
                        <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" class="search-input" id="tableSearch" placeholder="Cari sekolah / desa…">
                    </div>
                    <span class="text-muted small" id="searchCount"></span>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width:1100px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-center" width="4%">No</th>
                            <th width="22%">Nama Sekolah</th>
                            <th width="16%">Wilayah / Desa</th>
                            <th width="9%" class="text-end">Jarak (km)</th>
                            <th width="11%" class="text-center">Jalan Kaki</th>
                            <th width="11%" class="text-center">Berkendara</th>
                            <th width="10%" class="text-center">Perahu</th>
                            <th width="9%" class="text-center">Moda</th>
                            <th width="8%" class="text-center">Rute</th>
                            <th width="10%" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="jarakTableBody">
                        @forelse($matriksJarak as $idx => $j)
                        <tr class="searchable-row"
                            data-mode="{{ $j->mode_transport ?? 'darat' }}"
                            data-has-route="{{ $j->route_geojson ? '1' : '0' }}">
                            <td class="ps-4 text-center fw-semibold text-muted row-number">{{ $idx + 1 }}</td>

                            {{-- Sekolah --}}
                            <td>
                                <span class="fw-semibold text-dark small">{{ $j->sekolah->nama_sekolah ?? '-' }}</span>
                            </td>

                            {{-- Wilayah --}}
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill small fw-semibold">
                                    {{ $j->wilayahDesa->nama_wilayah ?? '-' }}
                                </span>
                            </td>

                            {{-- Jarak --}}
                            <td class="text-end">
                                <span class="fw-bold text-primary">{{ number_format($j->jarak, 2) }}</span>
                                <span class="text-muted small"> km</span>
                            </td>

                            {{-- Jalan Kaki --}}
                            <td class="text-center">
                                @if($j->walk_mnt !== null)
                                    @php
                                        $jam = intdiv((int)$j->walk_mnt, 60);
                                        $mnt = (int)$j->walk_mnt % 60;
                                    @endphp
                                    <span class="small text-secondary fw-semibold">
                                        {{ $jam > 0 ? $jam.'j ' : '' }}{{ $mnt }}m
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Berkendara --}}
                            <td class="text-center">
                                @if($j->drive_mnt !== null)
                                    @php
                                        $jam = intdiv((int)$j->drive_mnt, 60);
                                        $mnt = (int)$j->drive_mnt % 60;
                                    @endphp
                                    <span class="small fw-semibold" style="color:#4F46E5;">
                                        {{ $jam > 0 ? $jam.'j ' : '' }}{{ $mnt }}m
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Perahu --}}
                            <td class="text-center">
                                @if($j->boat_mnt !== null)
                                    @php
                                        $jam = intdiv((int)$j->boat_mnt, 60);
                                        $mnt = (int)$j->boat_mnt % 60;
                                    @endphp
                                    <span class="small fw-semibold" style="color:#0891b2;">
                                        {{ $jam > 0 ? $jam.'j ' : '' }}{{ $mnt }}m
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Moda --}}
                            <td class="text-center">
                                @if(($j->mode_transport ?? 'darat') === 'multimoda')
                                    <span class="badge badge-multimoda px-2 py-1 rounded-pill small fw-semibold">
                                        <i class="bi bi-water me-1"></i>Multimoda
                                    </span>
                                @else
                                    <span class="badge badge-darat px-2 py-1 rounded-pill small fw-semibold">
                                        <i class="bi bi-car-front me-1"></i>Darat
                                    </span>
                                @endif
                            </td>

                            {{-- Rute GeoJSON --}}
                            <td class="text-center">
                                @if($j->route_geojson)
                                    <span class="badge badge-rute px-2 py-1 rounded-pill small fw-semibold">
                                        <i class="bi bi-geo-alt-fill me-1"></i>Ada
                                    </span>
                                @else
                                    <span class="badge badge-norute px-2 py-1 rounded-pill small">Belum</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center pe-4">
                                <form action="{{ route('jarak.destroy', $j->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus data jarak ini?')"
                                      class="d-inline-flex gap-1 justify-content-center">
                                    <a href="{{ route('jarak.show', $j->id) }}"
                                       class="btn btn-sm btn-icon btn-act-view" title="Lihat">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('jarak.edit', $j->id) }}"
                                       class="btn btn-sm btn-icon btn-act-edit" title="Edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-act-del" title="Hapus">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="10" class="text-center py-5 text-muted small">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada data jarak. Klik <b>Tambah Data</b> untuk mulai mengisi.
                            </td>
                        </tr>
                        @endforelse
                        <tr class="d-none" id="noResultsRow">
                            <td colspan="10" class="text-center py-5 text-muted small">
                                <i class="bi bi-search fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                Tidak ada data yang cocok dengan pencarian / filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="pg-wrapper">
            <span class="pg-info" id="pgInfo"></span>
            <div class="pg-ctrl" id="pgCtrl"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const input       = document.getElementById('tableSearch');
    const countEl     = document.getElementById('searchCount');
    const pgInfo      = document.getElementById('pgInfo');
    const pgCtrl      = document.getElementById('pgCtrl');
    const noResults   = document.getElementById('noResultsRow');
    const filterBtns  = document.querySelectorAll('.filter-chip');
    const allRows     = Array.from(document.querySelectorAll('#jarakTableBody .searchable-row'));

    const PER_PAGE = 15;
    let currentPage = 1;
    let activeFilter = 'all';

    function getVisible() {
        const q = input.value.toLowerCase().trim();
        return allRows.filter(row => {
            const mode     = row.dataset.mode;
            const hasRoute = row.dataset.hasRoute === '1';
            const matchFilter =
                activeFilter === 'all'       ? true :
                activeFilter === 'has-route' ? hasRoute :
                                               mode === activeFilter;
            const matchSearch = q === '' || row.textContent.toLowerCase().includes(q);
            return matchFilter && matchSearch;
        });
    }

    function render() {
        const visible = getVisible();
        const total   = visible.length;
        const pages   = Math.max(1, Math.ceil(total / PER_PAGE));
        if (currentPage > pages) currentPage = pages;

        const start = (currentPage - 1) * PER_PAGE;
        const end   = start + PER_PAGE;

        allRows.forEach(r => r.style.display = 'none');
        let num = start + 1;
        visible.forEach((row, i) => {
            if (i >= start && i < end) {
                row.style.display = '';
                const numCell = row.querySelector('.row-number');
                if (numCell) numCell.textContent = num++;
            }
        });

        noResults.classList.toggle('d-none', total > 0);
        countEl.textContent = allRows.length !== total
            ? total + ' / ' + allRows.length + ' data' : allRows.length + ' data';

        pgInfo.textContent = total > 0
            ? 'Menampilkan ' + (start + 1) + '–' + Math.min(end, total) + ' dari ' + total + ' entri'
            : '';

        // Render pagination buttons
        pgCtrl.innerHTML = '';
        const mkBtn = (txt, page, disabled, active) => {
            const b = document.createElement('button');
            b.className = 'pg-btn' + (active ? ' active' : '');
            b.textContent = txt;
            b.disabled = disabled;
            b.addEventListener('click', () => { currentPage = page; render(); });
            pgCtrl.appendChild(b);
        };
        mkBtn('‹', currentPage - 1, currentPage === 1, false);
        const maxBtns = 5;
        let ps = Math.max(1, currentPage - 2);
        let pe = Math.min(pages, ps + maxBtns - 1);
        if (pe - ps < maxBtns - 1) ps = Math.max(1, pe - maxBtns + 1);
        for (let i = ps; i <= pe; i++) mkBtn(i, i, false, i === currentPage);
        mkBtn('›', currentPage + 1, currentPage === pages, false);
    }

    // Filter chips
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.dataset.filter;
            currentPage = 1;
            render();
        });
    });

    input.addEventListener('input', () => { currentPage = 1; render(); });
    render();
})();
</script>
@endpush