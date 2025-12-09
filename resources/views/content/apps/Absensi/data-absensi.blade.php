@extends('layouts.layoutMaster')

@section('title', 'Absensi')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js'
])
@endsection

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-3">📋 Absensi</h3>

    {{-- 🔍 FILTER TANGGAL --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="tanggal" class="form-label fw-semibold">Pilih Tanggal</label>
                    <input type="text" name="tanggal" id="tanggal" class="form-control"
                           placeholder="Pilih tanggal..."
                           value="{{ $tanggal ?? request('tanggal') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary" id="resetBtn">🔄 Reset</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 📅 DATA ABSENSI --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0">Data Absensi</h5>
        </div>
        <div class="card-body p-4" id="absensiTable">
            {{-- table akan di-load di sini --}}
            @include('content.apps.Absensi._table-absensi', ['absensi' => $absensi])
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tanggalInput = document.getElementById('tanggal');
    const tableContainer = document.getElementById('absensiTable');
    const resetBtn = document.getElementById('resetBtn');

    // Inisialisasi Flatpickr
    flatpickr(tanggalInput, {
        dateFormat: "Y-m-d",
        defaultDate: "{{ $tanggal ?? now()->format('Y-m-d') }}",
        locale: "id",
        onChange: function(selectedDates, dateStr) {
            loadAbsensi(dateStr);
        }
    });

    // Fungsi ambil data via AJAX
    function loadAbsensi(tanggal = null) {
        tableContainer.innerHTML = '<div class="text-center py-4">⏳ Memuat data...</div>';
        fetch("{{ route('absensi.indexAll') }}" + (tanggal ? '?tanggal=' + tanggal : ''), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            tableContainer.innerHTML = '<div class="text-danger text-center py-4">⚠️ Gagal memuat data</div>';
        });
    }

    // Tombol reset → kembali ke hari ini
    resetBtn.addEventListener('click', function () {
        tanggalInput._flatpickr.clear();
        loadAbsensi();
    });
});
</script>
@endsection
