@extends('layouts.admin')

@section('title', 'Kecamatan Area Data')
@section('page-title', 'Kecamatan Area Management')

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
        padding-top: 1.2rem;
        padding-bottom: 1.2rem;
        color: #334155;
    }
    .btn-action-outline {
        border: 2px solid #e2e8f0;
        color: #4F46E5;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
        background: transparent;
    }
    .btn-action-outline:hover {
        background-color: #4F46E5;
        border-color: #4F46E5;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
    }
    .btn-action-danger {
        border: 2px solid #e2e8f0;
        color: #ef4444;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
        background: transparent;
    }
    .btn-action-danger:hover {
        background-color: #ef4444;
        border-color: #ef4444;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 0.5rem;
    }
    .btn-icon svg { width: 16px; height: 16px; }
    .roi-color-dot {
        width: 16px; height: 16px; border-radius: 4px; display: inline-block;
        border: 1px solid rgba(0,0,0,0.1); flex-shrink: 0;
    }
    .school-count-badge {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.95rem; font-weight: 800; color: #001e40;
        background: #dbeafe; padding: 0.3rem 0.75rem; border-radius: 999px;
    }
    .search-wrapper { position: relative; }
    .search-wrapper .search-icon {
        position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
        color: #94a3b8; pointer-events: none;
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
    .no-results-row { display: none; }
</style>

<div class="container-fluid px-0 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Kecamatan Area Management</h4>
            <p class="text-muted small mb-0">ROI (Region of Interest) boundary of each kecamatan and the number of schools automatically detected within its reach.</p>
        </div>
        <a href="{{ route('kecamatan.create') }}" class="btn btn-action-outline px-4 py-2 rounded-pill shadow-sm">
            Add Kecamatan Area
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3" role="alert">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-medium text-success">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card modern-card shadow-sm p-3">
                <div class="text-muted small fw-semibold text-uppercase mb-1">Total Kecamatan</div>
                <div class="fs-3 fw-bold text-dark">{{ $kecamatans->count() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card modern-card shadow-sm p-3">
                <div class="text-muted small fw-semibold text-uppercase mb-1">Total Schools Covered</div>
                <div class="fs-3 fw-bold text-dark">{{ $kecamatans->sum('jumlah_sekolah') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card modern-card shadow-sm p-3">
                <div class="text-muted small fw-semibold text-uppercase mb-1">Widest Kecamatan</div>
                <div class="fs-5 fw-bold text-dark">{{ optional($kecamatans->sortByDesc('luas_wilayah')->first())->nama_kecamatan ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="card modern-card shadow-sm">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="table-header-title fw-bold mb-0">List of Kecamatan ROI Polygons</h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="search-count" id="searchCount"></span>
                    <div class="search-wrapper">
                        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" class="search-input" id="tableSearch" placeholder="Search kecamatan..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width: 1000px;" id="kecamatanTable">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="5%" class="ps-4 text-center">No.</th>
                            <th width="20%">Kecamatan</th>
                            <th width="18%">Kabupaten / Provinsi</th>
                            <th width="14%">Land Area</th>
                            <th width="20%">Schools within Reach (ROI)</th>
                            <th width="8%" class="text-center">Color</th>
                            <th width="15%" class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="kecamatanTableBody">
                        @forelse($kecamatans as $index => $k)
                        <tr class="searchable-row">
                            <td class="ps-4 text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark fs-6">{{ $k->nama_kecamatan }}</span>
                            </td>
                            <td>
                                <span class="small">
                                    {{ $k->kabupaten ?? '—' }}<br>
                                    <span class="text-muted">{{ $k->provinsi ?? '—' }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-primary fs-6">{{ $k->luas_wilayah ?? '-' }} <span class="text-muted fw-normal small">Ha</span></span>
                            </td>
                            <td>
                                <span class="school-count-badge">
                                    <i class="bi bi-building-fill"></i> {{ $k->jumlah_sekolah }} Sekolah
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="roi-color-dot" style="background:{{ $k->warna ?? '#ea580c' }};" title="{{ $k->warna ?? '#ea580c' }}"></span>
                            </td>
                            <td class="text-center pe-4">
                                <form action="{{ route('kecamatan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kecamatan ini?')" class="d-inline-flex gap-2 justify-content-center w-100">
                                    <a href="{{ route('kecamatan.edit', $k->id) }}" class="btn btn-sm btn-action-outline btn-icon" title="Edit / View ROI Analysis">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-action-danger btn-icon" title="Delete">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted small">
                                No kecamatan area data yet. Click "Add Kecamatan Area" to input the first ROI boundary
                                (e.g. Tinanggea, Wawonii Barat, or Soropia).
                            </td>
                        </tr>
                        @endforelse
                        <tr class="no-results-row" id="noResultsRow">
                            <td colspan="7" class="text-center py-5 text-muted small">
                                <svg width="40" height="40" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" class="mb-2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <div>No data found matching your search.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    const input = document.getElementById('tableSearch');
    const rows = Array.from(document.querySelectorAll('#kecamatanTableBody .searchable-row'));
    const noResults = document.getElementById('noResultsRow');
    const countEl = document.getElementById('searchCount');
    const total = rows.length;

    function applyFilter() {
        const q = input.value.toLowerCase().trim();
        let visible = 0;
        rows.forEach(function (r) {
            const match = q === '' || r.textContent.toLowerCase().includes(q);
            r.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        noResults.style.display = (visible === 0 && q !== '') ? '' : 'none';
        countEl.textContent = q !== '' ? visible + ' dari ' + total + ' data' : total + ' data';
    }

    applyFilter();
    input.addEventListener('input', applyFilter);
})();
</script>
@endsection