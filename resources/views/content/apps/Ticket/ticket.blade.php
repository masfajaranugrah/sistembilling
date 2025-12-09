@extends('layouts/layoutMaster')

@section('title', 'Daftar Ticket')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
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
    const dtTicketTable = $('.datatables-tickets').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
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

    // Event klik detail
    $(document).on('click', '.btn-detail, td.control', function() {
        const tr = $(this).closest('tr');
        const rowData = dtTicketTable.row(tr).data();
        if (!rowData) return;

        // Ambil ID ticket dari kolom kedua (sesuaikan dengan index kolom kamu)
        const ticketId = $(tr).find('form').attr('action')?.split('/').pop() || null;

        const html = `
            <p><strong>Nama Pelanggan:</strong> ${rowData[2]}</p>
            <p><strong>Problem:</strong> ${rowData[4]}</p>
            <p><strong>Prioritas:</strong> ${rowData[8]}</p>
            <p><strong>Status:</strong> ${rowData[9]}</p>
            <p><strong>Teknisi:</strong> ${rowData[10]}</p>
            <p><strong>Tanggal Dibuat:</strong> ${rowData[12]}</p>
            <p><strong>Action:</strong> ${rowData[15]}</p>

        `;
        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // SweetAlert2 konfirmasi delete di dalam modal
    $(document).on('submit', '.form-delete-modal', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Ticket ini akan dihapus permanen.",
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
            if (result.isConfirmed) form.submit();
        });
    });

    // SweetAlert2 delete dari tabel utama
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data ticket akan dihapus permanen.",
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
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection


@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1 class="dashboard-title">Ticket Support</h1>
  <div class="dashboard-subtitle">
    <i class="ri-customer-service-2-line"></i>
    <span>Manage customer support tickets and technical issues</span>
  </div>
</div>

<!-- Activity Card -->
<div class="activity-card">
  <div class="activity-header">
    <div class="activity-title">
      <i class="ri-ticket-line"></i>
      Daftar Ticket
    </div>
    <div class="activity-filters">
      <a href="{{ route('tickets.creates') }}" class="btn btn-primary">
        <i class="ri-add-line"></i>
        Tambah Ticket
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="datatables-tickets table">
      <thead>
        <tr>
          <th></th>
          <th>No</th>
          <th>Nama Pelanggan</th>
          <th>Alamat</th>
          <th>Problem</th>
          <th>Catatan Tambahan</th>
          <th>No Telp / WA</th>
          <th>Kategori</th>
          <th>Prioritas</th>
          <th>Status</th>
          <th>Teknisi</th>
          <th>Foto</th>
          <th>Tanggal</th>
          <th>Dibuat Oleh</th>
          <th>Catatan Teknisi</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tickets as $index => $ticket)
        <tr>
          <td></td>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $ticket->pelanggan->nama_lengkap ?? '-' }}</td>
          <td class="text-center">
            @if($ticket->location_link)
              <a href="{{ $ticket->location_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="ri-map-pin-line"></i>
              </a>
            @else
              <button class="btn btn-sm btn-outline-secondary" disabled><i class="ri-map-pin-off-line"></i></button>
            @endif
          </td>
          <td>{{ $ticket->issue_description }}</td>
          <td>{{ $ticket->additional_note }}</td>
          <td class="text-center">
            @if($ticket->phone)
              <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->phone) }}" target="_blank" class="text-success fs-5">
                <i class="ri-whatsapp-line"></i>
              </a>
            @endif
          </td>
          <td>{{ $ticket->category }}</td>
          <td>{{ ucfirst($ticket->priority) }}</td>
          <td>
            <span class="badge
              @if($ticket->status == 'pending') bg-warning text-dark
              @elseif($ticket->status == 'assigned') bg-info text-dark
              @elseif($ticket->status == 'progress') bg-primary
              @elseif(in_array($ticket->status, ['finished', 'approved'])) bg-success
              @elseif($ticket->status == 'rejected') bg-danger
              @else bg-secondary
              @endif">
              {{ ucfirst($ticket->status) }}
            </span>
          </td>
          <td>{{ $ticket->user->name ?? '-' }}</td>
          <td class="text-center">
            @if($ticket->technician_attachment)
              <a href="{{ asset('storage/' . $ticket->technician_attachment) }}" target="_blank" class="btn btn-sm btn-info">
                <i class="ri-image-line"></i>
              </a>
            @endif
          </td>
          <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
          <td>{{ $ticket->creator->name ?? '-' }}</td>
          <td>{{ $ticket->technician_note ?? '-' }}</td>
            <td class="text-center">
  <div class="d-flex justify-content-start align-items-center gap-2">
    <a href="{{ route('tiket.edit', $ticket->id) }}" class="btn btn-warning btn-sm" title="Edit">
      <i class="ri-pencil-line"></i>
    </a>
    <form action="{{ route('tickets.destroys', $ticket->id) }}" method="POST" class="m-0 p-0">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Hapus">
        <i class="ri-delete-bin-line"></i>
      </button>
    </form>
  </div>
</td>

          {{-- <td>
            <div class="d-flex justify-content-center ">
              <a href="{{ route('tiket.edit', $ticket->id) }}" class="btn btn-warning btn-sm">
                <i class="ri-pencil-line"></i>
              </a>
              <form action="{{ route('tickets.destroys', $ticket->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-delete">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </form>
            </div>
          </td> --}}
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<!-- End Activity Card -->

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Ticket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection
