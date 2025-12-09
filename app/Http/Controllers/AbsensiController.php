<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    protected $standardHours = 8.0;

    public function getAll(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today('Asia/Jakarta')->toDateString());

        $absensi = Absensi::with('user')
            ->whereDate('date', $tanggal)
            ->orderBy('time_in', 'asc')
            ->get();

        // Jika request dari AJAX → kirim hanya tabelnya
        if ($request->ajax()) {
            return view('content.apps.Absensi._table-absensi', compact('absensi'))->render();
        }

        // Jika bukan AJAX → render halaman penuh
        return view('content.apps.Absensi.data-absensi', compact('absensi', 'tanggal'));
    }

    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today('Asia/Jakarta'); // pastikan pakai timezone Jakarta
        // $today = now()->setTimezone('Asia/Jakarta')->addDay()->toDateString();

        // Ambil absensi user hari ini
        $attendances = Absensi::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('time_in', 'asc')
            ->get();

        return view('content.apps.Absensi.absensi', compact('attendances'));
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        // gunakan WIB
        // $today = now()->setTimezone('Asia/Jakarta')->addDay()->toDateString();

        $today = now()->setTimezone('Asia/Jakarta')->toDateString();

        $request->validate([
            'action' => 'required|in:checkin,checkout,lembur_in,lembur_out',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $attendance = Absensi::firstOrNew([
            'user_id' => $user->id,
            'date' => $today,
        ]);

        // fungsi untuk menyimpan foto jika ada
        $savePhoto = function ($field) use ($request, $user) {
            if ($request->hasFile('photo')) {
                return $request->file('photo')->store("absensi/{$user->id}", 'public');
            }

            return null;
        };

        switch ($request->action) {
            case 'checkin':
                if ($attendance->time_in) {
                    return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
                }
                $attendance->time_in = now()->setTimezone('Asia/Jakarta');
                $attendance->lat_in = $request->latitude;
                $attendance->lng_in = $request->longitude;
                $attendance->photo_in = $savePhoto('photo_in') ?? $attendance->photo_in;
                $attendance->save();

                return back()->with('success', 'Check-in berhasil.');

            case 'checkout':
                if (! $attendance->time_in) {
                    return back()->with('error', 'Belum melakukan check-in hari ini.');
                }
                if ($attendance->time_out) {
                    return back()->with('error', 'Anda sudah melakukan check-out hari ini.');
                }
                $attendance->time_out = now()->setTimezone('Asia/Jakarta');
                $attendance->lat_out = $request->latitude;
                $attendance->lng_out = $request->longitude;
                $attendance->photo_out = $savePhoto('photo_out') ?? $attendance->photo_out;

                [$total, $overtime] = $this->calculateHours($attendance->time_in, $attendance->time_out);
                $attendance->total_hours = $total;
                $attendance->overtime_hours = $overtime;
                $attendance->save();

                return back()->with('success', 'Check-out berhasil.');

            case 'lembur_in':
                if ($attendance->lembur_in) {
                    return back()->with('error', 'Anda sudah mulai lembur hari ini.');
                }
                $attendance->lembur_in = now()->setTimezone('Asia/Jakarta');
                $attendance->lat_lembur_in = $request->latitude;
                $attendance->lng_lembur_in = $request->longitude;
                $attendance->photo_lembur_in = $savePhoto('photo_lembur_in') ?? $attendance->photo_lembur_in;
                $attendance->save();

                return back()->with('success', 'Lembur dimulai.');

            case 'lembur_out':
                if (! $attendance->lembur_in) {
                    return back()->with('error', 'Belum mulai lembur hari ini.');
                }
                if ($attendance->lembur_out) {
                    return back()->with('error', 'Anda sudah selesai lembur hari ini.');
                }
                $attendance->lembur_out = now()->setTimezone('Asia/Jakarta');
                $attendance->lat_lembur_out = $request->latitude;
                $attendance->lng_lembur_out = $request->longitude;
                $attendance->photo_lembur_out = $savePhoto('photo_lembur_out') ?? $attendance->photo_lembur_out;

                [$total, $overtime] = $this->calculateHours($attendance->lembur_in, $attendance->lembur_out);
                $attendance->overtime_hours = $overtime;
                $attendance->save();

                return back()->with('success', 'Lembur selesai.');

            default:
                return back()->with('error', 'Aksi tidak valid.');
        }
    }

    protected function calculateHours($timeIn, $timeOut)
    {
        $in = Carbon::parse($timeIn);
        $out = Carbon::parse($timeOut);

        // Jika keluar lebih awal dari masuk (lewat tengah malam)
        if ($out->lt($in)) {
            $out->addDay();
        }

        $totalMinutes = $out->diffInMinutes($in);
        $totalHours = round($totalMinutes / 60, 2);

        // lembur = total - 8 jam
        $overtimeMinutes = max(0, $totalMinutes - ($this->standardHours * 60));

        // Kembalikan total jam dan lembur dalam menit
        return [$totalMinutes, $overtimeMinutes];
    }
}
