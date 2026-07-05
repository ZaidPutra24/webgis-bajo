@extends('layouts.admin')

@section('title', 'Statistics Data Form')
@section('page-title', 'Statistik: ' . $sekolah->nama_sekolah)

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
        <a href="{{ route('statistik.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-secondary">
            Back
        </a>
        <div class="school-badge px-4 py-2 fw-bold shadow-sm">
            {{ $sekolah->nama_sekolah }}
        </div>
    </div>

    <form action="{{ route('statistik.update', $sekolah->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            
            {{-- SEKSI DEMOGRAFI --}}
            <div class="col-xl-4 col-lg-5">
                <div class="modern-card p-4 h-100">
                    <h5 class="section-title fw-bold mb-2">Demographics</h5>
                    <p class="text-muted small mb-4">The total human population within the school environment.</p>

                    <div class="form-floating mb-3">
                        <input type="number" name="siswa_l" class="form-control modern-input" id="inpSiswaL" 
                               value="{{ old('siswa_l', $sekolah->statistik->siswa_l ?? 0) }}" min="0"
                               placeholder="Example: 180">
                        <label for="inpSiswaL" class="modern-label">Male Students</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="siswa_p" class="form-control modern-input" id="inpSiswaP" 
                               value="{{ old('siswa_p', $sekolah->statistik->siswa_p ?? 0) }}" min="0"
                               placeholder="Example: 170">
                        <label for="inpSiswaP" class="modern-label">Female Students</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="jumlah_siswa" class="form-control modern-input" id="inpSiswa" 
                               value="{{ old('jumlah_siswa', $sekolah->statistik->jumlah_siswa ?? 0) }}" min="0"
                               placeholder="Example: 350">
                        <label for="inpSiswa" class="modern-label">Total Students</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="daya_tampung" class="form-control modern-input" id="inpDayaTampung" 
                               value="{{ old('daya_tampung', $sekolah->statistik->daya_tampung ?? 0) }}" min="0"
                               placeholder="Example: 400">
                        <label for="inpDayaTampung" class="modern-label">Daya Tampung (Capacity)</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="jumlah_guru" class="form-control modern-input" id="inpGuru" 
                               value="{{ old('jumlah_guru', $sekolah->statistik->jumlah_guru ?? 0) }}" min="0"
                               placeholder="Example: 24">
                        <label for="inpGuru" class="modern-label">Teachers</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="jumlah_rombel" class="form-control modern-input" id="inpRombel" 
                               value="{{ old('jumlah_rombel', $sekolah->statistik->jumlah_rombel ?? 0) }}" min="0"
                               placeholder="Example: 12">
                        <label for="inpRombel" class="modern-label">Class Groups (Rombel)</label>
                    </div>
                </div>
            </div>

            {{-- SEKSI INFRASTRUKTUR --}}
            <div class="col-xl-8 col-lg-7">
                <div class="modern-card p-4 h-100">
                    <h5 class="section-title fw-bold mb-2">Capacity & Infrastructure</h5>
                    <p class="text-muted small mb-4">Building metrics and available physical facilities.</p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" name="ruang_kelas" class="form-control modern-input" id="inpKelas" 
                                       value="{{ old('ruang_kelas', $sekolah->statistik->ruang_kelas ?? 0) }}" min="0"
                                       placeholder="Example: 12">
                                <label for="inpKelas" class="modern-label">Physical Classrooms</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" name="laboratorium" class="form-control modern-input" id="inpLab" 
                                       value="{{ old('laboratorium', $sekolah->statistik->laboratorium ?? 0) }}" min="0"
                                       placeholder="Example: 2">
                                <label for="inpLab" class="modern-label">Laboratories</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" name="perpustakaan" class="form-control modern-input" id="inpPerpus" 
                                       value="{{ old('perpustakaan', $sekolah->statistik->perpustakaan ?? 0) }}" min="0"
                                       placeholder="Example: 1">
                                <label for="inpPerpus" class="modern-label">Library</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="col-12">
                <div class="modern-card p-4 text-center">
                    <p class="text-muted small mb-3">Make sure the information you enter is valid before saving.</p>
                    <button type="submit" class="btn btn-gradient btn-lg px-5 py-3 fw-bold fs-5 w-100 w-md-50">
                        {{ $sekolah->statistik ? 'Update Existing Data' : 'Save New Data' }}
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
// Auto-hitung jumlah_siswa = siswa_l + siswa_p saat salah satu berubah
(function () {
    var inpL    = document.getElementById('inpSiswaL');
    var inpP    = document.getElementById('inpSiswaP');
    var inpTot  = document.getElementById('inpSiswa');

    if (!inpL || !inpP || !inpTot) return;

    function recalc() {
        var l = parseInt(inpL.value, 10) || 0;
        var p = parseInt(inpP.value, 10) || 0;
        if (l > 0 || p > 0) {
            inpTot.value = l + p;
            inpTot.style.background = '#f0fdf4';
            inpTot.title = 'Auto-calculated from Male + Female students';
        }
    }

    inpL.addEventListener('input', recalc);
    inpP.addEventListener('input', recalc);

    // Reset highlight when user manually edits total
    inpTot.addEventListener('focus', function () {
        this.style.background = '';
        this.title = '';
    });
})();
</script>

@endsection