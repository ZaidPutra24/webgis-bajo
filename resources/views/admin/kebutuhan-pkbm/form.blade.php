@extends('layouts.admin')

@section('title', 'PKBM Needs Form')
@section('page-title', 'PKBM Needs: ' . $desa->nama_wilayah)

@section('content')
<style>
    .modern-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    .section-title {
        position: relative;
        padding-left: 1rem;
        color: #2b3445;
    }
    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 15%;
        height: 70%;
        width: 5px;
        background: linear-gradient(135deg, #4F46E5 0%, #06b6d4 100%);
        border-radius: 5px;
    }
    .modern-input {
        background-color: #f8fafc !important;
        border: 2px solid transparent !important;
        border-radius: 0.6rem !important;
        transition: all 0.2s;
        font-weight: 500;
        color: #334155;
    }
    .modern-input:focus {
        border-color: #4F46E5 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
    }
    .modern-label {
        color: #64748b;
        font-weight: 500;
        font-size: 0.8rem;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        border: none;
        border-radius: 0.75rem;
        color: white;
        transition: all 0.3s;
    }
    .btn-gradient:hover {
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
        transform: translateY(-2px);
        color: white;
    }
    .desa-badge {
        background: linear-gradient(135deg, #f0fdfa 0%, #e0f2fe 100%);
        color: #0369a1;
        border-radius: 0.75rem;
        font-weight: 700;
    }
</style>

<div class="container-fluid px-0 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('kebutuhan-pkbm.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-secondary">
            Back
        </a>
        <div class="desa-badge px-4 py-2 shadow-sm">
            {{ $desa->nama_wilayah }}
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3">
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kebutuhan-pkbm.update', $desa->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modern-card p-4 mb-4">
            <h5 class="section-title fw-bold mb-2">BPB (Never Attended School) — Past School Age</h5>
            <p class="text-muted small mb-3">Total, not yet detailed per Package A/B/C — source data has not reached that level.</p>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="number" name="bpb_19_24" class="form-control modern-input" id="inpBpb1924" min="0"
                               value="{{ old('bpb_19_24', $row->bpb_19_24 ?? 0) }}">
                        <label for="inpBpb1924" class="modern-label">BPB Age 19-24 Years</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="number" name="bpb_25_plus" class="form-control modern-input" id="inpBpb25" min="0"
                               value="{{ old('bpb_25_plus', $row->bpb_25_plus ?? 0) }}">
                        <label for="inpBpb25" class="modern-label">BPB Age 25+ Years</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="modern-card p-4 mb-4">
            <h5 class="section-title fw-bold mb-2">Data Source</h5>
            <div class="form-floating" style="max-width: 480px;">
                <input type="text" name="sumber_data" class="form-control modern-input" id="inpSumber"
                       value="{{ old('sumber_data', $row->sumber_data ?? '') }}"
                       placeholder="Example: Education Office Data 2026">
                <label for="inpSumber" class="modern-label">Data Source</label>
            </div>
        </div>

        <div class="modern-card p-4 text-center">
            <button type="submit" class="btn btn-gradient btn-lg px-5 py-3 fw-bold fs-5 w-100 w-md-50">
                Save Data
            </button>
        </div>
    </form>
</div>
@endsection
