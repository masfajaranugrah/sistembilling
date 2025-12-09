@extends('layouts/layoutMaster')

@section('title', 'Laba Keluar - Pages')

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

@section('content')

<div class="row mb-4">
    <!-- Form Filter Tanggal -->
    <div class="col-md-4">
        <form action="" method="GET" class="d-flex gap-2">
            <input type="text" id="tanggal" name="tanggal" class="form-control rounded" placeholder="Pilih tanggal" value="{{ request('tanggal', date('Y-m-d')) }}">
            <button class="btn btn-primary rounded">Filter</button>
        </form>
    </div>
</div>

<div class="row mb-4">
    <!-- Ringkasan Laba Keluar -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Total Laba Keluar</h6>
                <h3 class="fw-bold">Rp {{ number_format($totalKeluar,0,',','.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Laba Keluar -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-bold">Data Laba Keluar ({{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }})</h6>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Tanggal Keluar</th>
                    <th>Jam Keluar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $i)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $i->kode }}</td>
                    <td>{{ $i->kategori }}</td>
                    <td>Rp {{ number_format($i->jumlah,0,',','.') }}</td>
                    <td>{{ $i->keterangan ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($i->tanggal_keluar)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($i->tanggal_keluar)->format('H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Tidak ada data untuk tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th>Rp {{ number_format($totalKeluar,0,',','.') }}</th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-sm">
      <div class="modal-header border-bottom">
        <h5 class="modal-title" id="detailModalLabel">Detail Laba Keluar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            defaultDate: "{{ request('tanggal', date('Y-m-d')) }}",
            allowInput: true
        });
    });
</script>
@endsection
