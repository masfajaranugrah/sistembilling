@extends('layouts/layoutMaster')

@section('title', 'History Login')
 
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
@vite(['resources/assets/js/pelanggan-marketing.js', 'resources/assets/js/extended-ui-sweetalert2.js'])

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
    <h5 class="mb-0">Daftar Log Login</h5>
  </div>

  <div class="card-body">
    <table class="table datatables-basic">
      <thead>
        <tr>
          <th>User</th>
          <th>IP Address</th>
          <th>Browser</th>
          <th>Device</th>
          <th>platform</th>
          <th>Waktu Login</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td>{{ $log->user->name }}</td>
            <td>{{ $log->ip_address ?? '-' }}</td>
            <td>{{ $log->browser ?? '-' }}</td>
            <td>{{ $log->device ?? '-' }}</td>
            <td>{{ $log->platform ?? '-' }}</td>
            <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center">Tidak ada log login.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
