@extends('layouts/layoutMaster')

@section('title', 'Daftar Ticket')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])

<!-- Custom Style -->
<style>
/* Ganti ikon segitiga default DataTables responsive */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.control:before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.control:before {
  font-family: "Bootstrap Icons";
  content: "\f4fe"; /* plus-circle */
  font-weight: 700;
  background: none;
  border: none;
  color: #0d6efd; /* biru elegan */
  font-size: 1.1rem;
  box-shadow: none;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

/* Hapus semua pseudo element segitiga di mode responsive */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before,
table.dataTable.dtr-inline.collapsed > tbody > tr > td.control:before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.control:before {
  display: none !important;
  content: none !important;
}

/* Hilangkan padding ekstra bawaan DataTables */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control,
table.dataTable.dtr-inline.collapsed > tbody > tr > td.control,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.control {
  padding-left: 0 !important;
}


</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
@vite('resources/assets/vendor/libs/jquery/jquery.js')


<script>
document.addEventListener('DOMContentLoaded', function () {
  const table = $('#ticketsTable').DataTable({
    responsive: true,
    autoWidth: false,
    lengthChange: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    searching: true,
    ordering: true,
    order: [[12, 'desc']],
    paging: true,
    info: true,
    columnDefs: [
      {
        targets: 0, // kolom pertama = ikon
        className: 'text-center align-middle',
        orderable: false,
        render: function () {
          return `
            <button class="btn btn-sm by  toggle-details" title="Lihat detail">
              <i class="bi bi-plus-circle fs-6"></i>
            </button>
          `;
        }
      },
      { orderable: false, targets: -1 }
    ]
  });

  // Filter tim tambahan
  $('#teamFilter').on('change', function () {
    const value = $(this).val();
    table.column(9).search(value ? '^' + value + '$' : '', true, false).draw();
  });

  // Global search tambahan
  $('#globalSearch').on('keyup', function () {
    table.search(this.value).draw();
  });
});
</script>
@endsection


@section('content')

@php
use Carbon\Carbon;

$today = Carbon::today();

$teamProgress = $tickets
    ->filter(fn($ticket) => $ticket->user) // hanya tiket yang ada usernya
    ->groupBy(fn($ticket) => $ticket->user->name) // grup berdasarkan nama user / tim
    ->map(function($teamTickets) use ($today) {
        $todayTickets = $teamTickets->filter(fn($ticket) => $ticket->created_at->isSameDay($today));
        $total = $todayTickets->count();
        $finished = $todayTickets->whereIn('status', ['finished', 'approved'])->count();
        $pending = $todayTickets->whereIn('status', ['pending'])->count();
        $progress = $todayTickets->whereIn('status', ['progress'])->count();
        $assigned = $todayTickets->whereIn('status', ['assigned'])->count();
        return [
            'total' => $total,
            'finished' => $finished,
            'pending' => $pending,
            'progress' => $progress,
            'assigned' => $assigned
        ];
    });

@endphp

<div class="row mb-4">
    @foreach($teamProgress as $userName => $progress)
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white text-center fw-semibold">
                {{ $userName }}
            </div>
           <div class="card-body text-center">
    @if($progress['total'] > 0 && $progress['total'] === $progress['finished'])
        <p class="mb-1 text-success fw-semibold">
            Hari ini jobs sudah selesai sebanyak {{ $progress['finished'] }}
        </p>
    @else
        <p class="mb-1"><strong>Total Jobs:</strong> {{ $progress['total'] }}</p>
        <p class="mb-1 text-success"><strong>Selesai:</strong> {{ $progress['finished'] }}</p>
        <p class="mb-0 text-primary"><strong>On-Progress:</strong> {{ $progress['progress'] }}</p>
        <p class="mb-0 text-danger"><strong>Pending:</strong> {{ $progress['pending'] }}</p>
        <p class="mb-0 text-danger"><strong>Ditugaskan:</strong> {{ $progress['assigned'] }}</p>
    @endif
</div>

        </div>
    </div>
    @endforeach
</div>



<div class="card border-0 shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white rounded-top">
    <h5 class="mb-0 fw-semibold">🎟️ Daftar Ticket</h5>
    <a href="{{ route('tickets.create') }}" class="btn btn-light rounded-pill shadow-sm px-3 py-2 d-flex align-items-center">
      <i class="bi bi-plus-circle me-2 fs-5 text-primary"></i> 
      <span class="fw-semibold text-primary">Tambah Ticket</span>
    </a>
  </div>





  {{-- Tabel --}}
  <div class="card-datatable table-responsive p-3">
    <table id="ticketsTable" class="table table-hover table-bordered align-middle mb-0">
      <thead class="table-light">
        <tr>
            <th class="text-center" style="width: 40px;"></th> {{-- Kolom kontrol (ikon plus/minus) --}}
            <th class="text-center" style="width: 50px;">NO</th>
            <th>Nama Lengkap</th>
            <th class="text-center">Alamat</th>
            <th>Problem</th>
            <th>Catatan Tambahan</th>
            <th class="text-center">No Telp / WA</th>
            <th>Kategori</th>
            <th>Prioritas</th>
            <th>Status Pekerjaan</th>
            <th>Ditugaskan untuk</th>
            <th class="text-center">Foto Laporan</th>
            <th>Tanggal Dibuat</th>
            <th>Dibuat Oleh</th>
            <th>Catatan Teknisi</th>
            <th class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tickets as $index => $ticket)
        <tr>
            <td class="text-center"></td> {{-- Kolom ikon expand --}}
            <td class="text-center">
              <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill">
               {{ $tickets->count() - $index }}

              </span>
            </td>
          <td>{{ $ticket->pelanggan->nama_lengkap ?? '-' }}</td>
            <td class="text-center">
                @if($ticket->location_link)
                    <a href="{{ $ticket->location_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-geo-alt"></i>
                    </a>
                @else
                    <button class="btn btn-sm btn-outline-secondary" disabled>
                        <i class="bi bi-geo-alt-slash"></i>
                    </button>
                @endif
            </td>
            <td>{{ $ticket->issue_description }}</td>
            <td>{{ $ticket->additional_note }}</td>
            <td class="text-center">
                @if($ticket->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->phone) }}" target="_blank" class="text-success fs-5">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                @else
                    -
                @endif
            </td>
            <td>{{ $ticket->category }}</td>
            <td>{{ $ticket->priority }}</td>
            <td>
                <span class="badge 
                    @if($ticket->status == 'pending') bg-warning text-dark
                    @elseif($ticket->status == 'assigned') bg-info text-dark
                    @elseif($ticket->status == 'progress') bg-primary
                    @elseif(in_array($ticket->status, ['finished', 'approved'])) bg-success
                    @elseif($ticket->status == 'rejected') bg-danger
                    @else bg-secondary
                    @endif px-3 py-1 fs-7">
                    {{ ucfirst($ticket->status) }}
                </span>
            </td>
            <td>{{ $ticket->user->name ?? '-' }}</td>
            <td class="text-center">
                @if($ticket->technician_attachment)
                    <a href="{{ asset('storage/' . $ticket->technician_attachment) }}" target="_blank" class="btn btn-sm btn-info shadow-sm">
                        <i class="bi bi-image"></i>
                    </a>
                @endif
            </td>
            <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
            <td>{{ $ticket->creator->name ?? '-' }}</td> 
            <td>{{ $ticket->technician_note ?? '-' }}</td> 
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                  <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning btn-sm shadow-sm">
                      <i class="bi bi-pencil-square"></i>
                  </a>
                  <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                          <i class="bi bi-trash"></i>
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
@endsection
