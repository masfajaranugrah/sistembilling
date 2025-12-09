@extends('layouts/layoutMaster')

@section('title', 'Tambah Pengeluaran')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const kategoriSelect = document.getElementById('kategori');
    const dllInputWrapper = document.getElementById('kategori_dll_wrapper');
    const dllInput = document.getElementById('kategori_dll');
    const jumlahInput = document.getElementById('jumlah');

    // Tampilkan input kategori DLL jika dipilih
    kategoriSelect.addEventListener('change', () => {
        if(kategoriSelect.value === 'DLL') {
            dllInputWrapper.style.display = 'block';
            dllInput.required = true;
        } else {
            dllInputWrapper.style.display = 'none';
            dllInput.required = false;
        }
    });

    // Inisialisasi Flatpickr untuk tanggal & jam keluar
    flatpickr("#tanggal_keluar", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultDate: new Date(),
        time_24hr: true
    });

    // Format input jumlah menjadi Rupiah saat diketik
    jumlahInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if(value === '') value = 0;
        this.value = new Intl.NumberFormat('id-ID').format(value);
    });

    // Saat submit, kirim angka murni ke backend
    jumlahInput.form.addEventListener('submit', function() {
        jumlahInput.value = jumlahInput.value.replace(/\./g, '');
    });
});
</script>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tambah Pengeluaran</h5>
        <a href="{{ route('keluar.index') }}" class="btn btn-secondary">Batal</a>
    </div>
    <div class="card-body">
        <form action="{{ route('keluar.store') }}" method="POST">
            @csrf

            <!-- Kategori -->
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori_default as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kategori DLL -->
            <div class="mb-3" id="kategori_dll_wrapper" style="display:none;">
                <label for="kategori_dll" class="form-label">Nama Kategori (DLL)</label>
                <input type="text" class="form-control" id="kategori_dll" name="kategori_dll" placeholder="Masukkan nama kategori baru">
            </div>

            <!-- Nominal Harga dengan Rp di depan -->
            <div class="mb-3">
                <label for="jumlah" class="form-label">Nominal Harga</label>
                <div class="input-group">
                    <span class="input-group-text">Rp.</span>
                    <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan jumlah" required>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>

            <!-- Tanggal & Jam Keluar -->
            <div class="mb-3">
                <label for="tanggal_keluar" class="form-label">Tanggal & Jam Keluar</label>
                <input type="text" class="form-control" id="tanggal_keluar" name="tanggal_keluar" placeholder="Pilih tanggal & jam" required>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('keluar.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection