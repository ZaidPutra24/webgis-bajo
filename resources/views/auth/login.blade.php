<x-guest-layout>
<div class="login-card">

    {{-- ── HEADER ── --}}
    <div class="login-card-header">
        <div style="position:relative;z-index:1;">
            {{-- Logo kapal SVG --}}
            <div style="display:flex;justify-content:center;margin-bottom:14px;">
                <div style="width:60px;height:60px;background:rgba(255,255,255,0.12);border-radius:16px;display:flex;align-items:center;justify-content:center;border:1.5px solid rgba(255,255,255,0.18);">
                    <svg width="38" height="38" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="21.5" y="7" width="2" height="18" rx="1" fill="white"/>
                        <path d="M23 8 L23 23 L9 23 Z" fill="white" opacity="0.9"/>
                        <path d="M7 25 Q22 31 37 25" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                        <path d="M5 31 Q11 28 17 31 Q22 34 27 31 Q32 28 38 31" stroke="rgba(255,255,255,0.7)" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                        <path d="M8 36 Q14 33 20 36 Q25 39 31 36 Q36 33 41 36" stroke="rgba(255,255,255,0.35)" stroke-width="1.4" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <p class="login-brand-title">WEBGIS BAJO</p>
            <p class="login-brand-sub">Spatial School Mapping System</p>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="login-card-body">
        <h2 class="login-heading">Welcome back</h2>
        <p class="login-subheading">Sign in to access the admin dashboard</p>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="session-status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="field-label">Email Address</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.75L2.25 6.75"/>
                    </svg>
                    <input
                        id="email"
                        class="field-input"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="admin@example.com"/>
                </div>
                @error('email')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="field-label">Password</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                    <input
                        id="password"
                        class="field-input"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"/>
                </div>
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Remember me + Forgot --}}
            <div class="row-remember">
                <label class="remember-label" for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H5.25"/>
                </svg>
                Sign In to Dashboard
            </button>
        </form>

        {{-- Back to home --}}
        <div class="back-home">
            <a href="{{ url('/') }}">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Back to Map
            </a>
        </div>
    </div>

</div>
</x-guest-layout>
