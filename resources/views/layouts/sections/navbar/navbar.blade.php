@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$containerNav = ($configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
$navbarDetached = $navbarDetached ?? '';
@endphp

<!-- Navbar -->
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme {{ $navbarDetached }} {{ $containerNav }}" id="layout-navbar">

  <div class="{{ $containerNav }}">

    {{-- Brand --}}
    @if(isset($navbarFull))
      <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-6">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
          <span class="app-brand-logo demo">JMK</span>
          <span class="app-brand-text demo menu-text fw-semibold">JMK</span>
        </a>
      
      </div>
    @endif

    {{-- Menu Toggle --}}
    @if(!isset($navbarHideToggle))
      <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 {{ isset($menuHorizontal) || isset($contentNavbar) ? 'd-xl-none' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
          <i class="ri-menu-fill ri-22px"></i>
        </a>
      </div>
    @endif

    {{-- Navbar Right --}}
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

      {{-- Search (desktop) --}}
   
      <ul class="navbar-nav flex-row align-items-center ms-auto">

        {{-- Search (horizontal) --}}
        @if(isset($menuHorizontal))
          <li class="nav-item navbar-search-wrapper me-1 me-xl-0">
            <a class="nav-link btn btn-text-secondary rounded-pill search-toggler fw-normal" href="javascript:void(0);">
              <i class="ri-search-line ri-22px scaleX-n1-rtl"></i>
            </a>
          </li>
        @endif

        {{-- Style Switcher --}}
        @if($configData['hasCustomizer'])
          <li class="nav-item dropdown-style-switcher dropdown me-1 me-xl-0">
            <a class="nav-link btn rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" style="color: #0d9488;">
              <i class='ri-22px'></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
              <li><a class="dropdown-item" href="javascript:void(0);" data-theme="light"><i class='ri-sun-line ri-22px me-3'></i>Light</a></li>
              <li><a class="dropdown-item" href="javascript:void(0);" data-theme="dark"><i class="ri-moon-clear-line ri-22px me-3"></i>Dark</a></li>
              <li><a class="dropdown-item" href="javascript:void(0);" data-theme="system"><i class="ri-computer-line ri-22px me-3"></i>System</a></li>
            </ul>
          </li>
        @endif

        {{-- User Dropdown --}}
@php
    // Ambil user dari default atau guard 'nama_lengkap'
    $user = Auth::user() ?? (Auth::guard('customer')->check() ? Auth::guard('customer')->user() : null);

    // Tentukan inisial role atau nama
    $roleInitials = '-';
    if ($user) {
        if (!empty($user->role)) {
            $roleInitials = match(strtolower($user->role)) {
                'customer service', 'cs' => 'CS',
                'admin', 'ad' => 'AD',
                'team', 'tm' => 'TM',
                default => strtoupper(substr($user->role, 0, 2)),
            };
        } elseif (!empty($user->nama_lengkap)) {
            $roleInitials = strtoupper(substr($user->nama_lengkap, 0, 2));
        }
    }
@endphp

        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online text-white d-flex justify-content-center align-items-center rounded-circle" style="width: 40px; height: 40px; background-color: #0d9488;">
              <span class="fw-bold">{{ $roleInitials }}</span>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">

            {{-- Profile Info --}}
            <li>
              <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-2">
                    <div class="avatar avatar-online text-white d-flex justify-content-center align-items-center rounded-circle" style="width: 40px; height: 40px; background-color: #0d9488;">
                      <span class="fw-bold">{{ $roleInitials }} </span>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    @php
    $displayName = $user?->name ?? $user?->nama_lengkap ?? '-';
    $displayRole = $user?->role ?? 'customer';
@endphp
                    <span class="fw-medium d-block small">{{ $displayName }}</span>
                    <small class="text-muted">{{ $displayRole }}</small>
                  </div>
                </div>
              </a>
            </li>

            <li><div class="dropdown-divider"></div></li>

            {{-- My Profile --}}
            <li>
              <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
                <i class="ri-user-3-line ri-22px me-3"></i> My Profile
              </a>
            </li>

            <li><div class="dropdown-divider"></div></li>

            {{-- Logout / Login --}}
           @php
    $guard = null;
    if(Auth::guard('customer')->check()) {
        $guard = 'customer';
        $logoutRoute = '/pelanggan/jernihnet/login';
    } elseif(Auth::check()) {
        $guard = 'web';
        $logoutRoute = '/dashboard/login';
    }
@endphp

<li>
  <div class="d-grid px-4 pt-2 pb-1">
    @if($guard)
      <a class="btn btn-sm btn-danger d-flex"
         href="{{ $logoutRoute }}"
         onclick="event.preventDefault(); document.getElementById('logout-form-{{ $guard }}').submit();">
         Logout <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
      </a>

      <form id="logout-form-{{ $guard }}" method="POST" action="{{ $guard === 'customer' ? route('customer.logout') : route('logout') }}">
        @csrf
      </form>
    @else
      <a class="btn btn-sm btn-danger d-flex" href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
        Login <i class="ri-login-box-line ms-2 ri-16px"></i>
      </a>
    @endif
  </div>
</li>


          </ul>
        </li>
        {{-- /User Dropdown --}}

      </ul>

    </div>

    {{-- Search Small Screens --}}
    <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
      <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0" placeholder="Search..." aria-label="Search...">
      <i class="ri-close-fill search-toggler cursor-pointer"></i>
    </div>
    {{-- /Search Small Screens --}}

  </div>

</nav>
<!-- / Navbar -->
