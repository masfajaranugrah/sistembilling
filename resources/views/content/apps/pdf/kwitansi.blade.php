<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kwitansi Pembayaran</title>
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Times New Roman', Times, serif;
  background: #fff;
  padding: 30px;
}

.wrapper {
  max-width: 900px;
  margin: 0 auto;
  border: 2px solid #000;
  padding: 30px 30px;
  background: #fff;
  position: relative; /* penting agar footer absolute tetap di dalam wrapper */
}

/* ===================== */
/* HEADER FLEX */
/* ===================== */
.header {
  display: table;
  width: 100%;
  margin-bottom: 40px;
}

/* Kotak Logo */
.info-box {
  display: table-cell;
  width: 140px;
  vertical-align: top;
  padding-right: 20px;
}

.logo {
  width: 120px;
}

.logo img {
  width: 100%;
  height: auto;
  display: block;
}

/* Konten kanan */
.right-content {
  display: table-cell;
  vertical-align: top;
  width: auto;
}

.no-id {
  font-size: 12px;
  margin-bottom: 10px;
}

.title {
  text-align: center;
  font-weight: bold;
  font-size: 20px;
  margin-bottom: 15px;
  letter-spacing: 2px;
}

.info-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 15px;
}

.info-table td {
  font-size: 13px;
  padding: 4px 0;
  vertical-align: top;
}

.info-table td:first-child {
  width: 150px;
}

.info-table td:nth-child(2) {
  width: 20px;
}

.highlight {
  background: #d1d5db;
  padding: 2px 6px;
  display: inline-block;
}

/* Garis pembatas */
hr {
  border: none;
  border-top: 1px solid #000;
  margin: 3px 0;
}

/* Box nominal */
.amount-box {
  display: flex;
  width: 220px;
  margin-top: 15px;
  border: 1px solid #000;
}

.amount-box .amount {
  background: #d1d5db;
  font-weight: bold;
  font-size: 14px;
  padding: 5px 25px;
  letter-spacing: 2px;
}

/* ===================== */
/* FOOTER TANDA TANGAN */
/* ===================== */
.footer {
  position: absolute;
  top: 230px; /* jarak dari bawah halaman */
  right: 50px;
  text-align: center;
  font-size: 13px;
  font-family: 'Times New Roman', Times, serif;
}

.footer .date {
  font-weight: bold;
  display: block;
}

.footer .ttd-img {
  width: 120px;
  height: auto;
  margin-bottom: 5px;
}

.footer .name {
  font-weight: bold;
  text-decoration: underline;
  margin-bottom: 2px;
}

.footer .role {
  font-weight: bold;
  font-size: 12px;
}
.footer .position {
  font-size: 12px;
}

/* ===================== */
/* PRINT MODE */
/* ===================== */
@media print {
  @page {
    size: A4;
    margin: 10mm;
  }

  body { 
    padding: 0;
    margin: 0;
    background: #fff;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .wrapper { 
    border: 2px solid #000 !important;
    margin: 0;
    padding: 20px 30px;
    max-width: 100%;
    page-break-inside: avoid;
    position: relative;
  }

  .highlight {
    background: #d1d5db !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .amount-box .amount {
    background: #d1d5db !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  img {
    max-width: 100%;
    display: block !important;
  }

  .footer .ttd-img {
    display: inline-block !important;
  }
}
</style>
</head>
<body>
@php
// Nominal pembayaran
$nominal = $tagihan->paket->harga ?? 0;

// Format angka jadi Rp. 100.000
$formattedRupiah = number_format($nominal, 0, ',', '.');

// Terbilang
$fmt = new \NumberFormatter('id', \NumberFormatter::SPELLOUT); // tambahkan backslash
$terbilang = ucfirst($fmt->format($nominal)) . ' Rupiah';

// Tanggal sekarang dalam bahasa Indonesia
$tanggalSekarang = \Carbon\Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY');

// Bulan & Tahun dari tanggal_berakhir DB
if(!empty($tagihan->tanggal_berakhir)) {
    $tgl = \Carbon\Carbon::parse($tagihan->tanggal_berakhir);
    $bulanTahun = $tgl->locale('id')->isoFormat('MMMM YYYY'); // Desember 2025
} else {
    $bulanTahun = '-';
}
@endphp


<div class="wrapper">
  
  <div class="header">
    <div class="info-box">
      <div class="logo">
        <img src="{{ public_path('assets/img/logo.png') }}" alt="logo">
      </div>
    </div>

    <div class="right-content">
      <div class="no-id">NO ID. <span class="id-box"><strong>{{ strtoupper($tagihan->pelanggan->nomer_id ?? '-') }}</strong></span></div>
      <div class="title">KUITANSI</div>

      <table class="info-table">
        <tr>
          <td>Telah terima dari</td>
          <td>:</td>
          <td><strong>{{ strtoupper($tagihan->pelanggan->nama_lengkap ?? '-') }}</strong></td>
        </tr>
        <tr>
          <td>Uang sejumlah</td>
          <td>:</td>
          <td><span class="highlight"><strong> {{ $terbilang }}</strong></span></td>
        </tr>
        <tr> 
          <td>Untuk Pembayaran</td>
          <td>:</td>
          <td><strong>Tagihan pembayaran bulan {{ $bulanTahun }}</strong></td>
        </tr>
      </table>

      <hr><hr><hr>

      <div class="amount-box">
  <div class="amount">Rp.{{ $formattedRupiah }}</div>
      </div>
    </div>
  </div>

  <!-- Footer tanda tangan -->
  <div class="footer">
  <div class="date">Kalten, {{ $tanggalSekarang }}</div>
    <div class="role">PT. JERNIH MULTI KOMUNIKASI</div>
    <img class="ttd-img" src="{{ public_path('assets/img/jmk.png') }}" alt="Tanda Tangan">
    <div> <span class="name">(Rohmat Setia Nursemedi</span> )</br> <span class="position">Direktur</span> </div>

   </div>

</div>
</body>
</html>
