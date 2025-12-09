@extends('layouts/layoutMaster')

@section('title', 'Edit Karyawan')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#date_of_birth", {
        dateFormat: "Y-m-d"
    });
    flatpickr("#tanggal_masuk", {
        dateFormat: "Y-m-d"
    });
});
</script>
@endsection

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">Edit Data Karyawan</h4>
                <p class="text-muted mb-0">Perbarui informasi karyawan di bawah ini.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('karyawan.index') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Informasi Karyawan -->
                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Karyawan</h5>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nip" class="form-label">NIK</label>
                                <input type="text" class="form-control" id="nip" name="nip"
                                       value="{{ old('nip', $employee->nik) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="full_name" name="full_name"
                                       value="{{ old('full_name', $employee->full_name) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="full_address" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" id="full_address" name="full_address" rows="3" required>{{ old('full_address', $employee->full_address) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="place_of_birth" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth"
                                       value="{{ old('place_of_birth', $employee->place_of_birth) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Tanggal Lahir</label>
                                <input type="text" class="form-control" id="date_of_birth" name="date_of_birth"
                                       value="{{ old('date_of_birth', $employee->date_of_birth) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp"
                                       value="{{ old('no_hp', $employee->no_hp) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="text" class="form-control" id="tanggal_masuk" name="tanggal_masuk"
                                       value="{{ old('tanggal_masuk', $employee->tanggal_masuk) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                       value="{{ old('jabatan', $employee->jabatan) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bank" class="form-label">Bank</label>
                                <input type="text" class="form-control" id="bank" name="bank"
                                       value="{{ old('bank', $employee->bank) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="no_rekening" class="form-label">Nomor Rekening</label>
                                <input type="text" class="form-control" id="no_rekening" name="no_rekening"
                                       value="{{ old('no_rekening', $employee->no_rekening) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="atas_nama" class="form-label">Atas Nama Rekening</label>
                            <input type="text" class="form-control" id="atas_nama" name="atas_nama"
                                   value="{{ old('atas_nama', $employee->atas_nama) }}" required>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
