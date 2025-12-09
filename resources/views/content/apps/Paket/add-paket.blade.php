@extends('layouts/layoutMaster')

@section('title', 'Tambah Paket Internet')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/quill/typography.scss',
    'resources/assets/vendor/libs/quill/katex.scss',
    'resources/assets/vendor/libs/quill/editor.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/dropzone/dropzone.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/tagify/tagify.scss',
    'resources/assets/vendor/libs/highlight/highlight.scss'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/quill/katex.js',
    'resources/assets/vendor/libs/quill/quill.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/dropzone/dropzone.js',
    'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/tagify/tagify.js',
    'resources/assets/vendor/libs/highlight/highlight.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/forms-editors.js'])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hargaInput = document.getElementById('harga');

    hargaInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if(value) {
            this.value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        } else {
            this.value = '';
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        const rawValue = hargaInput.value.replace(/\D/g, '');
        hargaInput.value = rawValue;
    });
});
</script>
@endsection

@section('content')
<div class="app-ecommerce">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
        <div class="d-flex flex-column justify-content-center">
            <h4 class="mb-1 fw-bold text-primary">Tambah Paket Internet</h4>
            <p class="text-muted mb-0">Lengkapi detail berikut untuk membuat paket baru.</p>
        </div>
    </div>

    <form action="{{ route('paket.store') }}" method="POST">
        @csrf
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Paket</h5>
            </div>

            <div class="card-body p-4">

                <div class="mb-4">
                    <label class="form-label fw-medium" for="nama_paket">Nama Paket</label>
                    <input type="text" class="form-control" id="nama_paket" name="nama_paket"
                        placeholder="Contoh: Paket Hemat 1 Bulan" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium" for="harga">Harga (Rp)</label>
                        <input type="text" class="form-control" id="harga" name="harga" placeholder="50000" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium" for="masa_pembayaran">Masa Aktif (hari)</label>
                        <input type="number" class="form-control" id="masa_pembayaran" name="masa_pembayaran"
                            placeholder="30" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium" for="kecepatan">Kecepatan Internet (Mbps)</label>
                    <input type="number" class="form-control" id="kecepatan" name="kecepatan"
                        placeholder="Contoh: 20" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium" for="cycle">Cycle</label>
                    <select class="form-select" id="cycle" name="cycle" required>
                        <option value="">-- Pilih Cycle --</option>
                        <option value="daily" {{ old('cycle', $paket->cycle ?? '') === 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="weekly" {{ old('cycle', $paket->cycle ?? '') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ old('cycle', $paket->cycle ?? '') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="yearly" {{ old('cycle', $paket->cycle ?? '') === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i> Simpan Paket
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
