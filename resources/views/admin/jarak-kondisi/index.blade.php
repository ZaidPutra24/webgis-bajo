@extends('layouts.admin')

@section('title', 'Distance vs School Condition Analysis')
@section('page-title', 'Distance vs School Condition Analysis')

@section('content')
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

    .kelompok-card { border-radius: 1.1rem; border: 1.5px solid #e2e8f0; padding: 1rem 1.1rem; height: 100%; }
    .kelompok-card.tone-mudah { border-color: #bbf7d0; background: #f0fdf4; }
    .kelompok-card.tone-jauh { border-color: #fde68a; background: #fffbeb; }
    .kelompok-card.tone-kepulauan { border-color: #bae6fd; background: #f0f9ff; }
    .kelompok-card.tone-tidak_ada_data { border-color: #cbd5e1; background: #f8fafc; }
    .kelompok-title { font-weight: 700; color: #1e293b; font-size: .95rem; margin-bottom: .1rem; }
    .kelompok-count { font-size: 1.6rem; font-weight: 800; color: #1e293b; }
    .kelompok-metric { display: flex; justify-content: space-between; font-size: .78rem; color: #475569; padding: .18rem 0; border-top: 1px dashed rgba(0,0,0,.08); }
    .kelompok-metric b { color: #1e293b; }

    .filter-pills { display: flex; gap: .5rem; flex-wrap: wrap; }
    .filter-pill { border: 1.5px solid #e2e8f0; border-radius: 2rem; padding: .25rem .85rem; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s; background: transparent; color: #64748b; }
    .filter-pill:hover, .filter-pill.active { background: #4F46E5; border-color: #4F46E5; color: #fff; }

    .search-wrapper { position: relative; }
    .search-wrapper .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .search-input { border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 0.55rem 1rem 0.55rem 2.75rem; font-size: 0.9rem; color: #334155; background-color: #f8fafc; width: 240px; }
    .search-input:focus { border-color: #4F46E5; background-color: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }
    .search-count { font-size: 0.8rem; color: #94a3b8; }

    .custom-table thead th { font-weight: 600; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.04em; padding-top: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .custom-table tbody td { padding-top: .75rem; padding-bottom: .75rem; color: #334155; vertical-align: middle; }

    .badge-kelompok { border-radius: .5rem; padding: .2rem .55rem; font-size: .7rem; font-weight: 700; display: inline-block; white-space: nowrap; }
    .badge-kelompok.mudah { background: rgba(34,197,94,.1); color: #15803d; border: 1px solid #22c55e; }
    .badge-kelompok.jauh { background: rgba(217,119,6,.1); color: #b45309; border: 1px solid #d97706; }
    .badge-kelompok.kepulauan { background: rgba(8,145,178,.1); color: #0891b2; border: 1px solid #0891b2; }
    .badge-kelompok.tidak_ada_data { background: #f1f5f9; color: #64748b; border: 1px dashed #94a3b8; }

    .chip-indikator { display: inline-flex; align-items: center; gap: 4px; border-radius: .45rem; padding: .18rem .5rem; font-size: .7rem; font-weight: 600; white-space: nowrap; }
    .chip-indikator.baik { background: #ecfdf3; color: #166534; }
    .chip-indikator.kurang { background: #fef2f2; color: #991b1b; }
    .chip-indikator.tidak_ada_data { background: #f1f5f9; color: #94a3b8; }

    .beban-ganda-flag { color: #dc2626; font-weight: 700; font-size: .85rem; }

    .basis-note { font-size: .76rem; color: #64748b; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: .85rem; padding: .85rem 1rem; }
    .basis-note b { color: #334155; }
</style>

@php
    $chip = function (array $indikator) {
        return '<span class="chip-indikator ' . $indikator['status'] . '">' . e($indikator['label']) . '</span>';
    };
@endphp

<div class="container-fluid px-0 mb-5">

    <div class="mb-4">
        <h4 class="mb-1 text-dark fw-bold">Distance vs School Condition Analysis</h4>
        <p class="text-muted small mb-2" style="max-width: 900px;">
            Cross-tabulates each school's remoteness group (from real travel-time data, poin 3) against four service
            condition indicators: accreditation, student-teacher ratio, classroom adequacy, and electricity/internet
            access. This is a <strong>descriptive</strong> analysis, not a formal correlation/regression &mdash; the
            number of schools in this case study (Bajo area, 9 villages) is too small for statistical significance
            claims. Treat the patterns below as worth investigating, not as proven causation.
        </p>
        <div class="basis-note">
            <b>Regulatory basis for each threshold</b> (confirmed before building, not assumed):
            remoteness definition &rarr; Permendikbud No. 34/2012 jo. No. 13/2015 Ps. 2 (Kriteria Daerah Khusus) &middot;
            student:teacher ratio &rarr; PP No. 74/2008 Ps. 17 (max 20:1, 15:1 for SMK) &middot;
            classroom adequacy &rarr; Kepmendikdasmen No. 14/2026 &middot;
            accreditation status &rarr; Permendikbudristek No. 38/2023.
        </div>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(79,70,229,.1); color:#4F46E5;"><i class="bi bi-mortarboard-fill"></i></div>
                <div><div class="stat-value">{{ $totalSekolah }}</div><div class="stat-label">Schools Analyzed</div></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(220,38,38,.1); color:#dc2626;"><i class="bi bi-exclamation-diamond-fill"></i></div>
                <div><div class="stat-value">{{ $totalBebanGanda }}</div><div class="stat-label">Double Burden (remote + confirmed gap)</div></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(8,145,178,.1); color:#0891b2;"><i class="bi bi-water"></i></div>
                <div><div class="stat-value">{{ $ringkasanKelompok->firstWhere('kelompok', 'kepulauan')['jumlah_sekolah'] ?? 0 }}</div><div class="stat-label">Boat-only Access Schools</div></div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(217,119,6,.1); color:#d97706;"><i class="bi bi-signpost-2-fill"></i></div>
                <div><div class="stat-value">&gt; {{ rtrim(rtrim(number_format($ambangJauh, 1), '0'), '.') }} min</div><div class="stat-label">"Far" Threshold Used</div></div>
            </div>
        </div>
    </div>

    {{-- ── Ringkasan per kelompok keterpencilan ── --}}
    <div class="card modern-card shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <h5 class="table-header-title fw-bold mb-0">Summary by Remoteness Group</h5>
            <p class="text-muted small mb-0 mt-1">% shown is the share of schools <em>with data</em> that fall below the regulatory threshold for that indicator &mdash; missing data is excluded, never counted as a gap.</p>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($ringkasanKelompok as $k)
                <div class="col-lg-3 col-md-6">
                    <div class="kelompok-card tone-{{ $k['kelompok'] }}">
                        <div class="kelompok-title">{{ $k['label'] }}</div>
                        <div class="kelompok-count">{{ $k['jumlah_sekolah'] }} <span class="fs-6 fw-normal text-muted">school(s)</span></div>
                        @if($k['jumlah_sekolah'] > 0)
                        <div class="text-danger small fw-semibold mb-1">{{ $k['jumlah_beban_ganda'] }} double burden</div>
                        <div class="kelompok-metric"><span>Accreditation C/none</span><b>{{ $k['akreditasi']['persen_kurang'] !== null ? $k['akreditasi']['persen_kurang'].'%' : '–' }}</b></div>
                        <div class="kelompok-metric"><span>Ratio &gt; standard</span><b>{{ $k['rasio_siswa_guru']['persen_kurang'] !== null ? $k['rasio_siswa_guru']['persen_kurang'].'%' : '–' }}</b></div>
                        <div class="kelompok-metric"><span>Classroom shortage</span><b>{{ $k['ruang_kelas']['persen_kurang'] !== null ? $k['ruang_kelas']['persen_kurang'].'%' : '–' }}</b></div>
                        <div class="kelompok-metric"><span>Non-PLN power</span><b>{{ $k['listrik']['persen_kurang'] !== null ? $k['listrik']['persen_kurang'].'%' : '–' }}</b></div>
                        <div class="kelompok-metric"><span>No internet</span><b>{{ $k['internet']['persen_kurang'] !== null ? $k['internet']['persen_kurang'].'%' : '–' }}</b></div>
                        @else
                        <div class="text-muted small">No schools in this group.</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="card modern-card shadow-sm">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
                <h5 class="table-header-title fw-bold mb-0">Per-School Detail</h5>
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
            <div class="filter-pills ps-1">
                <button class="filter-pill active" data-filter="all">All</button>
                <button class="filter-pill" data-filter="beban_ganda">Double Burden</button>
                <button class="filter-pill" data-filter="kepulauan">Boat Only</button>
                <button class="filter-pill" data-filter="jauh">Far (Land)</button>
                <button class="filter-pill" data-filter="mudah">Accessible</button>
                <button class="filter-pill" data-filter="tidak_ada_data">No Distance Data</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover align-middle mb-0" style="min-width: 1350px;">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="3%" class="ps-4 text-center">No.</th>
                            <th width="17%">School</th>
                            <th width="9%">Level</th>
                            <th width="11%" class="text-center">Remoteness</th>
                            <th width="9%" class="text-center">Fastest Time</th>
                            <th width="10%" class="text-center">Accreditation</th>
                            <th width="11%" class="text-center">Student:Teacher</th>
                            <th width="11%" class="text-center">Classrooms</th>
                            <th width="8%" class="text-center">Power</th>
                            <th width="8%" class="text-center">Internet</th>
                            <th width="6%" class="text-center">Flag</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($sekolah as $index => $s)
                        <tr class="searchable-row"
                            data-status="{{ $s['kelompok_keterpencilan'] }}"
                            data-beban="{{ $s['beban_ganda'] ? '1' : '0' }}">
                            <td class="ps-4 text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td><span class="fw-bold text-dark">{{ $s['nama_sekolah'] }}</span></td>
                            <td class="text-muted small">{{ $s['jenjang'] ?? '–' }}</td>
                            <td class="text-center">
                                <span class="badge-kelompok {{ $s['kelompok_keterpencilan'] }}">
                                    @switch($s['kelompok_keterpencilan'])
                                        @case('mudah') Accessible @break
                                        @case('jauh') Far (Land) @break
                                        @case('kepulauan') Boat Only @break
                                        @default No Data
                                    @endswitch
                                </span>
                            </td>
                            <td class="text-center small">
                                {{ $s['keterpencilan_min_mnt'] !== null ? number_format($s['keterpencilan_min_mnt'], 1) . ' min' : '–' }}
                                @if($s['jumlah_desa_terhubung'] > 1)
                                    <div class="text-muted" style="font-size:.68rem;">from {{ $s['jumlah_desa_terhubung'] }} villages</div>
                                @endif
                            </td>
                            <td class="text-center">{!! $chip($s['akreditasi']) !!}</td>
                            <td class="text-center">{!! $chip($s['rasio_siswa_guru']) !!}</td>
                            <td class="text-center">{!! $chip($s['ruang_kelas']) !!}</td>
                            <td class="text-center">{!! $chip($s['listrik']) !!}</td>
                            <td class="text-center">{!! $chip($s['internet']) !!}</td>
                            <td class="text-center">
                                @if($s['beban_ganda'])
                                    <span class="beban-ganda-flag" title="Remote + at least one confirmed condition gap"><i class="bi bi-exclamation-triangle-fill"></i></span>
                                @else
                                    <span class="text-muted">–</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center text-muted py-4">No schools found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('tableSearch');
    const rows = Array.from(document.querySelectorAll('#tableBody .searchable-row'));
    const searchCount = document.getElementById('searchCount');
    const pills = document.querySelectorAll('.filter-pill');
    let activeFilter = 'all';

    function applyFilters() {
        const term = (searchInput?.value ?? '').trim().toLowerCase();
        let visible = 0;
        rows.forEach(row => {
            const matchesSearch = row.textContent.toLowerCase().includes(term);
            const matchesFilter = activeFilter === 'all'
                || (activeFilter === 'beban_ganda' ? row.dataset.beban === '1' : row.dataset.status === activeFilter);
            const show = matchesSearch && matchesFilter;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (searchCount) searchCount.textContent = visible + ' of ' + rows.length + ' schools';
    }

    searchInput?.addEventListener('input', applyFilters);
    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            activeFilter = pill.dataset.filter ?? 'all';
            applyFilters();
        });
    });
    applyFilters();
});
</script>
@endpush
@endsection
