@extends('layouts/layoutMaster')

@section('title', 'Tambah Barang - Inventory')

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('post-barang') }}" method="POST">
        @csrf

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">Tambah Barang Baru</h4>
                <p class="text-muted mb-0">Isi data barang dengan lengkap dan benar.</p>
            </div>

            <div class="d-flex gap-3">
                <a href="{{ route('barangs') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Barang</h5>
                    </div>
                    <div class="card-body">

                        {{-- Nama Barang --}}
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nama_barang"
                                name="nama_barang"
                                required
                                value="{{ old('nama_barang') }}"
                            >
                        </div>

                        {{-- Stok Awal --}}
                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok Awal</label>
                            <input
                                type="number"
                                class="form-control"
                                id="stok"
                                name="stok"
                                min="0"
                                value="{{ old('stok', 0) }}"
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
@endsection
