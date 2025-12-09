@extends('layouts/layoutMaster')

@section('title', 'Update Progress Ticket - Teknisi')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const attachmentInput = document.getElementById('attachment');
    const preview = document.getElementById('preview');

    attachmentInput?.addEventListener('change', function(event) {
        const file = this.files[0];
        if (!file) {
            preview.style.display = 'none';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.src = e.target.result;

            img.onload = function() {
                const canvas = document.createElement('canvas');
                const maxWidth = 1024, maxHeight = 1024;
                let width = img.width, height = img.height;

                if (width > height) {
                    if (width > maxWidth) {
                        height *= maxWidth / width;
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width *= maxHeight / height;
                        height = maxHeight;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function(blob) {
                    const newFile = new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(newFile);
                    attachmentInput.files = dataTransfer.files;

                    preview.src = URL.createObjectURL(newFile);
                    preview.style.display = 'block';
                }, 'image/jpeg', 0.7);
            }
        }
        reader.readAsDataURL(file);
    });
});
</script>
@endsection

@section('content')
<div class="container my-4">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <h4 class="fw-bold mb-4 text-center text-primary">
        <i class="ri-tools-line me-2"></i>Update Progress Ticket
      </h4>

      <form action="{{ route('jobs.update', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="row g-4">
        @csrf
        @method('PUT')

        <!-- Kiri: Detail Ticket -->
        <div class="col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
              <strong><i class="ri-information-line me-1"></i>Detail Ticket</strong>
            </div>
            <div class="card-body">
              <div class="mb-2">
                <label class="form-label fw-semibold">Nama Customer</label>
                <input type="text" class="form-control" value="{{ $ticket->pelanggan->nama_lengkap }}" disabled>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">No. Telepon</label>
                <input type="text" class="form-control" value="{{ $ticket->phone }}" disabled>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Link Lokasi</label>
                <input type="text" class="form-control" value="{{ $ticket->location_link }}" disabled>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Kategori</label>
                <input type="text" class="form-control" value="{{ ucfirst($ticket->category) }}" disabled>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Deskripsi Kendala</label>
                <textarea class="form-control" rows="3" disabled>{{ $ticket->issue_description }}</textarea>
              </div>
              <div>
                <label class="form-label fw-semibold">Prioritas</label>
                <input type="text" class="form-control" value="{{ ucfirst($ticket->priority) }}" disabled>
              </div>
            </div>
          </div>
        </div>

        <!-- Kanan: Update Progress -->
        <div class="col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
              <strong><i class="ri-upload-cloud-line me-1"></i>Update Progress</strong>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Status Ticket</label>
                <select name="status" class="form-select" required>
                  <option value="progress" {{ $ticket->status == 'progress' ? 'selected' : '' }}>On Progress</option>
                  <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="finished" {{ $ticket->status == 'finished' ? 'selected' : '' }}>Finished</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Upload Bukti (Opsional)</label>
                <input type="file" class="form-control" name="technician_attachment" id="attachment" accept="image/*">
                <div class="mt-3 text-center">
                  @if($ticket->technician_attachment)
                    <img id="preview" src="{{ asset('storage/' . $ticket->technician_attachment) }}" class="img-fluid rounded shadow-sm" style="max-height:200px;">
                  @else
                    <img id="preview" src="#" class="img-fluid rounded shadow-sm" style="display:none; max-height:200px;">
                  @endif
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Catatan Teknisi</label>
                <textarea name="technician_note" class="form-control" rows="3" placeholder="Tuliskan catatan tambahan...">{{ old('technician_note', $ticket->technician_note) }}</textarea>
              </div>

              <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                  <i class="ri-check-line me-1"></i>Update Progress
                </button>
                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary btn-lg">
                  <i class="ri-arrow-go-back-line me-1"></i>Batal
                </a>
              </div>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection
