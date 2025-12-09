<div class="card-datatable table-responsive">
    <table class="datatables-users table align-middle">
        <thead class="table-light text-center">
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Lembur Mulai</th>
                <th>Lembur Selesai</th>
                <th>Total Jam Kerja</th>
                <th>Total Lembur</th>
                <th>Lokasi Masuk</th>
                <th>Lokasi Pulang</th>
                <th>Foto Absensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensi as $i => $absen)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $absen->user->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($absen->date)->timezone('Asia/Jakarta')->format('d M Y') }}</td>
                <td>{{ $absen->time_in ? \Carbon\Carbon::parse($absen->time_in)->format('H:i') : '-' }}</td>
                <td>{{ $absen->time_out ? \Carbon\Carbon::parse($absen->time_out)->format('H:i') : '-' }}</td>
                <td>{{ $absen->lembur_in ? \Carbon\Carbon::parse($absen->lembur_in)->format('H:i') : '-' }}</td>
                <td>{{ $absen->lembur_out ? \Carbon\Carbon::parse($absen->lembur_out)->format('H:i') : '-' }}</td>

                {{-- Hitung total jam kerja --}}
                <td>
                    @if($absen->time_in && $absen->time_out)
                        @php
                            $in = \Carbon\Carbon::parse($absen->time_in);
                            $out = \Carbon\Carbon::parse($absen->time_out);
                            $durasi = $in->diff($out);
                        @endphp
                        {{ $durasi->h }} jam {{ $durasi->i }} menit
                    @else
                        0 jam 0 menit
                    @endif
                </td>

                {{-- Hitung total lembur --}}
                <td>
                    @if($absen->lembur_in && $absen->lembur_out)
                        @php
                            $in = \Carbon\Carbon::parse($absen->lembur_in);
                            $out = \Carbon\Carbon::parse($absen->lembur_out);
                            $durasi = $in->diff($out);
                        @endphp
                        {{ $durasi->h }} jam {{ $durasi->i }} menit
                    @else
                        0 jam 0 menit
                    @endif
                </td>

                {{-- Lokasi --}}
                <td>
                    @if($absen->lat_in && $absen->lng_in)
                        <a href="https://www.google.com/maps?q={{ $absen->lat_in }},{{ $absen->lng_in }}" target="_blank">📍 Lihat</a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($absen->lat_out && $absen->lng_out)
                        <a href="https://www.google.com/maps?q={{ $absen->lat_out }},{{ $absen->lng_out }}" target="_blank">📍 Lihat</a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($absen->photo_in)
                        <a href="{{ asset('storage/'.$absen->photo_in) }}" target="_blank">
                            <img src="{{ asset('storage/'.$absen->photo_in) }}" alt="Foto Masuk"
                                 style="width:40px; height:40px; object-fit:cover; border-radius:5px;">
                        </a>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center text-muted">Belum ada data absensi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
