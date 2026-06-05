@extends('layouts.admin')

@section('title', 'Add Distance Matrix Data')
@section('page-title', 'Input New Distance Matrix')

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
    .input-group-text-custom {
        border: 2px solid #e2e8f0;
        border-left: none;
        background-color: #f1f5f9;
        color: #64748b;
        font-weight: 600;
        padding-left: 1.25rem;
        padding-right: 1.25rem;
        border-top-right-radius: 0.75rem !important;
        border-bottom-right-radius: 0.75rem !important;
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
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Manage Spatial Distance</h4>
                    <p class="text-muted small mb-0">Add new geographic proximity parameters from school institutions to target village polygons.</p>
                </div>
                <a href="{{ route('jarak.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill shadow-sm">
                    Back
                </a>
            </div>

            <div class="card modern-card shadow-sm">
                <div class="card-header bg-white py-4 border-0 rounded-top-4">
                    <h5 class="form-header-title fw-bold mb-0">Input New Distance Matrix</h5>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <form action="{{ route('jarak.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="sekolah_id" class="form-label form-label-custom mb-2">Select School</label>
                            <select class="form-select form-select-custom @error('sekolah_id') is-invalid @enderror" id="sekolah_id" name="sekolah_id" required>
                                <option value="">-- Select School --</option>
                                @foreach($sekolahs as $s)
                                    <option value="{{ $s->id }}" {{ old('sekolah_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_sekolah }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sekolah_id') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="wilayah_id" class="form-label form-label-custom mb-2">Select Village Desa (ROI Komunitas Bajo)</label>
                            <select class="form-select form-select-custom @error('wilayah_id') is-invalid @enderror" id="wilayah_id" name="wilayah_id" required>
                                <option value="">-- Pilih Desa Induk --</option>
                                @foreach($wilayahs as $w)
                                    <option value="{{ $w->id }}" {{ old('wilayah_id') == $w->id ? 'selected' : '' }}>
                                        {{ $w->nama_wilayah }}
                                    </option>
                                @endforeach
                            </select>
                            @error('wilayah_id') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="jarak" class="form-label form-label-custom mb-2">Geographic Distance</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control form-control-custom @error('jarak') is-invalid @enderror" id="jarak" name="jarak" value="{{ old('jarak') }}" placeholder="Example: 17.58 or 0.11" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" required>
                                <span class="input-group-text input-group-text-custom">Km</span>
                                @error('jarak') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end pt-3 border-top border-1 border-light">
                            <a href="{{ route('jarak.index') }}" class="btn btn-action-cancel px-4 py-2 rounded-pill">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-action-save px-4 py-2 rounded-pill">
                                Save Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection