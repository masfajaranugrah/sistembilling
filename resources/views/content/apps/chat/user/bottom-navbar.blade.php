@php
use Illuminate\Support\Facades\Auth;
$user = Auth::guard('customer')->user();
@endphp

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <button class="tab-btn active" onclick="window.location.href='/dashboard/customer/tagihan/home'">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </button>

    <button class="tab-btn" onclick="window.location.href='{{ route('customer.chat') }}'">
        <i class="bi bi-envelope"></i>
        <span>Chat</span>
    </button>

    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/tagihan'">
        <i class="bi bi-card-list"></i>
        <span>Tagihan</span>
    </button>

    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/tagihan/selesai'">
        <i class="bi bi-file-earmark-text"></i>
        <span>Kwitansi</span>
    </button>

    <button id="btn-profile" class="tab-btn">
        <i class="bi bi-person-circle"></i>
        <span>Profile</span>
        <div id="profile-dropdown">
            <div id="profile-name">{{ $user->nama_lengkap ?? 'Nama Pelanggan' }}</div>
            <div id="btn-logout" class="logout-red">Logout</div>
        </div>
    </button>
</div>

<style>
/* Bottom Navbar */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 70px;
    background: #fff;
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -5px 15px rgba(0,0,0,0.08);
    border-radius: 15px 15px 0 0;
    z-index: 999;
}

.bottom-nav .tab-btn {
    background: none;
    border: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 0.82rem;
    color: #6b7280;
    position: relative;
    transition: all 0.3s ease;
    text-decoration: none !important;
}

.bottom-nav .tab-btn i {
    font-size: 20px; /* Ukuran ikon sesuai permintaan */
}

.bottom-nav .tab-btn span {
    font-size: 0.82rem;
    line-height: 1.2;
}

/* Active tab hanya ganti warna */
.bottom-nav .tab-btn.active {
    color: #3b82f6;
    font-weight: 600;
}

/* Profile Dropdown */
#profile-dropdown {
    position: absolute;
    bottom: 75px;
    right: 0;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    display: none;
    min-width: 200px;
    padding: 0.5rem 0;
    animation: fadeIn 0.2s ease-in-out;
    z-index: 1000;
}

#profile-dropdown div {
    padding: 0.8rem 1.2rem;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.2s ease;
}

/* Hover Item */
#profile-dropdown div:hover {
    background: #f3f4f6;
    border-radius: 12px;
}

/* Logout warna merah */
.logout-red {
    color: #dc2626;
    font-weight: 600;
}

.logout-red:hover {
    background: rgba(220,38,38,0.1);
    border-radius: 12px;
}

/* Animasi fadeIn */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Dropdown Profile Toggle
const btnProfile = document.getElementById('btn-profile');
const dropdown = document.getElementById('profile-dropdown');

btnProfile.addEventListener('click', (e)=>{
    e.stopPropagation();
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

// Klik di luar dropdown untuk menutup
document.addEventListener('click', ()=> dropdown.style.display = 'none');

// Logout
document.getElementById('btn-logout').addEventListener('click', ()=>{
    fetch('/customer/logout', {
        method:'POST',
        headers:{
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(()=> window.location.href='/')
    .catch(()=> Swal.fire('Error','Gagal logout','error'));
});
</script>
