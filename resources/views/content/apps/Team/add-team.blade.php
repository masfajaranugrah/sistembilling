@extends('layouts/layoutMaster')

@section('title', 'Tambah User Baru')

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
    // Inisialisasi Select2
    $('#role').select2({
        placeholder: 'Pilih peran pengguna',
        width: '100%'
    });
});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<div class="app-user-register">
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div>
                <h4 class="mb-1">Tambah User Baru</h4>
                <p class="text-muted mb-0">Isi data user dengan lengkap.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('users.index') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan User</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Informasi User</h5>
                    </div>
                    <div class="card-body">

                        <!-- Nama -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <!-- Role -->
                                         <div class="mb-3">
    <label for="role" class="form-label">Peran (Role)</label>
    <select class="form-select" id="role" name="role" required>
        <option value="">Pilih Role</option>
        <option value="administrator">Administrator</option>
        <option value="admin">Admin</option>
        <option value="marketing">Marketing</option>
        <option value="customer_service">Customer Service</option>
        <option value="team">Team</option>
        <option value="karyawan">Karyawan</option>
    </select>
</div>
                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
