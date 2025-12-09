@extends('layouts/layoutMaster')

@section('title', 'Edit Barang')

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('edit-barang', $barang->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">Edit Data Barang</h4>
                <p class="text-muted mb-0">Perbarui informasi barang di bawah ini.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('barangs') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Informasi Barang -->
                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Barang</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                                   value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok"
                                   value="{{ old('stok', $barang->stok) }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $barang->keterangan) }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
