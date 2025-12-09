<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Daftar Invoice</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { margin:5rem 0; font-family:'Poppins', sans-serif; background:#f5f7fa; }
#app-container {
    padding-bottom: 120px;   /* supaya tidak ketutup navbar bawah */
    display:flex;
    flex-direction:column;
    gap:1.4rem;
    padding:1rem;
}

/* Card Invoice */
.card-invoice {
    background:#fff;
    border-radius:14px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
    overflow:hidden;
    transition:.25s ease;
}
.card-invoice:hover { transform:translateY(-4px); }
.card-invoice .card-header {
    background:#17a2b8;
    color:#fff;
    text-align:center;
    padding:1rem 1.5rem;
    font-weight:600;
    font-size:1.05rem;
    letter-spacing:0.4px;
}
.card-invoice .card-body {
    padding:1.3rem 1.5rem !important; /* MARGIN DIPERBESAR */
}

.invoice-info {
    display:flex;
    justify-content:space-between;
    margin-bottom:.7rem;
    flex-wrap:wrap;
    gap:.7rem;
    font-size:.92rem;
}

.invoice-footer {
    display:flex;
    justify-content:flex-end;
    flex-wrap:wrap;
    margin-top:1.2rem;
    gap:.5rem;
}

.btn-download, .btn-view {
    padding:0.45rem 0.9rem;
    font-size:0.85rem;
    border-radius:8px;
    cursor:pointer;
    border:none;
    text-decoration:none !important;   /* HAPUS GARIS BAWAH */
    color:white;
    display:flex;
    align-items:center;
    gap:5px;
}
.btn-view { background:#17a2b8; }
.btn-view:hover { opacity:.85; }
.btn-download { background:#007bff; }
.btn-download:hover { opacity:.85; }

/* Bottom Navbar */
.bottom-nav {
    position:fixed; bottom:0; left:0; right:0;
    height:70px; background:#fff; border-top:1px solid #ddd;
    display:flex; justify-content:space-around; align-items:center;
    box-shadow:0 -2px 10px rgba(0,0,0,0.05); z-index:1000;
}
.bottom-nav button {
    background:none; border:none;
    display:flex; flex-direction:column; align-items:center;
    font-size:.8rem; color:#555; cursor:pointer;
}
.bottom-nav button.active { color:#17a2b8; font-weight:600; }
.bottom-nav i { font-size:1.3rem; margin-bottom:3px; }

/* Responsive */
@media (max-width:576px) {
    .invoice-info { flex-direction:column; font-size:.85rem; }
    .invoice-footer { flex-direction:column; justify-content:center; align-items:center; gap:.4rem; }
}
</style>
</head>

<body class="container py-4">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-primary">Daftar Tagihan Anda</h4>
    </div>

<div id="app-container">

</div>

<div class="bottom-nav">
    @include('content.apps.Customer.tagihan.bottom-navbar', ['active' => 'invoice'])
</div>

<script>
let oldInvoices = {};

async function fetchInvoice() {
    try {
        const res = await fetch('/dashboard/customer/tagihan/selesai/json');
        const data = await res.json();
        const invoices = data.data || [];
        const container = document.getElementById('app-container');

        let updated = false;
        const newInvoices = {};
        invoices.forEach(inv => {
            newInvoices[inv.id] = inv;
            if(!oldInvoices[inv.id] || oldInvoices[inv.id].kwitansi !== inv.kwitansi) updated = true;
        });

        if(!updated && Object.keys(oldInvoices).length>0) return;
        oldInvoices = newInvoices;

        container.innerHTML = "";

        if(invoices.length === 0){
            container.innerHTML = `
                <div class="alert alert-info text-center shadow-sm">
                    <strong>Belum ada invoice</strong> untuk akun Anda.
                </div>`;
            return;
        }

        const invoicesNew = [];
        const invoicesOld = [];
        const now = new Date();

        invoices.forEach(invoice => {
            let isNew = false;
            if(invoice.tanggal_pembayaran){
                const bayarDate = new Date(invoice.tanggal_pembayaran);
                const diffDays = Math.ceil(Math.abs(now - bayarDate) / (1000*60*60*24));
                if(diffDays <= 7) isNew = true;
            }
            if(isNew) invoicesNew.push(invoice); else invoicesOld.push(invoice);
        });

        const finalInvoices = [...invoicesNew, ...invoicesOld];
        finalInvoices.forEach(invoice => {
            const nama = invoice.nama_pelanggan ?? '-';
        const typePembayaran = invoice.type_pembayaran ?? '-';
        console.log(invoice)

            const idInvoice = invoice.nomer_id ?? '-';
            const harga = invoice.harga ?? 0;

            const tanggalMulai = invoice.tanggal_mulai
                ? new Date(invoice.tanggal_mulai).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})
                : '-';

            const tanggalBerakhir = invoice.tanggal_berakhir
                ? new Date(invoice.tanggal_berakhir).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})
                : '-';

            const tanggalBayar = invoice.tanggal_pembayaran
                ? new Date(invoice.tanggal_pembayaran).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})
                : '-';

            const hargaFormatted = new Intl.NumberFormat('id-ID').format(harga);

            const isNew = invoicesNew.includes(invoice);
            const badgeNew = isNew
                ? `<span style="background:#28a745;color:#fff;padding:3px 7px;border-radius:6px;font-size:0.75rem;margin-left:6px;">NEW</span>` : "";

            container.innerHTML += `
            <div class="card-invoice">
                <div class="card-header">
                    INVOICE ${idInvoice} ${badgeNew}
                </div>

                <div class="card-body">
                    <div class="invoice-info">
                        <div><strong>Nama:</strong> ${nama}</div>
                        <div><strong>Invoice:</strong> ${tanggalMulai}</div>
                        <div><strong>Jatuh Tempo:</strong> ${tanggalBerakhir}</div>
                        <div><strong>Tanggal Bayar:</strong> ${tanggalBayar}</div>
                        <td>{{ $tagihan->rekening->type_pembayaran ?? '-' }}</td>
    <div><strong>Type Pembayaran:</strong> ${typePembayaran}</div>

                    </div>

                    <div class="text-center my-3">
                        <h5 style="margin:0;">Rp ${hargaFormatted}</h5>
                    </div>

                    <div class="invoice-footer">

                        ${
                            invoice.kwitansi
                            ? `<a href="/${invoice.kwitansi}" target="_blank" class="btn-view">
                                    <i class="bi bi-eye"></i> Lihat Kuitansi
                               </a>`
                            : "-"
                        }

                        ${
                            invoice.kwitansi
                            ? `<button class="btn-download" onclick='downloadKuitansi(${JSON.stringify(invoice)})'>
                                    <i class="bi bi-download"></i> Download
                               </button>`
                            : "-"
                        }
                    </div>
                </div>
            </div>`;
        });

    } catch(e){ console.error(e); }
}

function downloadKuitansi(invoice){
    if(!invoice.kwitansi){
        Swal.fire('Error','Kuitansi belum tersedia','error');
        return;
    }

    const url = `/${invoice.kwitansi}`;

    fetch(url)
        .then(res => { if(!res.ok) throw new Error('File tidak ditemukan'); return res.blob(); })
        .then(blob => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Kuitansi-${invoice.pelanggan_id}.pdf`;
            link.click();
            URL.revokeObjectURL(link.href);
        })
        .catch(err => Swal.fire('Error', err.message, 'error'));
}

fetchInvoice();
setInterval(fetchInvoice, 5000);
</script>

</body>
</html>
