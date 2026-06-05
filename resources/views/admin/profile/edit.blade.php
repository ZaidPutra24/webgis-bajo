@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'Account & Profile Settings')

@push('styles')
<style>
    .profile-avatar {
        width: 90px; height: 90px; border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        display: flex; align-items: center; justify-content: center;
        font-size: 36px; color: #fff; font-weight: 700;
        box-shadow: 0 8px 24px rgba(99,102,241,.3);
        flex-shrink: 0;
    }
    .profile-card {
        border: none; border-radius: 16px;
        box-shadow: 0 2px 20px rgba(0,0,0,.06);
        background: #fff;
    }
    .profile-card .card-header {
        background: transparent;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 28px 16px;
    }
    .profile-card .card-body { padding: 24px 28px; }
    .section-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0;
    }
    .form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
    .form-control-custom {
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        padding: 10px 14px; font-size: 14px; transition: border-color .2s;
    }
    .form-control-custom:focus {
        border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .btn-save {
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        color: #fff; border: none; border-radius: 10px;
        padding: 10px 28px; font-size: 13px; font-weight: 700;
        transition: all .2s; box-shadow: 0 4px 12px rgba(99,102,241,.25);
    }
    .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,.35); color:#fff; }
    .btn-danger-custom {
        background: #fff; color: #ef4444; border: 1.5px solid #fca5a5;
        border-radius: 10px; padding: 10px 24px; font-size: 13px; font-weight: 700;
        transition: all .2s;
    }
    .btn-danger-custom:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
    .danger-zone { border: 1.5px solid #fca5a5; border-radius: 16px; padding: 24px 28px; background: #fff5f5; }
    .info-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;
        border-radius: 999px; padding: 4px 12px; font-size: 11px; font-weight: 700;
    }
</style>
@endpush

@section('content')

{{-- ─── PROFILE HEADER ─── --}}
<div class="profile-card mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4">
            <div class="profile-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-fill">
                <h4 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h4>
                <p class="text-muted mb-2" style="font-size:14px;">{{ Auth::user()->email }}</p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="info-badge">
                        <i class="bi bi-shield-check-fill"></i> Administrator
                    </span>
                    <span class="info-badge" style="background:#eff6ff;color:#2563eb;border-color:#bfdbfe;">
                        <i class="bi bi-calendar3"></i> Joined {{ Auth::user()->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ─── UPDATE PROFILE INFORMATION ─── --}}
    <div class="col-lg-6">
        <div class="profile-card h-100">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="section-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Profile Information</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">Update your name and email address</p>
                    </div>
                </div>
            </div>
            <div class="card-body">

                @if (session('status') === 'profile-updated')
                <div class="alert alert-success border-0 rounded-3 p-3 mb-4 d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="fw-medium" style="font-size:13px;">Profile updated successfully.</span>
                </div>
                @endif

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="mb-3">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name"
                               class="form-control form-control-custom @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                               placeholder="Enter your full name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email"
                               class="form-control form-control-custom @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required autocomplete="username"
                               placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-2 p-3 bg-warning bg-opacity-10 rounded-3" style="font-size:12px;">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                            Your email address is unverified.
                            <button form="send-verification"
                                    class="btn btn-link p-0 text-primary fw-semibold" style="font-size:12px;">
                                Resend verification email
                            </button>
                        </div>
                        @if (session('status') === 'verification-link-sent')
                        <p class="text-success mt-1" style="font-size:12px;">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            A new verification link has been sent to your email.
                        </p>
                        @endif
                        @endif
                    </div>

                    <button type="submit" class="btn btn-save">
                        <i class="bi bi-floppy-fill me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ─── UPDATE PASSWORD ─── --}}
    <div class="col-lg-6">
        <div class="profile-card h-100">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="section-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Change Password</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">Use a long, random password for security</p>
                    </div>
                </div>
            </div>
            <div class="card-body">

                @if (session('status') === 'password-updated')
                <div class="alert alert-success border-0 rounded-3 p-3 mb-4 d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="fw-medium" style="font-size:13px;">Password updated successfully.</span>
                </div>
                @endif

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label class="form-label" for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password"
                               class="form-control form-control-custom @error('current_password', 'updatePassword') is-invalid @enderror"
                               autocomplete="current-password" placeholder="Enter current password">
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" id="password" name="password"
                               class="form-control form-control-custom @error('password', 'updatePassword') is-invalid @enderror"
                               autocomplete="new-password" placeholder="Enter new password">
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control form-control-custom @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                               autocomplete="new-password" placeholder="Repeat new password">
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-save" style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 12px rgba(22,163,74,.25);">
                        <i class="bi bi-lock-fill me-2"></i>Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ─── DANGER ZONE ─── --}}
    <div class="col-12">
        <div class="danger-zone">
            <div class="d-flex align-items-start gap-3">
                <div class="section-icon bg-danger bg-opacity-10 text-danger" style="margin-top:2px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="flex-fill">
                    <h6 class="fw-bold text-danger mb-1">Delete Account</h6>
                    <p class="text-muted mb-3" style="font-size:13px;">
                        Once your account is deleted, all data and resources will be permanently removed. This action cannot be undone. Please download any data you wish to keep before proceeding.
                    </p>
                    <button type="button" class="btn btn-danger-custom"
                            data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash3-fill me-2"></i>Delete My Account
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ─── DELETE ACCOUNT MODAL ─── --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="section-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">Are you sure?</h5>
                </div>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-body px-4 py-3">
                    <p class="text-muted mb-3" style="font-size:13px;">
                        This will permanently delete your account. Please enter your password to confirm.
                    </p>
                    <label class="form-label" for="delete_password">Password</label>
                    <input type="password" id="delete_password" name="password"
                           class="form-control form-control-custom @error('password', 'userDeletion') is-invalid @enderror"
                           placeholder="Enter your password">
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer border-0 px-4 pb-4 gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4 fw-semibold"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">
                        <i class="bi bi-trash3-fill me-1"></i>Yes, Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-open delete modal if there are errors from userDeletion
    @if ($errors->userDeletion->isNotEmpty())
        var modal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        modal.show();
    @endif
</script>
@endpush

@endsection
