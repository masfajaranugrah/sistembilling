@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp
@php
use Illuminate\Support\Facades\Auth;
// Cek apakah customer sudah login
if (Auth::guard('customer')->check()) {
// Redirect langsung ke route dashboard customer
header("Location: " . route('customer.tagihan.home'));
exit;
}
@endphp
@extends('layouts/layoutMaster')

@section('title', 'Login Member')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/pages-auth.js'
])
@endsection

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
    <div class="authentication-inner py-6">

      <!-- Login -->
      <div class="card p-md-7 p-1">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-text demo text-heading fw-semibold">JMK</span>
          </a>
        </div>
        <!-- /Logo -->

        <div class="card-body mt-1">
          <h4 class="mb-1">Welcome Sahabat JMK ??</h4>
          <p class="mb-5">Silakan login menggunakan Nomor ID Anda</p>    
<form id="formAuthentication" class="mb-5" action="{{ route('login.member.post') }}" method="POST">
    @csrf
    <div class="form-floating form-floating-outline mb-5">
        <input type="text" class="form-control" id="no_whatsapp" name="no_whatsapp"
               placeholder="Masukkan No WhatsApp" autofocus>
        <label for="no_whatsapp">No WhatsApp</label>
    </div>

    <div class="mb-5">
        <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
    </div>
</form>        </div>
      </div>
      <!-- /Login -->


      <img alt="mask" src="{{ asset('assets/img/illustrations/auth-basic-login-mask-'.$configData['style'].'.png') }}"
           class="authentication-image d-none d-lg-block"
           data-app-light-img="illustrations/auth-basic-login-mask-light.png"
           data-app-dark-img="illustrations/auth-basic-login-mask-dark.png" />
    </div>
  </div>
</div>

@endsection
