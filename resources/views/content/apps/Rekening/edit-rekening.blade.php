@extends('layouts/layoutMaster')

@section('title', 'Edit Rekening')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
])
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bisa ditambahkan script khusus jika perlu
});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<div class="app-rekening-edit">
    <form action="{{ route('rekenings.update', $rekening->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div>
                <h4 class="mb-1">Edit Rekening</h4>
                <p class="text-muted mb-0">Perbarui data rekening di bawah ini.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('rekenings.index') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Informasi Rekening</h5>
                    </div>
                    <div class="card-body">

                        <!-- Nama Bank -->
                        <div class="mb-3">
                            <label for="nama_bank" class="form-label">Nama Bank</label>
                            <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                   value="{{ old('nama_bank', $rekening->nama_bank) }}" required>
                        </div>

                        <!-- Nomor Rekening -->
                        <div class="mb-3">
                            <label for="nomor_rekening" class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening"
                                   value="{{ old('nomor_rekening', $rekening->nomor_rekening) }}" required>
                        </div>

                        <!-- Nama Pemilik -->
                        <div class="mb-3">
                            <label for="nama_pemilik" class="form-label">Nama Pemilik</label>
                            <input type="text" class="form-control" id="nama_pemilik" name="nama_pemilik"
                                   value="{{ old('nama_pemilik', $rekening->nama_pemilik) }}" required>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
