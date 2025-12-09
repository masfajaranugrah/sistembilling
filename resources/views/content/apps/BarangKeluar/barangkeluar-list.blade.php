@extends('layouts/layoutMaster')

@section('title', 'Data Barang Keluar - Inventory')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {

  // Initialize DataTable
  const dtBarangKeluar = $('.datatables-barang').DataTable({
      paging: true,
      pageLength: 10,
      searching: true,
      ordering: true,
      responsive: true,
      columnDefs: [
        { orderable: false, targets: -1 } // Non-orderable last column (Aksi)
      ]
  });

  // --- Event Detail Barang Keluar ---
  $(document).on('click', '.btn-detail', function() {
      const row = $(this).closest('tr');
      const nama  = row.find('td:eq(1)').text();
      const jumlah = row.find('td:eq(2)').text();
      const diambil = row.find('td:eq(3)').text();
      const keterangan = row.find('td:eq(4)').text();
      const tanggal = row.find('td:eq(5)').text();

      const html = `
          <p><strong>Nama Barang:</strong> ${nama}</p>
          <p><strong>Jumlah:</strong> ${jumlah}</p>
          <p><strong>Diambil Oleh:</strong> ${diambil}</p>
          <p><strong>Keterangan:</strong> ${keterangan}</p>
          <p><strong>Tanggal:</strong> ${tanggal}</p>
      `;

      $('#detailModal .modal-body').html(html);
      $('#detailModal').modal('show');
  });

  // --- Event DELETE ---
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
</script>
@endsection

@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1 class="dashboard-title">Data Barang Keluar</h1>
  <div class="dashboard-subtitle">
    <i class="ri-logout-box-line"></i>
    <span>Track outgoing inventory and stock deductions</span>
  </div>
</div>

<!-- Activity Card -->
<div class="activity-card">
  <div class="activity-header">
    <div class="activity-title">
      <i class="ri-upload-line"></i>
      Daftar Barang Keluar
    </div>
    <div class="activity-filters">
      <a href="{{ route('add.barangkeluar') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Barang Keluar
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="datatables-barang table table-bordered">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Jumlah</th>
          <th>Diambil Oleh</th>
          <th>Keterangan</th>
          <th>Tanggal</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @foreach($barangKeluars as $barang)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $barang->barang->nama_barang }}</td>
          <td>{{ $barang->jumlah }}</td>
          <td>{{ $barang->diambil_oleh }}</td>
          <td>{{ $barang->keterangan }}</td>
          <td class="text-center">
            {{ \Carbon\Carbon::parse($barang->tanggal)->format('d M Y') }}
          </td>
          <td class="text-center">
            <div class="dropdown">
              <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="dropdown">
                <i class="ri-more-2-fill"></i>
              </button>
              <ul class="dropdown-menu">
                <li>
                  <button class="dropdown-item btn-detail">
                    <i class="ri-eye-line me-1"></i> Detail
                  </button>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('edit.barangkeluar', $barang->id) }}">
                    <i class="ri-edit-2-line me-1"></i> Edit
                  </a>
                </li>
                <li>
                  <form action="{{ route('delete.barangkeluar', $barang->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="dropdown-item btn-delete">
                      <i class="ri-delete-bin-line me-1"></i> Hapus
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<!-- End Activity Card -->

<!-- Modal Detail Barang Keluar -->
<div class="modal fade" id="detailModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Barang Keluar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection
