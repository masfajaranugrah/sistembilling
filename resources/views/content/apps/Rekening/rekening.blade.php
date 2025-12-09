@extends('layouts/layoutMaster')

@section('title', 'Rekening List - Pages')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

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

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inisialisasi DataTable
    const dtRekeningTable = $('.datatables-rekenings').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [-1] } // Kolom actions
        ],
        language: {
            paginate: {
                previous: '<i class="ri-arrow-left-s-line"></i>',
                next: '<i class="ri-arrow-right-s-line"></i>'
            }
        }
    });

    // Konfirmasi delete dengan SweetAlert2
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data rekening yang dihapus tidak dapat dikembalikan!",
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
  <h1 class="dashboard-title">Data Rekening</h1>
  <div class="dashboard-subtitle">
    <i class="ri-bank-card-line"></i>
    <span>Manage bank accounts for payment processing</span>
  </div>
</div>

<!-- Activity Card -->
<div class="activity-card">
  <div class="activity-header">
    <div class="activity-title">
      <i class="ri-bank-line"></i>
      Daftar Rekening
    </div>
    <div class="activity-filters">
      <a href="{{ route('rekenings.add') }}" class="btn btn-primary">
        <i class="ri-add-line"></i>
        Tambah Rekening
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="datatables-rekenings table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Bank</th>
          <th>Nomor Rekening</th>
          <th>Nama Pemilik Rekening</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rekenings as $rekening)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $rekening->nama_bank }}</td>
          <td>{{ $rekening->nomor_rekening }}</td>
          <td>{{ $rekening->nama_pemilik }}</td>
          <td>
            <a href="{{ route('rekenings.edit', $rekening->id) }}" class="btn btn-sm btn-primary">Edit</a>
            <form action="{{ route('rekenings.destroy', $rekening->id) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger btn-delete">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<!-- End Activity Card -->

@endsection
