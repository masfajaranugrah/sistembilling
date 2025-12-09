@extends('layouts/layoutMaster')

@section('title', 'DataTables - Tables')

<!-- Vendor Styles -->
@section('vendor-style')


@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
 
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/status-pelanggan-marketing.js'])
<script>
document.addEventListener('click', function(e) {
    const target = e.target.closest('.btn-preview-ktp');
    if(target) {
        const imageUrl = target.getAttribute('data-image');
        document.getElementById('previewKtpImage').src = imageUrl;
    }
});
</script>
@endsection

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daftar Pelanggan</h5>
    <a href="{{ route('add-pelanggan-marketing') }}">
      <button class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Pelanggan
      </button>
    </a>
  </div>

  <div class="card-datatable table-responsive p-3">
    <table class="datatables-basic table table-striped table-bordered nowrap">
      <thead>
        <tr>
          <th>NO</th>
          <th>Nama Lengkap</th>
          <th>Alamat</th>
          <th>No. KTP</th>
          <th>WhatsApp</th>
          <th>No Telp / SMS</th>
          <th>Deskripsi</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Berakhir</th>
          <th>Package</th>
          <th>Masa Pembayaran</th>
          <th>Foto KTP</th>
          <th>Status</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modal Preview Foto KTP -->
<div class="modal fade" id="previewKtpModal" tabindex="-1" aria-labelledby="previewKtpModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title" id="previewKtpModalTitle">Preview Foto KTP</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center p-3">
        <img id="previewKtpImage" src="" alt="Foto KTP" class="img-fluid rounded shadow-sm border" style="max-height:80vh; object-fit:contain;">
      </div>
    </div>
  </div>
</div>

<!-- Modal Offcanvas Add New -->
<div class="offcanvas offcanvas-end" id="add-new-record">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">New Record</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body flex-grow-1">
    <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
      <div class="col-sm-12 form-control-validation">
        <label class="form-label" for="basicFullname">Full Name</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-user"></i></span>
          <input type="text" id="basicFullname" class="form-control dt-full-name" placeholder="John Doe" />
        </div>
      </div>
      <div class="col-sm-12 form-control-validation">
        <label class="form-label" for="basicPost">Post</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-briefcase"></i></span>
          <input type="text" id="basicPost" class="form-control dt-post" placeholder="Web Developer" />
        </div>
      </div>
      <div class="col-sm-12 form-control-validation">
        <label class="form-label" for="basicEmail">Email / WhatsApp</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-mail"></i></span>
          <input type="text" id="basicEmail" class="form-control dt-email" placeholder="082xxxxxx" />
        </div>
      </div>
      <div class="col-sm-12 form-control-validation">
        <label class="form-label" for="basicDate">Joining Date</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-calendar"></i></span>
          <input type="text" class="form-control dt-date" id="basicDate" placeholder="MM/DD/YYYY" />
        </div>
      </div>
      <div class="col-sm-12 form-control-validation">
        <label class="form-label" for="basicSalary">Salary / Paket</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-currency-dollar"></i></span>
          <input type="number" id="basicSalary" class="form-control dt-salary" placeholder="10000" />
        </div>
      </div>
      <div class="col-sm-12 mt-2">
        <button type="submit" class="btn btn-primary me-sm-4 me-1">Submit</button>
        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
