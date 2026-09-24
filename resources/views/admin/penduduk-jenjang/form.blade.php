@extends('layouts.admin')

@section('title', 'School-Age Population & ATS Form')
@section('page-title', 'Data: ' . $desa->nama_wilayah)

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
    .jenjang-badge {
        background: linear-gradient(135deg, #f0fdfa 0%, #e0f2fe 100%);
        color: #0369a1;
        border-radius: 0.75rem;
        font-weight: 700;
        min-width: 70px;
        text-align: center;
    }
    .subset-hint {
        font-size: 0.72rem;
        color: #94a3b8;
    }
    .derived-note {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 1rem;
        color: #92400e;
    }
</style>

<div class="container-fluid px-0 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('penduduk-jenjang.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-secondary">
            Back
        </a>
        <div class="jenjang-badge px-4 py-2 shadow-sm">
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

    <div class="derived-note p-3 mb-4 small">
        <i class="bi bi-lock-fill me-1"></i>
        <strong>Density (capita/km²) is not entered here.</strong>
        Density is always calculated automatically = total level population ÷ village land area
        (current land area: <strong>{{ $desa->luas_wilayah !== null ? number_format((float) $desa->luas_wilayah, 4) . ' km²' : 'not filled' }}</strong>).
        See the results in the <a href="{{ route('kepadatan.index') }}">School-Age Density</a> menu.
    </div>

    <form action="{{ route('penduduk-jenjang.update', $desa->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modern-card p-4 mb-4">
            <h5 class="section-title fw-bold mb-2">Data Source</h5>
            <p class="text-muted small mb-3">Applies to all four levels below (single entry).</p>
            <div class="form-floating" style="max-width: 480px;">
                <input type="text" name="sumber_data" class="form-control modern-input" id="inpSumber"
                       value="{{ old('sumber_data', $existing->first()->sumber_data ?? '') }}"
                       placeholder="Example: ATS Office of Education 2026">
                <label for="inpSumber" class="modern-label">Data Source (e.g. "ATS Office of Education 2026")</label>
            </div>
        </div>

        @foreach($jenjangInti as $jenjangId => $label)
            @php $row = $existing->get($jenjangId); @endphp
            <div class="modern-card p-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="jenjang-badge px-3 py-2">{{ $label }}</span>
                    <p class="text-muted small mb-0">ATS/Dropouts are part of the School-Age Population below, not additional outside of it.</p>
                </div>

                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="form-floating">
                            <input type="number" name="penduduk_l[{{ $jenjangId }}]" class="form-control modern-input"
                                   id="pendL{{ $jenjangId }}" min="0"
                                   value="{{ old('penduduk_l.'.$jenjangId, $row->penduduk_l ?? 0) }}">
                            <label for="pendL{{ $jenjangId }}" class="modern-label">School-Age Population — Male</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="form-floating">
                            <input type="number" name="penduduk_p[{{ $jenjangId }}]" class="form-control modern-input"
                                   id="pendP{{ $jenjangId }}" min="0"
                                   value="{{ old('penduduk_p.'.$jenjangId, $row->penduduk_p ?? 0) }}">
                            <label for="pendP{{ $jenjangId }}" class="modern-label">School-Age Population — Female</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="form-floating">
                            <input type="number" name="putus_sekolah_l[{{ $jenjangId }}]" class="form-control modern-input"
                                   id="putusL{{ $jenjangId }}" min="0"
                                   value="{{ old('putus_sekolah_l.'.$jenjangId, $row->putus_sekolah_l ?? 0) }}">
                            <label for="putusL{{ $jenjangId }}" class="modern-label">ATS/Dropouts — Male</label>
                        </div>
                        <div class="subset-hint mt-1">≤ Male School-Age Pop.</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="form-floating">
                            <input type="number" name="putus_sekolah_p[{{ $jenjangId }}]" class="form-control modern-input"
                                   id="putusP{{ $jenjangId }}" min="0"
                                   value="{{ old('putus_sekolah_p.'.$jenjangId, $row->putus_sekolah_p ?? 0) }}">
                            <label for="putusP{{ $jenjangId }}" class="modern-label">ATS/Dropouts — Female</label>
                        </div>
                        <div class="subset-hint mt-1">≤ Female School-Age Pop.</div>
                    </div>
                </div>

                @if($row && $row->sumber_data)
                    <p class="text-muted small mb-0 mt-2">Currently saved source: <em>{{ $row->sumber_data }}</em></p>
                @endif
            </div>
        @endforeach

        <div class="modern-card p-4 text-center">
            <p class="text-muted small mb-3">Make sure ATS/Dropouts do not exceed the School-Age Population for the same gender before saving.</p>
            <button type="submit" class="btn btn-gradient btn-lg px-5 py-3 fw-bold fs-5 w-100 w-md-50">
                Save Data
            </button>
        </div>
    </form>
</div>
@endsection
