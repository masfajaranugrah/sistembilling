@extends('layouts/layoutMaster')

@section('title', 'Karyawan')

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
  const dtUserTable = $('.datatables-users').DataTable({
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
                render: function () {
                    return '<button class="btn btn-icon btn-sm btn-detail"><i class="ri-add-line"></i></button>';
                }
            },
            { orderable: false, targets: [11] }
        ],
      language: {
          paginate: {
              previous: '<i class="ri-arrow-left-s-line"></i>',
              next: '<i class="ri-arrow-right-s-line"></i>'
          }
      }
  });

  // --- Event tombol detail ---
// --- Event tombol detail ---
$(document).on('click', '.btn-detail, td.control', function() {
    const tr = $(this).closest('tr');
    const rowData = dtUserTable.row(tr).data();
    if (!rowData) return;

    // Ambil data dari rowData (sesuai index kolom di table)
    const html = `
        <p><strong>NIK:</strong> ${rowData[2]}</p>
        <p><strong>Nama Lengkap:</strong> ${rowData[3]}</p>
        <p><strong>Alamat:</strong> ${rowData[4]}</p>
        <p><strong>Tempat Lahir:</strong> ${rowData[5].split(',')[0]}</p>
        <p><strong>Tanggal Lahir:</strong> ${rowData[5].split(',')[1]}</p>
        <p><strong>No. HP:</strong> ${rowData[6]}</p>
        <p><strong>Tanggal Masuk:</strong> ${rowData[7]}</p>
        <p><strong>Jabatan:</strong> ${rowData[8]}</p>
        <p><strong>Bank:</strong> ${rowData[9].split('-')[0]}</p>
        <p><strong>No. Rekening:</strong> ${rowData[10]}</p>
        <p><strong>Atas Nama:</strong> ${rowData[11]}</p>
        <p><strong>Actions:</strong> ${rowData[12]}</p>
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
</script>
@endsection

{{-- CONTENT --}}
@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1 class="dashboard-title">Data Karyawan</h1>
  <div class="dashboard-subtitle">
    <i class="ri-user-settings-line"></i>
    <span>Manage employee information and records</span>
  </div>
</div>

<!-- Activity Card -->
<div class="activity-card">
  <div class="activity-header">
    <div class="activity-title">
      <i class="ri-team-line"></i>
      Daftar Karyawan
    </div>
    <div class="activity-filters">
      <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
        <i class="ri-user-add-line"></i> Tambah Karyawan
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="datatables-users table">
      <thead>
        <tr>
          <th>#</th>
          <th>No</th>
          <th>NIK</th>
          <th>Nama Lengkap</th>
          <th>Alamat</th>
          <th>Tempat & Tanggal Lahir</th>
          <th>No. HP</th>
          <th>Tanggal Masuk</th>
          <th>Jabatan</th>
          <th>Bank & Rekening</th>
          <th>No Rekening</th>
          <th>Atas Nama</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($employees as $employee)
        <tr>
          <td></td>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $employee->nik }}</td>
          <td>{{ $employee->full_name }}</td>
          <td>{{ $employee->full_address }}</td>
          <td>{{ $employee->place_of_birth }}, {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('d M Y') }}</td>
          <td>{{ $employee->no_hp }}</td>
          <td>{{ \Carbon\Carbon::parse($employee->tanggal_masuk)->format('d M Y') }}</td>
          <td>{{ $employee->jabatan }}</td>
          <td>{{ $employee->bank }}</td>
          <td>{{ $employee->no_rekening}} </td>
          <td>{{ $employee->atas_nama }}</td>

        <td class="text-center">
  <div class="dropdown">
    <button class="btn btn-sm btn-icon btn-outline-secondary" type="button" id="dropdownMenuButton{{ $employee->id }}" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="ri-more-2-fill"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $employee->id }}">
      <li>
        <a class="dropdown-item" href="{{ route('employees.edit', $employee->id) }}">
          <i class="ri-edit-2-line me-1"></i> Edit
        </a>
      </li>
      <li>
        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="delete-form d-inline">
          @csrf
          @method('DELETE')
          <button type="button" class="dropdown-item btn-delete">
            <i class="ri-delete-bin-line me-1"></i> Delete
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

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Karyawan</h5>
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
@endsection
