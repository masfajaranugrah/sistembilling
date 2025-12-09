@extends('layouts/layoutMaster')

@section('title', 'Edit Ticket - CS')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preview foto CS
    const csAttachmentInput = document.getElementById('cs_attachment');
    const csPreview = document.getElementById('cs_preview');
    csAttachmentInput?.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) {
            csPreview.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            csPreview.src = e.target.result;
            csPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    // Inisialisasi Select2
    $('#priority, #category, #user_id, #status').select2({ width: '100%' });
});
</script>
@endsection

@section('content')
<div class="app-cs-ticket">
    <form action="{{ route('tickets.updates', $ticket->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div>
                <h4 class="mb-1">Edit Ticket</h4>
                <p class="text-muted mb-0">Perbarui data ticket sesuai kebutuhan.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('tickets.indexs') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>

        <div class="row g-4">
            <!-- Informasi Customer -->
           <div class="col-12 col-lg-6">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Informasi Customer</h5>
        </div>
        <div class="card-body">
            <!-- Pilih Pelanggan -->
       <div class="mb-3">
    <label class="form-label">Pilih Pelanggan</label>
    <select id="pelangganSelect" class="form-select select2" disabled>
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
                {{ $ticket->pelanggan_id == $p->id ? 'selected' : '' }}
            >
                {{ $p->nomer_id }} - {{ $p->nama_lengkap }}
            </option>
        @endforeach
    </select>
    <!-- Hidden input tetap diperlukan agar data pelanggan_id dikirim -->
    <input type="hidden" name="pelanggan_id" id="pelanggan_id" value="{{ $ticket->pelanggan_id }}">
</div>


            <div class="mb-3">
                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                <input type="text" id="nama_pelanggan" class="form-control" readonly value="{{ optional($ticket->pelanggan)->nama_lengkap }}">
            </div>

            <div class="mb-3">
                <label for="alamat_pelanggan" class="form-label">Alamat Pelanggan</label>
                <textarea id="alamat_pelanggan" class="form-control" rows="2" readonly>
{{ optional($ticket->pelanggan)->alamat_jalan }}, RT {{ optional($ticket->pelanggan)->rt }}/RW {{ optional($ticket->pelanggan)->rw }}, {{ optional($ticket->pelanggan)->desa }}, {{ optional($ticket->pelanggan)->kecamatan }}
                </textarea>
            </div>

            <div class="mb-3">
                <label for="paket_pelanggan" class="form-label">Paket Internet</label>
                <input type="text" id="paket_pelanggan" class="form-control" readonly value="{{ optional($ticket->pelanggan->paket)->nama_paket }}">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">No. Telepon / WhatsApp</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ $ticket->phone }}">
            </div>

            <div class="mb-3">
                <label for="location_link" class="form-label">Link Lokasi (Google Maps / WA)</label>
                <input type="url" class="form-control" id="location_link" name="location_link" value="{{ $ticket->location_link }}">
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Kategori Kendala</label>
                <select class="form-select" id="category" name="category">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="internet_down" {{ $ticket->category == 'internet_down' ? 'selected' : '' }}>Internet Down</option>
                    <option value="modem_error" {{ $ticket->category == 'modem_error' ? 'selected' : '' }}>Modem Error</option>
                    <option value="kabel_putus" {{ $ticket->category == 'kabel_putus' ? 'selected' : '' }}>Kabel Putus</option>
                    <option value="lainnya" {{ $ticket->category == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="issue_description" class="form-label">Deskripsi Kendala</label>
                <textarea class="form-control" id="issue_description" name="issue_description" rows="3" required>{{ $ticket->issue_description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="additional_note" class="form-label">Catatan Tambahan</label>
                <textarea class="form-control" id="additional_note" name="additional_note" rows="2">{{ $ticket->additional_note }}</textarea>
            </div>
        </div>
    </div>
</div>

            <!-- Assignment & Status -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Assignment & Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioritas</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Assign User / Teknisi</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $ticket->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status Ticket</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="assigned" {{ $ticket->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                 <option value="approved" {{ $ticket->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $ticket->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>



                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">Simpan Perubahan</button>
                            <a href="{{ route('tickets.indexs') }}" class="btn btn-outline-secondary btn-lg">Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
