@extends('layouts/layoutMaster')

@section('title', 'Daftar Ticket Teknisi')

@section('content')
<div class="container my-4">

  <h4 class="fw-bold mb-4 text-center text-primary">
    <i class="ri-tools-line me-2"></i>Daftar Ticket Teknisi
  </h4>

  <!-- 🔘 Filter Buttons -->
  <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
    @php
        $levels = [
            'urgent' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
        ];
    @endphp

    @foreach($levels as $level => $color)
      <button
        class="btn btn-outline-{{ $color }} position-relative filter-btn"
        data-level="{{ $level }}"
      >
        {{ ucfirst($level) }}
        @if(isset($tickets[$level]) && $tickets[$level]->count() > 0)
          <span
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-{{ $color }}">
            {{ $tickets[$level]->count() }}
          </span>
        @endif
      </button>
    @endforeach

    <button class="btn btn-outline-secondary filter-btn" data-level="all">
      Semua
    </button>
  </div>

  <!-- 🔘 Ticket Cards -->
  <div id="ticket-container">
    @php
        $hasTickets = false;
        foreach($levels as $level => $color) {
            if(isset($tickets[$level]) && $tickets[$level]->count()) {
                $hasTickets = true;
                break;
            }
        }
    @endphp

    @if($hasTickets)
      @foreach($levels as $level => $color)
        @if(isset($tickets[$level]) && $tickets[$level]->count())
          <div class="ticket-group" data-level="{{ $level }}">
            <h5 class="mb-3 text-capitalize text-{{ $color }}">
              <i class="ri-flag-2-fill me-1"></i>{{ ucfirst($level) }}
              <span class="badge bg-light text-dark">{{ $tickets[$level]->count() }} Ticket(s)</span>
            </h5>

            <div class="row">
              @foreach($tickets[$level] as $ticket)
                <div class="col-12 col-md-6 col-lg-4 mb-4 ticket-card" data-ticket-id="{{ $ticket->id }}">
                  <div class="card border-3 shadow-sm border-{{ $color }} h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <strong>{{ $ticket->pelanggan->nama_lengkap }}</strong>
                      <span class="badge
                        @if($ticket->status == 'finished') bg-success
                        @elseif($ticket->status == 'progress') bg-primary
                        @elseif($ticket->status == 'pending') bg-warning text-dark
                        @else bg-secondary @endif">
                        {{ ucfirst($ticket->status) }}
                      </span>
                    </div>
                    <div class="card-body">
                      <p><strong>Masalah:</strong> {{ \Illuminate\Support\Str::limit($ticket->issue_description, 60) }}</p>

                      @if($ticket->additional_note)
                        <p><strong>Catatan:</strong> {{ \Illuminate\Support\Str::limit($ticket->additional_note, 60) }}</p>
                      @endif

                      @if($ticket->phone)
                        <p>
                          <strong>WA:</strong>
                          <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->phone) }}" target="_blank" class="text-success text-decoration-none">
                            <i class="bi bi-whatsapp me-1"></i>{{ $ticket->phone }}
                          </a>
                        </p>
                      @endif

                      <p><strong>Alamat:</strong>
                        @if($ticket->location_link)
                          <a href="{{ $ticket->location_link }}" target="_blank" class="text-decoration-none">
                            <i class="bi bi-geo-alt"></i> Lihat lokasi
                          </a>
                        @else
                          Tidak Ada
                        @endif
                      </p>

                      <p><strong>Teknisi:</strong> {{ $ticket->user->name ?? '-' }}</p>
                      <p><strong>Dibuat:</strong> {{ $ticket->created_at->format('d M Y') }}</p>

                      @if($ticket->attachment)
                        <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info w-100 mb-2">
                          <i class="ri-image-line me-1"></i>Lihat Foto
                        </a>
                      @endif

                      {{-- Tombol status cepat --}}
                      @if(in_array($ticket->status, ['pending', 'assigned']))
                        <form action="{{ route('jobs.autoUpdateStatus', $ticket->id) }}" method="POST" class="mb-1">
                          @csrf
                          @method('PATCH')
                          <input type="hidden" name="status" value="progress">
                          <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="ri-play-line me-1"></i> Mulai Pengerjaan
                          </button>
                        </form>
                      @elseif($ticket->status === 'progress')
                        <form action="{{ route('jobs.autoUpdateStatus', $ticket->id) }}" method="POST" class="mb-1">
                          @csrf
                          @method('PATCH')
                          <input type="hidden" name="status" value="finished">
                          <button type="submit" class="btn btn-sm btn-success w-100">
                            <i class="ri-check-line me-1"></i> Selesai
                          </button>
                        </form>
                      @endif

                      <a href="{{ route('jobs.edit', $ticket->id) }}" class="btn btn-sm btn-warning w-100">
                        <i class="ri-edit-line me-1"></i>Edit Detail
                      </a>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    @else
      <div class="alert alert-info text-center">
        Tidak ada job saat ini.
      </div>
    @endif
  </div>
</div>

{{-- 🔘 Script Filter --}}
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
 <script>
document.addEventListener('DOMContentLoaded', function() {
  // Filter buttons
  const buttons = document.querySelectorAll('.filter-btn');
  const groups = document.querySelectorAll('.ticket-group');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const level = btn.dataset.level;
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      groups.forEach(group => {
        if (level === 'all' || group.dataset.level === level) {
          group.style.display = '';
        } else {
          group.style.display = 'none';
        }
      });
    });
  });

  // 🔔 Realtime Notifikasi via Pusher
  const userId = "{{ auth()->id() }}";
  Pusher.logToConsole = false;

  const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
      cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
      forceTLS: true
  });

  const channel = pusher.subscribe('private-jobs.' + userId);
  channel.bind('App\\Events\\TicketCreated', function(data) {
      console.log('Ticket baru:', data);
      // Tambah suara
      new Audio('/sounds/notification.mp3').play();

      // Tambah kartu ticket baru ke container
      const container = document.getElementById('ticket-container');
      const cardHtml = `
      <div class="col-12 col-md-6 col-lg-4 mb-4 ticket-card">
        <div class="card border-3 shadow-sm border-warning h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>${data.customer_name}</strong>
            <span class="badge bg-warning text-dark">${data.status}</span>
          </div>
          <div class="card-body">
            <p><strong>Masalah:</strong> ${data.issue_description}</p>
            <p><strong>Prioritas:</strong> ${data.priority}</p>
          </div>
        </div>
      </div>`;
      container.insertAdjacentHTML('afterbegin', cardHtml);
  });
});
</script>

<style>
.filter-btn.active {
  color: #fff !important;
  font-weight: 600;
}
.filter-btn[data-level="urgent"].active { background-color: #dc3545; border-color: #dc3545; }
.filter-btn[data-level="medium"].active { background-color: #ffc107; border-color: #ffc107; color: #212529 !important; }
.filter-btn[data-level="low"].active { background-color: #198754; border-color: #198754; }
.filter-btn[data-level="all"].active { background-color: #6c757d; border-color: #6c757d; }
</style>

@endsection
