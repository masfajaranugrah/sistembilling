@extends('layouts/layoutMaster')

@section('title', 'History Ticket')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
@vite([ 'resources/assets/js/extended-ui-sweetalert2.js'])

<script>
  $(document).ready(function() {
    $('.datatables-basic').DataTable({
        responsive: true,
        autoWidth: false,
    });
});
</script>
@endsection

@section('content')
 <div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daftar Ticket</h5>
  </div>

  <div class="card-body">
    @forelse($tickets as $ticket)
        <h5>Tiket 👉 {{ $ticket->pelanggan->nama_lengkap ?? '-' }} - {{  $ticket->pelanggan->nomer_id }}</h5>
        <ul class="list-group mb-3">
            @foreach($ticket->statusLogs as $log)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ ucfirst($log->status) }}</span>
                    <small>{{ $log->created_at->format('d M Y H:i') }} oleh {{ $log->user->name }}</small>
                </li>
            @endforeach
        </ul>
    @empty
        <p>Tidak ada ticket.</p>
    @endforelse
  </div>
</div>
@endsection
