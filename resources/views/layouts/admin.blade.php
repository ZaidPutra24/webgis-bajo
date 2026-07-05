<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin WebGIS School') — WebGIS Bajo</title>

    <!-- Favicon: logo kapal WebGIS Bajo -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 44 44'%3E%3Crect width='44' height='44' rx='10' fill='%23001e40'/%3E%3Crect x='21.5' y='9' width='2' height='18' rx='1' fill='white'/%3E%3Cpath d='M23 10 L23 25 L10 25 Z' fill='white' opacity='0.9'/%3E%3Cpath d='M8 27 Q22 32 36 27' stroke='white' stroke-width='2.5' fill='none' stroke-linecap='round'/%3E%3Cpath d='M6 33 Q11 30 16 33 Q21 36 26 33 Q31 30 36 33' stroke='rgba(255,255,255,0.8)' stroke-width='1.8' fill='none' stroke-linecap='round'/%3E%3Cpath d='M9 37 Q14 34 19 37 Q24 40 29 37 Q34 34 39 37' stroke='rgba(255,255,255,0.5)' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E">
    
    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-bg: #0d1117;
            --sidebar-hover: rgba(255,255,255,.08);
            --sidebar-active: rgba(99,102,241,.25);
            --accent: #6366f1;
            --accent-2: #22c55e;
        }
        body { background: #f1f5f9; overflow-x: hidden; font-family: 'Segoe UI', system-ui, sans-serif; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 240px; height: 100vh;
            background: var(--sidebar-bg);
            color: #e2e8f0;
            display: flex; flex-direction: column;
            box-shadow: 4px 0 24px rgba(0,0,0,.25);
            z-index: 1040;
            overflow-y: auto;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand .brand-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent) 0%, #818cf8 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff; flex-shrink: 0;
        }
        .sidebar-brand .brand-name { font-size: 15px; font-weight: 700; color: #fff; line-height: 1.2; }
        .sidebar-brand .brand-sub  { font-size: 10px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: .06em; }

        .sidebar-section {
            padding: 12px 12px 4px;
            font-size: 9.5px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #475569;
        }
        .sidebar .nav-link {
            color: #94a3b8; font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 8px; margin: 1px 8px;
            transition: all .18s;
        }
        .sidebar .nav-link i { font-size: 15px; flex-shrink: 0; }
        .sidebar .nav-link:hover { color: #e2e8f0; background: var(--sidebar-hover); }
        .sidebar .nav-link.active {
            color: #fff; background: var(--sidebar-active);
            border-left: 3px solid var(--accent);
            padding-left: 11px;
        }
        .sidebar-footer { margin-top: auto; padding: 12px; border-top: 1px solid rgba(255,255,255,.07); }

        /* ─── TOPBAR ─── */
        .topbar {
            position: fixed; top: 0; left: 240px; right: 0; height: 60px;
            background: #fff; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; z-index: 1030;
            box-shadow: 0 1px 10px rgba(0,0,0,.06);
        }
        .topbar-title { font-size: 16px; font-weight: 700; color: #1e293b; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px; border-radius: 8px; background: #f8fafc;
            border: 1px solid #e2e8f0; font-size: 13px; color: #475569;
        }
        .topbar-user b { color: #1e293b; }

        /* ─── MAIN CONTENT ─── */
        .main-content {
            margin-left: 240px; padding-top: 60px;
            min-height: 100vh;
        }
        .page-body { padding: 28px; }

        /* ─── FLASH MESSAGES ─── */
        .flash-bar { margin: 0 28px; padding-top: 18px; }

        /* ─── MOBILE TOGGLE ─── */
        .sidebar-toggle { display: none; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { left: 0; }
            .sidebar-toggle { display: flex; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- ============ SIDEBAR ============ -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <!-- Logo SVG WebGIS Bajo (perahu layar + gelombang, sesuai halaman welcome) -->
        <svg width="36" height="36" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
            <rect width="44" height="44" rx="10" fill="rgba(255,255,255,0.12)"/>
            <rect x="21.5" y="9" width="2" height="18" rx="1" fill="white"/>
            <path d="M23 10 L23 25 L10 25 Z" fill="white" opacity="0.9"/>
            <path d="M8 27 Q22 32 36 27" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <path d="M6 33 Q11 30 16 33 Q21 36 26 33 Q31 30 36 33" stroke="rgba(255,255,255,0.7)" stroke-width="1.8" fill="none" stroke-linecap="round"/>
            <path d="M9 37 Q14 34 19 37 Q24 40 29 37 Q34 34 39 37" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        </svg>
        <div>
            <div class="brand-name">WebGIS Bajo</div>
            <div class="brand-sub">Admin Console</div>
        </div>
    </div>

    <div class="pt-2">
        <div class="sidebar-section">Main Menu</div>
        <nav>
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </nav>

        <div class="sidebar-section mt-2">Master Data</div>
        <nav>
            <a href="{{ route('kecamatan.index') }}"
               class="nav-link {{ Request::is('kecamatan*') ? 'active' : '' }}">
                <i class="bi bi-bounding-box-circles"></i> Kecamatan Areas
            </a>
            <a href="{{ route('wilayah.index') }}"
               class="nav-link {{ Request::is('wilayah*') ? 'active' : '' }}">
                <i class="bi bi-map"></i> Village Areas
            </a>
            <a href="{{ route('sekolah.index') }}"
               class="nav-link {{ Request::is('sekolah*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Schools
            </a>
        </nav>

        <div class="sidebar-section mt-2">Analysis & Statistics</div>
        <nav>
            <a href="{{ route('jarak.index') }}"
               class="nav-link {{ Request::is('jarak*') ? 'active' : '' }}">
                <i class="bi bi-arrows-expand"></i> Distance Analysis
            </a>
            <a href="{{ route('statistik.index') }}"
               class="nav-link {{ Request::is('statistik*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> School Statistics
            </a>
            <a href="{{ route('utilitas.index') }}"
               class="nav-link {{ request()->routeIs('utilitas.*') ? 'active' : '' }}">
                <i class="bi bi-plug-fill"></i> Curriculum & Utilities
            </a>
        </nav>

        <div class="sidebar-section mt-2">Account</div>
        <nav>
            <a href="{{ route('profile.edit') }}"
               class="nav-link {{ Request::is('profile*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i> My Profile
            </a>
            <a href="{{ url('/') }}" class="nav-link" target="_blank">
                <i class="bi bi-eye"></i> View Public Map
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}"
              onsubmit="return confirm('Are you sure you want to log out?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-right me-2"></i>Log Out
            </button>
        </form>
    </div>
</aside>

<!-- ============ TOPBAR ============ -->
<header class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-light sidebar-toggle border" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
    </div>
    <div class="topbar-right">
        <div class="topbar-user">
            <i class="bi bi-person-circle text-primary"></i>
            <b>{{ Auth::user()->name }}</b>
        </div>
    </div>
</header>

<!-- ============ MAIN ============ -->
<div class="main-content">

    {{-- Flash Messages --}}
    @if(session('success') || session('error') || session('warning'))
    <div class="flash-bar">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
            <i class="bi bi-x-circle-fill"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('warning') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
    </div>
    @endif

    <div class="page-body">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('show'));
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
    setTimeout(() => {
        document.querySelectorAll('.flash-bar .alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 4000);
</script>
@stack('scripts')
</body>
</html>
