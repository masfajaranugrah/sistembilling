@extends('layouts/layoutMaster')

@section('title', 'Pelanggan')

{{-- VENDOR STYLE --}}
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
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
    window.OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
            appId: "{{ env('ONESIGNAL_APP_ID') }}",
            safari_web_id: "",
            allowLocalhostAsSecureOrigin: true,
        });

        OneSignal.on('subscriptionChange', function (isSubscribed) {
            if (isSubscribed) {
                OneSignal.getUserId(function(player_id) {
                    fetch('/pelanggan/save-player-id', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ player_id })
                    });
                });
            }
        });
    });
</script>

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
          { orderable: false, targets: [12] } // kolom Actions
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
      e.stopPropagation(); // penting supaya klik detail tidak trigger delete
      const tr = $(this).closest('tr');
      const rowData = dtUserTable.row(tr).data();
      if (!rowData) return;

      const html = `
          <p><strong>No. ID:</strong> ${rowData[1]}</p>
          <p><strong>Nama Lengkap:</strong> ${rowData[2]}</p>
          <p><strong>No. WhatsApp:</strong> ${rowData[3]}</p>
          <p><strong>Alamat Lengkap:</strong> ${rowData[4]}</p>
          <p><strong>RT:</strong> ${rowData[5]}</p>
          <p><strong>RW:</strong> ${rowData[6]}</p>
          <p><strong>Kecamatan:</strong> ${rowData[7]}</p>
          <p><strong>Kabupaten:</strong> ${rowData[8]}</p>
          <p><strong>Tanggal Langganan:</strong> ${rowData[9]}</p>
          <p><strong>Foto KTP:</strong><br>${rowData[10]}</p>
          <p><strong>Status:</strong> ${rowData[11]}</p>
          <p><strong>Actions:</strong> ${rowData[12]}</p>
      `;
      $('#detailModal .modal-body').html(html);
      $('#detailModal').modal('show');
  });

  // --- Event tombol Delete pakai SweetAlert2 ---
  $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      e.stopPropagation(); // penting supaya tidak trigger modal detail
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
  <h1 class="dashboard-title">Data Pelanggan</h1>
  <div class="dashboard-subtitle">
    <i class="ri-user-line"></i>
    <span>Manage and monitor all customer accounts</span>
  </div>
</div>

<!-- Activity Card -->
<div class="activity-card">
  <div class="activity-header">
    <div class="activity-title">
      <i class="ri-user-3-line"></i>
      Daftar Pelanggan
    </div>
    <div class="activity-filters">
      <a href="{{ route('add-pelanggan') }}" class="btn btn-primary">
        <i class="ri-user-add-line"></i>
        Tambah Pelanggan
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="datatables-users table">
      <thead>
        <tr>
          <th>#</th>
          <th>No. ID</th>
          <th>Nama Lengkap</th>
          <th>No. WhatsApp</th>
          <th>Alamat Lengkap</th>
          <th>RT</th>
          <th>RW</th>
          <th>Kecamatan</th>
          <th>Kabupaten</th>
          <th>Tanggal Langganan</th>
          <th>Foto KTP</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pelanggan as $p)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $p->nomer_id }}</td>
          <td>{{ $p->nama_lengkap }}</td>
          <td>{{ $p->no_whatsapp }}</td>
          <td>{{ $p->alamat_jalan }}</td>
          <td>{{ $p->rt }}</td>
          <td>{{ $p->rw }}</td>
          <td>{{ $p->kecamatan }}</td>
          <td>{{ $p->kabupaten }}</td>
          <td>{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}</td>
          <td>
                  @if($p->foto_ktp)
                                    <img id="preview_ktp" src="{{ asset('storage/' . $p->foto_ktp) }}" class="img-thumbnail" style="max-width:200px;">
                                @else
                                    <img id="preview_ktp" src="#" style="display:none;" class="img-thumbnail" style="max-width:200px;">
                                @endif
          </td>
          <td>
            @php
              $statusClass = match(strtolower($p->status ?? '' )) {
                  'reject' => 'badge bg-danger',
                  'pending' => 'badge bg-warning text-dark',
                  'approve' => 'badge bg-success',
                  default => 'badge bg-secondary',
              };
            @endphp
            <span class="{{ $statusClass }}">{{ ucfirst($p->status ?? '-') }}</span>
          </td>
                      <td class="text-center">
  <div class="d-flex justify-content-start align-items-center gap-2">
    <a href="{{ route('pelanggan.edit', $p->id) }}" class="btn btn-warning btn-sm" title="Edit">
      <i class="ri-pencil-line"></i>
    </a>
    <form action="{{ route('pelanggan.delete', $p->id) }}" method="POST" class="m-0 p-0">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Hapus">
        <i class="ri-delete-bin-line"></i>
      </button>
    </form>
  </div>
</td>
          {{-- <td class="text-center">
            <div class="dropdown">
              <button class="btn btn-sm btn-icon btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                <i class="ri-more-2-fill"></i>
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('pelanggan.edit', $p->id) }}">
                  <i class="ri-edit-2-line me-1"></i> Edit
                </a></li>
                <li>
                  <form action="{{ route('pelanggan.delete', $p->id) }}" method="POST" class="delete-form d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="dropdown-item btn-delete">
                      <i class="ri-delete-bin-line me-1"></i> Delete
                    </button>
                  </form>
                </li>
              </ul>
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
        <h5 class="modal-title" id="detailModalLabel">Detail Pelanggan</h5>
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
