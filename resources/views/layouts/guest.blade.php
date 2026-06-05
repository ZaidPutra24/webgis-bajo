<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WebGIS Bajo') }} — Login</title>

        <!-- Favicon: logo kapal -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 44 44'%3E%3Crect width='44' height='44' rx='10' fill='%23001e40'/%3E%3Crect x='21.5' y='9' width='2' height='18' rx='1' fill='white'/%3E%3Cpath d='M23 10 L23 25 L10 25 Z' fill='white' opacity='0.9'/%3E%3Cpath d='M8 27 Q22 32 36 27' stroke='white' stroke-width='2.5' fill='none' stroke-linecap='round'/%3E%3Cpath d='M6 33 Q11 30 16 33 Q21 36 26 33 Q31 30 36 33' stroke='rgba(255,255,255,0.8)' stroke-width='1.8' fill='none' stroke-linecap='round'/%3E%3C/svg%3E">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com" rel="preconnect"/>
        <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet"/>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Source Sans 3', sans-serif;
                background: #f0f4f8;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }

            /* Background dekoratif */
            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background:
                    radial-gradient(ellipse 80% 60% at 10% 20%, rgba(0,30,64,0.12) 0%, transparent 60%),
                    radial-gradient(ellipse 60% 50% at 90% 80%, rgba(0,105,107,0.10) 0%, transparent 60%);
                pointer-events: none;
                z-index: 0;
            }

            /* Gelombang dekoratif bawah */
            body::after {
                content: '';
                position: fixed;
                bottom: -60px; left: -5%;
                width: 110%; height: 220px;
                background: linear-gradient(180deg, transparent 0%, rgba(0,30,64,0.06) 100%);
                border-radius: 50% 50% 0 0 / 60px 60px 0 0;
                pointer-events: none;
                z-index: 0;
            }

            .login-wrapper {
                position: relative;
                z-index: 1;
                width: 100%;
                max-width: 440px;
                padding: 0 16px;
            }

            .login-card {
                background: #ffffff;
                border-radius: 28px;
                box-shadow:
                    0 4px 6px rgba(0,30,64,0.04),
                    0 20px 60px rgba(0,30,64,0.10);
                overflow: hidden;
            }

            /* Header kartu */
            .login-card-header {
                background: linear-gradient(135deg, #001e40 0%, #003366 100%);
                padding: 32px 36px 28px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            .login-card-header::before {
                content: '';
                position: absolute;
                top: -40px; right: -40px;
                width: 140px; height: 140px;
                border-radius: 50%;
                background: rgba(255,255,255,0.04);
                pointer-events: none;
            }
            .login-card-header::after {
                content: '';
                position: absolute;
                bottom: -30px; left: -20px;
                width: 100px; height: 100px;
                border-radius: 50%;
                background: rgba(0,105,107,0.15);
                pointer-events: none;
            }

            .login-brand-title {
                font-family: 'Montserrat', sans-serif;
                font-size: 22px;
                font-weight: 800;
                color: #ffffff;
                letter-spacing: -0.02em;
                margin: 0;
                line-height: 1.2;
            }
            .login-brand-sub {
                font-size: 11px;
                color: rgba(255,255,255,0.55);
                letter-spacing: 0.12em;
                text-transform: uppercase;
                font-weight: 600;
                margin-top: 2px;
            }

            /* Body kartu */
            .login-card-body {
                padding: 32px 36px 36px;
            }

            .login-heading {
                font-family: 'Montserrat', sans-serif;
                font-size: 18px;
                font-weight: 700;
                color: #1a1c1f;
                margin: 0 0 4px;
            }
            .login-subheading {
                font-size: 13px;
                color: #737780;
                margin: 0 0 28px;
            }

            /* Label */
            .field-label {
                display: block;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: #43474f;
                margin-bottom: 6px;
            }

            /* Input */
            .field-input {
                display: block;
                width: 100%;
                padding: 11px 14px 11px 42px;
                border: 1.5px solid #e2e2e7;
                border-radius: 12px;
                font-size: 14px;
                font-family: 'Source Sans 3', sans-serif;
                color: #1a1c1f;
                background: #f9f9fe;
                transition: border-color .2s, box-shadow .2s, background .2s;
                outline: none;
                box-sizing: border-box;
            }
            .field-input:focus {
                border-color: #001e40;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(0,30,64,0.08);
            }
            .field-input::placeholder { color: #b0b3bb; }

            /* Input wrapper dengan icon */
            .input-wrapper {
                position: relative;
                margin-bottom: 18px;
            }
            .input-icon {
                position: absolute;
                left: 13px; top: 50%;
                transform: translateY(-50%);
                width: 18px; height: 18px;
                color: #b0b3bb;
                pointer-events: none;
                flex-shrink: 0;
            }

            /* Error */
            .field-error {
                font-size: 11px;
                color: #ba1a1a;
                margin-top: 5px;
                display: block;
            }

            /* Remember + Forgot */
            .row-remember {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 24px;
            }
            .remember-label {
                display: flex;
                align-items: center;
                gap: 7px;
                font-size: 13px;
                color: #43474f;
                cursor: pointer;
                user-select: none;
            }
            .remember-label input[type="checkbox"] {
                width: 15px; height: 15px;
                border-radius: 4px;
                accent-color: #001e40;
                cursor: pointer;
            }
            .forgot-link {
                font-size: 12px;
                font-weight: 700;
                color: #00696b;
                text-decoration: none;
                letter-spacing: 0.02em;
                transition: color .2s;
            }
            .forgot-link:hover { color: #001e40; text-decoration: underline; }

            /* Submit button */
            .btn-login {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                padding: 13px 0;
                background: #001e40;
                color: #ffffff;
                font-family: 'Montserrat', sans-serif;
                font-size: 13px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                border: none;
                border-radius: 14px;
                cursor: pointer;
                transition: background .2s, transform .15s, box-shadow .2s;
                box-shadow: 0 4px 16px rgba(0,30,64,0.20);
            }
            .btn-login:hover {
                background: #003366;
                box-shadow: 0 6px 20px rgba(0,30,64,0.28);
                transform: translateY(-1px);
            }
            .btn-login:active { transform: scale(0.98); }

            /* Back to home */
            .back-home {
                text-align: center;
                margin-top: 20px;
            }
            .back-home a {
                font-size: 12px;
                color: #737780;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                transition: color .2s;
                font-weight: 600;
            }
            .back-home a:hover { color: #001e40; }

            /* Session status */
            .session-status {
                background: #e6fafa;
                border: 1px solid #00696b;
                border-radius: 10px;
                padding: 10px 14px;
                font-size: 13px;
                color: #00696b;
                font-weight: 600;
                margin-bottom: 18px;
            }
        </style>
    </head>
    <body>
        <div class="login-wrapper">
            {{ $slot }}
        </div>
    </body>
</html>
