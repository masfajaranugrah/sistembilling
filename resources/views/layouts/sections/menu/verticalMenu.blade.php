@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

$user = Auth::check() ? Auth::user() : (Auth::guard('customer')->check() ? Auth::guard('customer')->user() : null);
$currentUserRole = $user ? strtolower($user->role ?? 'customer') : '';
$currentPath = request()->path();
$currentUrl = '/' . ltrim($currentPath, '/');
$menuData = $menuData ?? [];

$isMenuActive = function($menuUrl, $currentUrl) {
    if (empty($menuUrl) || $menuUrl === '#' || $menuUrl === 'javascript:void(0);') {
        return false;
    }
    $menuUrl = '/' . ltrim($menuUrl, '/');
    $menuUrl = rtrim($menuUrl, '/');
    $currentUrl = rtrim($currentUrl, '/');
    if ($currentUrl === $menuUrl) {
        return true;
    }
    if (Str::startsWith($currentUrl, $menuUrl . '/')) {
        return true;
    }
    return false;
};
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <style>
    /* =============================================
       SIDEBAR - PERMANENT EXPANDED (No Collapse)
       ============================================= */
    #layout-menu {
      width: 260px !important;
      min-width: 260px !important;
      max-width: 260px !important;
      background: #fff !important;
    }
    
    .layout-page { 
      padding-left: 260px !important;
    }

    /* =============================================
       MENU STYLES
       ============================================= */
    #layout-menu .menu-inner { list-style: none !important; padding: 0 20px 0 8px !important; }
    #layout-menu .menu-header { padding: 18px 8px 8px !important; display: block !important; }
    #layout-menu .menu-header-text {
      font-size: 11px !important; font-weight: 700 !important;
      text-transform: uppercase !important; color: #9ca3af !important;
    }
    #layout-menu .menu-item { margin: 2px 0 !important; list-style: none !important; }
    #layout-menu .menu-item > .menu-link {
      display: flex !important; align-items: center !important;
      padding: 10px 14px !important; border-radius: 8px !important;
      color: #374151 !important; text-decoration: none !important;
    }
    #layout-menu .menu-item > .menu-link > i {
      font-size: 20px !important; margin-right: 12px !important; color: #6b7280 !important;
      flex-shrink: 0 !important;
    }
    #layout-menu .menu-item > .menu-link > div {
      flex: 1 !important; font-size: 14px !important;
    }
    #layout-menu .menu-item > .menu-link:hover { background-color: #ccfbf1 !important; color: #115e59 !important; }
    #layout-menu .menu-item > .menu-link:hover > i { color: #0d9488 !important; }
    #layout-menu .menu-item.active > .menu-link { background-color: #ccfbf1 !important; color: #115e59 !important; }
    #layout-menu .menu-item.active > .menu-link > i { color: #0d9488 !important; }
    #layout-menu .menu-item.open > .menu-link { background-color: rgba(13, 148, 136, 0.08) !important; }
    
    /* Submenu */
    #layout-menu .menu-sub { 
      display: none !important; list-style: none !important; 
      margin-left: 22px !important; border-left: 2px solid #ccfbf1 !important; 
      padding: 4px 0 !important; 
    }
    #layout-menu .menu-item.open > .menu-sub { display: block !important; }
    #layout-menu .menu-sub .menu-item { 
      list-style: none !important; margin: 2px 0 !important; 
    }
    #layout-menu .menu-sub .menu-item::before,
    #layout-menu .menu-sub .menu-item::marker { 
      display: none !important; content: none !important; 
    }
    #layout-menu .menu-sub .menu-link { 
      padding: 8px 14px 8px 16px !important; font-size: 13px !important; 
      margin-left: 8px !important; border-radius: 6px !important;
      color: #6b7280 !important;
    }
    #layout-menu .menu-sub .menu-link::before { display: none !important; content: none !important; }
    #layout-menu .menu-sub .menu-link:hover { 
      background-color: #ccfbf1 !important; color: #115e59 !important; 
    }
    #layout-menu .menu-sub .menu-item.active > .menu-link {
      background-color: #ccfbf1 !important; 
      color: #115e59 !important;
      font-weight: 600 !important;
    }
    
    /* Toggle arrow for submenu */
    #layout-menu .menu-toggle { position: relative !important; }
    #layout-menu .menu-toggle::after { 
      content: '\ea6e' !important; font-family: 'remixicon' !important; 
      position: absolute !important; right: 14px !important; top: 50% !important;
      transform: translateY(-50%) !important;
      font-size: 16px !important; color: #9ca3af !important;
    }
    #layout-menu .menu-item.open > .menu-toggle::after { 
      transform: translateY(-50%) rotate(90deg) !important; color: #0d9488 !important; 
    }
    
    .menu-inner-shadow { display: none !important; }
  </style>

  @if (!isset($navbarFull))
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo demo">
          <img src="{{ asset('jmk.jpeg') }}" alt="JMK Logo" style="height: 50px; width: auto; object-fit: contain;">
        </span>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @if (!empty($menuData) && isset($menuData[0]->menu))
      @foreach ($menuData[0]->menu as $menu)
        @if (empty((array)$menu))
          @continue
        @endif

        @php
          $menuRoles = $menu->roles ?? null;
          $isAllowed = true;
          if ($menuRoles) {
            if (is_array($menuRoles)) {
              $isAllowed = in_array($currentUserRole, array_map('strtolower', $menuRoles));
            } else {
              $isAllowed = strtolower($menuRoles) === $currentUserRole;
            }
          }
        @endphp

        @if ($isAllowed)
          @if (isset($menu->menuHeader))
            <li class="menu-header mt-4">
              <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
            </li>
          @else
            <li class="menu-item">
              <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>
                @isset($menu->icon)
                  <i class="{{ $menu->icon }}"></i>
                @endisset
                <div>{{ $menu->name ?? '' }}</div>
                @isset($menu->badge)
                  <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                @endisset
              </a>

              @isset($menu->submenu)
                <ul class="menu-sub">
                  @foreach ($menu->submenu as $submenu)
                    @php
                      $submenuRoles = $submenu->roles ?? null;
                      $submenuAllowed = true;
                      if ($submenuRoles) {
                        if (is_array($submenuRoles)) {
                          $submenuAllowed = in_array($currentUserRole, array_map('strtolower', $submenuRoles));
                        } else {
                          $submenuAllowed = strtolower($submenuRoles) === $currentUserRole;
                        }
                      }
                    @endphp
                    @if ($submenuAllowed)
                      <li class="menu-item">
                        <a href="{{ url($submenu->url) }}" class="menu-link">
                          @isset($submenu->icon)
                            <i class="{{ $submenu->icon }} me-2"></i>
                          @endisset
                          <div>{{ $submenu->name }}</div>
                        </a>
                      </li>
                    @endif
                  @endforeach
                </ul>
              @endisset
            </li>
          @endif
        @endif
      @endforeach
    @else
      <li class="menu-item">
        <a href="#" class="menu-link disabled">
          <i class="ti ti-alert-triangle"></i>
          <div>No menu data found</div>
        </a>
      </li>
    @endif
  </ul>
</aside>
