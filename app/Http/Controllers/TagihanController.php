<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TagihanController extends Controller
{
    // get data json
    public function indexGetJson()
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan dengan status "belum bayar" beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $pelanggan = $item->pelanggan;
                $paket = $item->paket;

                return [
                    'id' => $item->id,
                    'nomer_id' => $pelanggan->nomer_id ?? '-',
                    'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                    'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                    'rt' => $pelanggan->rt ?? '-',
                    'rw' => $pelanggan->rw ?? '-',
                    'desa' => $pelanggan->desa ?? '-',
                    'kecamatan' => $pelanggan->kecamatan ?? '-',
                    'kabupaten' => $pelanggan->kabupaten ?? '-',
                    'provinsi' => $pelanggan->provinsi ?? '-',
                    'kode_pos' => $pelanggan->kode_pos ?? '-',

                    'paket' => [
                        'id' => $paket->id ?? null,
                        'nama_paket' => $paket->nama_paket ?? '-',
                        'harga' => $paket->harga ?? 0,
                        'kecepatan' => $paket->kecepatan ?? 0,
                        'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                        'durasi' => $paket->durasi ?? 0,
                    ],

                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_berakhir' => $item->tanggal_berakhir,
                    'status_pembayaran' => $item->status_pembayaran,
                    'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                    'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

        // Ambil list unik untuk dropdown
        $kabupatenList = $pelanggan->pluck('kabupaten')->unique()->values();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique()->values();

        // Statistik
        $totalCustomer = $pelanggan->count();
        $lunas = 0;
        $belumLunas = $tagihans->count();
        $totalPaket = $paket->count();

        return response()->json([
            'status' => true,
            'message' => 'Data tagihan berhasil diambil.',
            'data' => [
                'tagihans' => $tagihans,
                'pelanggan' => $pelanggan,
                'paket' => $paket,
                'statistics' => [
                    'total_customer' => $totalCustomer,
                    'lunas' => $lunas,
                    'belum_lunas' => $belumLunas,
                    'total_paket' => $totalPaket,
                ],
                'filters' => [
                    'kabupaten' => $kabupatenList,
                    'kecamatan' => $kecamatanList,
                ],
            ],
        ]);
    }

    public function getByIdJson($id)
    {
        // Ambil data tagihan berdasarkan ID + relasi pelanggan & paket
        $item = Tagihan::with(['pelanggan', 'paket'])->find($id);

        if (! $item) {
            return response()->json([
                'status' => false,
                'message' => 'Tagihan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $pelanggan = $item->pelanggan;
        $paket = $item->paket;

        // Bentuk JSON detail (sama dengan indexGetJson)
        $tagihanDetail = [
            'id' => $item->id,
            'nomer_id' => $pelanggan->nomer_id ?? '-',
            'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
            'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
            'rt' => $pelanggan->rt ?? '-',
            'rw' => $pelanggan->rw ?? '-',
            'desa' => $pelanggan->desa ?? '-',
            'kecamatan' => $pelanggan->kecamatan ?? '-',
            'kabupaten' => $pelanggan->kabupaten ?? '-',
            'provinsi' => $pelanggan->provinsi ?? '-',
            'kode_pos' => $pelanggan->kode_pos ?? '-',

            'paket' => [
                'id' => $paket->id ?? null,
                'nama_paket' => $paket->nama_paket ?? '-',
                'harga' => $paket->harga ?? 0,
                'kecepatan' => $paket->kecepatan ?? 0,
                'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                'durasi' => $paket->durasi ?? 0,
            ],

            'tanggal_mulai' => $item->tanggal_mulai,
            'tanggal_berakhir' => $item->tanggal_berakhir,
            'status_pembayaran' => $item->status_pembayaran,
            'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
            'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
            'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
            'catatan' => $item->catatan ?? '-',
        ];

        return response()->json([
            'status' => true,
            'message' => 'Detail tagihan berhasil diambil.',
            'data' => $tagihanDetail,
        ]);
    }




public function konfirmasiBayar(Request $request, $id)
{
$tagihan = Tagihan::with('pelanggan', 'paket')->findOrFail($id);


DB::beginTransaction();
try {
    // Upload bukti pembayaran (opsional)
    if ($request->hasFile('bukti_pembayaran')) {
        $file = $request->file('bukti_pembayaran');
        $path = $file->store('bukti_pembayaran', 'public');
        $tagihan->bukti_pembayaran = $path;
    }

    // Update status tagihan menjadi lunas
    $tagihan->status_pembayaran = 'lunas';
    $tagihan->tanggal_pembayaran = now();

    // Generate PDF kwitansi
    $pdf = Pdf::loadView('content.apps.pdf.kwitansi', ['tagihan' => $tagihan]);
    $filename = 'kwitansi-'.$tagihan->id.'.pdf';
    $pdfPath = 'kwitansi/'.$filename;
    Storage::disk('public')->put($pdfPath, $pdf->output());

    // Simpan path PDF ke field kwitansi
    $tagihan->kwitansi = $pdfPath;
    $tagihan->save();

    // Buat link publik PDF
    $pdfUrl = asset('storage/'.$pdfPath);

    // Buat record Income
    Income::create([
        'kode' => $this->getKode('penjualan'),
        'kategori' => 'penjualan',
        'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
        'keterangan' => 'Pembayaran paket '.$tagihan->paket->nama_paket.' dari '.$tagihan->pelanggan->nama_lengkap,
        'tanggal_masuk' => now(),
    ]);

    // ===== Kirim push notification sebelum return =====
    $pelanggan = $tagihan->pelanggan;
    if ($pelanggan && $pelanggan->webpushr_sid) {
        $end_point = 'https://api.webpushr.com/v1/notification/send/sid';

        $http_header = [
            'Content-Type: Application/Json',
            'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279', // ganti dengan API key Webpushr
            'webpushrAuthToken: 116294', // ganti dengan Auth Token Webpushr
        ];

        $req_data = [
            'title' => 'Terima Kasih',
            'message' => "Halo {$pelanggan->nama_lengkap}, pembayaran Anda sudah dikonfirmasi.",
            'target_url' => url('/'), // link ke halaman tagihan
            'sid' => $pelanggan->webpushr_sid,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Optional: log response untuk debug
    }
    // ===================================================

    DB::commit();

    return response()->json([
        'success' => true,
        'pdfUrl' => $pdfUrl,
        'message' => 'Pembayaran berhasil dikonfirmasi dan notifikasi terkirim!',
    ]);
} catch (\Exception $e) {
    DB::rollBack();

    return response()->json(['success' => false, 'message' => $e->getMessage()]);
}


}


 

    /**
     * Contoh fungsi helper untuk kirim WA (dummy)
     */
    private function sendWA($nomor, $pesan)
    {
        // TODO: implementasi request ke API WhatsApp
        // return true jika berhasil, false jika gagal
        return true;
    }

    public function index()
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::where('status', 'approve')
            ->whereDoesntHave('tagihans', function ($q) {
                $q->where('status_pembayaran', 'belum bayar');
            })->get();

        $paket = Paket::all();

        // Ambil semua tagihan dengan status "belum bayar" beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->where('status_pembayaran', 'belum bayar') // FILTER DISINI
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $pelanggan = $item->pelanggan;
                $paket = $item->paket;

                return [
                    'id' => $item->id,
                    'nomer_id' => $pelanggan->nomer_id ?? '-',
                    'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                    'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                    'rt' => $pelanggan->rt ?? '-',
                    'rw' => $pelanggan->rw ?? '-',
                    'desa' => $pelanggan->desa ?? '-',
                    'kecamatan' => $pelanggan->kecamatan ?? '-',
                    'kabupaten' => $pelanggan->kabupaten ?? '-',
                    'provinsi' => $pelanggan->provinsi ?? '-',
                    'kode_pos' => $pelanggan->kode_pos ?? '-',
                    'paket' => [
                        'id' => $paket->id ?? null,
                        'nama_paket' => $paket->nama_paket ?? '-',
                        'harga' => $paket->harga ?? 0,
                        'kecepatan' => $paket->kecepatan ?? 0,
                        'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                        'durasi' => $paket->durasi ?? 0,
                    ],
                    'tanggal_mulai' => $item->tanggal_mulai ?? null,
                    'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                    'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                    'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                    'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

        // Ambil list unik untuk filter dropdown
        $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

        // Statistik
        $totalCustomer = $pelanggan->count();
        $lunas = 0; // Karena kita hanya menampilkan "belum bayar"
        $belumLunas = $tagihans->count();
        $totalPaket = $paket->count();

        return view('content.apps.Tagihan.tagihan', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'
        ));
    }

    public function proses()
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan dengan status "belum bayar" beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->where('status_pembayaran', 'proses_verifikasi') // FILTER DISINI
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $pelanggan = $item->pelanggan;
                $paket = $item->paket;

                return [
                    'id' => $item->id,
                    'nomer_id' => $pelanggan->nomer_id ?? '-',
                    'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                    'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                    'rt' => $pelanggan->rt ?? '-',
                    'rw' => $pelanggan->rw ?? '-',
                    'desa' => $pelanggan->desa ?? '-',
                    'kecamatan' => $pelanggan->kecamatan ?? '-',
                    'kabupaten' => $pelanggan->kabupaten ?? '-',
                    'provinsi' => $pelanggan->provinsi ?? '-',
                    'kode_pos' => $pelanggan->kode_pos ?? '-',
                    'paket' => [
                        'id' => $paket->id ?? null,
                        'nama_paket' => $paket->nama_paket ?? '-',
                        'harga' => $paket->harga ?? 0,
                        'kecepatan' => $paket->kecepatan ?? 0,
                        'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                        'durasi' => $paket->durasi ?? 0,
                    ],
                    'tanggal_mulai' => $item->tanggal_mulai ?? null,
                    'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                    'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                    'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                    'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

        // Ambil list unik untuk filter dropdown
        $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

        // Statistik
        $totalCustomer = $pelanggan->count();
        $lunas = 0; // Karena kita hanya menampilkan "belum bayar"
        $belumLunas = $tagihans->count();
        $totalPaket = $paket->count();

        return view('content.apps.Tagihan.proses-tagihan', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'
        ));
    }

    public function lunas()
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan dengan status "belum bayar" beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->where('status_pembayaran', 'lunas')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $pelanggan = $item->pelanggan;
                $paket = $item->paket;

                // Buat URL kwitansi jika ada
                $kwitansiUrl = null;
                if (! empty($item->kwitansi)) {
                    $kwitansiUrl = url('/'.$item->kwitansi);
                }

                return [
                    'id' => $item->id,
                    'nomer_id' => $pelanggan->nomer_id ?? '-',
                    'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                    'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                    'rt' => $pelanggan->rt ?? '-',
                    'rw' => $pelanggan->rw ?? '-',
                    'desa' => $pelanggan->desa ?? '-',
                    'kecamatan' => $pelanggan->kecamatan ?? '-',
                    'kabupaten' => $pelanggan->kabupaten ?? '-',
                    'provinsi' => $pelanggan->provinsi ?? '-',
                    'kode_pos' => $pelanggan->kode_pos ?? '-',
                    'paket' => [
                        'id' => $paket->id ?? null,
                        'nama_paket' => $paket->nama_paket ?? '-',
                        'harga' => $paket->harga ?? 0,
                        'kecepatan' => $paket->kecepatan ?? 0,
                        'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                        'durasi' => $paket->durasi ?? 0,
                    ],
                    'tanggal_mulai' => $item->tanggal_mulai ?? null,
                    'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                    'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                    'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                    'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                    'kwitansi' => $kwitansiUrl, // <-- tambahkan ini
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

        // Ambil list unik untuk filter dropdown
        $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

        // Statistik
        $totalCustomer = $pelanggan->count();
        $lunas = 0; // Karena kita hanya menampilkan "belum bayar"
        $belumLunas = $tagihans->count();
        $totalPaket = $paket->count();

        return view('content.apps.Tagihan.tagihan-lunas', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'
        ));
    }

    /**
     * Update data tagihan
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date',
            'catatan' => 'nullable|string',
            'paket_id' => 'required|exists:pakets,id',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'kwitansi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $tagihan = Tagihan::findOrFail($id);
        $paket = Paket::findOrFail($request->paket_id);

        // Parse tanggal
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalBerakhir = $request->tanggal_berakhir
            ? \Carbon\Carbon::parse($request->tanggal_berakhir)
            : $tanggalMulai->copy()->addDays($paket->masa_pembayaran);

        // Handle bukti_pembayaran
        if ($request->hasFile('bukti_pembayaran')) {
            // Hapus file lama jika ada
            if ($tagihan->bukti_pembayaran && Storage::disk('public')->exists($tagihan->bukti_pembayaran)) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }

            // Simpan file baru
            $tagihan->bukti_pembayaran = $request->file('bukti_pembayaran')
                ->store('bukti_pembayaran', 'public');
        }

        // Handle kwitansi jika ada
        if ($request->hasFile('kwitansi')) {
            if ($tagihan->kwitansi && Storage::disk('public')->exists($tagihan->kwitansi)) {
                Storage::disk('public')->delete($tagihan->kwitansi);
            }

            $tagihan->kwitansi = $request->file('kwitansi')
                ->store('kwitansi', 'public');
        }

        // Update field lainnya
        $tagihan->update([
            'paket_id' => $request->paket_id,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Tagihan berhasil diperbarui!');
    }


public function store(Request $request)
{
    $request->validate([
        'pelanggan_id' => 'required|exists:pelanggans,id',
        'paket_id' => 'required|exists:pakets,id',
        'tanggal_mulai' => 'required|date',
        'tanggal_berakhir' => 'nullable|date',
        'catatan' => 'nullable|string',
    ]);

    $paket = Paket::findOrFail($request->paket_id);
    $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
    $tanggalBerakhir = $request->tanggal_berakhir
        ? \Carbon\Carbon::parse($request->tanggal_berakhir)
        : $tanggalMulai->copy()->addDays($paket->masa_pembayaran);

    $tagihan = Tagihan::create([
        'pelanggan_id' => $request->pelanggan_id,
        'paket_id' => $request->paket_id,
        'harga' => $paket->harga,
        'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
        'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
        'status_pembayaran' => 'belum bayar',
        'catatan' => $request->catatan,
    ]);

    $pelanggan = Pelanggan::find($request->pelanggan_id);

    // Kirim push notification jika SID tersedia
    if ($pelanggan && $pelanggan->webpushr_sid) {
        $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

        $payload = [
            'title' => 'Tagihan Baru',
            'message' => "Halo {$pelanggan->nama}, tagihan baru sudah ditambahkan.",
            'target_url' => url('/'),
            'sid' => $pelanggan->webpushr_sid,
        ];

        $headers = [
            'Content-Type: application/json',
            'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
            'webpushrAuthToken: 116294',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

       

        curl_close($ch);
    }

    return redirect()->back()->with('success', 'Tagihan berhasil ditambahkan dan notifikasi terkirim!');
}



    private function sendOneSignalNotification($playerId, $title, $message)
    {
        $content = [
            'en' => $message,
        ];

        $fields = [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => [$playerId],
            'headings' => ['en' => $title],
            'contents' => $content,
        ];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.env('ONESIGNAL_REST_API_KEY'),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Generate kode otomatis per kategori
     */
    private function getKode($kategori)
    {
        return match (strtolower($kategori)) {
            'internet' => '01',
            'penjualan' => '02',
            'piutang' => '03',
            default => 'O4', // DLL atau kategori custom
        };

    }

    // ? Update tagihan
    public function updateStatus($id)
    {
        $tagihan = \App\Models\Tagihan::with('pelanggan', 'paket')->find($id);

        if (! $tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        // Update status tagihan
        $tagihan->status_pembayaran = 'lunas';
        $tagihan->tanggal_pembayaran = now();
        $tagihan->save();

        // Buat data Income baru
        Income::create([
            'kode' => $this->getCode(), // atau gunakan helper getKode() jika mau auto-generate
            'kategori' => 'Tagihan',
            'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
            'keterangan' => 'Pembayaran paket '.$tagihan->paket->nama_paket.' dari '.$tagihan->pelanggan->nama_lengkap,
            'tanggal_masuk' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui menjadi lunas dan income tercatat.',
        ]);
    }

    // ? Hapus tagihan
    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();

        return redirect()->back()->with('success', '??? Tagihan berhasil dihapus!');
    }

    public function massStore(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $pelanggan = Pelanggan::with('paket')
            ->whereDoesntHave('tagihans', function ($q) {
                $q->where('status_pembayaran', 'belum bayar');
            })
            ->get();
        if ($pelanggan->count() == 0) {
            return back()->with('error', 'Tidak ada pelanggan untuk dibuatkan tagihan.');
        }

        DB::beginTransaction();

        try {
            foreach ($pelanggan as $p) {
                Tagihan::create([
                    'pelanggan_id' => $p->id,
                    'paket_id' => $p->paket_id,
                    'nama_lengkap' => $p->nama_lengkap,
                    'nomer_id' => $p->nomer_id,
                    'no_whatsapp' => $p->no_whatsapp,

                    // Alamat
                    'alamat_jalan' => $p->alamat_jalan,
                    'rt' => $p->rt,
                    'rw' => $p->rw,
                    'desa' => $p->desa,
                    'kecamatan' => $p->kecamatan,
                    'kabupaten' => $p->kabupaten,
                    'provinsi' => $p->provinsi,
                    'kode_pos' => $p->kode_pos,

                    // Paket
                    'harga' => $p->paket->harga,
                    'kecepatan' => $p->paket->kecepatan,
                    'masa_pembayaran' => $p->paket->masa_pembayaran,

                    // Tanggal
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_berakhir' => $request->tanggal_berakhir,

                    // Default
                    'status_pembayaran' => 'belum bayar',
                ]);
            }

            DB::commit();

            return back()->with('success', 'Tagihan berhasil dibuat untuk semua pelanggan!');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat tagihan massal: '.$th->getMessage());
        }
    }
}
