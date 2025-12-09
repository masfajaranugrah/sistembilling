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
  // 'resources/assets/vendor/libs/bootstrap/js/bootstrap.bundle.js',


])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi Select2
    $('#pelangganSelect').select2({
        placeholder: '-- Pilih Pelanggan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalTambahTagihan')
    });

    const tglMulai = document.getElementById('tanggal_mulai');
    const formatDate = d => d.toISOString().split('T')[0];
    tglMulai.value = formatDate(new Date());

    function fillFields(selected) {
        if (!selected || !selected.val()) {
            $('#nama_lengkap, #alamat_jalan, #rt, #rw, #desa, #kecamatan, #kabupaten, #provinsi, #kode_pos, #no_whatsapp, #nomer_id, #paket, #harga, #masa_pembayaran, #kecepatan, #pelanggan_id, #paket_id, #tanggal_berakhir').val('');
            return;
        }

        $('#nama_lengkap').val(selected.data('nama'));
        $('#no_whatsapp').val(selected.data('nowhatsapp'));
        $('#nomer_id').val(selected.data('nomorid'));
        $('#paket').val(selected.data('paket'));
        $('#harga').val(selected.data('harga'));
        $('#masa_pembayaran').val(selected.data('masa'));
        $('#kecepatan').val(selected.data('kecepatan'));
        $('#pelanggan_id').val(selected.val());
        $('#paket_id').val(selected.data('paket_id'));

        $('#alamat_jalan').val(selected.data('alamat_jalan'));
        $('#rt').val(selected.data('rt'));
        $('#rw').val(selected.data('rw'));
        $('#desa').val(selected.data('desa'));
        $('#kecamatan').val(selected.data('kecamatan'));
        $('#kabupaten').val(selected.data('kabupaten'));
        $('#provinsi').val(selected.data('provinsi'));
        $('#kode_pos').val(selected.data('kode_pos'));

        // Hitung tanggal jatuh tempo
        const startDate = new Date($('#tanggal_mulai').val());
        const endDate = new Date(startDate);
        const masa = selected.data('masa') || selected.data('durasi');
        if (masa) endDate.setDate(startDate.getDate() + parseInt(masa));
        $('#tanggal_berakhir').val(formatDate(endDate));
    }

    $('#pelangganSelect').on('change', function () {
        const selected = $(this).find('option:selected');
        fillFields(selected);
    });

    tglMulai.addEventListener('change', function () {
        const selected = $('#pelangganSelect').find('option:selected');
        fillFields(selected);
    });

    $('#modalTambahTagihan').on('shown.bs.modal', function () {
        const allOptions = $('#pelangganSelect option').filter(function () {
            return $(this).val() !== '';
        });
        if (allOptions.length === 1) {
            $('#pelangganSelect').val(allOptions.val()).trigger('change');
        }
    });

  // --- Inisialisasi DataTables ---
  const dtUserTable = $('.datatables-users').DataTable({
      paging: true,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      searching: true,
      ordering: true,
      responsive: {
          details: {
              type: 'column',
              target: 0,
              display: $.noop
          }
      },
      columnDefs: [
          {
              className: 'control text-center',
              orderable: false,
              searchable: false,
              targets: 0,
              render: function () {
                  return '<button class="btn btn-icon btn-sm btn-detail"><i class="ri-add-line"></i></button>';
              }
          },
          { orderable: false, targets: [12] } // kolom Actions
      ],
      language: {
          paginate: {
              previous: '<i class="ri-arrow-left-s-line"></i>',
              next: '<i class="ri-arrow-right-s-line"></i>'
          }
      }
  });

  // --- Event tombol detail ---
  $(document).on('click', '.btn-detail, td.control', function(e) {
      e.stopPropagation(); // penting supaya klik detail tidak trigger delete
      const tr = $(this).closest('tr');
      const rowData = dtUserTable.row(tr).data();
      if (!rowData) return;

      const html = `
        <p><strong>No. ID:</strong> ${rowData[1]}</p>
        <p><strong>Nama Lengkap:</strong> ${rowData[2]}</p>
        <p><strong>No. WhatsApp:</strong> ${rowData[3]}</p>
        <p><strong>Alamat Lengkap:</strong> ${rowData[4]}</p>
        <p><strong>Kecamatan:</strong> ${rowData[5]}</p>
        <p><strong>Kabupaten:</strong> ${rowData[6]}</p>
        <p><strong>Provinsi:</strong> ${rowData[7]}</p>
        <p><strong>Status Pembayaran:</strong> ${rowData[8]}</p>
        <p><strong>Nama Paket:</strong> ${rowData[9]}</p>
        <p><strong>Bukti Pembayaran:</strong> ${rowData[15]}</p>
        <p><strong>Kwitansi:</strong> ${rowData[16]}</p>
        <p><strong>Harga:</strong> ${rowData[10]}</p>
        <p><strong>Kecepatan:</strong> ${rowData[11]}</p>
        <p><strong>Tanggal Mulai:</strong> ${rowData[12]}</p>
        <p><strong>Tanggal Jatuh Tempo:</strong> ${rowData[13]}</p>

    `;
      $('#detailModal .modal-body').html(html);
      $('#detailModal').modal('show');
  });



    // --- Filter dropdown ---
// --- Filter dropdown ---
$('#statusPembayaranFilter').on('change', function() {
    const selected = $(this).val().toLowerCase();
    // Kolom Status Pembayaran = index 8
    dtUserTable.column(8).search(selected).draw();
});

$('#kabupatenFilter').on('change', function() {
    const selected = $(this).val().toLowerCase();
    // Kolom Kabupaten = index 6
    dtUserTable.column(6).search(selected).draw();
});

$('#kecamatanFilter').on('change', function() {
    const selected = $(this).val().toLowerCase();
    // Kolom Kecamatan = index 5
    dtUserTable.column(5).search(selected).draw();
});


    // --- Delete with SweetAlert2 ---
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        const form = this;

         Swal.fire({
          title: 'Yakin ingin menghapus?',
          text: "Data yang dihapus tidak dapat dikembalikan!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal',
          customClass: {
              confirmButton: 'btn btn-danger me-2',
              cancelButton: 'btn btn-secondary'
          },
          buttonsStyling: false
      }).then((result) => {
          if (result.isConfirmed) {
              form.submit();
          }
      });
    });

    // --- Inisialisasi Flatpickr ---
flatpickr("#tanggal_mulai", {
    dateFormat: "Y-m-d",
    defaultDate: new Date(),
    onChange: function(selectedDates, dateStr, instance) {
        // Update tanggal jatuh tempo otomatis jika mau
        const tanggalMulai = selectedDates[0];
        const masaPembayaran = parseInt($('#masa_pembayaran').val()) || 0;
        if (tanggalMulai && masaPembayaran) {
            const tanggalBerakhir = new Date(tanggalMulai);
            tanggalBerakhir.setDate(tanggalMulai.getDate() + masaPembayaran);
            flatpickr("#tanggal_berakhir").setDate(tanggalBerakhir);
        }
    }
});

flatpickr("#tanggal_berakhir", {
    dateFormat: "Y-m-d",
});



$(document).on('click', '.btn-konfirmasi', function(e) {
    e.preventDefault();
    const tagihanId = $(this).data('id');
    const nama = $(this).data('nama');

    // Step 1: Konfirmasi pembayaran
    Swal.fire({
        title: `Konfirmasi pembayaran`,
        html: `Apakah Anda yakin <strong>${nama}</strong> sudah melakukan pembayaran?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, sudah lunas!',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-success me-2',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {

            // Step 2: Upload bukti pembayaran
            Swal.fire({
                title: 'Upload Bukti Pembayaran',
                html: `<input type="file" id="buktiPembayaran" class="swal2-file" accept="image/*">`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Simpan & Kirim WA',
                cancelButtonText: 'Batal',
                focusConfirm: false,
                customClass: {
                    confirmButton: 'btn btn-success me-2',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const file = Swal.getPopup().querySelector('#buktiPembayaran').files[0];
                    if (!file) {
                        Swal.showValidationMessage('Silakan pilih file terlebih dahulu');
                    }
                    return file;
                }
            }).then((fileResult) => {
                if (fileResult.isConfirmed) {
                    const file = fileResult.value;
                    const formData = new FormData();
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('bukti_pembayaran', file);

                    // AJAX request ke backend
                    $.ajax({
                        url: `/dashboard/admin/tagihan/${tagihanId}/bayar`,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                // Step 3: Tampilkan hasil sukses dengan link WA dan PDF
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    html: `
                                        Bukti pembayaran berhasil diupload.<br><br>
                                        <a href="${response.waUrl}" target="_blank" class="btn btn-success me-2">Kirim WA</a>
                                        <a href="${response.pdfUrl}" target="_blank" class="btn btn-primary">Lihat Kwitansi PDF</a>
                                    `,
                                    showCloseButton: true,
                                    showConfirmButton: false
                                });

                                // Jika ingin WA otomatis terbuka:
                                // window.open(response.waUrl, '_blank');
                            } else {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat upload.', 'error');
                            }
                        },
                        error: function(err) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan server.', 'error');
                        }
                    });
                }
            });
        }
    });
});





$(document).on('click', '.btn-edit-tagihan', function() {
    const item = $(this).data('item'); // Ambil data JSON dari tombol

    // Set action form
    $('#formEditTagihan').attr('action', `/dashboard/admin/tagihan/${item.id}/update`);

    // Hidden input
    $('#edit_pelanggan_id').val(item.pelanggan_id);
    $('#edit_paket_id').val(item.paket_id);

    // Informasi pelanggan
    $('#editPelangganName').val(item.nama_lengkap);
    $('#edit_nama_lengkap').val(item.nama_lengkap);
    $('#edit_nomer_id').val(item.nomer_id);
    $('#edit_alamat_jalan').val(item.alamat_jalan);
    $('#edit_rt').val(item.rt);
    $('#edit_rw').val(item.rw);
    $('#edit_desa').val(item.desa);
    $('#edit_kecamatan').val(item.kecamatan);
    $('#edit_kabupaten').val(item.kabupaten);
    $('#edit_provinsi').val(item.provinsi);
    $('#edit_kode_pos').val(item.kode_pos);
    $('#edit_no_whatsapp').val(item.no_whatsapp);

    // Informasi paket
    $('#edit_paket').val(item.paket?.nama_paket ?? '');
    $('#edit_harga').val(item.paket?.harga ?? '');
    $('#edit_masa_pembayaran').val(item.paket?.masa_pembayaran ?? '');
    $('#edit_kecepatan').val(item.paket?.kecepatan ?? '');

    // Tanggal
    $('#edit_tanggal_mulai').val(item.tanggal_mulai);
    $('#edit_tanggal_berakhir').val(item.tanggal_berakhir);

    // Catatan & status
    $('#edit_catatan').val(item.catatan ?? '');

    // Tampilkan modal
    $('#modalEditTagihan').modal('show');
});


flatpickr("#edittanggal_mulai", {
    dateFormat: "Y-m-d",
    defaultDate: new Date(),
    onChange: function(selectedDates, dateStr, instance) {
        const tanggalMulai = selectedDates[0];
        const masaPembayaran = parseInt($('#masa_pembayaran').val()) || 0;
        if (tanggalMulai && masaPembayaran) {
            const tanggalBerakhir = new Date(tanggalMulai);
            tanggalBerakhir.setDate(tanggalMulai.getDate() + masaPembayaran);
            flatpickr("#tanggal_berakhir").setDate(tanggalBerakhir);
        }
    }
});

flatpickr("#edit_tanggal_berakhir", {
    dateFormat: "Y-m-d",
});



$(document).on('click', '.btn-edit-tagihan', function() {
    const item = $(this).data('item');

    $('#modalEditTagihan form').attr('action', `/dashboard/admin/tagihan/${item.id}`);
    $('#modalEditTagihan [name="pelanggan_id"]').val(item.pelanggan_id);
    $('#modalEditTagihan [name="paket_id"]').val(item.paket?.id ?? '');
    $('#modalEditTagihan [name="tanggal_mulai"]').val(item.tanggal_mulai);
    $('#modalEditTagihan [name="tanggal_berakhir"]').val(item.tanggal_berakhir);
    $('#modalEditTagihan [name="catatan"]').val(item.catatan ?? '');
    $('#modalEditTagihan [name="status_pembayaran"]').val(item.status_pembayaran ?? 'belum bayar');

    $('#modalEditTagihan').modal('show');
});



});



</script>



@endsection


@section('content')


<!-- Daftar Tagihan -->
<div class="card mt-4">
  <div class="card-header border-bottom">
    <div class="d-flex justify-content-between align-items-center row pt-4">
      <div class="col-md-6"><h5 class="mb-0">Daftar Tagihan Lunas</h5></div>

    </div>

 <div class="row g-3 mt-3">
  <div class="col-md-3">
    <select id="statusPembayaranFilter" class="form-select">
      <option value="">Semua Status</option>
      <option value="lunas">Sudah Lunas</option>
      <option value="belum bayar">Belum Bayar</option>
    </select>
  </div>

  <div class="col-md-3">
    <select id="kabupatenFilter" class="form-select">
      <option value="">Semua Kabupaten</option>
      @foreach($kabupatenList as $kab)
        <option value="{{ strtolower($kab) }}">{{ $kab }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-3">
    <select id="kecamatanFilter" class="form-select">
      <option value="">Semua Kecamatan</option>
      @foreach($kecamatanList as $kec)
        <option value="{{ strtolower($kec) }}">{{ $kec }}</option>
      @endforeach
    </select>
  </div>
</div>

  </div>
  <div class="card-datatable table-responsive">
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
      <th>Kwitansi</th>
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
    <a href="{{ asset('storage/' . $item['bukti_pembayaran']) }}" target="_blank">
      <img src="{{ asset('storage/' . $item['bukti_pembayaran']) }}"
           alt="Bukti Pembayaran"
           title="Bukti Pembayaran {{ $item['nama_lengkap'] }}"
           style="width:50px; height:auto; object-fit:cover; border-radius:4px;">
    </a>
  @else
    -
  @endif
</td>
<td>
@if(!empty($item['kwitansi']))
    <a href="{{$item['kwitansi']}}"
       target="_blank"
       class="btn btn-primary btn-sm">
       Lihat Kwitansi
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
      <!-- Tambahkan enctype multipart/form-data -->
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
              <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tagihan['tanggal_mulai'] }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tanggal Jatuh Tempo</label>
              <input type="date" name="tanggal_berakhir" class="form-control" value="{{ $tagihan['tanggal_berakhir'] }}" required>
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





@endsection
