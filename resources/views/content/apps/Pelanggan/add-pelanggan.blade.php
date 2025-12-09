@extends('layouts/layoutMaster')

@section('title', 'Tambah Pelanggan ')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/quill/typography.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paketSelect = document.getElementById('paket_id');
    const hargaDisplay = document.getElementById('harga_display');
    const masaDisplay = document.getElementById('masa_display');
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalBerakhir = document.getElementById('tanggal_berakhir');
    const paketData = @json($paket);

    const formatDate = (date) => {
        const d = new Date(date);
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${month}-${day}`;
    };

    const today = new Date();
    tanggalMulai.value = formatDate(today);

    function updateTanggalBerakhir(masaHari) {
        if (!masaHari) return tanggalBerakhir.value = '';
        const start = new Date(tanggalMulai.value);
        start.setDate(start.getDate() + parseInt(masaHari));
        tanggalBerakhir.value = formatDate(start);
    }

    paketSelect.addEventListener('change', () => {
        const selected = paketData.find(p => p.id == paketSelect.value);
        if (selected) {
            hargaDisplay.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(selected.harga);

            masaDisplay.textContent = `${selected.masa_pembayaran} Hari`;
            updateTanggalBerakhir(selected.masa_pembayaran);
        } else {
            hargaDisplay.textContent = '-';
            masaDisplay.textContent = '-';
            tanggalBerakhir.value = '';
        }
    });

    tanggalMulai.addEventListener('change', () => {
        const selected = paketData.find(p => p.id == paketSelect.value);
        if (selected) updateTanggalBerakhir(selected.masa_pembayaran);
    });

    // Preview foto KTP
    const fotoKtpInput = document.getElementById('foto_ktp');
    if(fotoKtpInput){
        fotoKtpInput.addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('preview_ktp');
            if (!file) {
                preview.style.display = 'none';
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endsection

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('pelanggan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">Tambah Pelanggan Baru</h4>
                <p class="text-muted mb-0">Isi data pelanggan dan akun login dengan lengkap dan benar.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('pelanggan') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <!-- ?? Informasi Pelanggan -->
                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Pelanggan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                 <div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="no_whatsapp">Nomor WhatsApp</label>
        <input type="text" class="form-control @error('no_whatsapp') is-invalid @enderror"
               id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp') }}">
        @error('no_whatsapp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="no_telp">Nomor Telepon</label>
        <input type="text" class="form-control @error('no_telp') is-invalid @enderror"
               id="no_telp" name="no_telp" value="{{ old('no_telp') }}">
        @error('no_telp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


                        <h6 class="text-muted mt-4 mb-2">Alamat Lengkap</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="alamat_jalan" class="form-label">Jalan</label>
                                <input type="text" class="form-control @error('alamat_jalan') is-invalid @enderror" id="alamat_jalan" name="alamat_jalan" value="{{ old('alamat_jalan') }}">
                                @error('alamat_jalan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="rt" class="form-label">RT</label>
                                <input type="text" class="form-control @error('rt') is-invalid @enderror" id="rt" name="rt" value="{{ old('rt') }}">
                                @error('rt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="rw" class="form-label">RW</label>
                                <input type="text" class="form-control @error('rw') is-invalid @enderror" id="rw" name="rw" value="{{ old('rw') }}">
                                @error('rw')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="kode_pos" class="form-label">Kode Pos</label>
                                <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}">
                                @error('kode_pos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="desa" class="form-label">Desa</label>
                                <input type="text" class="form-control @error('desa') is-invalid @enderror" id="desa" name="desa" value="{{ old('desa') }}">
                                @error('desa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="kecamatan" class="form-label">Kecamatan</label>
                                <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatan" name="kecamatan" value="{{ old('kecamatan') }}">
                                @error('kecamatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="kabupaten" class="form-label">Kabupaten</label>
                                <input type="text" class="form-control @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten" value="{{ old('kabupaten') }}">
                                @error('kabupaten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="provinsi" class="form-label">Provinsi</label>
                                <input type="text" class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" value="{{ old('provinsi') }}">
                                @error('provinsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="deskripsi">Deskripsi (Opsional)</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Upload Foto KTP -->
                        <div class="mb-4">
                            <label class="form-label" for="foto_ktp">Upload Foto KTP</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('foto_ktp') is-invalid @enderror" id="foto_ktp" name="foto_ktp" accept="image/*">
                                <label class="input-group-text" for="foto_ktp">Pilih</label>
                            </div>
                            <small class="text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                            @error('foto_ktp')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <div class="mt-3">
                                <img id="preview_ktp" src="#" alt="Preview Foto KTP" class="img-thumbnail" style="display:none; max-width: 200px;">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ?? Paket Internet -->
                <div class="card shadow-sm mb-6">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Paket Internet</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nomer_id" class="form-label">Nomor ID Pelanggan</label>
                                <input type="text" class="form-control @error('nomer_id') is-invalid @enderror" id="nomer_id" name="nomer_id" value="{{ old('nomer_id') }}" required>
                                @error('nomer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="paket_id" class="form-label">Pilih Paket</label>
                                <select class="form-select @error('paket_id') is-invalid @enderror" id="paket_id" name="paket_id" required>
                                    <option value="">-- Pilih Paket --</option>
                                    @foreach($paket as $p)
                                        <option value="{{ $p->id }}" {{ old('paket_id')==$p->id ? 'selected' : '' }}>{{ $p->nama_paket }} - {{ $p->kecepatan }} Mbps</option>
                                    @endforeach
                                </select>
                                @error('paket_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga</label>
                                <p id="harga_display" class="form-control bg-light">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Masa Aktif</label>
                                <p id="masa_display" class="form-control bg-light">-</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label">Tanggal Aktif</label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_berakhir" class="form-label">Tanggal Habis Kontrak</label>
                                <input type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror" id="tanggal_berakhir" name="tanggal_berakhir" value="{{ old('tanggal_berakhir') }}" required>
                                @error('tanggal_berakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
