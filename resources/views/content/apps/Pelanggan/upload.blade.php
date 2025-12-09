@extends('layouts/layoutMaster')

@section('title', 'Import Karyawan ')

{{-- Vendor Styles --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/dropzone/dropzone.scss',
    'resources/assets/vendor/libs/toastr/toastr.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss'
  ])
@endsection

{{-- Vendor Scripts --}}
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/dropzone/dropzone.js',
    'resources/assets/vendor/libs/toastr/toastr.js'
  ])
@endsection

{{-- Page Scripts --}}
@section('page-script')
  @vite([
    'resources/assets/js/upload-pelanggan.js',
    'resources/assets/js/ui-toasts.js'
  ])
@endsection


@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <h5 class="card-header">Import Data Pelanggan dari Excel</h5>
      <div class="card-body">
        {{-- Dropzone Form --}}
        <form
          action="{{ route('pelanggan.excel') }}"
          method="POST"
          enctype="multipart/form-data"
          class="dropzone needsclick"
          id="dropzone-basic">
          @csrf



          <div class="dz-message needsclick">
            Seret & lepaskan file Excel ke sini atau klik untuk memilih
            <span class="note needsclick">(File akan diupload ke server setelah klik tombol di bawah)</span>
          </div>


          <div class="fallback">
            <input name="file" type="file" />
          </div>


        </form>
    {{-- Tombol upload manual (di luar form agar tetap di bawah) --}}
        <div class="mt-3 text-center">
          <button type="button" id="submit-pelanggan" class="btn btn-primary">
            <i class="ti ti-upload"></i> Import Sekarang
          </button>

      </div>
    </div>
  </div>
</div>
@endsection
