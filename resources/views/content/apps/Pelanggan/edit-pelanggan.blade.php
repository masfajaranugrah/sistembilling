@extends('layouts/layoutMaster')

@section('title', 'Edit Pelanggan ')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/quill/typography.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const paketSelect = document.getElementById('paket_id');
    const hargaDisplay = document.getElementById('harga_display');
    const masaDisplay = document.getElementById('masa_display');
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalBerakhir = document.getElementById('tanggal_berakhir');
    const paketData = @json($paket);
    let manualOverride = false; // Flag jika admin ubah tanggal berakhir manual

    // Set nilai awal dari database
    tanggalMulai.value = "{{ old('tanggal_mulai', $pelanggan->tanggal_mulai) }}";
    tanggalBerakhir.value = "{{ old('tanggal_berakhir', $pelanggan->tanggal_berakhir) }}";

    // Flatpickr tanggal mulai
    flatpickr(tanggalMulai, {
        dateFormat: 'Y-m-d',
        defaultDate: tanggalMulai.value,
        onChange: function(selectedDates, dateStr) {
            const selected = paketData.find(p => p.id == paketSelect.value);
            if(selected && !manualOverride){
                updateTanggalBerakhir(selected.masa_pembayaran);
            }
        }
    });

    // Flatpickr tanggal berakhir (manual override diperbolehkan)
    flatpickr(tanggalBerakhir, {
        dateFormat: 'Y-m-d',
        defaultDate: tanggalBerakhir.value,
        allowInput: true, // admin bisa input manual
        onChange: function(selectedDates, dateStr){
            manualOverride = true; // jika diubah manual, jangan overwrite otomatis
        }
    });

    // Format tanggal
    const formatDate = (date) => {
        const d = new Date(date);
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${month}-${day}`;
    };

    // Update tanggal berakhir otomatis
    function updateTanggalBerakhir(masaHari) {
        if(!masaHari) return tanggalBerakhir._flatpickr.clear();
        const start = new Date(tanggalMulai.value);
        start.setDate(start.getDate() + parseInt(masaHari));
        tanggalBerakhir._flatpickr.setDate(formatDate(start));
    }

    // Tampilkan harga & masa aktif paket saat load page
    const initialPaket = paketData.find(p => p.id == "{{ old('paket_id', $pelanggan->paket_id) }}");
    if(initialPaket){
        hargaDisplay.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(initialPaket.harga);
        masaDisplay.textContent = `${initialPaket.masa_pembayaran} Hari`;
        if(!tanggalBerakhir.value){
            updateTanggalBerakhir(initialPaket.masa_pembayaran);
        }
        paketSelect.value = initialPaket.id;
    }

    // Event pilih paket
    paketSelect.addEventListener('change', () => {
        const selected = paketData.find(p => p.id == paketSelect.value);
        if(selected){
            hargaDisplay.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(selected.harga);
            masaDisplay.textContent = `${selected.masa_pembayaran} Hari`;
            if(!manualOverride){ // hanya update otomatis jika belum diubah manual
                updateTanggalBerakhir(selected.masa_pembayaran);
            }
        } else {
            hargaDisplay.textContent = '-';
            masaDisplay.textContent = '-';
            if(!manualOverride) tanggalBerakhir._flatpickr.clear();
        }
    });

    // Preview foto KTP
    const fotoInput = document.getElementById('foto_ktp');
    const preview = document.getElementById('preview_ktp');
    fotoInput.addEventListener('change', function() {
        const file = this.files[0];
        if(!file){
            preview.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

});

</script>

@endsection

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('pelanggan.update', $pelanggan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">Edit Pelanggan</h4>
                <p class="text-muted mb-0">Perbarui data pelanggan dengan lengkap dan benar.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('pelanggan') }}" class="btn btn-label-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Informasi Pelanggan -->
                <div class="card mb-6 shadow-sm">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Informasi Pelanggan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                   value="{{ old('nama_lengkap', $pelanggan->nama_lengkap) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="no_ktp" class="form-label">Nomor KTP</label>
                                <input type="text" class="form-control" id="no_ktp" name="no_ktp"
                                       value="{{ old('no_ktp', $pelanggan->no_ktp) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="no_whatsapp" class="form-label">Nomor WhatsApp</label>
                                <input type="text" class="form-control" id="no_whatsapp" name="no_whatsapp"
                                       value="{{ old('no_whatsapp', $pelanggan->no_whatsapp) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="no_telp" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="no_telp" name="no_telp"
                                       value="{{ old('no_telp', $pelanggan->no_telp) }}">
                            </div>
                        </div>

                        <h6 class="text-muted mt-4 mb-2">Alamat Lengkap</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="alamat_jalan" class="form-label">Jalan</label>
                                <input type="text" class="form-control" id="alamat_jalan" name="alamat_jalan"
                                       value="{{ old('alamat_jalan', $pelanggan->alamat_jalan) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="rt" class="form-label">RT</label>
                                <input type="text" class="form-control" id="rt" name="rt"
                                       value="{{ old('rt', $pelanggan->rt) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="rw" class="form-label">RW</label>
                                <input type="text" class="form-control" id="rw" name="rw"
                                       value="{{ old('rw', $pelanggan->rw) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="kode_pos" class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" id="kode_pos" name="kode_pos"
                                       value="{{ old('kode_pos', $pelanggan->kode_pos) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="desa" class="form-label">Desa</label>
                                <input type="text" class="form-control" id="desa" name="desa"
                                       value="{{ old('desa', $pelanggan->desa) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="kecamatan" class="form-label">Kecamatan</label>
                                <input type="text" class="form-control" id="kecamatan" name="kecamatan"
                                       value="{{ old('kecamatan', $pelanggan->kecamatan) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="kabupaten" class="form-label">Kabupaten</label>
                                <input type="text" class="form-control" id="kabupaten" name="kabupaten"
                                       value="{{ old('kabupaten', $pelanggan->kabupaten) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="provinsi" class="form-label">Provinsi</label>
                                <input type="text" class="form-control" id="provinsi" name="provinsi"
                                       value="{{ old('provinsi', $pelanggan->provinsi) }}">
                            </div>
                        </div>

                        <!-- Upload Foto KTP -->
                        <div class="mb-4">
                            <label for="foto_ktp" class="form-label">Upload Foto KTP</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="foto_ktp" name="foto_ktp" accept="image/*">
                                <label class="input-group-text" for="foto_ktp">Pilih</label>
                            </div>
                            <small class="text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                            <div class="mt-3">
                                @if($pelanggan->foto_ktp)
                                    <img id="preview_ktp" src="{{ asset('storage/' . $pelanggan->foto_ktp) }}" class="img-thumbnail" style="max-width:200px;">
                                @else
                                    <img id="preview_ktp" src="#" style="display:none;" class="img-thumbnail" style="max-width:200px;">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paket Internet -->
                <div class="card shadow-sm mb-6">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Paket Internet</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nomer_id" class="form-label">Nomor ID Pelanggan</label>
                                <input type="text" class="form-control" id="nomer_id" name="nomer_id"
                                       value="{{ old('nomer_id', $pelanggan->nomer_id) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="paket_id" class="form-label">Pilih Paket</label>
                                <select class="form-select" id="paket_id" name="paket_id" required>
                                    <option value="">-- Pilih Paket --</option>
                                    @foreach($paket as $p)
                                        <option value="{{ $p->id }}" {{ $pelanggan->paket_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_paket }} - {{ $p->kecepatan }} Mbps
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Harga</label>
                                <p id="harga_display" class="form-control bg-light">-</p>
                            </div>
                            <div class="col-md-6">
                                <label>Masa Aktif</label>
                                <p id="masa_display" class="form-control bg-light">-</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="text" class="form-control flatpickr" id="tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_berakhir" class="form-label">Tanggal Berakhir</label>
                                <input type="text" class="form-control flatpickr" id="tanggal_berakhir" name="tanggal_berakhir" required readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Pelanggan -->
                <div class="card shadow-sm mb-6">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Status Pelanggan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ $pelanggan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approve" {{ $pelanggan->status == 'approve' ? 'selected' : '' }}>Approve</option>
                                <option value="reject" {{ $pelanggan->status == 'reject' ? 'selected' : '' }}>Reject</option>
                            </select>
                            <small class="text-muted">Pilih status pelanggan sesuai keputusan admin.</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
