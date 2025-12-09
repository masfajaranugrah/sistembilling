@extends('layouts/layoutMaster')

@section('title', 'Pengeluaran')

{{-- VENDOR STYLE --}}
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

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {

  // --- Inisialisasi DataTables ---
  const dtExpenseTable = $('.datatables-expense').DataTable({
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
          { orderable: false, targets: [7] } // kolom Actions
      ],
      language: {
          paginate: {
              previous: '<i class="ri-arrow-left-s-line"></i>',
              next: '<i class="ri-arrow-right-s-line"></i>'
          }
      }
  });

  // --- Event tombol detail ---
  $(document).on('click', '.btn-detail, td.control', function(e) {
      e.stopPropagation();
      const tr = $(this).closest('tr');
      const rowData = dtExpenseTable.row(tr).data();
      if (!rowData) return;

      const html = `
          <p><strong>Kode:</strong> ${rowData[1]}</p>
          <p><strong>Kategori:</strong> ${rowData[2]}</p>
          <p><strong>Jumlah:</strong> Rp ${parseFloat(rowData[3]).toLocaleString()}</p>
          <p><strong>Keterangan:</strong> ${rowData[4] || '-'}</p>
          <p><strong>Tanggal Keluar:</strong> ${rowData[5]}</p>
          <p><strong>Jam Keluar:</strong> ${rowData[6]}</p>
      `;
      $('#detailModal .modal-body').html(html);
      $('#detailModal').modal('show');
  });

  // --- Event tombol Delete pakai SweetAlert2 ---
  $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      e.stopPropagation();
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
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Data Pengeluaran</h5>
      <a href="{{ route('keluar.create') }}" class="btn btn-primary">
        <i class="ri-add-line me-1"></i> Tambah Pengeluaran
      </a>
    </div>
    <div class="card-datatable table-responsive">
      <table class="datatables-expense table">
        <thead>
          <tr>
            <th>#</th>
            <th>Kode</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
            <th>Tanggal Keluar</th>
            <th>Jam Keluar</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($expenses as $e)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $e->kode }}</td>
            <td>{{ $e->kategori }}</td>
            <td>Rp {{ number_format($e->jumlah, 0, ',', '.') }}</td>
            <td>{{ $e->keterangan ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($e->tanggal_keluar)->format('d-m-Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($e->tanggal_keluar)->format('H:i') }}</td>


            <td class="text-center">
              <div class="d-flex justify-content-start align-items-center gap-2">
                <a href="{{ route('keluar.edit', $e->id) }}" class="btn btn-warning btn-sm" title="Edit">
                  <i class="ri-pencil-line"></i>
                </a>
                <form action="{{ route('keluar.destroy', $e->id) }}" method="POST" class="m-0 p-0">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Hapus">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Pengeluaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection
