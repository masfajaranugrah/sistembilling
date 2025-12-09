<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Invoice Tagihan</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<style>
body {
    background: #f5f7fa;
    font-family: 'Poppins', sans-serif;
    padding-bottom: 90px;
}

/* Card Style */
.card-invoice {
    border-radius: 14px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.07);
    border: none;
    overflow: hidden;
}

.card-header-invoice {
    background: linear-gradient(100deg, #6366f1, #3b82f6);
    padding: 1.2rem;
    color: white;
    text-align: center;
}

.card-header-invoice h5 {
    font-size: 1.1rem;
    margin-bottom: 4px;
}

.card-body p {
    font-size: 0.87rem;
}

/* Status Badge */
.status-badge {
    padding: 6px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 50px;
}

.status-lunas { background:#10b981; color:#fff; }
.status-verifikasi { background:#fbbf24; color:#fff; }
.status-belum { background:#ef4444; color:#fff; }

/* Bayar Button */
.btn-bayar {
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 600;
}

.info-text-big p {
    font-size: 1rem !important; /* lebih besar dari default */
}
.ppn-text {
    font-size: 1.05rem;
    color: #ef4444;
    font-weight: 700;
}
.invoice-info .info-label {
    font-size: 1.15rem;          /* ukuran lebih besar */
    margin-bottom: 6px;
}

.ppn-label {
    font-size: 1.25rem;          /* paling besar */
    font-weight: 700;
    color: #ef4444;              /* merah elegan */
    margin-top: 10px;
    display: block;
}

/* Bottom Navbar */
.bottom-nav {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    height: 72px;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -3px 15px rgba(0,0,0,0.06);
}

.bottom-nav button {
    background: none;
    border: none;
    text-align: center;
    color: #6b7280;
}

.bottom-nav button i {
    font-size: 1.45rem;
}

@media (max-width: 480px) {
    .card-body p { font-size: 0.82rem; }
    h4.fw-bold { font-size: 1.2rem; }
}
</style>
</head>

<body>

<div class="container py-4">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-primary">Daftar Tagihan Anda</h4>
    </div>

    @forelse($tagihans as $tagihan)
    @php
        $pelanggan = $tagihan->pelanggan ?? null;
        $paket = $tagihan->paket ?? null;
    @endphp

    <div class="card card-invoice mb-4">

        <div class="card-header-invoice">
            <h5>INVOICE TAGIHAN</h5>
            <small>PT. Jernih Multi Komunikasi</small>
        </div>

        <div class="card-body px-3">

          <div class="mb-3" style="font-size: 1rem;">
            <p class="mb-1"><strong>No. ID:</strong> {{ $pelanggan->nomer_id ?? '-' }}</p>
            <p class="mb-1"><strong>Nama:</strong> {{ $pelanggan->nama_lengkap ?? '-' }}</p>
            <p class="mb-1"><strong>Invoice:</strong> {{ \Carbon\Carbon::parse($tagihan->tanggal_mulai)->format('d M Y') }}</p>
            <p class="mb-1"><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->format('d M Y') }}</p>

            <p class="mt-2 fw-bold" style="color:#ef4444;">
                no ppn*
            </p>
          </div>

          <hr>

          <div class="text-center my-3">
              <p class="fw-semibold mb-2">
                  Tagihan Periode: {{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->translatedFormat('F Y') }}
              </p>

              <h4 class="fw-bold text-danger">
                  Rp {{ number_format($paket->harga ?? 0, 0, ',', '.') }}
              </h4>

              <small class="text-muted">
                  ({{ ucwords(\NumberFormatter::create('id_ID', \NumberFormatter::SPELLOUT)->format($paket->harga ?? 0)) }} Rupiah)
              </small>
          </div>

          <div class="text-center mt-3">
              @if($tagihan->status_pembayaran === 'lunas')
                  <span class="status-badge status-lunas">LUNAS</span>
              @elseif($tagihan->status_pembayaran === 'proses_verifikasi')
                  <span class="status-badge status-verifikasi">MENUNGGU VERIFIKASI</span>
              @else
                  <span class="status-badge status-belum">BELUM BAYAR</span>
                  <div class="mt-3">
                      <button class="btn btn-primary btn-sm btn-bayar bayar-btn" data-id="{{ $tagihan->id }}">
                          <i class="bi bi-wallet2 me-1"></i> Bayar Sekarang
                      </button>
                  </div>
              @endif
          </div>

        </div>
    </div>

    @empty
    <div class="alert alert-info text-center mt-4">
       Saat ini anda tidak memiliki tagihan. </br>
Jika anda sudah melakukan pembayaran bisa cek kwitansi</br>
	<a href="https://layanan.jernih.net.id/dashboard/customer/tagihan/selesai">
	<button class="btn btn-primary">disini</button>
</a>
	
    </div>
    @endforelse
</div>

<div class="bottom-nav">
    @include('content.apps.Customer.tagihan.bottom-navbar', ['active' => 'invoice'])
</div>

<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

/* ================================
   FUNGSI KOMPRES GAMBAR (iOS/Android)
================================== */
function compressImage(file, maxWidth = 1280, quality = 0.6) {
    return new Promise((resolve, reject) => {
        if (file.type === "application/pdf") {
            resolve(file);
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                let ratio = img.width / img.height;
                if (img.width > maxWidth) {
                    canvas.width = maxWidth;
                    canvas.height = maxWidth / ratio;
                } else {
                    canvas.width = img.width;
                    canvas.height = img.height;
                }

                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                canvas.toBlob(
                    (blob) => {
                        if (!blob) reject("Gagal mengonversi gambar.");
                        else resolve(new File([blob], file.name, { type: "image/jpeg" }));
                    },
                    "image/jpeg",
                    quality
                );
            };
            img.src = event.target.result;
        };
        reader.onerror = () => reject("Gagal membaca file.");
        reader.readAsDataURL(file);
    });
}

/* ================================
   EVENT BAYAR
================================== */
document.querySelectorAll('.bayar-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tagihanId = btn.dataset.id;
        const rekenings = @json($rekenings);

        let htmlRekening = '<div class="d-flex flex-column gap-2">';
        rekenings.forEach(r => {
            htmlRekening += `
            <label class="card p-2 border cursor-pointer">
                <input type="radio" name="type_pembayaran" value="${r.id}" style="margin-right:10px">
                <strong>${r.nama_bank}</strong> - <strong>${r.nomor_rekening}</strong>
            </label>`;
        });
        htmlRekening += '</div>';

        Swal.fire({
            title: 'Pilih Rekening Tujuan',
            html: htmlRekening,
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const selected = document.querySelector('input[name="type_pembayaran"]:checked');
                if (!selected) Swal.showValidationMessage('Pilih salah satu rekening!');
                return selected ? selected.value : null;
            }
        }).then(result => {
            if (!result.isConfirmed) return;
            const selectedRekening = rekenings.find(r => r.id == result.value);

            Swal.fire({
                title: 'Upload Bukti Pembayaran',
                html: `
                    <p><strong>${selectedRekening.nama_bank}</strong> - A.N (${selectedRekening.nama_pemilik})</p>
                    <input type="file" id="bukti-pembayaran" class="swal2-file" accept="image/*,application/pdf">
                `,
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,

                preConfirm: async () => {
                    const fileInput = document.getElementById('bukti-pembayaran');
                    if (!fileInput.files.length) return Swal.showValidationMessage('Pilih file bukti pembayaran!');

                    let file = fileInput.files[0];

                    try { file = await compressImage(file); }
                    catch (e) { return Swal.showValidationMessage("Gagal kompres gambar: " + e); }

                    const formData = new FormData();
                    formData.append('bukti_pembayaran', file);
                    formData.append('type_pembayaran', selectedRekening.id);
                    formData.append('_method', 'PUT');

                    return fetch(`/dashboard/customer/tagihan/${tagihanId}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => { if (!data.success) throw new Error(data.message); return data; })
                    .catch(err => Swal.showValidationMessage(`Gagal upload: ${err.message}`));
                }
            }).then(uploadResult => {
                if (uploadResult.isConfirmed) Swal.fire('Sukses!', 'Bukti pembayaran berhasil dikirim.', 'success').then(() => location.reload());
            });
        });
    });
});
</script>

</body>
</html>
