@extends('layouts/layoutMaster')

@section('title', 'Data Barang - Inventory')

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

  const dtBarang = $('.datatables-barang').DataTable({
      paging: true,
      pageLength: 10,
      searching: true,
      ordering: true,
      responsive: true
  });

  // --- Event Detail Barang ---
  $(document).on('click', '.btn-detail', function() {

      const row = $(this).closest('tr');

      const nama  = row.find('td:eq(1)').text();
      const stok  = row.find('td:eq(2)').text();
      const status = row.find('td:eq(3)').text();

      const html = `
          <p><strong>Nama Barang:</strong> ${nama}</p>
          <p><strong>Stok:</strong> ${stok}</p>
          <p><strong>Status:</strong> ${status}</p>
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
          text: "Data barang yang dihapus tidak dapat dikembalikan!",
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

{{-- CONTENT --}}
@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1 class="dashboard-title">Data Barang</h1>
  <div class="dashboard-subtitle">
    <i class="ri-box-3-line"></i>
    <span>Manage inventory items and stock levels</span>
  </div>
</div>

<!-- Activity Card -->
<div class="activity-card">
  <div class="activity-header">
    <div class="activity-title">
      <i class="ri-archive-line"></i>
      Daftar Barang
    </div>
    <div class="activity-filters">
      <a href="{{ route('add-barang') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Barang
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="datatables-barang table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Stok</th>
          <th>Status</th>
          <th>Keterangan</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @foreach($barangs as $barang)
        <tr>
          <td>{{ $loop->iteration }}</td>

          <td>{{ $barang->nama_barang }}</td>

          <td>{{ $barang->stok }}</td>

          <td>
            @if($barang->stok == 0)
              <span class="badge bg-danger">Habis</span>
            @else
              <span class="badge bg-success">Masih</span>
            @endif
          </td>
          <td>{{ $barang->keterangan }}</td>

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
                  <a class="dropdown-item" href="{{ route('get-barang', $barang->id) }}">
                    <i class="ri-edit-2-line me-1"></i> Edit
                  </a>
                </li>

                <li>
                  <form action="{{ route('delete-barang', $barang->id) }}" method="POST">
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

<!-- Modal Detail Barang -->
<div class="modal fade" id="detailModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detail Barang</h5>
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
