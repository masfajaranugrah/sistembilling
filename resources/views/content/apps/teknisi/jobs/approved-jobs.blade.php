@extends('layouts/layoutMaster')

@section('title', 'Daftar Ticket Teknisi')

@section('content')
<div class="container my-3">

    @php
        $hasTickets = false;
        foreach(['urgent', 'medium', 'low'] as $level) {
            if(isset($tickets[$level]) && $tickets[$level]->count()) {
                $hasTickets = true;
                break;
            }
        }
    @endphp

    @if($hasTickets)
        @foreach(['urgent', 'medium', 'low'] as $level)
            @if(isset($tickets[$level]) && $tickets[$level]->count())
                <h4 class="mb-3 text-capitalize">
                    {{ ucfirst($level) }}
                    <span class="badge bg-secondary">{{ $tickets[$level]->count() }} Job(s)</span>
                </h4>

                <div class="row mb-4">
                    @foreach($tickets[$level] as $ticket)
                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                            <div class="card shadow-sm border-3
                                @if($level == 'urgent') border-danger
                                @elseif($level == 'medium') border-warning
                                @else border-success @endif">

                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong>{{ $ticket->customer_name }}</strong>
                                    <span class="badge
                                        @if($ticket->status == 'finished') bg-success
                                        @elseif($ticket->status == 'progress') bg-primary
                                        @elseif($ticket->status == 'pending') bg-warning text-dark
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </div>

                                <div class="card-body">
                                    <p><strong>Masalah:</strong> {{ $ticket->issue_description }}</p>

                                    @if($ticket->additional_note)
                                        <p><strong>Catatan:</strong> {{ $ticket->additional_note }}</p>
                                    @endif

                                    <p><strong>Alamat:</strong>
                                        @if($ticket->location_link)
                                            <a href="{{ $ticket->location_link }}" target="_blank" class="text-decoration-none">
                                                Lihat Lokasi
                                            </a>
                                        @else
                                            Tidak Ada
                                        @endif
                                    </p>

                                    <p><strong>Ditugaskan untuk:</strong> {{ $ticket->user->name ?? '-' }}</p>
                                    <p><strong>Dibuat oleh:</strong> {{ $ticket->creator->name ?? '-' }}</p>
                                    <p><strong>Tanggal:</strong> {{ $ticket->created_at->format('d M Y') }}</p>

                                    @if($ticket->attachment)
                                        <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank" class="btn btn-sm btn-info w-100 mb-2">
                                            Lihat Foto
                                        </a>
                                    @endif

                                    {{-- Tombol lihat detail saja --}}
                                    <a href="{{ route('jobs.show', $ticket->id) }}" class="btn btn-sm btn-warning w-100">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @else
        <div class="alert alert-info text-center">
            Tidak ada job terselesaikan saat ini.
        </div>
    @endif

</div>
@endsection
