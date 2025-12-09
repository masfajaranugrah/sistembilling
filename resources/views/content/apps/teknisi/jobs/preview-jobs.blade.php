@extends('layouts/layoutMaster')

@section('title', 'Preview Ticket - Teknisi')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preview foto teknisi
    const preview = document.getElementById('preview');
    if(preview && preview.src === '') preview.style.display = 'none';
});
</script>
@endsection

@section('content')
<div class="container my-3">

    <!-- Info Ticket -->
    <div class="card mb-3">
        <div class="card-header bg-light"><strong>Detail Tickets</strong></div>
        <div class="card-body">
            <div class="mb-2">
                <label>Nama Customers</label>
                <input type="text" class="form-control" value="{{ $ticket->pelanggan->nama_lengkap }}" disabled>
            </div>
            <div class="mb-2">
                <label>No. Telepon</label>
                <input type="text" class="form-control" value="{{ $ticket->phone }}" disabled>
            </div>
            <div class="mb-2">
                <label>Link Lokasi</label>
                <input type="text" class="form-control" value="{{ $ticket->location_link }}" disabled>
            </div>
            <div class="mb-2">
                <label>Kategori</label>
                <input type="text" class="form-control" value="{{ ucfirst($ticket->category) }}" disabled>
            </div>
            <div class="mb-2">
                <label>Deskripsi Kendala</label>
                <textarea class="form-control" rows="3" disabled>{{ $ticket->issue_description }}</textarea>
            </div>
            <div class="mb-2">
                <label>Prioritas</label>
                <input type="text" class="form-control" value="{{ ucfirst($ticket->priority) }}" disabled>
            </div>
        </div>
    </div>

    <!-- Status & Bukti Teknisi -->
    <div class="card mb-3">
        <div class="card-header bg-light"><strong>Status & Bukti Teknisi</strong></div>
        <div class="card-body">
            <div class="mb-3">
                <label>Status Ticket</label>
                <input type="text" class="form-control" value="{{ ucfirst($ticket->status) }}" disabled>
            </div>

            <div class="mb-3">
                <label>Upload Bukti / Foto</label>
                <div class="mt-2 text-center">
                    @if($ticket->technician_attachment)
                        <img id="preview" src="{{ asset('storage/' . $ticket->technician_attachment) }}" class="img-fluid rounded" style="max-height:200px;">
                    @else
                        <img id="preview" src="#" class="img-fluid rounded" style="display:none; max-height:200px;">
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label>Catatan Teknisi</label>
                <textarea class="form-control" rows="2" disabled>{{ $ticket->technician_note }}</textarea>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('jobs.approved') }}" class="btn btn-outline-secondary btn-lg">Kembali</a>
            </div>
        </div>
    </div>

    <!-- Info Tambahan -->
    <div class="card mb-3">
        <div class="card-header bg-light"><strong>Info Tambahan</strong></div>
        <div class="card-body">
            <p><strong>Ditugaskan untuk:</strong> {{ $ticket->user->name ?? '-' }}</p>
            <p><strong>Dibuat oleh:</strong> {{ $ticket->creator->name ?? '-' }}</p>
            <p><strong>Tanggal Dibuat:</strong> {{ $ticket->created_at->format('d M Y') }}</p>
        </div>
    </div>

</div>
@endsection
