@extends('layouts.admin')

@section('title', 'Form Data Curriculum & Utilities')
@section('page-title', 'Utilities Data Form: ' . $sekolah->nama_sekolah)

@section('content')
<style>
    /* Custom CSS untuk Modern & Minimalis Form tanpa dekorasi berlebih */
    .modern-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        background: #ffffff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .modern-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
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
        border-radius: 0.75rem !important;
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
    .school-badge {
        background: linear-gradient(135deg, #f0fdfa 0%, #e0f2fe 100%);
        color: #0369a1;
        border-radius: 2rem;
    }
</style>

<div class="container-fluid px-0 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('utilitas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-secondary">
            Back
        </a>
        <div class="school-badge px-4 py-2 fw-bold shadow-sm">
            {{ $sekolah->nama_sekolah }}
        </div>
    </div>

    <form action="{{ route('utilitas.update', $sekolah->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            
            <div class="col-xl-4 col-lg-5">
                <div class="modern-card p-4 h-100">
                    <h5 class="section-title fw-bold mb-2">Academic</h5>
                    <p class="text-muted small mb-4">Learning system and operational scheduling.</p>

                    <div class="form-floating mb-3">
                        <input type="text" name="kurikulum" class="form-control modern-input" id="inpCurriculum" 
                               value="{{ old('kurikulum', $sekolah->utilitas->kurikulum ?? '') }}" 
                               placeholder="Contoh: Curriculum Merdeka, K-13">
                        <label for="inpCurriculum" class="modern-label">Applied Curriculum</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="penyelenggara" class="form-control modern-input" id="inpOperator" 
                               value="{{ old('penyelenggara', $sekolah->utilitas->penyelenggara ?? '') }}" 
                               placeholder="Contoh: Pagi / 5 Hari">
                        <label for="inpOperator" class="modern-label">Operational Hours</label>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="modern-card p-4 h-100">
                    <h5 class="section-title fw-bold mb-2">Utility & Infrastructure</h5>
                    <p class="text-muted small mb-4">Metrics for physical facilities and supporting utilities of the school.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="akses_internet" class="form-control modern-input" id="inpInternet" 
                                       value="{{ old('akses_internet', $sekolah->utilitas->akses_internet ?? '') }}" 
                                       placeholder="Contoh: Telkomsel Flash, Indihome, Tidak Ada">
                                <label for="inpInternet" class="modern-label">Main Internet Access</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="sumber_listrik" class="form-control modern-input" id="inpListrik" 
                                       value="{{ old('sumber_listrik', $sekolah->utilitas->sumber_listrik ?? '') }}" 
                                       placeholder="Contoh: PLN, Diesel / Genset">
                                <label for="inpListrik" class="modern-label">Main Power Source</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" name="daya_listrik" class="form-control modern-input" id="inpDaya" 
                                       value="{{ old('daya_listrik', $sekolah->utilitas->daya_listrik ?? '') }}" min="0" 
                                       placeholder="Contoh: 1300, 2200, 3500">
                                <label for="inpDaya" class="modern-label">Power (Watt) (VA)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" name="luas_tanah" class="form-control modern-input" id="inpLuas" 
                                       value="{{ old('luas_tanah', $sekolah->utilitas->luas_tanah ?? '') }}" min="0" 
                                       placeholder="Contoh: 1500.50, 2400.00">
                                <label for="inpLuas" class="modern-label">Land Area (m²) (m²)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="modern-card p-4 text-center">
                    <p class="text-muted small mb-3">Make sure the utility information entered is valid before saving.</p>
                    <button type="submit" class="btn btn-gradient btn-lg px-5 py-3 fw-bold fs-5 w-100 w-md-50">
                        {{ $sekolah->utilitas ? 'Update Existing Data' : 'Save New Data' }}
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection