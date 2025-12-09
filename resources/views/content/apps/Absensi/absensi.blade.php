@extends('layouts.layoutMaster')

@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1 class="dashboard-title">Absensi</h1>
  <div class="dashboard-subtitle">
    <i class="ri-calendar-check-line"></i>
    <span>Manage daily attendance and overtime records</span>
  </div>
</div>

<!-- Absensi Buttons Card -->
<div class="activity-card mb-4">
    <div class="activity-header">
        <div class="activity-title">
            <i class="ri-time-line"></i>
            Absensi Hari Ini
        </div>
    </div>
    <div class="p-4">
        <div class="d-flex flex-column flex-md-row justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary btn-lg px-5" onclick="openModal('checkin')">Check In</button>
            <button class="btn btn-danger btn-lg px-5" onclick="openModal('checkout')">Check Out</button>
            <button class="btn btn-warning btn-lg px-5" onclick="openModal('lembur_in')">Mulai Lembur</button>
            <button class="btn btn-success btn-lg px-5" onclick="openModal('lembur_out')">Selesai Lembur</button>
        </div>
    </div>
</div>

<!-- Activity Card for Log -->
<div class="activity-card">
    <div class="activity-header">
        <div class="activity-title">
            <i class="ri-file-list-3-line"></i>
            Log Absensi
        </div>
    </div>
    <div class="table-responsive">
        <table class="datatables-users table">


            <thead class="table-light">
    <tr>
        <th>#</th>
        <th>Tanggal</th>
        <th>Masuk</th>

        <th>Pulang</th>

        <th>Lembur Mulai</th>

        <th>Lembur Selesai</th>

        <th>Total Jam Kerja</th>
        <th>Total Lembur</th>
        <th>Lokasi Masuk</th>
        <th>Lokasi Pulang</th>
        <th>Foto Absensi</th>
    </tr>
</thead>
<tbody>
@forelse($attendances as $i => $absen)
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ \Carbon\Carbon::parse($absen->date)->timezone('Asia/Jakarta')->format('d M Y') }}</td>

    {{-- Jam Masuk & Foto --}}
    <td>{{ $absen->time_in ? \Carbon\Carbon::parse($absen->time_in)->format('H:i') : '-' }}</td>


    {{-- Jam Pulang & Foto --}}
    <td>{{ $absen->time_out ? \Carbon\Carbon::parse($absen->time_out)->format('H:i') : '-' }}</td>


    {{-- Lembur Mulai & Foto --}}
    <td>{{ $absen->lembur_in ? \Carbon\Carbon::parse($absen->lembur_in)->format('H:i') : '-' }}</td>


    {{-- Lembur Selesai & Foto --}}
    <td>{{ $absen->lembur_out ? \Carbon\Carbon::parse($absen->lembur_out)->format('H:i') : '-' }}</td>

    {{-- Total Jam Kerja & Lembur --}}
    <td>
        @if($absen->time_in && $absen->time_out)
            @php
                [$inHour, $inMinute] = explode(':', \Carbon\Carbon::parse($absen->time_in)->timezone('Asia/Jakarta')->format('H:i'));
                [$outHour, $outMinute] = explode(':', \Carbon\Carbon::parse($absen->time_out)->timezone('Asia/Jakarta')->format('H:i'));
                $inTotal = ($inHour*60) + $inMinute;
                $outTotal = ($outHour*60) + $outMinute;
                if($outTotal < $inTotal) $outTotal += 24*60;
                $selisih = $outTotal - $inTotal;
                $jam = floor($selisih/60);
                $menit = $selisih%60;
            @endphp
            {{ $jam }} jam {{ $menit }} menit
        @else
            0 jam 0 menit
        @endif
    </td>
    <td>
        @if($absen->lembur_in && $absen->lembur_out)
            @php
                [$inHour, $inMinute] = explode(':', \Carbon\Carbon::parse($absen->lembur_in)->timezone('Asia/Jakarta')->format('H:i'));
                [$outHour, $outMinute] = explode(':', \Carbon\Carbon::parse($absen->lembur_out)->timezone('Asia/Jakarta')->format('H:i'));
                $inTotal = ($inHour*60) + $inMinute;
                $outTotal = ($outHour*60) + $outMinute;
                if($outTotal < $inTotal) $outTotal += 24*60;
                $selisih = $outTotal - $inTotal;
                $jam = floor($selisih/60);
                $menit = $selisih%60;
            @endphp
            {{ $jam }} jam {{ $menit }} menit
        @else
            0 jam 0 menit
        @endif
    </td>

    {{-- Lokasi --}}
    <td>
        @if($absen->lat_in && $absen->lng_in)
            <a href="https://www.google.com/maps?q={{ $absen->lat_in }},{{ $absen->lng_in }}" target="_blank">📍 Lihat</a>
        @else
            -
        @endif
    </td>
    <td>
        @if($absen->lat_out && $absen->lng_out)
            <a href="https://www.google.com/maps?q={{ $absen->lat_out }},{{ $absen->lng_out }}" target="_blank">📍 Lihat</a>
        @else
            -
        @endif
    </td>
     <td>
        @if($absen->photo_in)
            <a href="{{ asset('storage/'.$absen->photo_in) }}" target="_blank">
                <img src="{{ asset('storage/'.$absen->photo_in) }}" alt="Foto Masuk" style="width:40px; height:40px; object-fit:cover; border-radius:5px;">
            </a>
        @else
            -
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="14" class="text-muted">Belum ada data absensi.</td>
</tr>
@endforelse
</tbody>
        </table>
    </div>
</div>
<!-- End Activity Card -->

{{-- 🗺️ Modal Absensi --}}
<div class="modal fade" id="absenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="modalTitle">Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="absenForm" method="POST" enctype="multipart/form-data" action="{{ route('absensi.submit') }}">
                @csrf
                <input type="hidden" name="action" id="absenAction">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <div class="modal-body">
                    <h6 class="fw-semibold mb-2">📍 Pilih atau Gunakan Lokasi Anda</h6>
                    <div id="map" style="height: 300px; border-radius: 10px;"></div>

                    <div class="text-center mt-3">
                        <p id="coordsDisplay" class="text-muted small mb-1">Menunggu lokasi...</p>
                        <a id="mapsLink" href="#" target="_blank" class="text-decoration-none d-none">🌍 Lihat di Google Maps</a>
                        <small class="text-muted d-block mt-1">Klik peta untuk ubah titik lokasi</small>
                    </div>

                    <div class="mt-4 text-center">
                        <h6 class="fw-semibold mb-2">📸 Upload Foto Selfie (Opsional)</h6>
                        <input type="file" name="photo" accept="image/*" class="form-control w-75 mx-auto">
                        <small class="text-muted d-block mt-1">Tidak wajib jika kamera tidak tersedia</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Kirim Absensi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 🌍 Leaflet Map --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map, marker, accuracyCircle, watchId;
let isFollowing = true, manualOverride = false;

function openModal(action) {
    document.getElementById('absenAction').value = action;
    document.getElementById('modalTitle').textContent = {
        checkin: "🕓 Check In",
        checkout: "🏁 Check Out",
        lembur_in: "🌙 Mulai Lembur",
        lembur_out: "☀️ Selesai Lembur"
    }[action] || "Absensi";

    const modalEl = document.getElementById('absenModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
    modalEl.addEventListener('shown.bs.modal', onModalShown, { once: true });
}

function onModalShown() {
    ensureMapOverlay();
    initMap();

    if(!navigator.geolocation){
        showCoords('Browser tidak mendukung geolokasi.');
        removeMapOverlay();
        return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => { setMarker(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy); startWatch(); removeMapOverlay(); },
        err => { showCoords('Gagal mendapatkan lokasi.'); setFallback(); removeMapOverlay(); },
        { enableHighAccuracy:true, timeout:10000, maximumAge:0 }
    );
}

function initMap(){
    if(map) return setTimeout(()=>map.invalidateSize(), 200);
    map = L.map('map',{ zoomControl:true }).setView([0,0],2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{ attribution:'© OpenStreetMap contributors'}).addTo(map);

    marker = L.marker([0,0],{ draggable:true }).addTo(map).bindPopup("📍 Titik Absensi");
    accuracyCircle = L.circle([0,0],{ radius:0 }).addTo(map);

    map.on('click', e => { manualOverride=true; isFollowing=false; updateMarker(e.latlng.lat,e.latlng.lng); });
    marker.on('dragend', e => { manualOverride=true; isFollowing=false; updateMarker(e.target.getLatLng().lat,e.target.getLatLng().lng); });

    const FollowControl = L.Control.extend({
        onAdd:function(){
            const el=L.DomUtil.create('div','leaflet-control-follow btn btn-sm btn-light');
            el.style.padding='4px 8px'; el.style.cursor='pointer'; el.innerText='Follow';
            L.DomEvent.on(el,'click', L.DomEvent.stopPropagation).on(el,'click',L.DomEvent.preventDefault)
                .on(el,'click',()=>{ isFollowing=!isFollowing; manualOverride=!isFollowing; updateFollow(); });
            return el;
        }
    });
    map.addControl(new FollowControl({position:'topright'}));
    updateFollow();
    setTimeout(()=>map.invalidateSize(),250);
}

function startWatch(){
    if(!navigator.geolocation) return;
    if(watchId) navigator.geolocation.clearWatch(watchId);
    watchId = navigator.geolocation.watchPosition(
        pos=>{ if(!manualOverride && isFollowing) setMarker(pos.coords.latitude,pos.coords.longitude,pos.coords.accuracy); },
        err=>showCoords('Gagal melacak lokasi.'),
        { enableHighAccuracy:true, maximumAge:2000, timeout:10000 }
    );
}

function setMarker(lat,lng,accuracy=0){ updateMarker(lat,lng,accuracy); if(marker) marker.setLatLng([lat,lng]); if(accuracyCircle) accuracyCircle.setLatLng([lat,lng]).setRadius(Math.max(accuracy,5)); if(map && !manualOverride && isFollowing) map.flyTo([lat,lng],17,{animate:true,duration:0.7}); }
function updateMarker(lat,lng,accuracy=null){ document.getElementById('latitude').value=lat; document.getElementById('longitude').value=lng; if(accuracy!==null && accuracyCircle) accuracyCircle.setRadius(Math.max(accuracy,5)); showCoords(`Latitude: ${lat.toFixed(6)}, Longitude: ${lng.toFixed(6)}${accuracy?` (±${Math.round(accuracy)} m)`:''}`); }
function updateFollow(){ for(let el of document.getElementsByClassName('leaflet-control-follow')){ if(isFollowing && !manualOverride){ el.classList.add('active'); el.innerText='Following'; } else{ el.classList.remove('active'); el.innerText='Follow'; } } }
function showCoords(text){ const d=document.getElementById('coordsDisplay'), l=document.getElementById('mapsLink'); d.textContent=text; const m=text.match(/(-?\d+\.\d+).*?(-?\d+\.\d+)/); if(m){ l.href=`https://www.google.com/maps?q=${m[1]},${m[2]}`; l.classList.remove('d-none'); }else{ l.classList.add('d-none'); } }
function setFallback(){ setMarker(-6.2,106.816666,1000); map.setView([-6.2,106.816666],13); }
function ensureMapOverlay(){ const el=document.getElementById('map'); if(!el.querySelector('.loading-overlay')){ const o=document.createElement('div'); o.className='loading-overlay'; Object.assign(o.style,{position:'absolute',top:0,left:0,right:0,bottom:0,display:'flex',alignItems:'center',justifyContent:'center',background:'rgba(255,255,255,0.8)'}); o.innerHTML='<div>Menunggu izin lokasi…</div>'; el.style.position='relative'; el.appendChild(o); } }
function removeMapOverlay(){ const o=document.querySelector('#map .loading-overlay'); if(o) o.remove(); }

document.getElementById('absenModal').addEventListener('hidden.bs.modal',()=>{ if(watchId) navigator.geolocation.clearWatch(watchId); watchId=null; manualOverride=false; isFollowing=true; updateFollow(); });
</script>
@endsection
