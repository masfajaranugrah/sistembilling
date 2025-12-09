@extends('layouts/layoutMaster')

@section('title', 'Tambah Barang Masuk - Inventory')


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



@section('content')
<div class="app-ecommerce">
    <form action="{{ route('add.barangmasuk') }}" method="POST">
        @csrf

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">Tambah Barang Masuk</h4>
                <p class="text-muted mb-0">Pilih barang, jumlah, jenis, dan tanggal barang masuk.</p>
            </div>

            <div class="d-flex gap-3">
                <a href="{{ route('index.barangmasuk') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Barang Masuk</h5>
                    </div>
                    <div class="card-body">

                        {{-- Pilih Barang --}}
                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Nama Barang</label>
                            <select class="form-select" id="barang_id" name="barang_id" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}" {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama_barang }} (Stok: {{ $barang->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jumlah --}}
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required
                                   value="{{ old('jumlah', 1) }}">
                        </div>

                        {{-- Jenis --}}
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis Barang Masuk</label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="pembelian" {{ old('jenis') == 'pembelian' ? 'selected' : '' }}>Pembelian</option>
                                <option value="pengembalian_barang" {{ old('jenis') == 'pengembalian_barang' ? 'selected' : '' }}>Pengembalian Barang</option>
                            </select>
                        </div>

                        {{-- Tanggal Masuk --}}
                        <div class="mb-3">
                            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                            <input type="text" id="tanggal" name="tanggal_masuk" class="form-control"
                                   value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
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
