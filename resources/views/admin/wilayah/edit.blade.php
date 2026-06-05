@extends('layouts.admin')

@section('title', 'Edit Wilayah Desa')
@section('page-title', 'Edit Village Area Data')

@section('content')
<style>
    /* Custom CSS Form Style dari Desain Sebelumnya */
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
</style>

<div class="container-fluid px-0 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Village Area Management</h4>
                    <p class="text-muted small mb-0">Ubah konfigurasi batas spasial, kalkulasi luas area, dan struktur sintaksis representasi data GeoJSON.</p>
                </div>
                <a href="{{ route('wilayah.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill shadow-sm">
                    Back
                </a>
            </div>

            <div class="card modern-card shadow-sm">
                <div class="card-header bg-white py-4 border-0 rounded-top-4">
                    <h5 class="form-header-title fw-bold mb-0">Edit Village Area Data</h5>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <form action="{{ route('wilayah.update', $wilayah->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="nama_wilayah" class="form-label form-label-custom mb-2">Village Name / Desa</label>
                            <input type="text" class="form-control form-control-custom @error('nama_wilayah') is-invalid @enderror" id="nama_wilayah" name="nama_wilayah" value="{{ old('nama_wilayah', $wilayah->nama_wilayah) }}" required>
                            @error('nama_wilayah') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="luas_wilayah" class="form-label form-label-custom mb-2">Luas Wilayah (Hektar)</label>
                            <input type="number" step="0.01" class="form-control form-control-custom @error('luas_wilayah') is-invalid @enderror" id="luas_wilayah" name="luas_wilayah" value="{{ old('luas_wilayah', $wilayah->luas_wilayah) }}">
                            @error('luas_wilayah') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="geojson" class="form-label form-label-custom mb-2">Raw GeoJSON Teks</label>
                            <textarea class="form-control form-control-custom geojson-textarea @error('geojson') is-invalid @enderror" id="geojson" name="geojson" rows="8" required>{{ old('geojson', $wilayah->geojson) }}</textarea>
                            @error('geojson') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end pt-3 border-top border-1 border-light">
                            <a href="{{ route('wilayah.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-action-save px-4 py-2 rounded-pill">
                                Perbarui Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection