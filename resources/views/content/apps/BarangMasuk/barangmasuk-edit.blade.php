@extends('layouts/layoutMaster')

@section('title', 'Edit Barang Masuk - Inventory')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.css',
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select2
    $('#barang_id').select2({
        placeholder: "Pilih barang",
        allowClear: true,
        width: '100%'
    });

    // Flatpickr
    flatpickr("#tanggal_masuk", {
        dateFormat: "Y-m-d",
        defaultDate: "{{ old('tanggal_masuk', $barangMasuk->tanggal_masuk) }}",
        allowInput: true
    });
});
</script>
@endsection

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('edit.barangmasuk', $barangMasuk->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">Edit Barang Masuk</h4>
                <p class="text-muted mb-0">Perbarui informasi barang masuk.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('index.barangmasuk') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Barang Masuk</h5>
                    </div>
                    <div class="card-body">

                        {{-- Nama Barang --}}
                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Nama Barang</label>
                            <select class="form-select" id="barang_id" name="barang_id" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}"
                                        {{ old('barang_id', $barangMasuk->barang_id) == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama_barang }} (Stok: {{ $barang->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jumlah --}}
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required
                                   value="{{ old('jumlah', $barangMasuk->jumlah) }}">
                        </div>

                        {{-- Jenis --}}
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis Barang Masuk</label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="pembelian" {{ old('jenis', $barangMasuk->jenis) == 'pembelian' ? 'selected' : '' }}>Pembelian</option>
                                <option value="pengembalian_barang" {{ old('jenis', $barangMasuk->jenis) == 'pengembalian_barang' ? 'selected' : '' }}>Pengembalian Barang</option>
                            </select>
                        </div>

                        {{-- Tanggal Masuk --}}
                        <div class="mb-3">
                            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                            <input type="text" class="form-control" id="tanggal_masuk" name="tanggal_masuk"
                                   value="{{ old('tanggal_masuk', $barangMasuk->tanggal_masuk) }}" required>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $barangMasuk->keterangan) }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
