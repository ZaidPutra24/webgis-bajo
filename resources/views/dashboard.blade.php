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
    .log-table tbody tr:hover { background: #f8fafc; }
    .badge-status { font-size: 10px; font-weight: 700; letter-spacing: .05em; padding: 4px 10px; border-radius: 20px; }
    .welcome-banner { border: none; border-radius: 16px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #0ea5e9 100%); color: #fff; }
    .welcome-banner .btn-light { font-weight: 600; font-size: 13px; border-radius: 8px; }
    .welcome-banner .btn-outline-light { font-weight: 600; font-size: 13px; border-radius: 8px; }
</style>
@endpush

@section('content')

{{-- ─── WELCOME BANNER ─── --}}
<div class="card welcome-banner shadow-sm mb-4 p-4">
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <h4 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name }}</h4>
            <p class="mb-3 opacity-75" style="font-size:14px;">
                Manage spatial data, Bajo coastal area mapping, and education statistics through this centralised administration panel.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('utilitas.index') }}" class="btn btn-light btn-sm quick-btn">
                    <i class="bi bi-sliders text-primary"></i> Curriculum & Utilities
                </a>
                <a href="{{ route('wilayah.index') }}" class="btn btn-outline-light btn-sm quick-btn">
                    <i class="bi bi-map"></i> Village Area Data
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

{{-- ─── STAT CARDS ─── --}}
<div class="row g-3 mb-4">
    {{-- Village Areas --}}
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

    {{-- Schools --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em;">Total Schools</div>
                        <div class="fs-2 fw-bold text-dark">{{ $totalSekolah }}</div>
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

    {{-- Distance Records --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em;">Distance Records</div>
                        @php $totalJarak = \App\Models\JarakSekolahLokasi::count(); @endphp
                        <div class="fs-2 fw-bold text-dark">{{ $totalJarak }}</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-arrows-expand"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('jarak.index') }}" class="text-decoration-none small text-warning fw-semibold d-flex align-items-center gap-1">
                        Distance Analysis <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="accent-bar bg-warning"></div>
        </div>
    </div>

    {{-- Statistics Filled --}}
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em;">Statistics Data</div>
                        @php $statCount = \App\Models\StatistikSekolah::count(); @endphp
                        <div class="fs-2 fw-bold text-dark">{{ $statCount }}</div>
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

    {{-- School Level Composition --}}
    <div class="col-lg-5">
        <div class="card section-card h-100 p-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-dark">School Composition by Level</h6>
                <span class="badge bg-light text-secondary border" style="font-size:10px;">Live</span>
            </div>
            <div class="d-flex flex-column gap-3 py-1">
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">SD / MI (Elementary)</span>
                        <span class="fw-bold text-dark">{{ $persenSd }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-primary" style="width:{{ $persenSd }}%"></div>
                    </div>
                    <div class="text-muted" style="font-size:11px;margin-top:3px;">
                        @php $sdCount = \App\Models\Sekolah::whereBetween('jenjang_id', [1, 2])->count(); @endphp
                        {{ $sdCount }} schools
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">SMP / MTs (Junior High)</span>
                        <span class="fw-bold text-dark">{{ $persenSmp }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-success" style="width:{{ $persenSmp }}%"></div>
                    </div>
                    <div class="text-muted" style="font-size:11px;margin-top:3px;">
                        @php $smpCount = \App\Models\Sekolah::whereBetween('jenjang_id', [3, 4])->count(); @endphp
                        {{ $smpCount }} schools
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">SMA / MA (Senior High)</span>
                        <span class="fw-bold text-dark">{{ $persenSma }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-warning" style="width:{{ $persenSma }}%"></div>
                    </div>
                    <div class="text-muted" style="font-size:11px;margin-top:3px;">
                        @php $smaCount = \App\Models\Sekolah::whereBetween('jenjang_id', [5, 6])->count(); @endphp
                        {{ $smaCount }} schools
                    </div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top">
                <a href="{{ route('sekolah.index') }}" class="btn btn-outline-primary btn-sm quick-btn w-100 justify-content-center">
                    <i class="bi bi-table"></i> View All School Data
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Access Modules --}}
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
                        <i class="bi bi-plus-circle-fill"></i> Add Village Area
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

            <div class="mt-auto pt-3 border-top">
                <h6 class="fw-bold mb-2 text-dark" style="font-size:13px;">Data Completeness Status</h6>
                <div class="d-flex gap-3 flex-wrap">
                    @php
                        $totalSk = \App\Models\Sekolah::count();
                        $statTerisi = \App\Models\StatistikSekolah::count();
                        $utilTerisi = \App\Models\KurikulumUtilitas::count();
                        $statPct = $totalSk > 0 ? round($statTerisi / $totalSk * 100) : 0;
                        $utilPct = $totalSk > 0 ? round($utilTerisi / $totalSk * 100) : 0;
                    @endphp
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Statistics Filled</span>
                            <span class="fw-bold">{{ $statTerisi }}/{{ $totalSk }}</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-info" style="width:{{ $statPct }}%"></div>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Utilities Filled</span>
                            <span class="fw-bold">{{ $utilTerisi }}/{{ $totalSk }}</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:{{ $utilPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection