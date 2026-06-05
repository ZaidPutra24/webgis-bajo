<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Signing In — WEBGIS BAJO</title>

    <!-- Favicon kapal -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 44 44'%3E%3Crect width='44' height='44' rx='10' fill='%23001e40'/%3E%3Crect x='21.5' y='9' width='2' height='18' rx='1' fill='white'/%3E%3Cpath d='M23 10 L23 25 L10 25 Z' fill='white' opacity='0.9'/%3E%3Cpath d='M8 27 Q22 32 36 27' stroke='white' stroke-width='2.5' fill='none' stroke-linecap='round'/%3E%3Cpath d='M6 33 Q11 30 16 33 Q21 36 26 33 Q31 30 36 33' stroke='rgba(255,255,255,0.8)' stroke-width='1.8' fill='none' stroke-linecap='round'/%3E%3C/svg%3E">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet"/>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:    #001e40;
            --navy2:   #003366;
            --teal:    #00696b;
            --teal-lt: #e6fafa;
            --white:   #ffffff;
            --muted:   #64748b;
            --bg:      #f0f5fb;
            --success: #15803d;
            --success-lt: #dcfce7;
        }

        html, body {
            height: 100%;
            background: var(--bg);
            font-family: 'Source Sans 3', sans-serif;
            overflow: hidden;
        }

        /* ── background decorative blobs ── */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.18;
            pointer-events: none;
            z-index: 0;
        }
        .blob-1 { width: 500px; height: 500px; background: var(--navy); top: -150px; left: -150px; }
        .blob-2 { width: 400px; height: 400px; background: var(--teal); bottom: -120px; right: -120px; }
        .blob-3 { width: 260px; height: 260px; background: #4f9cf9; top: 40%; left: 60%; opacity: .10; }

        /* wave strip at bottom */
        .wave-strip {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 90px;
            background: linear-gradient(180deg, transparent, rgba(0,30,64,0.06));
            border-radius: 60% 60% 0 0 / 30px 30px 0 0;
            z-index: 0;
            pointer-events: none;
        }

        /* ── card ── */
        .scene {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }

        .card {
            background: var(--white);
            border-radius: 28px;
            box-shadow:
                0 2px 4px rgba(0,30,64,0.04),
                0 12px 40px rgba(0,30,64,0.10),
                0 40px 80px rgba(0,30,64,0.06);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
            animation: cardIn .55s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes cardIn {
            from { opacity:0; transform: translateY(24px) scale(.97); }
            to   { opacity:1; transform: translateY(0)    scale(1);   }
        }

        /* card header */
        .card-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy2) 100%);
            padding: 32px 36px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .card-header::before {
            content:''; position:absolute;
            top:-50px; right:-50px;
            width:160px; height:160px; border-radius:50%;
            background: rgba(255,255,255,0.04);
        }
        .card-header::after {
            content:''; position:absolute;
            bottom:-40px; left:-30px;
            width:120px; height:120px; border-radius:50%;
            background: rgba(0,105,107,0.15);
        }

        /* logo box */
        .logo-box {
            position: relative; z-index: 1;
            width: 64px; height: 64px; margin: 0 auto 14px;
            background: rgba(255,255,255,0.10);
            border: 1.5px solid rgba(255,255,255,0.18);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .brand-name {
            position: relative; z-index: 1;
            font-family: 'Montserrat', sans-serif;
            font-size: 20px; font-weight: 800;
            color: var(--white); letter-spacing: -.02em;
        }
        .brand-sub {
            position: relative; z-index: 1;
            font-size: 10.5px; color: rgba(255,255,255,.5);
            letter-spacing: .12em; text-transform: uppercase;
            font-weight: 600; margin-top: 2px;
        }

        /* card body */
        .card-body {
            padding: 36px 36px 40px;
            text-align: center;
        }

        /* ── PHASE: loading ── */
        #phase-loading { display: flex; flex-direction: column; align-items: center; gap: 0; }
        #phase-success { display: none;  flex-direction: column; align-items: center; gap: 0; }

        /* spinner ring */
        .spinner-wrap {
            position: relative;
            width: 72px; height: 72px;
            margin: 0 auto 24px;
        }
        .spinner-ring {
            width: 72px; height: 72px;
            border-radius: 50%;
            border: 3px solid #e8edf2;
            border-top-color: var(--navy);
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* inner dot pulse */
        .spinner-dot {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 16px; height: 16px;
            background: var(--navy);
            border-radius: 50%;
            animation: pulse 1.4s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: translate(-50%,-50%) scale(1); }
            50%       { opacity: .4; transform: translate(-50%,-50%) scale(.65); }
        }

        .loading-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 17px; font-weight: 700;
            color: #1a1c1f; margin-bottom: 6px;
        }
        .loading-sub {
            font-size: 13px; color: var(--muted);
            margin-bottom: 28px; line-height: 1.5;
        }

        /* progress bar */
        .progress-track {
            width: 100%; height: 5px;
            background: #e8edf2;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--navy), var(--teal));
            border-radius: 999px;
            width: 0%;
            transition: width .12s linear;
        }
        .progress-label {
            font-size: 11px; font-weight: 700;
            color: var(--muted); letter-spacing: .05em;
            text-align: right;
        }

        /* step indicators */
        .steps {
            display: flex; flex-direction: column;
            gap: 10px; width: 100%;
            margin-top: 24px;
        }
        .step {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            background: #f8fafc;
            font-size: 12.5px; color: var(--muted);
            font-weight: 600;
            transition: all .3s ease;
            opacity: .5;
        }
        .step.active  { opacity: 1; background: #f0f4ff; color: var(--navy); }
        .step.done    { opacity: 1; background: #f0fdf4; color: var(--success); }
        .step-icon {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 13px;
            background: #e8edf2; color: var(--muted);
            transition: all .3s;
        }
        .step.active .step-icon { background: #dbeafe; color: var(--navy); }
        .step.done   .step-icon { background: #bbf7d0; color: var(--success); }

        /* ── PHASE: success ── */
        .success-ring-wrap {
            position: relative;
            width: 80px; height: 80px;
            margin: 0 auto 22px;
        }
        .success-ring {
            width: 80px; height: 80px; border-radius: 50%;
            background: var(--success-lt);
            border: 3px solid #86efac;
            display: flex; align-items: center; justify-content: center;
            animation: successPop .5s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes successPop {
            from { transform: scale(.4); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
        .success-ring svg {
            animation: checkDraw .5s .2s ease both;
        }
        @keyframes checkDraw {
            from { opacity: 0; transform: scale(.5) rotate(-20deg); }
            to   { opacity: 1; transform: scale(1)  rotate(0deg); }
        }

        /* ripple ring */
        .ripple {
            position: absolute; inset: -8px;
            border-radius: 50%;
            border: 2px solid #86efac;
            animation: rippleOut 1.2s ease-out .3s infinite;
            opacity: 0;
        }
        @keyframes rippleOut {
            from { transform: scale(.85); opacity: .6; }
            to   { transform: scale(1.3); opacity: 0; }
        }

        .success-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 20px; font-weight: 800;
            color: var(--success); margin-bottom: 6px;
            animation: fadeUp .4s .2s ease both;
        }
        .success-sub {
            font-size: 13px; color: var(--muted);
            margin-bottom: 28px; line-height: 1.5;
            animation: fadeUp .4s .3s ease both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* redirect bar */
        .redirect-bar {
            width: 100%; padding: 13px 18px;
            background: linear-gradient(135deg, var(--navy), var(--navy2));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: space-between;
            color: var(--white);
            animation: fadeUp .4s .4s ease both;
        }
        .redirect-bar-left { display: flex; flex-direction: column; gap: 2px; text-align: left; }
        .redirect-bar-label { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; opacity: .6; }
        .redirect-bar-dest  { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; }
        .redirect-countdown {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,.12);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px; font-weight: 800;
        }

        /* info footer */
        .info-row {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 20px;
            font-size: 11px; color: #b0b8c4;
            font-weight: 600; letter-spacing: .04em;
        }
        .info-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: #b0b8c4; flex-shrink: 0;
        }
    </style>
</head>
<body>

<!-- Background blobs -->
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>
<div class="wave-strip"></div>

<div class="scene">
    <div class="card">

        <!-- Header -->
        <div class="card-header">
            <div class="logo-box">
                <svg width="38" height="38" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="21.5" y="7" width="2" height="18" rx="1" fill="white"/>
                    <path d="M23 8 L23 23 L9 23 Z" fill="white" opacity="0.9"/>
                    <path d="M7 25 Q22 31 37 25" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                    <path d="M5 31 Q11 28 17 31 Q22 34 27 31 Q32 28 38 31" stroke="rgba(255,255,255,0.7)" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                    <path d="M8 36 Q14 33 20 36 Q25 39 31 36 Q36 33 41 36" stroke="rgba(255,255,255,0.35)" stroke-width="1.4" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <p class="brand-name">WEBGIS BAJO</p>
            <p class="brand-sub">Spatial School Mapping System</p>
        </div>

        <!-- Body -->
        <div class="card-body">

            <!-- ── PHASE LOADING ── -->
            <div id="phase-loading" style="display:flex;">

                <div class="spinner-wrap">
                    <div class="spinner-ring"></div>
                    <div class="spinner-dot"></div>
                </div>

                <p class="loading-title">Sedang masuk ke sistem…</p>
                <p class="loading-sub">Memverifikasi kredensial dan menyiapkan sesi Anda</p>

                <div class="progress-track">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="progress-label" id="progressLabel">0%</div>

                <div class="steps">
                    <div class="step" id="step1">
                        <div class="step-icon" id="step1-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 12c0 6.627 5.373 12 12 12s12-5.373 12-12c0-2.929-1.05-5.61-2.783-7.68A11.955 11.955 0 0112 2.964z"/>
                            </svg>
                        </div>
                        <span id="step1-text">Memverifikasi identitas</span>
                    </div>
                    <div class="step" id="step2">
                        <div class="step-icon" id="step2-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </div>
                        <span id="step2-text">Membuat sesi aman</span>
                    </div>
                    <div class="step" id="step3">
                        <div class="step-icon" id="step3-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                            </svg>
                        </div>
                        <span id="step3-text">Memuat data dashboard</span>
                    </div>
                </div>
            </div>

            <!-- ── PHASE SUCCESS ── -->
            <div id="phase-success" style="display:none;flex-direction:column;align-items:center;">

                <div class="success-ring-wrap">
                    <div class="ripple"></div>
                    <div class="success-ring">
                        <svg width="36" height="36" fill="none" stroke="#15803d" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                </div>

                <p class="success-title">Login Berhasil!</p>
                <p class="success-sub">Selamat datang kembali di Admin Dashboard</p>

                <div class="redirect-bar">
                    <div class="redirect-bar-left">
                        <span class="redirect-bar-label">Mengalihkan ke</span>
                        <span class="redirect-bar-dest">Admin Dashboard</span>
                    </div>
                    <div class="redirect-countdown" id="countdown">3</div>
                </div>

            </div>

            <!-- Info row -->
            <div class="info-row">
                <div class="info-dot"></div>
                <span>WEBGIS BAJO</span>
                <div class="info-dot"></div>
                <span>Spatial Mapping</span>
                <div class="info-dot"></div>
                <span>2026</span>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    var fill     = document.getElementById('progressFill');
    var label    = document.getElementById('progressLabel');
    var step1    = document.getElementById('step1');
    var step2    = document.getElementById('step2');
    var step3    = document.getElementById('step3');
    var phaseLoad= document.getElementById('phase-loading');
    var phaseOk  = document.getElementById('phase-success');
    var cdEl     = document.getElementById('countdown');

    var progress = 0;
    var target   = 0;
    var DEST     = "{{ route('dashboard') }}";

    // ── Timeline ──
    // t=300ms  → step1 active (0%)
    // t=700ms  → step1 done, step2 active (35%)
    // t=1400ms → step2 done, step3 active (65%)
    // t=2100ms → step3 done (95%)
    // t=2600ms → progress 100%, switch to success
    // t=2600+3s → redirect

    function setStep(el, state) {
        el.classList.remove('active','done');
        if (state) el.classList.add(state);
    }

    // Smooth progress animation
    var rafId;
    function animateProgress() {
        if (progress < target) {
            progress = Math.min(progress + 1.2, target);
            fill.style.width  = progress + '%';
            label.textContent = Math.round(progress) + '%';
        }
        if (progress < 100) rafId = requestAnimationFrame(animateProgress);
    }
    rafId = requestAnimationFrame(animateProgress);

    // Step 1 active
    setTimeout(function () {
        setStep(step1, 'active');
        target = 35;
    }, 300);

    // Step 1 done → step 2 active
    setTimeout(function () {
        setStep(step1, 'done');
        setStep(step2, 'active');
        target = 65;
    }, 900);

    // Step 2 done → step 3 active
    setTimeout(function () {
        setStep(step2, 'done');
        setStep(step3, 'active');
        target = 88;
    }, 1700);

    // Step 3 done → 100%
    setTimeout(function () {
        setStep(step3, 'done');
        target = 100;
    }, 2400);

    // Switch to success phase
    setTimeout(function () {
        cancelAnimationFrame(rafId);
        fill.style.width  = '100%';
        label.textContent = '100%';

        phaseLoad.style.display = 'none';
        phaseOk.style.display   = 'flex';

        // Countdown 3 → 2 → 1
        var count = 3;
        var tick = setInterval(function () {
            count--;
            cdEl.textContent = count;
            if (count <= 0) {
                clearInterval(tick);
                window.location.href = DEST;
            }
        }, 1000);
    }, 2750);
})();
</script>
</body>
</html>
