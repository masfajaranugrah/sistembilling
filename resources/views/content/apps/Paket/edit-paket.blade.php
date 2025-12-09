@extends('layouts/layoutMaster')

@section('title', 'Edit Paket Internet')

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
  // Format input harga Rupiah realtime
  const hargaInput = document.getElementById('harga');

  function formatRupiah(value) {
      if(!value) return '';
      // hapus semua selain angka
      let number = value.replace(/\D/g, '');
      return 'Rp. ' + new Intl.NumberFormat('id-ID').format(number);
  }

  hargaInput.addEventListener('input', function(e) {
      e.target.value = formatRupiah(e.target.value);
  });

  // Strip Rp dan titik sebelum submit
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
      hargaInput.value = hargaInput.value.replace(/[Rp.\s]/g, '');
  });
</script>
@endsection

@section('content')
<div class="app-ecommerce">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">Edit Paket: <span class="text-primary">{{ $paket->nama_paket }}</span></h4>
      <p class="text-muted mb-0">Perbarui informasi paket internet yang tersedia.</p>
    </div>
  </div>

  <form action="{{ route('paket.update', $paket->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-6">
      <div class="card-header bg-light">
        <h5 class="card-title mb-0 fw-semibold text-dark">Informasi Paket</h5>
      </div>
      <div class="card-body">

        {{-- Nama Paket --}}
        <div class="mb-4">
          <label class="form-label" for="namaTitle">Nama Paket</label>
          <input
            type="text"
            class="form-control"
            id="namaTitle"
            name="namaTitle"
            value="{{ old('namaTitle', $paket->nama_paket) }}"
            placeholder="Contoh: Paket Hemat 1 Bulan"
            required
          >
        </div>

        {{-- Harga dan Masa Pembayaran --}}
        <div class="row mb-4">
          <div class="col-md-6">
            <label class="form-label" for="harga">Harga</label>
            <input
              type="text"
              class="form-control"
              id="harga"
              name="harga"
              value="{{ old('harga', 'Rp. '.number_format($paket->harga,0,',','.')) }}"
              placeholder="Rp. 50.000"
              required
            >
          </div>

          <div class="col-md-6">
            <label class="form-label" for="masaPembayaran">Masa Aktif (hari)</label>
            <input
              type="number"
              class="form-control"
              id="masaPembayaran"
              name="masaPembayaran"
              value="{{ old('masaPembayaran', $paket->masa_pembayaran) }}"
              placeholder="30"
              required
            >
          </div>
        </div>

        {{-- Kecepatan Internet --}}
        <div class="mb-4">
          <label class="form-label fw-medium" for="kecepatan">Kecepatan Internet (Mbps)</label>
          <input
            type="number"
            class="form-control"
            id="kecepatan"
            name="kecepatan"
            value="{{ old('kecepatan', $paket->kecepatan) }}"
            placeholder="Contoh: 20"
            required
          >
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



        {{-- Tombol Aksi --}}
        <div class="d-flex justify-content-between mt-5">
          <a href="{{ route('paket.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
          </a>
          <button type="submit" class="btn btn-success">
            <i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i> Simpan Perubahan
          </button>
        </div>

      </div>
    </div>
  </form>
</div>
@endsection
