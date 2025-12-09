@extends('layouts/layoutMaster')

@section('title', 'Daftar Paket')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
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
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ambil semua tombol delete
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
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
});
</script>
@endsection

@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1 class="dashboard-title">Data Paket</h1>
  <div class="dashboard-subtitle">
    <i class="ri-folder-line"></i>
    <span>Manage internet packages and pricing plans</span>
  </div>
</div>

<!-- Paket Grid -->
<div class="row gy-6">
  @if($pakets->isEmpty())
    <div class="col-12">
      <div class="alert alert-info text-center">
        Tidak ada paket saat ini.
      </div>
    </div>
  @else
    @foreach($pakets as $paket)
      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card h-100 shadow-sm border-0 rounded-4 hover-zoom">
          <div class="card-body d-flex flex-column justify-content-between p-4">
            <div class="d-flex align-items-start mb-3">
              <div class="avatar me-3">
                <div class="avatar-initial bg-gradient-primary text-white rounded-3 shadow-sm">
                  <i class="ri-folder-line ri-28px"></i>
                </div>
              </div>
              <div class="card-info">
                <h5 class="mb-2 fw-bold">{{ $paket->nama_paket }}</h5>
                <span class="badge text-black fw-bold bg-success mb-2">Rp. {{ number_format($paket->harga, 0, ',', '.') }}</span>
                <p class="mb-0 text-muted">Kecepatan: <strong>{{ $paket->kecepatan ?? '-' }} Mbps</strong></p>
              </div>
            </div>

            <div class="d-flex justify-content-between mt-auto gap-2">
              <a href="{{ route('paket.edit', $paket->id) }}" class="btn btn-sm btn-outline-warning flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                <i class="ri-edit-line"></i> Edit
              </a>

              <form action="{{ route('paket.destroy', $paket->id) }}" method="POST" class="flex-grow-1">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-1 btn-delete">
                  <i class="ri-delete-bin-line"></i> Delete
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @endif
</div>


<style>
.hover-zoom:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.12);
  transition: all 0.3s ease-in-out;
}
.card-info h5 { font-size: 1.15rem; }
.avatar-initial.bg-gradient-primary {
  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
}
.btn-outline-warning:hover { background: #ffc107; color: #fff; }
.btn-outline-danger:hover { background: #dc3545; color: #fff; }
</style>
@endsection
