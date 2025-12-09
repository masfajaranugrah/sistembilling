@extends('layouts/layoutMaster')

@section('title', 'Buku Besar Laba')

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
    // Inisialisasi Flatpickr untuk input tanggal custom
    flatpickr("input[name='tanggal_awal']", {
        dateFormat: "Y-m-d",
        defaultDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1) // awal bulan
    });
    flatpickr("input[name='tanggal_akhir']", {
        dateFormat: "Y-m-d",
        defaultDate: new Date(new Date().getFullYear(), new Date().getMonth()+1, 0) // akhir bulan
    });
});
</script>
@endsection

@section('content')
<div class="row mb-4">
    <!-- Filter Periode -->
    <div class="col-md-12 d-flex gap-2 flex-wrap mb-3">
        <a href="{{ request()->fullUrlWithQuery(['periode' => 'hari_ini']) }}"
           class="btn btn-outline-primary {{ request('periode','hari_ini') == 'hari_ini' ? 'active' : '' }}">Hari Ini</a>
        <a href="{{ request()->fullUrlWithQuery(['periode' => '7_hari']) }}"
           class="btn btn-outline-primary {{ request('periode') == '7_hari' ? 'active' : '' }}">7 Hari</a>
        <a href="{{ request()->fullUrlWithQuery(['periode' => 'bulan_ini']) }}"
           class="btn btn-outline-primary {{ request('periode') == 'bulan_ini' ? 'active' : '' }}">Bulan Ini</a>
        <a href="{{ request()->fullUrlWithQuery(['periode' => 'tahun_ini']) }}"
           class="btn btn-outline-primary {{ request('periode') == 'tahun_ini' ? 'active' : '' }}">Tahun Ini</a>

        <!-- Custom Range -->
        <form action="" method="GET" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="periode" value="custom">
            <input type="text" name="tanggal_awal" class="form-control rounded" placeholder="Tanggal Awal" value="{{ request('tanggal_awal', date('Y-m-01')) }}">
            <input type="text" name="tanggal_akhir" class="form-control rounded" placeholder="Tanggal Akhir" value="{{ request('tanggal_akhir', date('Y-m-t')) }}">
            <button class="btn btn-primary rounded" type="submit">Filter</button>
        </form>
    </div>
</div>

<div class="row mb-4">
    <!-- Ringkasan Laba -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total Laba Masuk</h6>
                <h3 class="card-title fw-bold">Rp {{ number_format($totalMasuk,0,',','.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total Laba Keluar</h6>
                <h3 class="card-title fw-bold">Rp {{ number_format($totalKeluar,0,',','.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Saldo Bersih</h6>
                <h3 class="card-title fw-bold">Rp {{ number_format($saldoBersih,0,',','.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Gabungan Laba Masuk & Keluar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-bold">Transaksi Laba</h6>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover table-striped align-middle">
            <!-- Tambahkan kolom Tanggal di tabel -->
<thead class="table-light">
    <tr>
        <th>#</th>
        <th>Kode</th>
        <th>Kategori</th>
        <th>Masuk</th>
        <th>Keluar</th>
        <th>Keterangan</th>
        <th>Tanggal</th>
        <th>Jam</th>
    </tr>
</thead>
<tbody>
    @php
        $allTransactions = collect();
        foreach($incomes as $i) {
            $allTransactions->push([
                'kode' => $i->kode,
                'kategori' => $i->kategori,
                'masuk' => $i->jumlah,
                'keluar' => 0,
                'keterangan' => $i->keterangan,
                'tanggal' => \Carbon\Carbon::parse($i->tanggal_masuk)->format('d-m-Y'),
                'jam' => \Carbon\Carbon::parse($i->tanggal_masuk)->format('H:i')
            ]);
        }
        foreach($expenses as $e) {
            $allTransactions->push([
                'kode' => $e->kode,
                'kategori' => $e->kategori,
                'masuk' => 0,
                'keluar' => $e->jumlah,
                'keterangan' => $e->keterangan,
                'tanggal' => \Carbon\Carbon::parse($e->tanggal_keluar)->format('d-m-Y'),
                'jam' => \Carbon\Carbon::parse($e->tanggal_keluar)->format('H:i')
            ]);
        }
        // Urut berdasarkan tanggal & jam
        $allTransactions = $allTransactions->sortBy(function($t){
            return \Carbon\Carbon::createFromFormat('d-m-Y H:i', $t['tanggal'].' '.$t['jam']);
        })->values();
    @endphp

    @forelse($allTransactions as $t)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $t['kode'] }}</td>
        <td>{{ $t['kategori'] }}</td>
        <td>{{ $t['masuk'] ? 'Rp '.number_format($t['masuk'],0,',','.') : '-' }}</td>
        <td>{{ $t['keluar'] ? 'Rp '.number_format($t['keluar'],0,',','.') : '-' }}</td>
        <td>{{ $t['keterangan'] ?? '-' }}</td>
        <td>{{ $t['tanggal'] }}</td>
        <td>{{ $t['jam'] }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-center text-muted">Tidak ada transaksi untuk periode ini.</td>
    </tr>
    @endforelse
</tbody>

            <tfoot class="table-light">
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th>Rp {{ number_format($totalMasuk,0,',','.') }}</th>
                    <th>Rp {{ number_format($totalKeluar,0,',','.') }}</th>
                    <th colspan="3"></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-end">Saldo Bersih</th>
                    <th colspan="5">Rp {{ number_format($saldoBersih,0,',','.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
