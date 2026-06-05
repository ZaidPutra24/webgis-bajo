@extends('layouts.admin')

@section('title', 'School Statistics Data')
@section('page-title', 'Manage School Facilities & Demographics')

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
</style>

<div class="container-fluid px-0 mb-5">

    <div class="mb-4">
        <h4 class="mb-1 text-dark fw-bold">School Statistics & Quantity</h4>
        <p class="text-muted small mb-0">Monitoring list of student quantity, teacher availability, study groups, and input status review.</p>
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
                <h5 class="table-header-title fw-bold mb-0">School List & Metric Status Input</h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="search-count" id="searchCount"></span>
                    <div class="search-wrapper">
                        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" class="search-input" id="tableSearch" placeholder="Search school name..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width: 1100px;">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="5%"  class="ps-4 text-center">No.</th>
                            <th width="20%">School Name</th>
                            <th width="20%">Student Demographics</th>
                            <th width="15%" class="text-center">Human Resources & Class Groups</th>
                            <th width="20%">Infrastructure & Facilities</th>
                            <th width="10%" class="text-center">Data Status</th>
                            <th width="10%" class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody id="statistikTableBody">
                        @forelse($sekolahs as $index => $sekolah)
                        <tr class="searchable-row">
                            <td class="ps-4 text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark fs-6">{{ $sekolah->nama_sekolah }}</span>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="text-muted mb-1">Male: <strong class="text-dark">{{ $sekolah->statistik->siswa_l ?? '0' }}</strong></div>
                                    <div class="text-muted mb-1">Female: <strong class="text-dark">{{ $sekolah->statistik->siswa_p ?? '0' }}</strong></div>
                                    <div class="text-muted">Total: <strong class="text-primary fw-bold">{{ $sekolah->statistik->jumlah_siswa ?? '0' }}</strong></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="small">
                                    <div class="text-muted mb-1">Teachers: <strong class="text-dark">{{ $sekolah->statistik->jumlah_guru ?? '0' }}</strong></div>
                                    <div class="text-muted">Class Groups: <strong class="text-dark">{{ $sekolah->statistik->jumlah_rombel ?? '0' }}</strong></div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="text-muted mb-1">Classrooms: <strong class="text-dark">{{ $sekolah->statistik->ruang_kelas ?? '0' }}</strong></div>
                                    <div class="text-muted mb-1">Laboratories: <strong class="text-dark">{{ $sekolah->statistik->laboratorium ?? '0' }}</strong></div>
                                    <div class="text-muted">Library: <strong class="text-dark">{{ $sekolah->statistik->perpustakaan ?? '0' }}</strong></div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($sekolah->statistik)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-semibold small">
                                        Completed
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill fw-semibold small">
                                        Empty
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('statistik.edit', $sekolah->id) }}" class="btn btn-sm btn-action-outline px-4 rounded-pill">
                                    Manage Statistics
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted small">
                                No school data available. Please add new schools and their statistics by clicking the "Add School Data" button in the Schools menu.
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
    const rows = document.querySelectorAll('#statistikTableBody .searchable-row');
    const noResults = document.getElementById('noResultsRow');
    const countEl = document.getElementById('searchCount');
    const total = rows.length;
    function run() {
        const q = input.value.toLowerCase().trim();
        let visible = 0;
        rows.forEach(r => {
            const match = q === '' || r.textContent.toLowerCase().includes(q);
            r.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        noResults.style.display = (visible === 0 && q !== '') ? '' : 'none';
        countEl.textContent = (q !== '') ? visible + ' dari ' + total + ' data' : total + ' data';
    }
    run();
    input.addEventListener('input', run);
})();
</script>
@endsection
