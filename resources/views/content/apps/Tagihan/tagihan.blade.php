@extends('layouts/layoutMaster')

@section('title', 'Logistics Dashboard - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {
$(document).on('shown.bs.modal', '[id^="modalEditTagihan-"]', function () {
    // Flatpickr untuk Tanggal Mulai
    flatpickr($(this).find('.flatpickr-edit-start'), {
        dateFormat: "Y-m-d",
        allowInput: true
    });

    // Flatpickr untuk Tanggal Berakhir
    flatpickr($(this).find('.flatpickr-edit-end'), {
        dateFormat: "Y-m-d",
        allowInput: true
    });
});

    // =======================
    //  Select2 Pelanggan
    // =======================
    $('#pelangganSelect').select2({
        placeholder: '-- Pilih Pelanggan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalTambahTagihan')
    });

    const formatDate = d => d.toISOString().split('T')[0];
    const tglMulai = document.getElementById('tanggal_mulai');
    tglMulai.value = formatDate(new Date());

    function fillFields(selected) {
        if (!selected || !selected.val()) {
            $('#nama_lengkap, #alamat_jalan, #rt, #rw, #desa, #kecamatan, #kabupaten, #provinsi, #kode_pos, #no_whatsapp, #nomer_id, #paket, #harga, #masa_pembayaran, #kecepatan, #pelanggan_id, #paket_id, #tanggal_berakhir').val('');
            return;
        }

        const fields = [
            'nama','alamat_jalan','rt','rw','desa','kecamatan','kabupaten','provinsi',
            'kode_pos','nowhatsapp','nomorid','paket','harga','masa','kecepatan','paket_id'
        ];

        fields.forEach(f => {
            const el = $('#' + (f === 'masa' ? 'masa_pembayaran' : f));
            el.val(selected.data(f));
        });

        $('#pelanggan_id').val(selected.val());

        // Hitung jatuh tempo
        const startDate = new Date($('#tanggal_mulai').val());
        const masa = selected.data('masa') || selected.data('durasi');
        if (masa) {
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + parseInt(masa));
            $('#tanggal_berakhir').val(formatDate(endDate));
        }
    }

    $('#pelangganSelect').on('change', function () {
        fillFields($(this).find('option:selected'));
    });

    tglMulai.addEventListener('change', function () {
        fillFields($('#pelangganSelect').find('option:selected'));
    });

    $('#modalTambahTagihan').on('shown.bs.modal', function () {
        const list = $('#pelangganSelect option').filter((_, el) => el.value);
        if (list.length === 1) {
            $('#pelangganSelect').val(list.val()).trigger('change');
        }
    });

    // =======================
    //  DataTables
    // =======================
    const dtUserTable = $('.datatables-users').DataTable({
        paging: true,
        pageLength: 10,
        ordering: true,
        responsive: { details: { type: 'column', target: 0, display: $.noop } },
        columnDefs: [
            {
                className: 'control text-center',
                orderable: false,
                searchable: false,
                targets: 0,
                render: () => '<button class="btn btn-icon btn-sm btn-detail"><i class="ri-add-line"></i></button>'
            },
            { orderable: false, targets: [17] } // actions column
        ],
        language: {
            paginate: {
                previous: '<i class="ri-arrow-left-s-line"></i>',
                next: '<i class="ri-arrow-right-s-line"></i>'
            }
        }
    });

    // =======================
    //  Detail Modal
    // =======================
    $(document).on('click', '.btn-detail, td.control', function(e) {
        e.stopPropagation();
        const tr = $(this).closest('tr');
        const row = dtUserTable.row(tr).data();
        if (!row) return;

        const html = `
            <p><strong>No. ID:</strong> ${row[1]}</p>
            <p><strong>Nama Lengkap:</strong> ${row[2]}</p>
            <p><strong>No. WhatsApp:</strong> ${row[3]}</p>
            <p><strong>Alamat Lengkap:</strong> ${row[4]}</p>
            <p><strong>Kecamatan:</strong> ${row[5]}</p>
            <p><strong>Kabupaten:</strong> ${row[6]}</p>
            <p><strong>Provinsi:</strong> ${row[7]}</p>
            <p><strong>Status:</strong> ${row[8]}</p>
            <p><strong>Paket:</strong> ${row[9]}</p>
            <p><strong>Harga:</strong> ${row[10]}</p>
            <p><strong>Kecepatan:</strong> ${row[11]}</p>
            <p><strong>Tanggal Mulai:</strong> ${row[12]}</p>
            <p><strong>Jatuh Tempo:</strong> ${row[13]}</p>
<p><strong>Bukti Pembayaran:</strong> ${row[15] ? row[15] : "-"}</p>
            <p><strong>Catatan:</strong> ${row[16]}</p>
            <p><strong>Action:</strong> ${row[17]}</p>
        `;
        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // =======================
    //  Filter Dropdown
    // =======================
    $('#statusPembayaranFilter').on('change', function() {
        dtUserTable.column(8).search($(this).val()).draw();
    });
    $('#kabupatenFilter').on('change', function() {
        dtUserTable.column(6).search($(this).val()).draw();
    });
    $('#kecamatanFilter').on('change', function() {
        dtUserTable.column(5).search($(this).val()).draw();
    });

    // =======================
    //  SweetAlert Delete
    // =======================
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(res => res.isConfirmed && form.submit());
    });

    // =======================
    //  Flatpickr
    // =======================
    flatpickr("#tanggal_mulai", { dateFormat: "Y-m-d", defaultDate: new Date() });
    flatpickr("#tanggal_berakhir", { dateFormat: "Y-m-d" });
    flatpickr("#edit_tanggal_mulai", { dateFormat: "Y-m-d" });
    flatpickr("#edit_tanggal_berakhir", { dateFormat: "Y-m-d" });

    // =======================
    //  Konfirmasi Pembayaran
    // =======================
    $(document).on('click', '.btn-konfirmasi', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `Apakah <strong>${nama}</strong> sudah membayar?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lunas',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(res => {
            if (!res.isConfirmed) return;

            $.post(`/dashboard/admin/tagihan/${id}/bayar`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(resp => {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', timer: 1200, showConfirmButton: false })
                         .then(() => location.reload());
                }
            })
            .fail(() => Swal.fire('Gagal', 'Kesalahan server', 'error'));
        });
    });

    // =======================
    //  Edit Tagihan
    // =======================
    $(document).on('click', '.btn-edit-tagihan', function() {
        const item = $(this).data('item');

        $('#formEditTagihan').attr('action', `/dashboard/admin/tagihan/${item.id}/update`);
        $('#edit_pelanggan_id').val(item.pelanggan_id);
        $('#edit_paket_id').val(item.paket_id);

        // Informasi pelanggan
        ['nama_lengkap','nomer_id','alamat_jalan','rt','rw','desa','kecamatan','kabupaten','provinsi','kode_pos','no_whatsapp'].forEach(key => {
            $('#edit_' + key).val(item[key]);
        });

        // Informasi paket
        $('#edit_paket').val(item.paket?.nama_paket ?? '');
        $('#edit_harga').val(item.paket?.harga ?? '');
        $('#edit_masa_pembayaran').val(item.paket?.masa_pembayaran ?? '');
        $('#edit_kecepatan').val(item.paket?.kecepatan ?? '');

        // Tanggal
        $('#edit_tanggal_mulai').val(item.tanggal_mulai);
        $('#edit_tanggal_berakhir').val(item.tanggal_berakhir);
        $('#edit_catatan').val(item.catatan ?? '');
        $('#edit_status_pembayaran').val(item.status_pembayaran);

        // Flatpickr
        flatpickr('#edit_tanggal_mulai', { dateFormat: "Y-m-d", defaultDate: item.tanggal_mulai, allowInput: true });
        flatpickr('#edit_tanggal_berakhir', { dateFormat: "Y-m-d", defaultDate: item.tanggal_berakhir, allowInput: true });

        $('#modalEditTagihan').modal('show');
    });

    // =======================
    //  Mass Tagihan
    // =======================
    $('#modalMassTagihan').on('shown.bs.modal', function () {
        flatpickr(".flatpickr-select-start-all", { dateFormat: "Y-m-d", defaultDate: new Date(), minDate: "today", allowInput: true });
        flatpickr(".flatpickr-select-start-end", { dateFormat: "Y-m-d", defaultDate: new Date().fp_incr(7), minDate: "today", allowInput: true });
    });

});
</script>
@endsection



@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1 class="dashboard-title">Dashboard</h1>
  <div class="dashboard-subtitle">
    <i class="ri-calendar-line"></i>
    <span>Welcome back! Here's what's happening with your billing system.</span>
    <span class="date-range-badge">
      <i class="ri-calendar-2-line"></i>
      {{ date('d M Y') }} - {{ date('d M Y', strtotime('+30 days')) }}
    </span>
  </div>
</div>

<!-- Total Balance Hero Card -->
<div class="balance-hero-card">
  <div class="balance-label">
    <i class="ri-wallet-3-line"></i>
    Total Pendapatan Bulan Ini
  </div>
  <div class="balance-amount">
    Rp {{ number_format(($lunas ?? 0) * 250000, 0, ',', '.') }}
  </div>
  <div class="balance-change positive">
    <i class="ri-arrow-up-line"></i>
    <span>15.8%</span>
  </div>
  <div class="balance-actions">
    <button class="balance-btn balance-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahTagihan">
      <i class="ri-add-line"></i>
      Add Tagihan
    </button>
    <button class="balance-btn balance-btn-secondary" data-bs-toggle="modal" data-bs-target="#modalMassTagihan">
      <i class="ri-refresh-line"></i>
      Mass Tagihan
    </button>
  </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
  <!-- Total Customer -->
  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-label">Total Customer</div>
        <div class="stat-value">{{ number_format($totalCustomer ?? 0) }}</div>
        <div class="stat-change positive">
          <i class="ri-arrow-up-line"></i>
          <span>46.0%</span>
        </div>
      </div>
      <div class="stat-icon income">
        <i class="ri-group-line"></i>
      </div>
    </div>
    <div class="stat-footer">
      Seluruh pelanggan terdaftar
    </div>
  </div>

  <!-- Pembayaran Lunas -->
  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-label">Pembayaran Lunas</div>
        <div class="stat-value">{{ number_format($lunas ?? 0) }}</div>
        <div class="stat-change positive">
          <i class="ri-arrow-up-line"></i>
          <span>{{ $totalCustomer > 0 ? round($lunas / $totalCustomer * 100, 1) : 0 }}%</span>
        </div>
      </div>
      <div class="stat-icon income">
        <i class="ri-checkbox-circle-line"></i>
      </div>
    </div>
    <div class="stat-footer">
      Tagihan yang sudah dibayar
    </div>
  </div>

  <!-- Belum Lunas -->
  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-label">Belum Lunas</div>
        <div class="stat-value">{{ number_format($belumLunas ?? 0) }}</div>
        <div class="stat-change negative">
          <i class="ri-arrow-down-line"></i>
          <span>{{ $totalCustomer > 0 ? round($belumLunas / $totalCustomer * 100, 1) : 0 }}%</span>
        </div>
      </div>
      <div class="stat-icon expense">
        <i class="ri-error-warning-line"></i>
      </div>
    </div>
    <div class="stat-footer">
      Tagihan yang belum dibayar
    </div>
  </div>

  <!-- Jumlah Paket -->
  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-label">Jumlah Paket</div>
        <div class="stat-value">{{ number_format($totalPaket ?? 0) }}</div>
        <div class="stat-change positive">
          <i class="ri-arrow-up-line"></i>
          <span>35.2%</span>
        </div>
      </div>
      <div class="stat-icon saving">
        <i class="ri-box-3-line"></i>
      </div>
    </div>
    <div class="stat-footer">
      Total paket tersedia
    </div>
  </div>
</div>

<!-- Filters Section -->
<div class="filters-section">
  <div class="filters-header">
    <div class="filters-title">
      <i class="ri-filter-3-line"></i>
      Filter Data Tagihan
    </div>
  </div>
  <div class="filters-grid">
    <div class="filter-group">
      <label for="statusPembayaranFilter">Status Pembayaran</label>
      <select id="statusPembayaranFilter" class="form-select">
        <option value="">Semua Status</option>
        <option value="lunas">Sudah Lunas</option>
        <option value="belum bayar">Belum Bayar</option>
      </select>
    </div>
    <div class="filter-group">
      <label for="kabupatenFilter">Kabupaten</label>
      <select id="kabupatenFilter" class="form-select">
        <option value="">Semua Kabupaten</option>
        @foreach($kabupatenList as $kab)
          <option value="{{ strtolower($kab) }}">{{ $kab }}</option>
        @endforeach
      </select>
    </div>
    <div class="filter-group">
      <label for="kecamatanFilter">Kecamatan</label>
      <select id="kecamatanFilter" class="form-select">
        <option value="">Semua Kecamatan</option>
        @foreach($kecamatanList as $kec)
          <option value="{{ strtolower($kec) }}">{{ $kec }}</option>
        @endforeach
      </select>
    </div>
  </div>
</div>

<!-- Activity Card / Daftar Tagihan -->
<div class="activity-card">
  <div class="activity-header">
    <div class="activity-title">
      <i class="ri-file-list-3-line"></i>
      Daftar Tagihan
    </div>
    <div class="activity-filters">
      <button class="filter-btn active">
        <i class="ri-file-list-line"></i>
        Semua
      </button>
      <button class="filter-btn" onclick="window.location.href='{{ route('tagihan.lunas') }}'">
        <i class="ri-check-line"></i>
        Lunas
      </button>
      <button class="filter-btn" onclick="window.location.href='{{ route('tagihan.proses') }}'">
        <i class="ri-time-line"></i>
        Proses
      </button>
    </div>
  </div>
  <div class="table-responsive">
<table class="datatables-users table">
  <thead>
    <tr>
      <th>#</th>
      <th>No. ID</th>
      <th>Nama Lengkap</th>
      <th>No. WhatsApp</th>
      <th>Alamat Lengkap</th>
       <th>Kecamatan</th>
      <th>Kabupaten</th>
      <th>Provinsi</th>
      <th>Status Pembayaran</th>
      <th>Nama Paket</th>
      <th>Harga</th>
      <th>Kecepatan</th>
      <th>Tanggal Mulai</th>
      <th>Tanggal Jatuh Tempo</th>
      <th>Status Pembayaran</th>
      <th>Bukti Pembayaran</th>
      <th>Catatan</th>
       <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($tagihans as $item)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $item['nomer_id'] }}</td>
      <td>{{ $item['nama_lengkap'] }}</td>
      <td>{{ $item['no_whatsapp'] }}</td>
      <td>
        @php
          $alamatParts = [];
          if($item['alamat_jalan']) $alamatParts[] = $item['alamat_jalan'];
          if($item['rt'] || $item['rw']) $alamatParts[] = 'RT '.$item['rt'].' / RW '.$item['rw'];
          if($item['desa']) $alamatParts[] = 'Desa '.$item['desa'];
          if($item['kecamatan']) $alamatParts[] = 'Kecamatan '.$item['kecamatan'];
          if($item['kabupaten']) $alamatParts[] = 'Kabupaten '.$item['kabupaten'];
          if($item['provinsi']) $alamatParts[] = $item['provinsi'];
         @endphp
        {{ implode(', ', $alamatParts)   }}
      </td>

      <td>{{ $item['kecamatan'] ?? '-' }}</td>
      <td>{{ $item['kabupaten'] ?? '-' }}</td>
      <td>{{ $item['provinsi'] ?? '-' }}</td>
   <td>
    @php
        $status = strtolower($item['status_pembayaran'] ?? '');
        $badgeClass = match($status) {
            'lunas' => 'badge bg-success text-dark',       // Hijau background, teks hitam
            'belum bayar' => 'badge bg-warning text-dark', // Kuning background, teks hitam
            default => 'badge bg-secondary text-dark',     // Default abu-abu
        };
    @endphp
    <span class="{{ $badgeClass }}">{{ ucfirst($status ?: '-') }}</span>
</td>
      <td>{{ $item['paket']['nama_paket'] ?? '-' }}</td>
      <td>Rp {{ number_format($item['paket']['harga'] ?? 0, 0, ',', '.') }}</td>
      <td>{{ $item['paket']['kecepatan'] ?? '-' }} Mbps</td>
      <td>{{ $item['tanggal_mulai'] ? \Carbon\Carbon::parse($item['tanggal_mulai'])->format('d M Y') : '-' }}</td>
      <td>{{ $item['tanggal_berakhir'] ? \Carbon\Carbon::parse($item['tanggal_berakhir'])->format('d M Y') : '-' }}</td>
      <td>
        @php
          $status = strtolower($item['status_pembayaran']);
          $badgeClass = match($status) {
            'lunas' => 'badge bg-success',
            'belum bayar' => 'badge bg-warning text-dark',
            default => 'badge bg-secondary',
          };
        @endphp
        <span class="{{ $badgeClass }}">{{ ucfirst($status) }}</span>
      </td>


<td>
  @if(!empty($item['bukti_pembayaran']))
    <a href="{{ asset('storage/kwitansi/' . $item['bukti_pembayaran']) }}" target="_blank">
      @php
        $ext = pathinfo($item['bukti_pembayaran'], PATHINFO_EXTENSION);
      @endphp
      @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif']))
        <img src="'.asset('storage/'.$item['bukti_pembayaran']).'"
               alt="Bukti Pembayaran"
             title="Bukti Pembayaran '.$item['nama_lengkap'].'"
             style="width:50px; height:auto; object-fit:cover; border-radius:4px;">
      @else
        <i class="bi bi-file-earmark-pdf" style="font-size:24px;"></i>
      @endif
    </a>
  @else
    -
  @endif
</td>



      <td>{{ $item['catatan'] ?? '-' }}</td>


      <td>
    <div class="d-flex gap-1">

        <!-- Tombol Edit -->
       <button type="button"
    class="btn btn-sm btn-outline-primary btn-edit-tagihan"
    data-item='@json($item)'
    data-bs-toggle="modal"
    data-bs-target="#modalEditTagihan-{{ $item['id'] }}">
    <i class="bi bi-pencil-square"></i> Edit
</button>


        <!-- Tombol Hapus -->
      <form action="{{ route('tagihan.destroy', $item['id']) }}" method="POST" class="delete-form d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">
        <i class="bi bi-trash"></i> Hapus
    </button>
</form>

        <!-- Tombol Konfirmasi Bayar -->
         @if($status === 'lunas')
          <button class="btn btn-sm btn-secondary" disabled>Sudah Bayar</button>
       @else
        <button class="btn btn-sm btn-success btn-konfirmasi" data-id="{{ $item['id'] }}" data-nama="{{ $item['nama_lengkap'] }}">
            Sudah Bayar
        </button>
     @endif
    </div>
</td>

    </tr>
    @endforeach
  </tbody>
</table>
  </div>
</div>
<!-- End Activity Card -->

<!-- Modal Tambah Tagihan -->
<div class="modal fade" id="modalTambahTagihan" tabindex="-1" aria-labelledby="modalTambahTagihanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-light text-white">
               <div class="card-header  mb-4">
          <h5 class="modal-title card-title mb-0 fw-semibold text-dark"></i> Tambah Tagihan Manual</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
               </div>

        </div>

        <div class="modal-body">
          <div class="row g-3">

            <!-- Pilih Pelanggan -->
            <div class="col-12">
              <label class="form-label">Pilih Pelanggan</label>
            <select id="pelangganSelect" class="form-select select2" required>
             <option value="">-- Pilih Pelanggan --</option>
               @foreach($pelanggan as $p)
              <option
            value="{{ $p->id }}"
            data-paket_id="{{ optional($p->paket)->id }}"
            data-nama="{{ $p->nama_lengkap }}"
            data-alamat_jalan="{{ $p->alamat_jalan }}"
            data-rt="{{ $p->rt }}"
            data-rw="{{ $p->rw }}"
            data-desa="{{ $p->desa }}"
            data-kecamatan="{{ $p->kecamatan }}"
            data-kabupaten="{{ $p->kabupaten }}"
            data-provinsi="{{ $p->provinsi }}"
            data-kode_pos="{{ $p->kode_pos }}"
            data-nowhatsapp="{{ $p->no_whatsapp }}"
            data-nomorid="{{ $p->nomer_id }}"
            data-paket="{{ optional($p->paket)->nama_paket }}"
            data-harga="{{ optional($p->paket)->harga }}"
            data-masa="{{ optional($p->paket)->masa_pembayaran }}"
            data-kecepatan="{{ optional($p->paket)->kecepatan }}"
            data-durasi="{{ optional($p->paket)->durasi }}"
        >
             {{ $p->nomer_id }} - {{ $p->nama_lengkap }}
        </option>
    @endforeach
            </select>
            </div>

            <input type="hidden" name="pelanggan_id" id="pelanggan_id">
            <input type="hidden" name="paket_id" id="paket_id">

            <div class="col-md-6">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" id="nama_lengkap" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nomor ID</label>
              <input type="text" id="nomer_id" class="form-control" readonly>
            </div>

           <div class="col-md-12">
  <label class="form-label">Alamat Jalan</label>
  <input type="text" id="alamat_jalan" class="form-control" readonly>
</div>
<div class="col-md-3">
  <label class="form-label">RT</label>
  <input type="text" id="rt" class="form-control" readonly>
</div>
<div class="col-md-3">
  <label class="form-label">RW</label>
  <input type="text" id="rw" class="form-control" readonly>
</div>
<div class="col-md-6">
  <label class="form-label">Desa / Kelurahan</label>
  <input type="text" id="desa" class="form-control" readonly>
</div>
<div class="col-md-6">
  <label class="form-label">Kecamatan</label>
  <input type="text" id="kecamatan" class="form-control" readonly>
</div>
<div class="col-md-6">
  <label class="form-label">Kabupaten / Kota</label>
  <input type="text" id="kabupaten" class="form-control" readonly>
</div>
<div class="col-md-6">
  <label class="form-label">Provinsi</label>
  <input type="text" id="provinsi" class="form-control" readonly>
</div>
<div class="col-md-6">
  <label class="form-label">Kode Pos</label>
  <input type="text" id="kode_pos" class="form-control" readonly>
</div>


            <div class="col-md-6">
              <label class="form-label">Nomor WhatsApp</label>
              <input type="text" id="no_whatsapp" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nama Paket</label>
              <input type="text" id="paket" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Harga Paket</label>
              <input type="text" id="harga" name="harga" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Masa Pembayaran</label>
              <input type="text" id="masa_pembayaran" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Kecepatan</label>
              <input type="text" id="kecepatan" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal Mulai</label>
              <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal Jatuh Tempo</label>
              <input type="date" id="tanggal_berakhir" name="tanggal_berakhir" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Catatan tambahan..."></textarea>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Tagihan</button>
        </div>
      </form>
    </div>
  </div>
</div>




<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Pelanggan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Tagihan -->

@foreach ($tagihans as $tagihan)
<div class="modal fade" id="modalEditTagihan-{{ $tagihan['id'] }}" tabindex="-1" aria-labelledby="modalEditTagihanLabel-{{ $tagihan['id'] }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.update', $tagihan['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="modal-header bg-light text-dark">
          <h5 class="modal-title fw-semibold" id="modalEditTagihanLabel-{{ $tagihan['id'] }}">Edit Tagihan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <!-- Pelanggan -->
            <div class="col-12">
              <label class="form-label">Nama Pelanggan</label>
              <input type="text" class="form-control" value="{{ $tagihan['nama_lengkap'] ?? '-' }}" readonly>
            </div>
            <input type="hidden" name="pelanggan_id" value="{{ $tagihan['pelanggan_id'] ?? '' }}">
            <input type="hidden" name="paket_id" value="{{ $tagihan['paket']['id'] ?? '' }}">

            <!-- Info tagihan lainnya -->
            <div class="col-md-6">
              <label class="form-label">Tanggal Mulai</label>
<input type="text" name="tanggal_mulai" class="form-control flatpickr-edit-start" value="{{ $tagihan['tanggal_mulai'] }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tanggal Jatuh Tempo</label>
<input type="text" name="tanggal_berakhir" class="form-control flatpickr-edit-end" value="{{ $tagihan['tanggal_berakhir'] }}" required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan</label>
              <textarea class="form-control" name="catatan" rows="2">{{ $tagihan['catatan'] ?? '' }}</textarea>
            </div>

            <!-- Upload Bukti Pembayaran -->
            <div class="col-md-6">
              <label class="form-label">Bukti Pembayaran</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach



{{-- modal select all  --}}
<div class="modal fade" id="modalMassTagihan" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.massStore') }}" method="POST">
        @csrf
        <div class="modal-header bg-warning">
          <h5 class="modal-title fw-bold">Buat Tagihan untuk Semua Pelanggan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- === TAMPILKAN DAFTAR PELANGGAN === -->
          <div class="alert alert-info">
            <strong>{{ count($pelanggan) }} pelanggan</strong> akan dibuatkan tagihan.
          </div>

          <div class="border rounded p-2 mb-3" style="max-height: 200px; overflow-y: auto;">
            @foreach ($pelanggan as $p)
              <div class="py-1 border-bottom small">
                <strong>{{ $p->nomer_id }}</strong> � {{ $p->nama_lengkap }}
              </div>
            @endforeach
          </div>
          <!-- === END PREVIEW === -->

          <div class="mb-3">
            <label class="form-label">Tanggal Mulai</label>
           <input type="text" name="tanggal_mulai" class="form-control flatpickr-select-start-all" required>

          </div>

          <div class="mb-3">
            <label class="form-label">Tanggal Jatuh Tempo</label>
           <input type="text" name="tanggal_berakhir" class="form-control flatpickr-select-start-end" required>

          </div>

          <p class="text-danger small">* Semua pelanggan di atas akan otomatis dibuatkan tagihan baru.</p>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Buat Semua Tagihan</button>
        </div>
      </form>
    </div>
  </div>
</div>




@endsection
