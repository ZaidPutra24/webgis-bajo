@extends('layouts.admin')

@section('title', 'Schools')
@section('page-title', 'School Data Management')

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
    /* Search */
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
    /* Filter badges */
    .filter-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .filter-pill {
        border: 1.5px solid #e2e8f0; border-radius: 2rem; padding: 0.25rem 0.85rem;
        font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.15s;
        background: transparent; color: #64748b;
    }
    .filter-pill:hover, .filter-pill.active {
        background: #4F46E5; border-color: #4F46E5; color: #fff;
    }
    .no-results-row { display: none; }
</style>

<div class="container-fluid px-0 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-dark fw-bold">School Data Management</h4>
            <p class="text-muted small mb-0">List of school identity registrations, national school identification numbers, levels, accreditation, and coordinate mapping.</p>
        </div>
        <a href="{{ route('sekolah.create') }}" class="btn btn-action-outline px-4 py-2 rounded-pill shadow-sm">
            Add School
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

    <div class="card modern-card shadow-sm">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
                <h5 class="table-header-title fw-bold mb-0">School List</h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="search-count" id="searchCount"></span>
                    <div class="search-wrapper">
                        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" class="search-input" id="tableSearch" placeholder="Search school names, NPSN..." autocomplete="off">
                    </div>
                </div>
            </div>
            <!-- Filter Pills -->
            <div class="filter-pills ps-1">
                <button class="filter-pill active" data-filter="all">All</button>
                <button class="filter-pill" data-filter="Negeri">Negeri</button>
                <button class="filter-pill" data-filter="Swasta">Swasta</button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width: 1000px;" id="sekolahTable">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="5%" class="ps-4 text-center">No.</th>
                            <th width="20%">School Name</th>
                            <th width="10%">NPSN</th>
                            <th width="10%">Level</th>
                            <th width="15%">Status</th>
                            <th width="10%">Accreditation</th>
                            <th width="15%">Coordinates (Lat, Long)</th>
                            <th width="15%" class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="sekolahTableBody">
                        @forelse($sekolahs as $index => $s)
                        <tr class="searchable-row" data-status="{{ $s->status }}">
                            <td class="ps-4 text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark fs-6">{{ $s->nama_sekolah }}</span>
                            </td>
                            <td>
                                <span class="text-secondary fw-medium">{{ $s->npsn ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 rounded-pill fw-semibold small">
                                    {{ $s->jenjang->kode ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($s->status == 'Negeri')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-semibold small">
                                        Negeri
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill fw-semibold small">
                                        Swasta
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-primary fs-6">{{ $s->akreditasi ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="small text-muted fw-medium">{{ $s->latitude }}, {{ $s->longitude }}</span>
                            </td>
                            <td class="text-center pe-4">
                                <form action="{{ route('sekolah.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Delete this school data?')" class="d-inline-flex gap-2 justify-content-center w-100">
                                    <a href="{{ route('sekolah.edit', $s->id) }}" class="btn btn-sm btn-action-outline px-3 rounded-pill">
                                        Edit
                                    </a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-action-danger px-3 rounded-pill">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted small">
                                No school data has been saved in the system yet.
                            </td>
                        </tr>
                        @endforelse
                        <tr class="no-results-row" id="noResultsRow">
                            <td colspan="8" class="text-center py-5 text-muted small">
                                <svg width="40" height="40" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" class="mb-2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <div>Tidak ada sekolah yang cocok dengan pencarian.</div>
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
    const searchInput = document.getElementById('tableSearch');
    const rows = document.querySelectorAll('#sekolahTableBody .searchable-row');
    const noResults = document.getElementById('noResultsRow');
    const countEl = document.getElementById('searchCount');
    const filterPills = document.querySelectorAll('.filter-pill');
    const total = rows.length;
    let activeFilter = 'all';

    function applyFilters() {
        const q = searchInput.value.toLowerCase().trim();
        let visible = 0;
        rows.forEach(function (row) {
            const text = row.textContent.toLowerCase();
            const status = row.dataset.status;
            const matchSearch = q === '' || text.includes(q);
            const matchFilter = activeFilter === 'all' || status === activeFilter;
            if (matchSearch && matchFilter) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });
        noResults.style.display = (visible === 0) ? '' : 'none';
        countEl.textContent = (q !== '' || activeFilter !== 'all')
            ? visible + ' dari ' + total + ' data'
            : total + ' data';
    }

    applyFilters();

    searchInput.addEventListener('input', applyFilters);

    filterPills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.dataset.filter;
            applyFilters();
        });
    });
})();
</script>
@endsection