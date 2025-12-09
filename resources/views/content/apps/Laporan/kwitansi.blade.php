@extends('layouts/layoutMaster')

@section('title', 'Laporan Kwitansi')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    var table = $('#tabelTagihan').DataTable({
      responsive: true,
      pageLength: 10
    });

    $('#statusPembayaranFilter').on('change', function() {
      var selected = $(this).val().toLowerCase();
      table.column(6).search(selected).draw(); // filter kolom Status Pembayaran
    });

    $('#kabupatenFilter').on('change', function() {
      var selected = $(this).val().toLowerCase();
      table.column(7).search(selected).draw(); // filter kolom Kabupaten
    });

    $('#kecamatanFilter').on('change', function() {
      var selected = $(this).val().toLowerCase();
      table.column(8).search(selected).draw(); // filter kolom Kecamatan
    });
  });
</script>
@endsection

@section('content')

<div class="card mt-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5>Daftar Kwitansi</h5>
    <a href="{{ route('laporan.kwitansi.export') }}" class="btn btn-success">
      <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>
  </div>

  <div class="card-datatable table-responsive p-3">
    <table id="tabelTagihan" class="datatables-products table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Lengkap</th>
          <th>Alamat</th>
          <th>Nama Paket</th>
          <th>Harga Paket</th>
          <th>Kecepatan</th>
          <th>Status Pembayaran</th>
          <th>Kabupaten</th>
          <th>Kecamatan</th>
          <th>Kwitansi</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tagihans as $tagihan)
        <tr>
          <td>{{ $tagihan->id }}</td>
          <td>{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}</td>
          <td>{{ $tagihan->pelanggan->alamat_jalan ?? '-' }}</td>
          <td>{{ $tagihan->paket->nama_paket ?? '-' }}</td>
          <td>{{ number_format($tagihan->harga, 0, ',', '.') }}</td>
          <td>{{ $tagihan->paket->kecepatan ?? '-' }}</td>
          <td>{{ $tagihan->status_pembayaran }}</td>
          <td>{{ $tagihan->pelanggan->kabupaten ?? '-' }}</td>
          <td>{{ $tagihan->pelanggan->kecamatan ?? '-' }}</td>
           <td>
  @if($tagihan->kwitansi_url)
    <a href="{{ $tagihan->kwitansi_url }}" target="_blank">
      <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Lihat
    </a>
  @else
    -
  @endif
</td>

          <td>{{ $tagihan->catatan ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
