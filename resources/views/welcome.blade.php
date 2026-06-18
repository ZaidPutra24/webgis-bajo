<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>WEBGIS BAJO | Spatial School Mapping</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 44 44'%3E%3Crect width='44' height='44' rx='10' fill='%23001e40'/%3E%3Crect x='21.5' y='9' width='2' height='18' rx='1' fill='white'/%3E%3Cpath d='M23 10 L23 25 L10 25 Z' fill='white' opacity='0.9'/%3E%3Cpath d='M8 27 Q22 32 36 27' stroke='white' stroke-width='2.5' fill='none' stroke-linecap='round'/%3E%3Cpath d='M6 33 Q11 30 16 33 Q21 36 26 33 Q31 30 36 33' stroke='rgba(255,255,255,0.8)' stroke-width='1.8' fill='none' stroke-linecap='round'/%3E%3Cpath d='M9 37 Q14 34 19 37 Q24 40 29 37 Q34 34 39 37' stroke='rgba(255,255,255,0.5)' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet"/>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>

    <!-- Leaflet CSS -->
    <link crossorigin="" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" rel="stylesheet"/>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-surface": "#1a1c1f",
                        "primary-fixed-dim": "#a7c8ff",
                        "error-container": "#ffdad6",
                        "tertiary": "#1b1f20",
                        "surface-tint": "#3a5f94",
                        "surface-container-high": "#e8e8ed",
                        "surface-container-highest": "#e2e2e7",
                        "inverse-on-surface": "#f1f0f5",
                        "on-primary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "surface-dim": "#dad9de",
                        "primary": "#001e40",
                        "on-secondary": "#ffffff",
                        "on-error": "#ffffff",
                        "surface-bright": "#f9f9fe",
                        "primary-container": "#003366",
                        "surface-variant": "#e2e2e7",
                        "on-primary-fixed": "#001b3c",
                        "inverse-surface": "#2f3034",
                        "secondary-fixed-dim": "#2ddbde",
                        "tertiary-container": "#303436",
                        "on-primary-container": "#799dd6",
                        "on-secondary-container": "#006e70",
                        "on-error-container": "#93000a",
                        "tertiary-fixed": "#e0e3e5",
                        "outline-variant": "#c3c6d1",
                        "surface": "#f9f9fe",
                        "primary-fixed": "#d5e3ff",
                        "background": "#f9f9fe",
                        "error": "#ba1a1a",
                        "inverse-primary": "#a7c8ff",
                        "on-surface-variant": "#43474f",
                        "on-background": "#1a1c1f",
                        "secondary-container": "#56f5f8",
                        "on-tertiary": "#ffffff",
                        "surface-container": "#eeedf2",
                        "secondary-fixed": "#5af8fb",
                        "outline": "#737780",
                        "secondary": "#00696b",
                        "surface-container-low": "#f4f3f8"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px"
                    },
                    fontFamily: {
                        "headline-md": ["Montserrat"],
                        "body-md": ["Source Sans 3"],
                        "headline-lg": ["Montserrat"],
                        "display-lg": ["Montserrat"],
                        "label-md": ["Source Sans 3"],
                    },
                    fontSize: {
                        "headline-md": ["24px", { lineHeight: "1.4", fontWeight: "600" }],
                        "body-md": ["16px", { lineHeight: "1.6", fontWeight: "400" }],
                        "label-md": ["14px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
                        "headline-lg": ["32px", { lineHeight: "1.3", fontWeight: "600" }],
                        "display-lg": ["56px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }]
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background: radial-gradient(circle at top right, #f0f7ff, #f9f9fe);
            overflow-x: hidden;
        }
        .water-pattern {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            opacity: 0.25;
            pointer-events: none;
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
        }
        .leaflet-container { font-family: 'Source Sans 3', sans-serif !important; }
        .leaflet-popup-content-wrapper {
            border-radius: 20px !important;
            padding: 0 !important;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.18), 0 8px 10px -6px rgb(0 0 0 / 0.12) !important;
            overflow: hidden;
        }
        .leaflet-popup-content {
            margin: 0 !important;
            width: auto !important;
        }
        .leaflet-popup-tip-container { display: none; }
        .custom-marker { transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .custom-marker:hover { transform: scale(1.2) translateY(-5px); }

        .webgis-logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .webgis-logo-icon svg { display: block; }

        /* ── USER PANEL ── */
        .user-panel-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9998;
            background: rgba(0,30,64,0.18);
            backdrop-filter: blur(2px);
        }
        .user-panel-overlay.open { display: block; }

        .user-panel {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 360px;
            max-width: 95vw;
            background: #fff;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            box-shadow: -8px 0 40px rgba(0,30,64,0.14);
            padding-top: 0;
        }
        .user-panel.open { transform: translateX(0); }

        .user-panel-header {
            background: #001e40;
            padding: 28px 24px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .panel-section {
            padding: 20px 24px 0;
        }
        .panel-section-title {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #737780;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e2e7;
        }
        .panel-stat-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #f4f3f8;
            margin-bottom: 10px;
            transition: all .2s;
            cursor: default;
        }
        .panel-stat-card:hover {
            background: #e8e8ed;
            transform: translateX(4px);
        }
        .panel-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .panel-stat-label {
            font-size: 11px;
            color: #43474f;
            font-weight: 600;
        }
        .panel-stat-value {
            font-size: 22px;
            font-weight: 800;
            color: #001e40;
            line-height: 1.1;
        }

        /* ── POPUP TAB STYLES ── */
        .popup-wrapper { font-family: 'Source Sans 3', sans-serif; min-width: 320px; max-width: 350px; }
        .popup-header { padding: 14px 16px 12px; }
        .popup-tab-bar {
            display: flex;
            background: #f4f3f8;
            border-bottom: 1px solid #e2e2e7;
        }
        .popup-tab {
            flex: 1;
            padding: 9px 4px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #737780;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: all .2s;
            border-bottom: 3px solid transparent;
        }
        .popup-tab.active {
            color: #001e40;
            background: #fff;
            border-bottom: 3px solid #001e40;
        }
        .popup-tab:hover:not(.active) { background: #eeedf2; color: #001e40; }
        .popup-panel { display: none; padding: 14px 16px; background: #fff; }
        .popup-panel.active { display: block; }
        .popup-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            padding: 5px 0;
            border-bottom: 1px dashed #e2e2e7;
        }
        .popup-row:last-child { border-bottom: none; }
        .popup-row .label { color: #43474f; }
        .popup-row .value { font-weight: 700; color: #001e40; text-align: right; max-width: 180px; }
        .popup-chip {
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }
        .popup-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 4px; }
        .popup-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 6px; }
        .popup-stat-card {
            background: #f4f3f8;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 11px;
        }
        .popup-stat-card .sc-label { color: #43474f; margin-bottom: 2px; font-size: 10px; }
        .popup-stat-card .sc-value { font-weight: 800; color: #001e40; font-size: 15px; }
        .popup-stat-card.sc-accent { background: #e6fafa; }
        .popup-stat-card.sc-accent .sc-value { color: #00696b; }
        .popup-jarak-list::-webkit-scrollbar { width: 4px; }
        .popup-jarak-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .popup-jarak-list::-webkit-scrollbar-thumb { background: #00696b; border-radius: 4px; }
        .popup-empty {
            text-align: center;
            padding: 20px 10px;
            font-size: 12px;
            color: #737780;
            font-style: italic;
        }
        .popup-btn-ukur {
            width: 100%;
            margin-top: 12px;
            background: #001e40;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 10px 0;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .popup-btn-ukur:hover { background: #003366; }

        /* ── LEGEND ── */
        .map-legend {
            position: absolute;
            bottom: 24px;
            left: 16px;
            z-index: 999;
            background: rgba(255,255,255,0.95);
            border-radius: 14px;
            padding: 10px 14px;
            box-shadow: 0 4px 20px rgba(0,30,64,0.12);
            font-family: 'Source Sans 3', sans-serif;
            font-size: 11px;
            border: 1px solid #e2e2e7;
        }
        .legend-title {
            font-weight: 700;
            color: #001e40;
            margin-bottom: 6px;
            font-size: 10px;
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 4px;
            color: #43474f;
        }
        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,0.8);
        }

        /* ── TOAST ── */
        #toast-ukur {
            position: fixed;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            background: #001e40;
            color: #fff;
            padding: 12px 24px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 8px 24px rgba(0,30,64,0.3);
            z-index: 9999;
            display: none;
            white-space: nowrap;
        }

        /* ── MAP MODE SWITCHER ── */
        .map-mode-switcher {
            position: absolute;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            border-radius: 999px;
            padding: 5px 6px;
            box-shadow: 0 4px 20px rgba(0,30,64,0.14);
            border: 1px solid rgba(255,255,255,0.8);
            max-width: calc(100% - 32px);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .map-mode-switcher::-webkit-scrollbar { display: none; }
        .map-mode-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            color: #43474f;
            background: transparent;
            transition: all .2s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .map-mode-btn:hover { background: #f4f3f8; color: #001e40; }
        .map-mode-btn.active {
            background: #001e40;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0,30,64,0.25);
        }
        .map-mode-btn .mode-icon {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            flex-shrink: 0;
            display: inline-block;
        }
        .mode-icon-standard  { background: linear-gradient(135deg,#a8d5b5,#6db8e8); }
        .mode-icon-satellite { background: linear-gradient(135deg,#2c3e50,#4a6741); }
        .mode-icon-terrain   { background: linear-gradient(135deg,#c5a55a,#7a9e6e); }
        .mode-icon-dark      { background: linear-gradient(135deg,#1a1a2e,#16213e); }

        /* ── IMAGE MODAL ── */
        .image-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(0,0,0,0.75);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            animation: fadeIn 0.25s ease;
        }
        .image-modal-overlay.open { display: flex; }
        .image-modal-content {
            width: min(90vw, 960px);
            height: min(80vh, 720px);
            border-radius: 16px;
            background: rgba(255,255,255,0.04);
            box-shadow: 0 24px 48px rgba(0,0,0,0.4);
            object-fit: contain;
            object-position: center;
            display: block;
            cursor: default;
            animation: scaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .image-modal-close {
            position: fixed;
            top: 24px;
            right: 32px;
            z-index: 100000;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.25);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            color: #fff;
            font-size: 28px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
            backdrop-filter: blur(4px);
        }
        .image-modal-close:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.08);
        }
        .image-modal-title {
            position: fixed;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            font-weight: 600;
            font-family: 'Source Sans 3', sans-serif;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 80vw;
            pointer-events: none;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { transform: scale(0.85); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* ── POPUP IMAGE THUMBNAIL ── */
        .popup-img-thumb {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            transition: opacity .2s;
        }
        .popup-img-thumb:hover { opacity: 0.85; }
        .popup-img-thumb-placeholder {
            width: 100%;
            height: 140px;
            background: #f1f5f9;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
        }

        /* ── FILTER CONTROLS ── */
        .filter-controls-wrapper {
            background: #fff;
            border-radius: 16px;
            padding: 16px 20px;
            margin-top: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,.06);
            border: 1px solid #e2e2e7;
        }
        .filter-controls-wrapper .filter-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }
        .filter-controls-wrapper .filter-group-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #737780;
            margin-right: 6px;
        }
        .filter-controls-wrapper .filter-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 999px;
            border: 1.5px solid #e2e2e7;
            background: transparent;
            font-size: 11px;
            font-weight: 600;
            color: #43474f;
            cursor: pointer;
            transition: all .2s;
            white-space: nowrap;
            line-height: 1;
        }
        .filter-controls-wrapper .filter-btn:hover {
            background: #f4f3f8;
            border-color: #c3c6d1;
        }
        .filter-controls-wrapper .filter-btn.active {
            background: #001e40;
            border-color: #001e40;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,30,64,0.2);
        }
        .filter-controls-wrapper .filter-btn.reset-btn {
            border-color: #ef4444;
            color: #ef4444;
        }
        .filter-controls-wrapper .filter-btn.reset-btn:hover {
            background: #ef4444;
            color: #fff;
        }
        .filter-controls-wrapper .filter-divider {
            width: 1px;
            height: 28px;
            background: #e2e2e7;
            margin: 0 8px;
        }
        .filter-controls-wrapper .filter-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 16px;
        }
        .filter-controls-wrapper .filter-stats {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            margin-left: auto;
        }
        .filter-controls-wrapper .filter-stats span {
            color: #001e40;
            font-weight: 800;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .map-mode-btn .mode-label { display: none; }
            .map-mode-btn { padding: 6px 10px; gap: 0; }
            .map-legend {
                bottom: 8px;
                left: 8px;
                padding: 6px 10px;
                font-size: 9px;
            }
            .legend-title { font-size: 9px; margin-bottom: 4px; }
            .legend-item { gap: 5px; margin-bottom: 2px; }
            .legend-dot, .legend-item > div[style] {
                width: 10px !important;
                height: 10px !important;
                flex-shrink: 0;
            }
            #toast-ukur {
                white-space: normal;
                max-width: 88vw;
                text-align: center;
                font-size: 11px;
                padding: 10px 16px;
                line-height: 1.4;
            }
            .popup-wrapper { min-width: 260px; max-width: 280px; }
            .image-modal-content { width: 95vw; height: 70vh; }
            .image-modal-close { top: 16px; right: 16px; width: 40px; height: 40px; font-size: 22px; }
            .popup-img-thumb { height: 100px; }
            .filter-controls-wrapper { padding: 12px 14px; }
            .filter-controls-wrapper .filter-btn { font-size: 10px; padding: 3px 8px; }
            .filter-controls-wrapper .filter-group-label { font-size: 8px; }
            .filter-divider { display: none; }
            .filter-controls-wrapper .filter-stats { font-size: 9px; margin-left: 0; width: 100%; }
            .user-panel { width: 100%; max-width: 100vw; }
            .user-panel-header { padding: 20px 16px; }
            .panel-section { padding: 16px 16px 0; }
        }
    </style>
</head>

<body class="font-body-md text-on-surface">
<div class="water-pattern"></div>
<div id="toast-ukur"></div>

<!-- ─── IMAGE MODAL ─── -->
<div class="image-modal-overlay" id="imageModal" onclick="closeImageModal()">
    <button class="image-modal-close" onclick="closeImageModal()">✕</button>
    <img class="image-modal-content" id="imageModalContent" src="" alt="Image preview">
    <div class="image-modal-title" id="imageModalTitle"></div>
</div>

<!-- ─── USER PANEL OVERLAY ─── -->
<div class="user-panel-overlay" id="panelOverlay" onclick="closeUserPanel()"></div>

<!-- ===================== USER PANEL ===================== -->
<aside class="user-panel" id="userPanel">
    <div class="user-panel-header">
        <div class="webgis-logo-icon">
            <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="44" height="44" rx="12" fill="rgba(255,255,255,0.12)"/>
                <rect x="21.5" y="9" width="2" height="18" rx="1" fill="white"/>
                <path d="M23 10 L23 25 L10 25 Z" fill="white" opacity="0.9"/>
                <path d="M8 27 Q22 32 36 27" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <path d="M6 33 Q11 30 16 33 Q21 36 26 33 Q31 30 36 33" stroke="rgba(255,255,255,0.7)" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                <path d="M9 37 Q14 34 19 37 Q24 40 29 37 Q34 34 39 37" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
            </svg>
        </div>
        <div>
            <p style="font-size:18px;font-weight:800;color:#fff;margin:0;line-height:1.2;">WEBGIS BAJO</p>
            <p style="font-size:11px;color:rgba(255,255,255,0.6);margin:0;">Spatial School Mapping</p>
        </div>
        <button onclick="closeUserPanel()" style="margin-left:auto;background:rgba(255,255,255,0.1);border:none;border-radius:50%;width:32px;height:32px;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:18px;">✕</button>
    </div>

    <!-- Statistik Ringkas -->
    <div class="panel-section">
        <div class="panel-section-title">Overview Statistics</div>

        <div class="panel-stat-card">
            <div class="panel-stat-icon" style="background:#dbeafe;">
                <span class="material-symbols-outlined" style="color:#1d6fb5;" aria-hidden="true">school</span>
            </div>
            <div>
                <div class="panel-stat-label">Schools with Coordinates</div>
                <div class="panel-stat-value">{{ $sekolahs->count() }}/{{ $totalSekolah }}</div>
            </div>
        </div>

        <div class="panel-stat-card">
            <div class="panel-stat-icon" style="background:#d1fae5;">
                <span class="material-symbols-outlined" style="color:#15803d;" aria-hidden="true">map</span>
            </div>
            <div>
                <div class="panel-stat-label">Village Areas</div>
                <div class="panel-stat-value">{{ $wilayahs->count() }}</div>
            </div>
        </div>

        <div class="panel-stat-card">
            <div class="panel-stat-icon" style="background:#ede9fe;">
                <span class="material-symbols-outlined" style="color:#7c3aed;" aria-hidden="true">group</span>
            </div>
            <div>
                <div class="panel-stat-label">Total Students</div>
                <div class="panel-stat-value">{{ number_format($totalSiswa) }}</div>
            </div>
        </div>

        <div class="panel-stat-card">
            <div class="panel-stat-icon" style="background:#fef3c7;">
                <span class="material-symbols-outlined" style="color:#d97706;" aria-hidden="true">route</span>
            </div>
            <div>
                <div class="panel-stat-label">Distance Records</div>
                @php $totalJarak = \App\Models\JarakSekolahLokasi::count(); @endphp
                <div class="panel-stat-value">{{ $totalJarak }}</div>
            </div>
        </div>
    </div>

    <!-- Peta -->
    <div class="panel-section" style="padding-top:18px;">
        <div class="panel-section-title">Map Access</div>
        <a href="#map-section" onclick="closeUserPanel(); setTimeout(()=>document.getElementById('map-section').scrollIntoView({behavior:'smooth'}),200)"
           style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:16px;background:linear-gradient(135deg,#001e40,#003366);color:#fff;text-decoration:none;margin-bottom:10px;transition:all .2s;"
           onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform=''">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:22px;">explore</span>
            <div>
                <div style="font-size:13px;font-weight:700;">Explore Interactive Map</div>
                <div style="font-size:10px;opacity:.7;">View all school markers & areas</div>
            </div>
        </a>
    </div>

    <!-- Jenjang Sekolah Breakdown -->
    <div class="panel-section" style="padding-top:18px;">
        <div class="panel-section-title">Schools by Level</div>
        @php
            $byJenjang = $sekolahsAll->groupBy(fn($s) => $s->jenjang->nama_jenjang ?? 'Unknown');
            $jenjangColors = ['#dbeafe','#d1fae5','#fee2e2','#ede9fe','#fef3c7','#fce7f3','#e0e7ff','#ccfbf1','#fef9c3','#fae8ff','#ede9fe','#f5f3ff','#ede9fe','#e9d5ff','#f3e8ff'];
            $jenjangTextColors = ['#1d6fb5','#15803d','#c0392b','#7c3aed','#d97706','#9d174d','#1e40af','#065f46','#713f12','#7c3aed','#5b21b6','#7c3aed','#5b21b6','#4c1d95','#4c1d95'];
            $ci = 0;
        @endphp
        @foreach($byJenjang as $nama => $group)
        @php $color = $jenjangColors[$ci % count($jenjangColors)]; $tc = $jenjangTextColors[$ci % count($jenjangTextColors)]; $ci++; @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:12px;background:{{ $color }};margin-bottom:8px;">
            <span style="font-size:12px;font-weight:700;color:{{ $tc }};">{{ $nama }}</span>
            <span style="font-size:18px;font-weight:800;color:{{ $tc }};">{{ $group->count() }}</span>
        </div>
        @endforeach
    </div>

    <!-- Power Sources Summary -->
    <div class="panel-section" style="padding-top:18px;padding-bottom:24px;">
        <div class="panel-section-title">Power Sources</div>
        @php
            $powerSources = $sekolahsAll
                ->filter(fn($s) => $s->utilitas && trim((string) $s->utilitas->sumber_listrik) !== '')
                ->groupBy(fn($s) => trim((string) $s->utilitas->sumber_listrik));
        @endphp
        @if($powerSources->count() > 0)
            @foreach($powerSources as $source => $group)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;background:#f4f3f8;margin-bottom:8px;">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;color:#001e40;font-size:18px;">bolt</span>
                <div style="flex:1;">
                    <div style="font-size:12px;font-weight:700;color:#001e40;">{{ $source }}</div>
                </div>
                <span style="font-size:16px;font-weight:800;color:#00696b;">{{ $group->count() }}</span>
            </div>
            @endforeach
        @else
            <div style="text-align:center;padding:16px;font-size:12px;color:#737780;font-style:italic;">No utility data entered yet.</div>
        @endif
    </div>
</aside>

<!-- ===================== NAVBAR ===================== -->
<nav class="bg-white/70 backdrop-blur-md sticky top-0 z-[1000] border-b border-white/50 shadow-sm">
    <div class="flex justify-between items-center w-full px-5 md:px-16 py-4 max-w-[1440px] mx-auto">
        <!-- Logo -->
        <div class="flex items-center gap-2 md:gap-3 cursor-pointer" onclick="openUserPanel()" title="Open Info Panel">
            <!-- SVG Logo sama dengan panel -->
            <svg width="28" height="28" class="md:w-9 md:h-9 flex-shrink-0" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="44" height="44" rx="10" fill="#001e40"/>
                <rect x="21.5" y="9" width="2" height="18" rx="1" fill="white"/>
                <path d="M23 10 L23 25 L10 25 Z" fill="white" opacity="0.9"/>
                <path d="M8 27 Q22 32 36 27" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <path d="M6 33 Q11 30 16 33 Q21 36 26 33 Q31 30 36 33" stroke="rgba(255,255,255,0.7)" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                <path d="M9 37 Q14 34 19 37 Q24 40 29 37 Q34 34 39 37" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
            </svg>
            <span class="font-[Montserrat] font-extrabold text-primary tracking-tight whitespace-nowrap text-base md:text-[24px]">WEBGIS BAJO</span>
        </div>
        <div class="flex items-center gap-2 md:gap-3">
            <button onclick="openUserPanel()"
                class="flex items-center gap-2 px-3 md:px-4 py-2 rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container transition-all font-label-md text-label-md">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 0;">info</span>
                <span class="hidden md:inline">Info</span>
            </button>
            @auth
                <a href="{{ route('dashboard') }}"
                   class="bg-primary text-on-primary px-4 md:px-6 py-2 md:py-2.5 rounded-full font-label-md text-label-md shadow-lg hover:-translate-y-0.5 transition-all active:scale-95 whitespace-nowrap text-xs md:text-label-md">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="bg-primary text-on-primary px-4 md:px-6 py-2 md:py-2.5 rounded-full font-label-md text-label-md shadow-lg hover:-translate-y-0.5 transition-all active:scale-95 flex items-center gap-1.5 md:gap-2 whitespace-nowrap text-xs md:text-label-md">
                    <span class="material-symbols-outlined text-sm md:text-base" style="font-variation-settings:'FILL' 1;">login</span>
                    <span class="hidden sm:inline">Login </span>Dashboard
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- ===================== MAIN ===================== -->
<main class="max-w-[1440px] mx-auto px-5 md:px-16 py-10 md:py-20">

    <!-- Hero Header -->
    <header class="mb-12 text-center md:text-left max-w-4xl">
        <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-secondary-container/20 text-secondary mb-4 border border-secondary/10">
            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">verified</span>
            <span class="font-label-md text-[12px] uppercase tracking-widest">Integrated Geographic Information System</span>
        </div>
        <h1 class="font-display-lg text-2xl sm:text-4xl md:text-[56px] leading-snug md:leading-tight font-extrabold text-primary mb-6 tracking-tight">
            Spatial Mapping of Education Facilities
            <span class="text-secondary">Bajo Region</span>
        </h1>
        <p class="text-on-surface-variant text-body-lg max-w-2xl leading-relaxed">
            A geographic data analysis platform supporting sustainable and inclusive coastal education infrastructure planning.
        </p>
        <div class="flex flex-wrap gap-3 mt-6">
            <button onclick="openUserPanel()"
                class="group inline-flex items-center gap-2 px-6 py-3 rounded-full border-2 border-primary text-primary font-label-md text-label-md shadow-md hover:bg-primary hover:text-white hover:-translate-y-0.5 transition-all active:scale-95">
                <span class="material-symbols-outlined text-lg transition-all" style="font-variation-settings:'FILL' 0;">bar_chart</span>
                View Statistics & Info
            </button>
            <a href="#map-section"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-full border-2 border-primary text-primary font-label-md text-label-md hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 0;">explore</span>
                Explore Map
            </a>
        </div>
    </header>

    <!-- Map Container -->
    <section id="map-section" class="relative group">
        <div class="absolute -inset-4 bg-gradient-to-tr from-secondary/10 to-primary/5 rounded-[40px] blur-2xl opacity-50 pointer-events-none"></div>

        <div class="relative">
            <div id="map"
                 class="relative w-full h-[350px] sm:h-[480px] md:h-[580px] lg:h-[650px] rounded-[32px] shadow-2xl border-4 border-white overflow-hidden bg-surface-container z-10">
            </div>

            <!-- ── MAP MODE SWITCHER ── -->
            <div class="map-mode-switcher" id="mapModeSwitcher">
                <button class="map-mode-btn active" id="btn-mode-standard" onclick="switchMapMode('standard')">
                    <span class="mode-icon mode-icon-standard"></span>
                    <span class="mode-label">Standard</span>
                </button>
                <button class="map-mode-btn" id="btn-mode-satellite" onclick="switchMapMode('satellite')">
                    <span class="mode-icon mode-icon-satellite"></span>
                    <span class="mode-label">Satellite</span>
                </button>
                <button class="map-mode-btn" id="btn-mode-terrain" onclick="switchMapMode('terrain')">
                    <span class="mode-icon mode-icon-terrain"></span>
                    <span class="mode-label">Terrain</span>
                </button>
                <button class="map-mode-btn" id="btn-mode-dark" onclick="switchMapMode('dark')">
                    <span class="mode-icon mode-icon-dark"></span>
                    <span class="mode-label">Dark</span>
                </button>
            </div>

            {{-- LEGENDA MARKER PER JENJANG --}}
            <div class="map-legend">
                <div class="legend-title">School Level</div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#ec4899;"></div>
                    <span>PAUD / TK / RA</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#3b82f6;"></div>
                    <span>SD / MI</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#22c55e;"></div>
                    <span>SMP / MTS</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#eab308;"></div>
                    <span>SMA / MA / SMK</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#8b5cf6;"></div>
                    <span>Non-Formal</span>
                </div>
                <div class="legend-title mt-2">School Type</div>
                <div class="legend-item">
                    <div style="width:14px;height:14px;border-radius:50%;background:#43474f;border:2px solid rgba(255,255,255,0.8);flex-shrink:0;"></div>
                    <span>Public School (Circle)</span>
                </div>
                <div class="legend-item">
                    <div style="width:14px;height:14px;border-radius:3px;background:#43474f;border:2px dashed rgba(255,255,255,0.8);flex-shrink:0;"></div>
                    <span>Private School (Square)</span>
                </div>
            </div>

            <!-- Tombol Mode Ukur -->
            <button id="btn-ukur"
                    onclick="aktifkanModeUkur()"
                    class="absolute bottom-6 right-4 md:bottom-10 md:right-10 z-50 w-14 h-14 md:w-20 md:h-20 bg-white shadow-2xl rounded-full flex items-center justify-center text-primary group/compass hover:scale-110 active:scale-90 transition-all border border-outline-variant/30">
                <div class="relative flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl">explore</span>
                    <div class="absolute inset-0 rounded-full border-2 border-secondary border-dashed animate-[spin_10s_linear_infinite]"></div>
                </div>
                <div class="absolute -top-12 opacity-0 group-hover/compass:opacity-100 transition-opacity bg-primary text-white text-[10px] py-1 px-3 rounded-full whitespace-nowrap">
                    ACTIVATE MEASURE MODE
                </div>
            </button>
        </div>
    </section>

    <!-- ─── FILTER CONTROLS (di bawah peta) ─── -->
    <div class="filter-controls-wrapper" id="mapFilterControls">
        <div class="filter-row">
            <div class="filter-group">
                <span class="filter-group-label">Level</span>
                <button class="filter-btn active" data-filter="level" data-value="all">All</button>
                <button class="filter-btn" data-filter="level" data-value="paud">PAUD/TK/RA</button>
                <button class="filter-btn" data-filter="level" data-value="sd">SD/MI</button>
                <button class="filter-btn" data-filter="level" data-value="smp">SMP/MTS</button>
                <button class="filter-btn" data-filter="level" data-value="sma">SMA/MA/SMK</button>
                <button class="filter-btn" data-filter="level" data-value="nonformal">Non-Formal</button>
            </div>
            <div class="filter-divider"></div>
            <div class="filter-group">
                <span class="filter-group-label">Type</span>
                <button class="filter-btn active" data-filter="type" data-value="all">All</button>
                <button class="filter-btn" data-filter="type" data-value="Negeri">Negeri</button>
                <button class="filter-btn" data-filter="type" data-value="Swasta">Swasta</button>
            </div>
            <button class="filter-btn reset-btn" id="resetFilters" onclick="resetAllFilters()">
                <span class="material-symbols-outlined" style="font-size:14px;">refresh</span> Reset
            </button>
            <div class="filter-stats" id="filterStats">
                <span id="visibleCount">0</span> / <span id="totalCount">0</span> markers visible
            </div>
        </div>
    </div>

    <!-- Sumber Data -->
    <div class="text-center mt-5">
        <p style="font-size:11px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:#737780;">
            Source &nbsp;·&nbsp; Primary Data Analysis &amp; Spatial Processing &nbsp;·&nbsp; 2026
        </p>
    </div>
</main>

<!-- ===================== FOOTER ===================== -->
<footer class="bg-surface-bright border-t border-outline-variant mt-20">
    <div class="flex flex-col md:flex-row justify-between items-center w-full px-5 md:px-16 py-12 max-w-[1440px] mx-auto">
        <div class="flex items-center gap-4 mb-6 md:mb-0">
            <svg width="28" height="28" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="44" height="44" rx="10" fill="#001e40"/>
                <rect x="21.5" y="9" width="2" height="18" rx="1" fill="white"/>
                <path d="M23 10 L23 25 L10 25 Z" fill="white" opacity="0.9"/>
                <path d="M8 27 Q22 32 36 27" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <path d="M6 33 Q11 30 16 33 Q21 36 26 33 Q31 30 36 33" stroke="rgba(255,255,255,0.7)" stroke-width="1.8" fill="none" stroke-linecap="round"/>
            </svg>
            <p class="font-label-md text-label-md text-on-surface-variant">© {{ date('Y') }} WEBGIS BAJO. All rights reserved.</p>
        </div>
        <div class="flex flex-wrap justify-center gap-8">
            <a href="#" class="font-label-md text-label-md text-on-surface-variant hover:underline decoration-secondary">Privacy Policy</a>
            <a href="#" class="font-label-md text-label-md text-on-surface-variant hover:underline decoration-secondary">Terms of Service</a>
            <a href="#" class="font-label-md text-label-md text-on-surface-variant hover:underline decoration-secondary">Contact Us</a>
            <a href="#" class="font-label-md text-label-md text-on-surface-variant hover:underline decoration-secondary">About the Project</a>
        </div>
    </div>
</footer>

<!-- ===================== SCRIPTS ===================== -->
<script crossorigin="" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ── IMAGE MODAL ──
function openImageModal(src, title) {
    var modal = document.getElementById('imageModal');
    var img = document.getElementById('imageModalContent');
    var titleEl = document.getElementById('imageModalTitle');
    img.src = src;
    titleEl.textContent = title || '';
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    var modal = document.getElementById('imageModal');
    modal.classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeImageModal();
});

// ── USER PANEL ──
function openUserPanel() {
    document.getElementById('userPanel').classList.add('open');
    document.getElementById('panelOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeUserPanel() {
    document.getElementById('userPanel').classList.remove('open');
    document.getElementById('panelOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

// ── POPUP TAB HELPER ──
function switchTab(containerId, tabId) {
    var wrapper = document.getElementById(containerId);
    if (!wrapper) return;
    wrapper.querySelectorAll('.popup-tab').forEach(function(t) { t.classList.remove('active'); });
    wrapper.querySelectorAll('.popup-panel').forEach(function(p) { p.classList.remove('active'); });
    var tab    = wrapper.querySelector('[data-tab="'   + tabId + '"]');
    var panel  = wrapper.querySelector('[data-panel="' + tabId + '"]');
    if (tab)   tab.classList.add('active');
    if (panel) panel.classList.add('active');
}

function showToast(msg, duration) {
    var t = document.getElementById('toast-ukur');
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(function() { t.style.display = 'none'; }, duration || 3000);
}

// ── MAP ──
var map = L.map('map', {
    zoomControl: false,
    attributionControl: false
}).setView([-3.923008054692204, 122.31642156126752], 12);

// ── TILE LAYERS ──
var tileLayers = {
    standard: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.esri.com/">Esri</a>, Maxar, GeoEye, Earthstar Geographics'
    }),
    terrain: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a> contributors'
    }),
    dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
    })
};

var activeLayer = tileLayers.standard;
activeLayer.addTo(map);

function switchMapMode(mode) {
    map.removeLayer(activeLayer);
    activeLayer = tileLayers[mode];
    activeLayer.addTo(map);
    activeLayer.bringToBack();
    document.querySelectorAll('.map-mode-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    var activeBtn = document.getElementById('btn-mode-' + mode);
    if (activeBtn) activeBtn.classList.add('active');
}

L.control.zoom({ position: 'topright' }).addTo(map);

var boundsArray = [];

// ── WILAYAH LAYERS ──
@foreach($wilayahs as $wilayah)
    try {
        var geojsonData_{{ $wilayah->id }} = {!! $wilayah->geojson !!};
        var wilayahLayer_{{ $wilayah->id }} = L.geoJSON(geojsonData_{{ $wilayah->id }}, {
            style: {
                color: "#00696b", weight: 2, opacity: 0.8,
                fillColor: "#00696b", fillOpacity: 0.15, dashArray: '5, 10'
            }
        }).addTo(map);

        var wilayahPopupContent = `
            <div style="min-width:200px;font-family:'Source Sans 3',sans-serif;padding:12px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                    <div style="width:32px;height:32px;border-radius:50%;background:#e6fafa;display:flex;align-items:center;justify-content:center;">
                        <span class="material-symbols-outlined" style="color:#00696b;font-size:18px;">terrain</span>
                    </div>
                    <div>
                        <p style="font-size:11px;color:#43474f;margin:0;letter-spacing:.05em;font-weight:600;text-transform:uppercase;">Regional ROI</p>
                        <h4 style="font-size:16px;font-weight:700;color:#001e40;margin:0;">{{ addslashes($wilayah->nama_wilayah) }}</h4>
                    </div>
                </div>
                @if($wilayah->gambar)
                <div style="margin-bottom:10px;border-radius:8px;overflow:hidden;cursor:pointer;width:100%;height:120px;"
                    onclick="event.stopPropagation(); openImageModal('{{ asset('img/wilayah/' . $wilayah->gambar) }}', '{{ addslashes($wilayah->nama_wilayah) }}')">
                    <img src="{{ asset('img/wilayah/' . $wilayah->gambar) }}"
                        style="width:100%;height:100%;object-fit:cover;"
                        onerror="this.style.display='none'"
                        alt="Foto {{ addslashes($wilayah->nama_wilayah) }}">
                </div>
                @endif
                <div style="background:#f4f3f8;border-radius:12px;padding:10px;font-size:13px;color:#43474f;">
                    <div style="display:flex;justify-content:space-between;padding-bottom:6px;">
                        <span>Region Area</span>
                        <span style="font-weight:700;color:#001e40;">{{ $wilayah->luas_wilayah ?? "-" }} Ha</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding-bottom:6px;border-top:1px solid #e2e8f0;padding-top:6px;">
                        <span>School-Age Males</span>
                        <span style="font-weight:700;color:#001e40;">{{ $wilayah->penduduk_usia_sekolah_l ?? "—" }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span>School-Age Females</span>
                        <span style="font-weight:700;color:#001e40;">{{ $wilayah->penduduk_usia_sekolah_p ?? "—" }}</span>
                    </div>
                    @if($wilayah->gambar)
                    <div style="margin-top:8px;padding-top:8px;border-top:1px solid #e2e8f0;">
                        <span style="font-size:10px;color:#00696b;font-weight:600;cursor:pointer;"
                              onclick="event.stopPropagation(); openImageModal('{{ asset('img/wilayah/' . $wilayah->gambar) }}', '{{ addslashes($wilayah->nama_wilayah) }}')">
                            Click to see photos of the village
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        `;

        wilayahLayer_{{ $wilayah->id }}.bindPopup(wilayahPopupContent);
        wilayahLayer_{{ $wilayah->id }}.bindTooltip("{{ addslashes($wilayah->nama_wilayah) }}", { sticky: true });
        boundsArray.push(wilayahLayer_{{ $wilayah->id }}.getBounds());
    } catch (e) {
        console.error("Gagal memuat GeoJSON untuk wilayah ID: {{ $wilayah->id }}", e);
    }
@endforeach

// ── SCHOOL MARKERS & FILTER ──
var schoolMarkers = [];

@foreach($sekolahs as $sekolah)
@if($sekolah->latitude !== null && $sekolah->longitude !== null)
(function() {
    var lat = {{ $sekolah->latitude }};
    var lng = {{ $sekolah->longitude }};
    var uid = 'popup_{{ $sekolah->id }}';

    @php
        $jenjangId   = (int) $sekolah->jenjang_id;
        $kodeJenjang = $sekolah->jenjang->kode ?? '?';
        $isSwasta    = $sekolah->status === 'Swasta';

        $jenjangWarna = [
            1  => '#ec4899', 2  => '#f43f5e', 3  => '#3b82f6', 4  => '#6366f1',
            5  => '#22c55e', 6  => '#10b981', 7  => '#eab308', 8  => '#f59e0b',
            9  => '#f97316', 10 => '#e879f9', 11 => '#8b5cf6', 12 => '#7c3aed',
            13 => '#6d28d9', 14 => '#5b21b6', 15 => '#4c1d95'
        ];
        $markerColor = $jenjangWarna[$jenjangId] ?? '#6b7280';
        $markerShadow = $markerColor . '66';

        $borderRadius = $isSwasta ? '6px'  : '50%';
        $borderStyle  = $isSwasta ? 'dashed': 'solid';

        $level = 'other';
        if (in_array($jenjangId, [1,2,10])) $level = 'paud';
        elseif (in_array($jenjangId, [3,4])) $level = 'sd';
        elseif (in_array($jenjangId, [5,6])) $level = 'smp';
        elseif (in_array($jenjangId, [7,8,9])) $level = 'sma';
        elseif (in_array($jenjangId, [11,12,13,14,15])) $level = 'nonformal';
    @endphp

    var markerIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="' +
              'width:42px;height:42px;' +
              'background:{{ $markerColor }};' +
              'color:#fff;' +
              'border-radius:{{ $borderRadius }};' +
              'display:flex;align-items:center;justify-content:center;' +
              'box-shadow:0 6px 18px {{ $markerShadow }};' +
              'border:3px {{ $borderStyle }} #fff;' +
              'font-size:10px;font-weight:900;font-family:Montserrat,sans-serif;' +
              'letter-spacing:0.01em;line-height:1;" >' +
              '{{ $kodeJenjang }}' +
              '</div>',
        iconSize: [42, 42],
        iconAnchor: [21, 42],
        popupAnchor: [0, -46]
    });

    @php $jarakRows = ''; @endphp
    @if($sekolah->semuaJarakLokasi->count() > 0)
        @php
            $jarakRows = '';
            foreach($sekolah->semuaJarakLokasi as $dj) {
                $namaWilayah = htmlspecialchars($dj->nama_wilayah ?? 'Desa', ENT_QUOTES, 'UTF-8');
                $nilaiJarak  = number_format($dj->pivot->jarak, 2);
                $jarakRows  .= '<div class="popup-row">' .
                                '<span class="label">' . $namaWilayah . '</span>' .
                                '<span style="font-weight:700;color:#ba1a1a;background:#ffdad6;padding:1px 8px;border-radius:999px;font-size:11px;">' .
                                $nilaiJarak . ' Km</span></div>';
            }
        @endphp
        var jarakHtml = '<div class="popup-jarak-list" style="max-height:110px;overflow-y:auto;margin-top:4px;">' +
                        {!! json_encode($jarakRows) !!} +
                        '</div>';
    @else
        var jarakHtml = '<div class="popup-empty">Distance data to the village has not been inputted.</div>';
    @endif

    @if($sekolah->statistik)
        @php
            $s          = $sekolah->statistik;
            $siswaL     = $s->siswa_l       ?? 0;
            $siswaP     = $s->siswa_p       ?? 0;
            $totalSiswa = $s->jumlah_siswa  ?? 0;
            $guru       = $s->jumlah_guru   ?? 0;
            $rombel     = $s->jumlah_rombel ?? 0;
            $rKelas     = $s->ruang_kelas   ?? 0;
            $lab        = $s->laboratorium  ?? 0;
            $perpus     = $s->perpustakaan  ?? 0;
        @endphp
        var tabStatistikHtml =
            '<p style="font-size:10px;font-weight:700;color:#43474f;letter-spacing:.05em;text-transform:uppercase;margin:0 0 4px;">Students Demography</p>' +
            '<div class="popup-grid-3">' +
            '  <div class="popup-stat-card sc-accent"><div class="sc-label">Male Students</div><div class="sc-value">{{ $siswaL }}</div></div>' +
            '  <div class="popup-stat-card sc-accent"><div class="sc-label">Female Students</div><div class="sc-value">{{ $siswaP }}</div></div>' +
            '  <div class="popup-stat-card sc-accent"><div class="sc-label">Total Students</div><div class="sc-value">{{ $totalSiswa }}</div></div>' +
            '</div>' +
            '<p style="font-size:10px;font-weight:700;color:#43474f;letter-spacing:.05em;text-transform:uppercase;margin:8px 0 4px;">Personel</p>' +
            '<div class="popup-grid-2">' +
            '  <div class="popup-stat-card"><div class="sc-label">Teachers</div><div class="sc-value">{{ $guru }}</div></div>' +
            '  <div class="popup-stat-card"><div class="sc-label">Class Groups (Rombel)</div><div class="sc-value">{{ $rombel }}</div></div>' +
            '</div>' +
            '<p style="font-size:10px;font-weight:700;color:#43474f;letter-spacing:.05em;text-transform:uppercase;margin:8px 0 4px;">Capacity & Infrastructure</p>' +
            '<div class="popup-grid-3">' +
            '  <div class="popup-stat-card"><div class="sc-label">Classrooms Fisik</div><div class="sc-value">{{ $rKelas }}</div></div>' +
            '  <div class="popup-stat-card"><div class="sc-label">Lab</div><div class="sc-value">{{ $lab }}</div></div>' +
            '  <div class="popup-stat-card"><div class="sc-label">Library</div><div class="sc-value">{{ $perpus }}</div></div>' +
            '</div>';
    @else
        var tabStatistikHtml = '<div class="popup-empty">School statistics data has not been entered.</div>';
    @endif

    @if($sekolah->utilitas)
        @php
            $u         = $sekolah->utilitas;
            $kurikulum = addslashes(htmlspecialchars($u->kurikulum     ?? '-', ENT_QUOTES));
            $penyeleng = addslashes(htmlspecialchars($u->penyelenggara ?? '-', ENT_QUOTES));
            $internet  = addslashes(htmlspecialchars($u->akses_internet ?? '', ENT_QUOTES));
            $listrik   = addslashes(htmlspecialchars($u->sumber_listrik ?? '-', ENT_QUOTES));
            $daya      = $u->daya_listrik ? number_format($u->daya_listrik) . ' VA' : '-';
            $luas      = $u->luas_tanah   ? number_format($u->luas_tanah, 0, ',', '.') . ' m²' : '-';
            $internetColor  = $u->akses_internet ? '#00696b' : '#ba1a1a';
            $internetBg     = $u->akses_internet ? '#e6fafa' : '#ffdad6';
            $internetLabel  = $u->akses_internet ? $internet : 'Tidak Ada';
        @endphp
        var tabUtilitasHtml =
            '<div class="popup-row"><span class="label">Curriculum</span><span class="value">{{ $kurikulum }}</span></div>' +
            '<div class="popup-row"><span class="label">Operator</span><span class="value">{{ $penyeleng }}</span></div>' +
            '<div class="popup-row"><span class="label">Internet Access</span>' +
            '<span class="popup-chip" style="background:{{ $internetBg }};color:{{ $internetColor }};">{{ $internetLabel }}</span></div>' +
            '<div class="popup-row"><span class="label">Power Source</span><span class="value">{{ $listrik }}</span></div>' +
            '<div class="popup-row"><span class="label">Power Capacity</span><span class="value">{{ $daya }}</span></div>' +
            '<div class="popup-row"><span class="label">Land Area</span><span class="value">{{ $luas }}</span></div>';
    @else
        var tabUtilitasHtml = '<div class="popup-empty">The school utility data has not been entered.</div>';
    @endif

    @php
        $namaSekolah  = addslashes(htmlspecialchars($sekolah->nama_sekolah, ENT_QUOTES));
        $namaJenjang  = addslashes(htmlspecialchars($sekolah->jenjang->nama_jenjang ?? '', ENT_QUOTES));
        $statusLabel  = strtoupper($sekolah->status ?? '');
        $npsn         = $sekolah->npsn ?? '-';
        $akreditasi   = $sekolah->akreditasi ?? '-';
        $alamat       = addslashes(htmlspecialchars($sekolah->alamat ?? '-', ENT_QUOTES));
        $headerColor  = $markerColor;
        $wilayahNama  = $sekolah->semuaJarakLokasi->count() > 0
                        ? htmlspecialchars($sekolah->semuaJarakLokasi->first()->nama_wilayah ?? '', ENT_QUOTES, 'UTF-8')
                        : '';
        $hasImage     = !empty($sekolah->img);
    @endphp

    var imgHtml = '';
    @if($hasImage)
        imgHtml = '<img src="{{ asset('img/sekolah/' . $sekolah->img) }}" ' +
                  'class="popup-img-thumb" ' +
                  'onclick="event.stopPropagation(); openImageModal(this.src, \'{{ $namaSekolah }}\')" ' +
                  'onerror="this.style.display=\'none\'" ' +
                  'alt="{{ $namaSekolah }}">';
    @else
        imgHtml = '<div class="popup-img-thumb-placeholder"><span>📷 No Image</span></div>';
    @endif

    var tabInfoHtml =
        '<div class="popup-row"><span class="label">NPSN</span><span class="value">{{ $npsn }}</span></div>' +
        '<div class="popup-row"><span class="label">Level</span>' +
        '<span class="popup-chip" style="background:#f0f4ff;color:#001e40;border:1px solid #d5e3ff;">{{ addslashes($sekolah->jenjang->kode ?? "-") }} &mdash; {{ $namaJenjang }}</span></div>' +
        '<div class="popup-row"><span class="label">Status</span>' +
        '<span class="popup-chip" style="background:{{ $isSwasta ? "#fff3cd" : "#e6fafa" }};color:{{ $isSwasta ? "#92400e" : "#001e40" }};">{{ $statusLabel }}</span></div>' +
        '<div class="popup-row"><span class="label">Accreditation</span>' +
        '<span class="popup-chip" style="background:#e6fafa;color:#00696b;font-size:13px;font-weight:900;">{{ $akreditasi }}</span></div>' +
        '<div class="popup-row"><span class="label">Address</span><span class="value" style="font-size:11px;font-weight:600;color:#43474f;">{{ $alamat }}</span></div>' +
        '<div style="margin-top:10px;padding-top:6px;border-top:1px dashed #e2e2e7;">' +
        '<p style="font-size:10px;color:#43474f;font-weight:700;letter-spacing:.05em;text-transform:uppercase;margin:0 0 4px;">Distance to Village</p>' +
        jarakHtml + '</div>';

    var btnUkur =
        '<button class="popup-btn-ukur" onclick="hitungJarakKeKlik(' + lat + ', ' + lng + ', \'{{ $namaSekolah }}\')">' +
        '<span class="material-symbols-outlined" style="font-size:16px;">straighten</span>' +
        'Measure Distance to Other Points</button>';

    var popupContent =
        '<div class="popup-wrapper" id="' + uid + '">' +
        imgHtml +
        '<div class="popup-header" style="background:{{ $headerColor }};">' +
        '<h4 style="font-size:14px;font-weight:700;color:#fff;margin:0 0 3px;line-height:1.3;">{{ $namaSekolah }}</h4>' +
        '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">' +
        '<span style="font-size:10px;color:rgba(255,255,255,0.8);font-weight:600;">{{ $namaJenjang }}</span>' +
        @if($wilayahNama)
        '<span style="color:rgba(255,255,255,0.5);font-size:10px;">•</span>' +
        '<span style="font-size:10px;color:rgba(255,255,255,0.75);">{{ $wilayahNama }}</span>' +
        @endif
        '</div>' +
        '</div>' +
        '<div class="popup-tab-bar">' +
        '<button class="popup-tab active" data-tab="info" onclick="switchTab(\'' + uid + '\',\'info\')">Info</button>' +
        '<button class="popup-tab" data-tab="statistik" onclick="switchTab(\'' + uid + '\',\'statistik\')">Statistics</button>' +
        '<button class="popup-tab" data-tab="utilitas" onclick="switchTab(\'' + uid + '\',\'utilitas\')">Utilities</button>' +
        '</div>' +
        '<div class="popup-panel active" data-panel="info">' + tabInfoHtml + btnUkur + '</div>' +
        '<div class="popup-panel" data-panel="statistik">' + tabStatistikHtml + btnUkur + '</div>' +
        '<div class="popup-panel" data-panel="utilitas">' + tabUtilitasHtml + btnUkur + '</div>' +
        '</div>';

    var marker = L.marker([lat, lng], { icon: markerIcon })
        .addTo(map)
        .bindPopup(popupContent, { maxWidth: 350, minWidth: Math.min(300, window.innerWidth - 40) });

    schoolMarkers.push({
        marker: marker,
        level: '{{ $level }}',
        type: '{{ $sekolah->status }}'
    });
})();
@endif
@endforeach

if (boundsArray.length > 0) {
    var group = new L.featureGroup(
        boundsArray.map(function(b) { return L.rectangle(b); })
    );
    map.fitBounds(group.getBounds(), { padding: [20, 20] });
}

// ── FILTER LOGIC ──
// Semua marker disimpan di schoolMarkers, tidak ada yang dihapus permanen
// Filter hanya menyembunyikan/menampilkan marker dengan addLayer/removeLayer

function updateFilterStats() {
    var visible = 0;
    schoolMarkers.forEach(function(item) {
        if (map.hasLayer(item.marker)) visible++;
    });
    document.getElementById('visibleCount').textContent = visible;
    document.getElementById('totalCount').textContent = schoolMarkers.length;
}

function applyFilters() {
    var activeLevel = document.querySelector('.filter-btn[data-filter="level"].active');
    var activeType = document.querySelector('.filter-btn[data-filter="type"].active');
    var levelFilter = activeLevel ? activeLevel.dataset.value : 'all';
    var typeFilter = activeType ? activeType.dataset.value : 'all';

    schoolMarkers.forEach(function(item) {
        var show = true;
        if (levelFilter !== 'all' && item.level !== levelFilter) show = false;
        if (typeFilter !== 'all' && item.type !== typeFilter) show = false;
        if (show) {
            if (!map.hasLayer(item.marker)) map.addLayer(item.marker);
        } else {
            if (map.hasLayer(item.marker)) map.removeLayer(item.marker);
        }
    });

    updateFilterStats();
}

function resetAllFilters() {
    // Reset semua tombol filter ke 'All'
    document.querySelectorAll('.filter-btn[data-filter="level"]').forEach(function(b) {
        b.classList.remove('active');
        if (b.dataset.value === 'all') b.classList.add('active');
    });
    document.querySelectorAll('.filter-btn[data-filter="type"]').forEach(function(b) {
        b.classList.remove('active');
        if (b.dataset.value === 'all') b.classList.add('active');
    });
    applyFilters();
    showToast('🔄 All filters reset — all markers are visible', 2000);
}

// Event listener untuk tombol filter
document.querySelectorAll('.filter-btn:not(.reset-btn)').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        var filter = this.dataset.filter;
        document.querySelectorAll('.filter-btn[data-filter="' + filter + '"]').forEach(function(b) {
            b.classList.remove('active');
        });
        this.classList.add('active');
        applyFilters();
    });
});

// Inisialisasi statistik setelah semua marker ditambahkan
setTimeout(updateFilterStats, 500);

// ── MEASURE MODE ──
var lineLayer    = null;
var measureMode  = false;

function aktifkanModeUkur() {
    measureMode = true;
    var btn = document.getElementById('btn-ukur');
    btn.style.background = '#00696b';
    btn.querySelector('.material-symbols-outlined').style.color = '#fff';
    showToast('🎯 Measure Distance Mode Activated — Click on the map to measure distance to other points', 4000);
    setTimeout(function() {
        btn.style.background = '';
        btn.querySelector('.material-symbols-outlined').style.color = '';
        measureMode = false;
    }, 4000);
}

function hitungJarakKeKlik(sekolahLat, sekolahLng, namaSekolah) {
    map.closePopup();
    showToast('📍 Click on the map to measure distance from "' + namaSekolah + '"', 5000);
    map.once('click', function(e) {
        var clickLat = e.latlng.lat;
        var clickLng = e.latlng.lng;
        var jarakMeter = map.distance([sekolahLat, sekolahLng], [clickLat, clickLng]);
        var jarakKm    = (jarakMeter / 1000).toFixed(2);
        if (lineLayer) map.removeLayer(lineLayer);
        lineLayer = L.polyline(
            [[sekolahLat, sekolahLng], [clickLat, clickLng]],
            { color: '#ba1a1a', weight: 3, dashArray: '6, 10' }
        ).addTo(map);
        L.popup()
            .setLatLng([clickLat, clickLng])
            .setContent(
                '<div style="font-family:\'Source Sans 3\',sans-serif;min-width:220px;padding:12px;">' +
                '<p style="font-size:11px;color:#43474f;margin:0 0 6px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;">Proximity Distance Analysis</p>' +
                '<p style="font-size:13px;color:#1a1c1f;margin:0 0 4px;">' +
                'From: <b>' + namaSekolah + '</b></p>' +
                '<p style="font-size:20px;font-weight:800;color:#ba1a1a;margin:0;">' + jarakKm + ' Km</p>' +
                '<p style="font-size:11px;color:#43474f;margin:4px 0 0;">(' + Math.round(jarakMeter) + ' meters straight line)</p>' +
                '</div>'
            )
            .openOn(map);
    });
}
</script>
</body>
</html>