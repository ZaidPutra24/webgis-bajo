@extends('layouts.admin')

@section('page-title', 'Curriculum & Utilities')

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
    .no-results-row { display: none; }
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
        <h4 class="mb-1 text-dark fw-bold">Curriculum & Utilities Sekolah</h4>
        <p class="text-muted small mb-0">Monitoring list of academic systems, electricity, internet facilities, and land of all agencies.</p>
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
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="table-header-title fw-bold mb-0">Curriculum Management Data & Utility Facilities</h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="search-count" id="searchCount"></span>
                    <div class="search-wrapper">
                        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" class="search-input" id="tableSearch" placeholder="Search schools or curriculum..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width: 1000px;">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="5%"  class="ps-4 text-center">No.</th>
                            <th width="25%">School Name</th>
                            <th width="25%">Curriculum & Operator</th>
                            <th width="30%">Basic Utilities</th>
                            <th width="15%" class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="utilitasTableBody">
                        @forelse($sekolah as $index => $item)
                        <tr class="searchable-row">
                            <td class="ps-4 text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark fs-6">{{ $item->nama_sekolah }}</span>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="text-muted mb-1">Curriculum: <strong class="text-dark">{{ $item->utilitas->kurikulum ?? '-' }}</strong></div>
                                    <div class="text-muted">Operator: <strong class="text-dark">{{ $item->utilitas->penyelenggara ?? '-' }}</strong></div>
                                </div>
                            </td>
                            <td>
                                <div class="row row-cols-2 g-2 small">
                                    <div class="text-muted">Internet: <strong class="text-dark">{{ $item->utilitas->akses_internet ?? '-' }}</strong></div>
                                    <div class="text-muted">Main Power Source: <strong class="text-dark">{{ $item->utilitas->sumber_listrik ?? '-' }}</strong></div>
                                    <div class="text-muted">Power: <strong class="text-dark">{{ isset($item->utilitas->daya_listrik) ? $item->utilitas->daya_listrik . ' VA' : '-' }}</strong></div>
                                    <div class="text-muted">Land Area: <strong class="text-dark">{{ isset($item->utilitas->luas_tanah) ? $item->utilitas->luas_tanah . ' m²' : '-' }}</strong></div>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('utilitas.edit', $item->id) }}" class="btn btn-sm btn-action-outline px-4 rounded-pill">
                                    Manage Utilities
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted small">
                                No school data available. Please add new schools and their utility information by clicking the "Add School Data" button in the Schools menu.
                            </td>
                        </tr>
                        @endforelse
                        <tr class="no-results-row" id="noResultsRow">
                            <td colspan="5" class="text-center py-5 text-muted small">
                                <svg width="40" height="40" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" class="mb-2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <div>No data found matching your search.</div>
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
    const rows = Array.from(document.querySelectorAll('#utilitasTableBody .searchable-row'));
    const noResults = document.getElementById('noResultsRow');
    const countEl = document.getElementById('searchCount');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const PER_PAGE = 10;
    const total = rows.length;
    let currentPage = 1;

    function getFilteredRows() {
        const q = input.value.toLowerCase().trim();
        return rows.filter(r => q === '' || r.textContent.toLowerCase().includes(q));
    }

    function renderPage() {
        const filteredRows = getFilteredRows();
        const totalFiltered = filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(totalFiltered / PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PER_PAGE;
        const end = start + PER_PAGE;

        rows.forEach(r => r.style.display = 'none');
        let displayNum = 1;
        filteredRows.forEach(function (row, idx) {
            if (idx >= start && idx < end) {
                row.style.display = '';
                row.querySelector('td:first-child').textContent = start + displayNum;
                displayNum++;
            }
        });

        noResults.style.display = (totalFiltered === 0 && input.value.trim() !== '') ? '' : 'none';
        countEl.textContent = input.value.trim() !== '' ? totalFiltered + ' dari ' + total + ' data' : total + ' data';

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
})();
</script>
@endsection