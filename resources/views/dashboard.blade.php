@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'WebGIS Statistics Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .stat-card { border: none; border-radius: 14px; box-shadow: 0 2px 16px rgba(0,0,0,.06); transition: transform .2s, box-shadow .2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.1); }
    .stat-card .accent-bar { height: 4px; border-radius: 0 0 14px 14px; }
    .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .quick-btn { border-radius: 10px; font-size: 13px; font-weight: 600; padding: 9px 16px; transition: all .18s; display: inline-flex; align-items: center; gap: 7px; }
    .quick-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.12); }
    .section-card { border: none; border-radius: 14px; box-shadow: 0 2px 16px rgba(0,0,0,.06); }
    .progress { border-radius: 8px; }
    .badge-status { font-size: 10px; font-weight: 700; letter-spacing: .05em; padding: 4px 10px; border-radius: 20px; }
    .welcome-banner { border: none; border-radius: 16px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #0ea5e9 100%); color: #fff; }
    .welcome-banner .btn-light { font-weight: 600; font-size: 13px; border-radius: 8px; }
    .welcome-banner .btn-outline-light { font-weight: 600; font-size: 13px; border-radius: 8px; }
    .jenjang-pill { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; }
</style>
@endpush

@php
/*
 * ─── Mapping jenjang_id → grup ─────────────────────────────────────────────
 * Disesuaikan dengan JenjangSeeder terbaru (15 jenjang):
 *  id 1=TK, 2=RA       → PAUD / Anak Usia Dini
 *  id 3=SD, 4=MI        → SD / MI  (Dasar)
 *  id 5=SMP, 6=MTS      → SMP / MTS (Menengah Pertama)
 *  id 7=SMA, 8=MA, 9=SMK → SMA / MA / SMK (Menengah Atas)
 *  id 10=PAUD           → PAUD
 *  id 11=KB, 12=PKBM,
 *     13=SKB, 14=LKP,
 *     15=TBM             → Non-Formal
 */
use App\Models\Sekolah;
use App\Models\StatistikSekolah;
use App\Models\KurikulumUtilitas;
use App\Models\JarakSekolahLokasi;

$allSekolah  = Sekolah::with('jenjang')->get();
$total       = $allSekolah->count();

// Hitung per grup
$cntPaud     = $allSekolah->whereIn('jenjang_id', [1, 2, 10])->count();   // TK, RA, PAUD
$cntDasar    = $allSekolah->whereIn('jenjang_id', [3, 4])->count();       // SD, MI
$cntMenPert  = $allSekolah->whereIn('jenjang_id', [5, 6])->count();       // SMP, MTS
$cntMenAtas  = $allSekolah->whereIn('jenjang_id', [7, 8, 9])->count();    // SMA, MA, SMK
$cntNonFormal= $allSekolah->whereIn('jenjang_id', [11,12,13,14,15])->count(); // KB,PKBM,SKB,LKP,TBM

$pctPaud      = $total > 0 ? round($cntPaud     / $total * 100) : 0;
$pctDasar     = $total > 0 ? round($cntDasar    / $total * 100) : 0;
$pctMenPert   = $total > 0 ? round($cntMenPert  / $total * 100) : 0;
$pctMenAtas   = $total > 0 ? round($cntMenAtas  / $total * 100) : 0;
$pctNonFormal = $total > 0 ? round($cntNonFormal/ $total * 100) : 0;

// Negeri vs Swasta
$cntNegeri = $allSekolah->where('status', 'Negeri')->count();
$cntSwasta = $allSekolah->where('status', 'Swasta')->count();

// Statistik kelengkapan data
$statCount  = StatistikSekolah::count();
$utilCount  = KurikulumUtilitas::count();
$statPct    = $total > 0 ? round($statCount / $total * 100) : 0;
$utilPct    = $total > 0 ? round($utilCount / $total * 100) : 0;

// Koordinat
$cntBerKoordinat = $allSekolah->whereNotNull('latitude')->whereNotNull('longitude')->count();
$cntTanpaKoordinat = $total - $cntBerKoordinat;
@endphp

@section('content')

{{-- ─── WELCOME BANNER ─── --}}
<div class="card welcome-banner shadow-sm mb-4 p-4">
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <h4 class="fw-bold mb-1">Welcome, {{ Auth::user()->name }} 👋</h4>
            <p class="mb-3 opacity-75" style="font-size:14px;">
                Manage spatial data, coastal area mapping, and educational statistics through this centralized admin panel.
                There are <strong>{{ $total }} School</strong> from <strong>5 level</strong> that are registered.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('utilitas.index') }}" class="btn btn-light btn-sm quick-btn">
                    <i class="bi bi-sliders text-primary"></i> Curriculum & Utilities
                </a>
                <a href="{{ route('wilayah.index') }}" class="btn btn-outline-light btn-sm quick-btn">
                    <i class="bi bi-map"></i> Area Data
                </a>
                <a href="{{ route('sekolah.index') }}" class="btn btn-outline-light btn-sm quick-btn">
                    <i class="bi bi-building"></i> School Data
                </a>
                <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-light btn-sm quick-btn">
                    <i class="bi bi-globe"></i> View Public Map
                </a>
            </div>
        </div>
        <div class="col-md-4 text-center d-none d-md-block">
            <i class="bi bi-globe-asia-australia text-white opacity-25" style="font-size:110px; line-height:1;"></i>
        </div>
    </div>
</div>

{{-- ─── STAT CARDS ROW 1 ─── --}}
<div class="row g-3 mb-3">
    {{-- Total Sekolah --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em;">Total Schools</div>
                        <div class="fs-2 fw-bold text-dark">{{ $total }}</div>
                        <div class="mt-1 d-flex gap-1 flex-wrap">
                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:10px;">{{ $cntNegeri }} Negeri</span>
                            <span class="badge bg-warning bg-opacity-10 text-warning" style="font-size:10px;">{{ $cntSwasta }} Swasta</span>
                        </div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-building-fill"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('sekolah.index') }}" class="text-decoration-none small text-success fw-semibold d-flex align-items-center gap-1">
                        Manage Schools <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="accent-bar bg-success"></div>
        </div>
    </div>

    {{-- Wilayah --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em;">Village Areas</div>
                        <div class="fs-2 fw-bold text-dark">{{ $totalWilayah }}</div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-map-fill"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('wilayah.index') }}" class="text-decoration-none small text-primary fw-semibold d-flex align-items-center gap-1">
                        Manage Areas <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="accent-bar bg-primary"></div>
        </div>
    </div>

    {{-- Jarak Records --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em;">Distance Data</div>
                        @php $totalJarak = \App\Models\JarakSekolahLokasi::count(); @endphp
                        <div class="fs-2 fw-bold text-dark">{{ $totalJarak }}</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-arrows-expand"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('jarak.index') }}" class="text-decoration-none small text-warning fw-semibold d-flex align-items-center gap-1">
                        Distance Data <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="accent-bar bg-warning"></div>
        </div>
    </div>

    {{-- Data Statistik --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em;">Statistical Data</div>
                        <div class="fs-2 fw-bold text-dark">{{ $statCount }}</div>
                        <div class="mt-1">
                            <span class="badge bg-info bg-opacity-10 text-info" style="font-size:10px;">{{ $statPct }}% complete</span>
                        </div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('statistik.index') }}" class="text-decoration-none small text-info fw-semibold d-flex align-items-center gap-1">
                        Input Statistics <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="accent-bar bg-info"></div>
        </div>
    </div>
</div>

{{-- ─── BOTTOM GRID ─── --}}
<div class="row g-3">

    {{-- Komposisi Jenjang — sesuai 15 jenjang terbaru --}}
    <div class="col-lg-5">
        <div class="card section-card h-100 p-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-dark">School Composition by Level</h6>
                <span class="badge bg-light text-secondary border" style="font-size:10px;">{{ $total }} Total</span>
            </div>

            <div class="d-flex flex-column gap-3 py-1">

                {{-- PAUD / TK / RA --}}
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">PAUD / TK / RA <span class="text-muted fw-normal" style="font-size:10px;">(Anak Usia Dini)</span></span>
                        <span class="fw-bold text-dark">{{ $pctPaud }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-pink" style="width:{{ $pctPaud }}%;background:#ec4899;"></div>
                    </div>
                    <div class="text-muted" style="font-size:11px;margin-top:3px;">{{ $cntPaud }} sekolah</div>
                </div>

                {{-- SD / MI --}}
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">SD / MI <span class="text-muted fw-normal" style="font-size:10px;">(Pendidikan Dasar)</span></span>
                        <span class="fw-bold text-dark">{{ $pctDasar }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-primary" style="width:{{ $pctDasar }}%;"></div>
                    </div>
                    <div class="text-muted" style="font-size:11px;margin-top:3px;">{{ $cntDasar }} sekolah</div>
                </div>

                {{-- SMP / MTS --}}
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">SMP / MTS <span class="text-muted fw-normal" style="font-size:10px;">(Menengah Pertama)</span></span>
                        <span class="fw-bold text-dark">{{ $pctMenPert }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-success" style="width:{{ $pctMenPert }}%;"></div>
                    </div>
                    <div class="text-muted" style="font-size:11px;margin-top:3px;">{{ $cntMenPert }} sekolah</div>
                </div>

                {{-- SMA / MA / SMK --}}
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">SMA / MA / SMK <span class="text-muted fw-normal" style="font-size:10px;">(Menengah Atas)</span></span>
                        <span class="fw-bold text-dark">{{ $pctMenAtas }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-warning" style="width:{{ $pctMenAtas }}%;"></div>
                    </div>
                    <div class="text-muted" style="font-size:11px;margin-top:3px;">{{ $cntMenAtas }} sekolah</div>
                </div>

                {{-- Non-Formal --}}
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">PKBM / LKP / SKB / KB / TBM <span class="text-muted fw-normal" style="font-size:10px;">(Non-Formal)</span></span>
                        <span class="fw-bold text-dark">{{ $pctNonFormal }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" style="width:{{ $pctNonFormal }}%;background:#8b5cf6;"></div>
                    </div>
                    <div class="text-muted" style="font-size:11px;margin-top:3px;">{{ $cntNonFormal }} sekolah</div>
                </div>
            </div>

            {{-- Legenda warna jenjang --}}
            <div class="mt-3 pt-2 border-top d-flex flex-wrap gap-2">
                <span class="jenjang-pill" style="background:#fce7f3;color:#9d174d;"><span style="width:8px;height:8px;border-radius:50%;background:#ec4899;display:inline-block;"></span>PAUD/TK/RA</span>
                <span class="jenjang-pill" style="background:#dbeafe;color:#1e40af;"><span style="width:8px;height:8px;border-radius:50%;background:#3b82f6;display:inline-block;"></span>SD/MI</span>
                <span class="jenjang-pill" style="background:#d1fae5;color:#065f46;"><span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span>SMP/MTS</span>
                <span class="jenjang-pill" style="background:#fef9c3;color:#713f12;"><span style="width:8px;height:8px;border-radius:50%;background:#eab308;display:inline-block;"></span>SMA/MA/SMK</span>
                <span class="jenjang-pill" style="background:#ede9fe;color:#4c1d95;"><span style="width:8px;height:8px;border-radius:50%;background:#8b5cf6;display:inline-block;"></span>Non-Formal</span>
            </div>

            <div class="mt-3">
                <a href="{{ route('sekolah.index') }}" class="btn btn-outline-primary btn-sm quick-btn w-100 justify-content-center">
                    <i class="bi bi-table"></i> View All School Data
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Access + Kelengkapan Data --}}
    <div class="col-lg-7">
        <div class="card section-card h-100 p-4 bg-white">
            <h6 class="fw-bold mb-3 text-dark">Quick Access</h6>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <a href="{{ route('sekolah.create') }}" class="btn btn-outline-primary btn-sm quick-btn w-100 justify-content-start">
                        <i class="bi bi-plus-circle-fill"></i> Add School
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('wilayah.create') }}" class="btn btn-outline-success btn-sm quick-btn w-100 justify-content-start">
                        <i class="bi bi-plus-circle-fill"></i> Add Area
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('statistik.index') }}" class="btn btn-outline-info btn-sm quick-btn w-100 justify-content-start">
                        <i class="bi bi-bar-chart-fill"></i> Input Statistics
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('utilitas.index') }}" class="btn btn-outline-secondary btn-sm quick-btn w-100 justify-content-start">
                        <i class="bi bi-plug-fill"></i> Input Utilities
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('jarak.index') }}" class="btn btn-outline-warning btn-sm quick-btn w-100 justify-content-start">
                        <i class="bi bi-arrows-expand"></i> Distance Analysis
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-dark btn-sm quick-btn w-100 justify-content-start">
                        <i class="bi bi-globe"></i> Public Map
                    </a>
                </div>
            </div>

            {{-- Kelengkapan Data --}}
            <div class="pt-3 border-top">
                <h6 class="fw-bold mb-2 text-dark" style="font-size:13px;">Data Completeness Status</h6>
                <div class="d-flex gap-3 flex-wrap mb-3">
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Completed Statistics</span>
                            <span class="fw-bold">{{ $statCount }}/{{ $total }}</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-info" style="width:{{ $statPct }}%"></div>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Completed Utilities</span>
                            <span class="fw-bold">{{ $utilCount }}/{{ $total }}</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:{{ $utilPct }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Koordinat GPS --}}
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">GPS Coordinates Available</span>
                    <span class="fw-bold">{{ $cntBerKoordinat }}/{{ $total }}</span>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar bg-warning" style="width:{{ $total > 0 ? round($cntBerKoordinat/$total*100) : 0 }}%"></div>
                </div>
                @if($cntTanpaKoordinat > 0)
                <div class="alert alert-warning py-2 px-3 mb-0 mt-2" style="font-size:12px;border-radius:8px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>{{ $cntTanpaKoordinat }} schools</strong> do not have GPS coordinates — they will not appear on the map.
                    <a href="{{ route('sekolah.index') }}" class="alert-link ms-1">Complete now →</a>
                </div>
                @endif
            </div>

            {{-- Breakdown per jenjang (tabel ringkas) --}}
            <div class="pt-3 mt-3 border-top">
                <h6 class="fw-bold mb-2 text-dark" style="font-size:13px;">Jumlah Sekolah per Jenjang</h6>
                <div class="row g-1">
                    @php
                        $jenjangStats = [
                            ['ids'=>[1],'label'=>'TK','color'=>'#ec4899','bg'=>'#fce7f3'],
                            ['ids'=>[2],'label'=>'RA','color'=>'#f43f5e','bg'=>'#ffe4e6'],
                            ['ids'=>[3],'label'=>'SD','color'=>'#3b82f6','bg'=>'#dbeafe'],
                            ['ids'=>[4],'label'=>'MI','color'=>'#6366f1','bg'=>'#e0e7ff'],
                            ['ids'=>[5],'label'=>'SMP','color'=>'#22c55e','bg'=>'#d1fae5'],
                            ['ids'=>[6],'label'=>'MTS','color'=>'#10b981','bg'=>'#ccfbf1'],
                            ['ids'=>[7],'label'=>'SMA','color'=>'#eab308','bg'=>'#fef9c3'],
                            ['ids'=>[8],'label'=>'MA','color'=>'#f59e0b','bg'=>'#fef3c7'],
                            ['ids'=>[9],'label'=>'SMK','color'=>'#f97316','bg'=>'#ffedd5'],
                            ['ids'=>[10],'label'=>'PAUD','color'=>'#e879f9','bg'=>'#fae8ff'],
                            ['ids'=>[11],'label'=>'KB','color'=>'#8b5cf6','bg'=>'#ede9fe'],
                            ['ids'=>[12],'label'=>'PKBM','color'=>'#7c3aed','bg'=>'#f5f3ff'],
                            ['ids'=>[13],'label'=>'SKB','color'=>'#6d28d9','bg'=>'#ede9fe'],
                            ['ids'=>[14],'label'=>'LKP','color'=>'#5b21b6','bg'=>'#e9d5ff'],
                            ['ids'=>[15],'label'=>'TBM','color'=>'#4c1d95','bg'=>'#f3e8ff'],
                        ];
                    @endphp
                    @foreach($jenjangStats as $js)
                    @php $cnt = $allSekolah->whereIn('jenjang_id', $js['ids'])->count(); @endphp
                    <div class="col-4">
                        <div class="d-flex align-items-center justify-content-between rounded px-2 py-1" style="background:{{ $js['bg'] }};">
                            <span style="font-size:11px;font-weight:700;color:{{ $js['color'] }};">{{ $js['label'] }}</span>
                            <span style="font-size:13px;font-weight:800;color:{{ $js['color'] }};">{{ $cnt }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection