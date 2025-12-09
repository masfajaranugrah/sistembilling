@extends('layouts/layoutMaster')

@section('title', 'Buku Besar Laba Masuk')

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
            <input type="text" name="tanggal" id="tanggal" class="form-control rounded" placeholder="Pilih tanggal" value="{{ request('tanggal', date('Y-m-d')) }}">
            <button class="btn btn-primary rounded" type="submit">Filter</button>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total Laba Masuk</h6>
                <h3 class="card-title fw-bold">Rp {{ number_format($totalMasuk,0,',','.') }}</h3>
                <p class="text-muted mb-0">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-bold">Data Laba Masuk ({{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }})</h6>
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
                    <th>Jam Masuk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $i)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $i->kode }}</td>
                    <td>{{ $i->kategori }}</td>
                    <td>Rp {{ number_format($i->jumlah,0,',','.') }}</td>
                    <td>{{ $i->keterangan ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($i->tanggal_masuk)->format('H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Tidak ada data untuk tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th>Rp {{ number_format($totalMasuk,0,',','.') }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection

@section('page-script')
<script>
    // Pastikan DOM sudah siap
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            defaultDate: "{{ request('tanggal', date('Y-m-d')) }}",
            allowInput: true
        });
    });
</script>
@endsection
