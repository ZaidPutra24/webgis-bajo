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
                    <p class="text-muted small mb-0">Edit spatial boundaries, calculate area, and modify GeoJSON data structure.</p>
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
                    <form action="{{ route('wilayah.update', $wilayah->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="nama_wilayah" class="form-label form-label-custom mb-2">Village Name / Desa</label>
                            <input type="text" class="form-control form-control-custom @error('nama_wilayah') is-invalid @enderror" id="nama_wilayah" name="nama_wilayah" value="{{ old('nama_wilayah', $wilayah->nama_wilayah) }}" required>
                            @error('nama_wilayah') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="luas_wilayah" class="form-label form-label-custom mb-2">Land Area (Hektar)</label>
                            <input type="number" step="0.01" class="form-control form-control-custom @error('luas_wilayah') is-invalid @enderror" id="luas_wilayah" name="luas_wilayah" value="{{ old('luas_wilayah', $wilayah->luas_wilayah) }}">
                            @error('luas_wilayah') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="gambar" class="form-label form-label-custom mb-2">Village Image <span class="text-muted fw-normal">(Optional)</span></label>
                            @if(isset($wilayah) && $wilayah->gambar)
                                <div class="mb-3 p-3 rounded-3 border border-2" style="border-color:#e2e8f0;background:#f8fafc;" id="fotoPreviewBox">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('img/wilayah/' . $wilayah->gambar) }}"
                                             alt="Current Image" class="rounded-2" id="fotoPreviewImg"
                                             style="width:80px;height:80px;object-fit:cover;flex-shrink:0;"
                                             onerror="this.style.display='none'">
                                        <div class="flex-grow-1">
                                            <div class="small text-muted mb-1">Current Image:</div>
                                            <code class="small">{{ $wilayah->gambar }}</code>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" id="btnHapusFoto">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="me-1">
                                                <polyline points="3 6 5 6 21 6"/><path d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6m5 0V4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                            Delete Image
                                        </button>
                                    </div>
                                    <div class="mt-2 p-2 rounded-2 bg-danger bg-opacity-10 border border-danger border-opacity-25 d-none" id="konfirmasiHapusFoto">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="small text-danger fw-semibold">Are you sure you want to delete this photo?</span>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnBatalHapus">Cancel</button>
                                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" id="btnKonfirmasiHapus">Yes, Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 rounded-3 border border-2 d-none" style="background:#f0fdf4;border-color:#86efac!important;" id="fotoTerhapusBox">
                                    <div class="d-flex align-items-center gap-2">
                                        <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="small text-success fw-semibold">Image will be deleted when saving.</span>
                                        <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-auto" id="btnUndoHapus">Undo</button>
                                    </div>
                                </div>
                            @endif
                            <input type="hidden" name="hapus_gambar" id="hapusGambarInput" value="0">
                            <div class="mt-2" id="uploadFotoWrap">
                                <input type="file" class="form-control form-control-custom @error('gambar') is-invalid @enderror"
                                       id="gambar" name="gambar" accept="image/*">
                                <div class="form-text text-muted mt-1">
                                    {{ isset($wilayah) && $wilayah->gambar ? 'Upload new image to replace the existing one.' : 'Format: JPG, PNG, WEBP. Max 2MB.' }}
                                </div>
                            </div>
                            @error('gambar') <div class="invalid-feedback mt-2">{{ $message }}</div> @enderror
                        </div>

                        <script>
                        (function() {
                            var btnHapus      = document.getElementById('btnHapusFoto');
                            var btnBatal      = document.getElementById('btnBatalHapus');
                            var btnKonfirmasi = document.getElementById('btnKonfirmasiHapus');
                            var btnUndo       = document.getElementById('btnUndoHapus');
                            var konfirmasiBox = document.getElementById('konfirmasiHapusFoto');
                            var previewBox    = document.getElementById('fotoPreviewBox');
                            var terhapusBox   = document.getElementById('fotoTerhapusBox');
                            var hapusInput    = document.getElementById('hapusGambarInput');
                            var fileInput     = document.getElementById('gambar');

                            if (btnHapus) {
                                btnHapus.addEventListener('click', function() {
                                    konfirmasiBox.classList.remove('d-none');
                                    btnHapus.classList.add('d-none');
                                });
                            }
                            if (btnBatal) {
                                btnBatal.addEventListener('click', function() {
                                    konfirmasiBox.classList.add('d-none');
                                    btnHapus.classList.remove('d-none');
                                });
                            }
                            if (btnKonfirmasi) {
                                btnKonfirmasi.addEventListener('click', function() {
                                    hapusInput.value = '1';
                                    previewBox.classList.add('d-none');
                                    terhapusBox.classList.remove('d-none');
                                    if (fileInput) fileInput.value = '';
                                });
                            }
                            if (btnUndo) {
                                btnUndo.addEventListener('click', function() {
                                    hapusInput.value = '0';
                                    previewBox.classList.remove('d-none');
                                    terhapusBox.classList.add('d-none');
                                    konfirmasiBox.classList.add('d-none');
                                    if (btnHapus) btnHapus.classList.remove('d-none');
                                });
                            }
                        })();
                        </script>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="penduduk_usia_sekolah_l" class="form-label form-label-custom mb-2">Penduduk Usia Sekolah (L)</label>
                                <input type="number" min="0"
                                       class="form-control form-control-custom @error('penduduk_usia_sekolah_l') is-invalid @enderror"
                                       id="penduduk_usia_sekolah_l" name="penduduk_usia_sekolah_l"
                                       value="{{ old('penduduk_usia_sekolah_l', $wilayah->penduduk_usia_sekolah_l ?? '') }}"
                                       placeholder="Jumlah laki-laki">
                                @error('penduduk_usia_sekolah_l') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="penduduk_usia_sekolah_p" class="form-label form-label-custom mb-2">Penduduk Usia Sekolah (P)</label>
                                <input type="number" min="0"
                                       class="form-control form-control-custom @error('penduduk_usia_sekolah_p') is-invalid @enderror"
                                       id="penduduk_usia_sekolah_p" name="penduduk_usia_sekolah_p"
                                       value="{{ old('penduduk_usia_sekolah_p', $wilayah->penduduk_usia_sekolah_p ?? '') }}"
                                       placeholder="Jumlah perempuan">
                                @error('penduduk_usia_sekolah_p') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
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
                                Save Change
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection