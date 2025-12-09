@extends('layouts/layoutMaster')

@section('title', 'Tambah Barang Keluar - Inventory')

@section('vendor-style')
<!-- Select2 CSS -->
@vite([
     'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
<!-- Select2 JS -->
@vite([
     'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Select2 untuk dropdown barang
    $('#barang_id').select2({
        placeholder: "Pilih barang",
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('store.barangkeluar') }}" method="POST">
        @csrf

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">Tambah Barang Keluar</h4>
                <p class="text-muted mb-0">Pilih barang yang keluar dan jumlahnya.</p>
            </div>

            <div class="d-flex gap-3">
                <a href="{{ route('index.barangkeluar') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Barang Keluar</h5>
                    </div>
                    <div class="card-body">

                        {{-- Dropdown Barang --}}
                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Nama Barang</label>
                            <select name="barang_id" id="barang_id" class="form-select" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}">
                                        {{ $barang->nama_barang }} (Stok: {{ $barang->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jumlah --}}
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Keluar</label>
                            <input
                                type="number"
                                class="form-control"
                                id="jumlah"
                                name="jumlah"
                                min="1"
                                value="{{ old('jumlah', 1) }}"
                                required
                            >
                        </div>
<div class="mb-3">
    <label for="diambil_oleh" class="form-label">Diambil Oleh</label>
    <input
        type="text"
        class="form-control"
        id="diambil_oleh"
        name="diambil_oleh"
        value="{{ old('diambil_oleh') }}"
        required
    >
</div>
<div class="mb-3">
    <label for="tanggal" class="form-label">Tanggal Keluar</label>
    <input
        type="text"
        class="form-control"
        id="tanggal"
        name="tanggal"
        value="{{ old('tanggal', date('Y-m-d')) }}"
        required
    >
</div>


                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea
                                class="form-control"
                                id="keterangan"
                                name="keterangan"
                                rows="3"
                            >{{ old('keterangan') }}</textarea>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select2 untuk dropdown barang
    $('#barang_id').select2({
        placeholder: "Pilih barang",
        allowClear: true,
        width: '100%'
    });

    // Flatpickr untuk tanggal barang keluar
    flatpickr("#tanggal", {
        dateFormat: "Y-m-d",
        defaultDate: "{{ date('Y-m-d') }}" // default hari ini
    });
});
</script>

@endsection
