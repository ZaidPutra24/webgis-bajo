@extends('layouts.admin')

@section('title', 'PKBM Needs')
@section('page-title', 'PKBM Needs Data (Package A/B/C)')

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
    .info-banner {
        background: linear-gradient(135deg, #f0fdfa 0%, #eef2ff 100%);
        border-radius: 1rem;
        border: 1px solid #e0e7ff;
    }
</style>

<div class="container-fluid px-0 mb-5">

    <div class="mb-4">
        <h4 class="mb-1 text-dark fw-bold">PKBM Needs (Package A/B/C)</h4>
        <p class="text-muted small mb-0">Number of BPB (Never Attended School) ages 19-24 & 25+ per village — prospective Package A/B/C equivalency participants.</p>
    </div>

    <div class="info-banner p-3 mb-4">
        <div class="d-flex gap-2 align-items-start">
            <i class="bi bi-info-circle-fill text-primary mt-1"></i>
            <div class="small text-secondary">
                This data is separate from <a href="{{ route('penduduk-jenjang.index') }}" class="fw-semibold">School-Age Population & ATS per Level</a>
                because PKBM does not have formal school-age cohorts like PAUD/SD/SMP/SMA — its targets are
                BPB who have passed school age. The total here is <strong>total</strong> needs,
                not yet detailed per Package A/B/C (source data has not reached that level).
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
            <h5 class="table-header-title fw-bold mb-0">List of Villages</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width: 900px;">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="5%" class="ps-4 text-center">No.</th>
                            <th width="25%">Village Name</th>
                            <th width="15%" class="text-center">BPB 19-24</th>
                            <th width="15%" class="text-center">BPB 25+</th>
                            <th width="15%" class="text-center">Total Needs</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="10%" class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wilayahs as $index => $desa)
                        @php $row = $desa->kebutuhan_row; @endphp
                        <tr>
                            <td class="ps-4 text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td><span class="fw-bold text-dark fs-6">{{ $desa->nama_wilayah }}</span></td>
                            <td class="text-center">{{ $row->bpb_19_24 ?? 0 }}</td>
                            <td class="text-center">{{ $row->bpb_25_plus ?? 0 }}</td>
                            <td class="text-center fw-bold">{{ $row ? $row->total_kebutuhan : 0 }}</td>
                            <td class="text-center">
                                @if(!$row)
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill fw-semibold small">
                                        No Data Yet
                                    </span>
                                @elseif(str_starts_with((string) $row->sumber_data, 'ATS Riil'))
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-semibold small">
                                        Real Data
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 rounded-pill fw-semibold small">
                                        Manual Input
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('kebutuhan-pkbm.edit', $desa->id) }}" class="btn btn-sm btn-action-outline px-4 rounded-pill">
                                    Manage
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted small">
                                No village data available yet. Please add villages via the Village Areas menu first.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
