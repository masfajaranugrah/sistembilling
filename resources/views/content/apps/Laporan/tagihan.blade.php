@extends('layouts/layoutMaster')

@section('title', 'Laporan Tagihan')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/cleavejs/cleave.js',
  'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection


{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {

  // --- Inisialisasi DataTables ---
  const dtTagihan = $('.datatables-tagihan').DataTable({
      paging: true,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      searching: true,
      ordering: true,
      responsive: {
          details: {
              type: 'column',
              target: 0,
              display: $.noop
          }
      },
      columnDefs: [
          {
              className: 'control text-center',
              orderable: false,
              searchable: false,
              targets: 0,
              render: function() {
                  return '<button class="btn btn-icon btn-sm btn-detail"><i class="ri-add-line"></i></button>';
              }
          },
          { orderable: false, targets: [13] } // kolom actions / bukti pembayaran
      ],
      language: {
          search: "Cari:",
          lengthMenu: "Tampilkan _MENU_ data",
          info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          paginate: {
              previous: '<i class="ri-arrow-left-s-line"></i>',
              next: '<i class="ri-arrow-right-s-line"></i>'
          },
          zeroRecords: "Tidak ada data yang ditemukan"
      }
  });

  // --- Filter Multi Kolom (Kecamatan, Kabupaten, Status Pembayaran) ---
  function filterTable() {
      const kecamatan = $('#filterKecamatan').val();
      const kabupaten = $('#filterKabupaten').val();
      const status = $('#filterStatus').val();

      dtTagihan
        .columns(9).search(kecamatan)   // Kecamatan
        .columns(8).search(kabupaten)   // Kabupaten
        .columns(7).search(status)      // Status Pembayaran
        .draw();
  }

  $('#filterKecamatan, #filterKabupaten, #filterStatus').on('change', filterTable);

  // --- Event tombol detail ---
  $(document).on('click', '.btn-detail, td.control', function() {
      const tr = $(this).closest('tr');
      const rowData = dtTagihan.row(tr).data();
      if (!rowData) return;

      const html = `
          <p><strong>Nama Lengkap:</strong> ${rowData[2]}</p>
          <p><strong>Alamat:</strong> ${rowData[3]}</p>
          <p><strong>Nama Paket:</strong> ${rowData[4]}</p>
          <p><strong>Harga Paket:</strong> ${rowData[5]}</p>
          <p><strong>Kecepatan:</strong> ${rowData[6]}</p>
          <p><strong>Status Pembayaran:</strong> ${rowData[7]}</p>
          <p><strong>Kabupaten:</strong> ${rowData[8]}</p>
          <p><strong>Kecamatan:</strong> ${rowData[9]}</p>
          <p><strong>Tanggal Mulai:</strong> ${rowData[10]}</p>
          <p><strong>Tanggal Berakhir:</strong> ${rowData[11]}</p>
          <p><strong>Catatan:</strong> ${rowData[12]}</p>
      `;
      $('#detailModal .modal-body').html(html);
      $('#detailModal').modal('show');
  });

  // --- Event tombol Delete pakai SweetAlert2 ---
  $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      const form = $(this).closest('form');

      Swal.fire({
          title: 'Yakin ingin menghapus?',
          text: "Data yang dihapus tidak dapat dikembalikan!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal',
          customClass: {
              confirmButton: 'btn btn-danger me-2',
              cancelButton: 'btn btn-secondary'
          },
          buttonsStyling: false
      }).then((result) => {
          if (result.isConfirmed) {
              form.submit();
          }
      });
  });

});

// --- Export Excel sesuai filter ---
$('#btnExportExcel').on('click', function(e) {
    e.preventDefault();

    const kecamatan = $('#filterKecamatan').val();
    const kabupaten = $('#filterKabupaten').val();
    const status = $('#filterStatus').val();

    // Buat URL dengan query params
    let url = "{{ route('laporan.tagihan.export') }}";
    const params = new URLSearchParams();
    if (kecamatan) params.append('kecamatan', kecamatan);
    if (kabupaten) params.append('kabupaten', kabupaten);
    if (status) params.append('status', status);

    if (params.toString()) url += '?' + params.toString();

    // Redirect ke URL export
    window.location.href = url;
});

function updateExportInputs() {
    $('#exportStatus').val($('#filterStatus').val());
    $('#exportKabupaten').val($('#filterKabupaten').val());
    $('#exportKecamatan').val($('#filterKecamatan').val());
}

// Update hidden inputs saat filter berubah
$('#filterStatus, #filterKabupaten, #filterKecamatan').on('change', function() {
    updateExportInputs();
});

// Inisialisasi saat halaman load
updateExportInputs();

</script>
@endsection


{{-- CONTENT --}}
@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1 class="dashboard-title">Laporan Tagihan</h1>
  <div class="dashboard-subtitle">
    <i class="ri-file-list-3-line"></i>
    <span>View and export billing reports and analytics</span>
  </div>
</div>

<!-- Filters Section -->
<div class="filters-section">
  <div class="filters-header">
    <div class="filters-title">
      <i class="ri-filter-3-line"></i>
      Filter Laporan
    </div>
    <form id="formExport" action="{{ route('laporan.tagihan.export') }}" method="GET">
      <input type="hidden" name="status" id="exportStatus">
      <input type="hidden" name="kabupaten" id="exportKabupaten">
      <input type="hidden" name="kecamatan" id="exportKecamatan">
      <button type="submit" class="btn btn-success">
        <i class="ri-file-excel-line"></i> Export Excel
      </button>
    </form>
  </div>
  <div class="filters-grid mt-3">
    <div class="filter-group">
      <label for="filterKecamatan">Filter Kecamatan</label>
      <select id="filterKecamatan" class="form-select">
        <option value="">-- Semua Kecamatan --</option>
        @foreach($kecamatans as $kecamatan)
          <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
        @endforeach
      </select>
    </div>
    <div class="filter-group">
      <label for="filterKabupaten">Filter Kabupaten</label>
      <select id="filterKabupaten" class="form-select">
        <option value="">-- Semua Kabupaten --</option>
        @foreach($kabupatens as $kabupaten)
          <option value="{{ $kabupaten }}">{{ $kabupaten }}</option>
        @endforeach
      </select>
    </div>
    <div class="filter-group">
      <label for="filterStatus">Filter Status Pembayaran</label>
      <select id="filterStatus" class="form-select">
        <option value="">-- Semua Status --</option>
        <option value="lunas">Lunas</option>
        <option value="belum bayar">Belum Lunas</option>
      </select>
    </div>
  </div>
</div>

<!-- Activity Card -->
<div class="activity-card">
  <div class="activity-header">
    <div class="activity-title">
      <i class="ri-file-list-3-line"></i>
      Data Laporan Tagihan
    </div>
  </div>
  <div class="table-responsive">
    <table class="datatables-tagihan table">
      <thead>
        <tr>
          <th>#</th>
          <th>ID</th>
          <th>Nama Lengkap</th>
          <th>Alamat</th>
          <th>Nama Paket</th>
          <th>Harga Paket</th>
          <th>Kecepatan</th>
          <th>Status Pembayaran</th>
          <th>Kabupaten</th>
          <th>Kecamatan</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Berakhir</th>
          <th>Catatan</th>
          <th>Bukti Pembayaran</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tagihans as $tagihan)
        <tr>
          <td></td>
          <td>{{ $tagihan->id }}</td>
          <td>{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}</td>
          <td>{{ $tagihan->pelanggan->alamat_jalan ?? '-' }}</td>
          <td>{{ $tagihan->paket->nama_paket ?? '-' }}</td>
          <td>{{ number_format($tagihan->harga, 0, ',', '.') }}</td>
          <td>{{ $tagihan->paket->kecepatan ?? '-' }}</td>
          <td>{{ $tagihan->status_pembayaran }}</td>
          <td>{{ $tagihan->pelanggan->kabupaten ?? '-' }}</td>
          <td>{{ $tagihan->pelanggan->kecamatan ?? '-' }}</td>
          <td>{{ $tagihan->tanggal_mulai ?? '-' }}</td>
          <td>{{ $tagihan->tanggal_berakhir ?? '-' }}</td>
          <td>{{ $tagihan->catatan ?? '-' }}</td>
          <td>
            @if(!empty($tagihan->bukti_pembayaran))
              <a href="{{ asset('storage/' . $tagihan->bukti_pembayaran) }}" target="_blank">
                <img src="{{ asset('storage/' . $tagihan->bukti_pembayaran) }}"
                     alt="Bukti Pembayaran"
                     style="width:50px; height:auto; object-fit:cover; border-radius:4px;">
              </a>
            @else
              -
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<!-- End Activity Card -->

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Tagihan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Konten diisi JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
</div>
<!-- End Filters and Activity Card -->

<!-- Detail Modal -->