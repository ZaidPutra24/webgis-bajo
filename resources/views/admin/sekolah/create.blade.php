@extends('layouts.admin')

@section('title', 'Add School')
@section('page-title', 'Add School Baru')

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
    .form-control-custom, .form-select-custom {
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
        color: #334155;
        transition: all 0.2s ease-in-out;
        background-color: #f8fafc;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #4F46E5;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        color: #1e293b;
    }
    .radio-container-custom {
        background-color: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.75rem 1.25rem;
    }
    .form-check-input:checked {
        background-color: #4F46E5;
        border-color: #4F46E5;
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
</style>

<div class="container-fluid px-0 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Institution Registration</h4>
                    <p class="text-muted small mb-0">Add new spatial boundary configuration, area estimation calculation, and GeoJSON data representation structure.</p>
                </div>
                <a href="{{ route('sekolah.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill shadow-sm">
                    Back
                </a>
            </div>

            <div class="card modern-card shadow-sm">
                <div class="card-header bg-white py-4 border-0 rounded-top-4">
                    <h5 class="form-header-title fw-bold mb-0">Add School Baru</h5>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <form action="{{ route('sekolah.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="nama_sekolah" class="form-label form-label-custom mb-2">School Name</label>
                            <input type="text" class="form-control form-control-custom @error('nama_sekolah') is-invalid @enderror" id="nama_sekolah" name="nama_sekolah" value="{{ old('nama_sekolah') }}" placeholder="Example: SDN 1 Bajo" required>
                            @error('nama_sekolah') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="jenjang_id" class="form-label form-label-custom mb-2">School Level</label>
                            <select class="form-select form-select-custom @error('jenjang_id') is-invalid @enderror" id="jenjang_id" name="jenjang_id" required>
                                <option value="">-- Choose School Level --</option>
                                @foreach($jenjangs as $j)
                                    <option value="{{ $j->id }}" {{ old('jenjang_id') == $j->id ? 'selected' : '' }}>
                                        {{ $j->kode }} - {{ $j->nama_jenjang }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenjang_id') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- FIX: Nilai status diganti Negeri/Swasta agar sesuai validasi controller --}}
                        <div class="mb-4">
                            <label class="form-label form-label-custom mb-2 d-block">School Status</label>
                            <div class="radio-container-custom d-inline-flex gap-4">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="status" id="status_negeri" value="Negeri" {{ old('status', 'Negeri') == 'Negeri' ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark fw-medium small" for="status_negeri">Negeri</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="status" id="status_swasta" value="Swasta" {{ old('status') == 'Swasta' ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark fw-medium small" for="status_swasta">Swasta</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="npsn" class="form-label form-label-custom mb-2">NPSN</label>
                                <input type="text" class="form-control form-control-custom @error('npsn') is-invalid @enderror" id="npsn" name="npsn" value="{{ old('npsn') }}" placeholder="Enter NPSN">
                                @error('npsn') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="akreditasi" class="form-label form-label-custom mb-2">Accreditation</label>
                                <input type="text" class="form-control form-control-custom @error('akreditasi') is-invalid @enderror" id="akreditasi" name="akreditasi" value="{{ old('akreditasi') }}" placeholder="Example: A, B, C">
                                @error('akreditasi') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label form-label-custom mb-2">Latitude</label>
                                <input type="number" step="any" class="form-control form-control-custom @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="Opsional — contoh: -5.44321">
                                @error('latitude') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label form-label-custom mb-2">Longitude</label>
                                <input type="number" step="any" class="form-control form-control-custom @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="Opsional — contoh: 122.5112">
                                @error('longitude') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="alamat" class="form-label form-label-custom mb-2">Address Sekolah</label>
                            <textarea class="form-control form-control-custom @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Example: Jl. Raya Bajo No. 123">{{ old('alamat') }}</textarea>
                            @error('alamat') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="img" class="form-label form-label-custom mb-2">Foto Sekolah <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="file" class="form-control form-control-custom @error('img') is-invalid @enderror"
                                   id="img" name="img" accept="image/jpg,image/jpeg,image/png,image/webp">
                            <div class="form-text text-muted mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</div>
                            @error('img') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end pt-3 border-top border-1 border-light">
                            <a href="{{ route('sekolah.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-action-save px-4 py-2 rounded-pill">
                                Save Sekolah
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection