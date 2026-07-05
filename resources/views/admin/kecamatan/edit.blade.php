@extends('layouts.admin')

@section('title', 'Edit Kecamatan Area')
@section('page-title', 'Edit Kecamatan Area')

@section('content')
<style>
    .modern-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    .form-header-title {
        position: relative;
        padding-left: 1rem;
        color: #2b3445;
    }
    .form-header-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        height: 60%;
        width: 5px;
        background: linear-gradient(135deg, #4F46E5 0%, #06b6d4 100%);
        border-radius: 5px;
    }
    .form-label-custom {
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .form-control-custom {
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
        color: #334155;
        transition: all 0.2s ease-in-out;
        background-color: #f8fafc;
    }
    .form-control-custom:focus {
        border-color: #4F46E5;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        color: #1e293b;
    }
    .geojson-textarea {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.85rem;
        line-height: 1.6;
        color: #475569;
        background-color: #f8fafc;
    }
    .btn-action-save {
        background-color: #4F46E5;
        border: 2px solid #4F46E5;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
    }
    .btn-action-save:hover {
        background-color: #4338ca;
        border-color: #4338ca;
        color: #ffffff;
        transform: translateY(-1px);
    }
    .btn-action-cancel {
        border: 2px solid #e2e8f0;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: transparent;
    }
    .btn-action-cancel:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #334155;
    }
    .roi-summary-card {
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        border-radius: 1.25rem;
        padding: 1.75rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    .roi-summary-icon {
        width: 56px; height: 56px; border-radius: 50%;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; flex-shrink: 0;
    }
    .roi-summary-value { font-size: 2.1rem; font-weight: 800; line-height: 1; }
    .roi-summary-label { font-size: 0.8rem; opacity: 0.75; margin-top: 4px; }
    .roi-school-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.6rem 0.9rem; border-radius: 0.65rem; background: #f8fafc;
        border: 1px solid #e2e8f0; margin-bottom: 0.5rem; font-size: 0.85rem;
    }
    .roi-school-badge {
        font-size: 0.68rem; font-weight: 700; padding: 0.2rem 0.55rem;
        border-radius: 999px; background: #dbeafe; color: #1d4ed8; white-space: nowrap;
    }
</style>

<div class="container-fluid px-0 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Edit Kecamatan Area</h4>
                    <p class="text-muted small mb-0">Update the ROI boundary data for "{{ $kecamatan->nama_kecamatan }}".</p>
                </div>
                <a href="{{ route('kecamatan.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill shadow-sm">
                    Back
                </a>
            </div>

            <!-- ── ROI ANALYSIS: Jumlah sekolah dalam jangkauan kecamatan ini ── -->
            <div class="roi-summary-card mb-4 shadow">
                <div class="roi-summary-icon">
                    <i class="bi bi-buildings-fill"></i>
                </div>
                <div>
                    <div class="roi-summary-value">{{ $sekolahDalamRoi->count() }} Sekolah</div>
                    <div class="roi-summary-label">
                        Number of schools within the reach of Kecamatan {{ $kecamatan->nama_kecamatan }}
                        (calculated automatically from the school coordinates vs. this ROI boundary)
                    </div>
                </div>
            </div>

            @if($sekolahDalamRoi->count() > 0)
            <div class="card modern-card shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0 rounded-top-4">
                    <h6 class="form-header-title fw-bold mb-0">School List within this ROI</h6>
                </div>
                <div class="card-body pt-2" style="max-height:280px; overflow-y:auto;">
                    @foreach($sekolahDalamRoi as $s)
                    <div class="roi-school-item">
                        <span class="fw-semibold text-dark">{{ $s->nama_sekolah }}</span>
                        <span class="roi-school-badge">{{ $s->jenjang->nama_jenjang ?? '-' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card modern-card shadow-sm">
                <div class="card-header bg-white py-4 border-0 rounded-top-4">
                    <h5 class="form-header-title fw-bold mb-0">Kecamatan Boundary Data</h5>
                </div>

                <div class="card-body p-4 pt-2">
                    <form action="{{ route('kecamatan.update', $kecamatan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="nama_kecamatan" class="form-label form-label-custom mb-2">Kecamatan Name</label>
                            <input type="text" class="form-control form-control-custom @error('nama_kecamatan') is-invalid @enderror"
                                   id="nama_kecamatan" name="nama_kecamatan" value="{{ old('nama_kecamatan', $kecamatan->nama_kecamatan) }}" required>
                            @error('nama_kecamatan') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="kabupaten" class="form-label form-label-custom mb-2">Kabupaten/Kota</label>
                                <input type="text" class="form-control form-control-custom @error('kabupaten') is-invalid @enderror"
                                       id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $kecamatan->kabupaten) }}">
                                @error('kabupaten') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="provinsi" class="form-label form-label-custom mb-2">Provinsi</label>
                                <input type="text" class="form-control form-control-custom @error('provinsi') is-invalid @enderror"
                                       id="provinsi" name="provinsi" value="{{ old('provinsi', $kecamatan->provinsi) }}">
                                @error('provinsi') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="luas_wilayah" class="form-label form-label-custom mb-2">Land Area (Hektar)</label>
                                <input type="number" step="0.01" class="form-control form-control-custom @error('luas_wilayah') is-invalid @enderror"
                                       id="luas_wilayah" name="luas_wilayah" value="{{ old('luas_wilayah', $kecamatan->luas_wilayah) }}">
                                @error('luas_wilayah') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="warna" class="form-label form-label-custom mb-2">ROI Color on Map</label>
                                <input type="color" class="form-control form-control-custom @error('warna') is-invalid @enderror"
                                       id="warna" name="warna" value="{{ old('warna', $kecamatan->warna ?? '#ea580c') }}" style="height:48px;padding:6px;">
                                @error('warna') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="geojson" class="form-label form-label-custom mb-2">Raw GeoJSON Text (Kecamatan Boundary)</label>
                            <textarea class="form-control form-control-custom geojson-textarea @error('geojson') is-invalid @enderror"
                                      id="geojson" name="geojson" rows="8" required>{{ old('geojson', $kecamatan->geojson) }}</textarea>
                            @error('geojson') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end pt-3 border-top border-1 border-light">
                            <a href="{{ route('kecamatan.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-action-save px-4 py-2 rounded-pill">
                                Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection