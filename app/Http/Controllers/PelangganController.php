<?php

namespace App\Http\Controllers;

use App\Imports\PelangganImport;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PelangganController extends Controller
{
    // index

    // public function getData()
    // {
    //     // Ambil hanya pelanggan dengan status pending
    //     $pelanggan = Pelanggan::with('paket')
    //         ->where('status', 'pending') // hanya pending
    //         ->get();

    //     // Tambahkan nomor urut seperti antrian
    //     $pelanggan = $pelanggan->values()->map(function ($item, $index) {
    //         $item->nomor_urut = $index + 1; // mulai dari 1

    //         return $item;
    //     });

    //     return response()->json([
    //         'data' => $pelanggan,
    //     ]);
    // }

    public function getDataAprove()
    {
        // Ambil hanya pelanggan dengan status pending
        $pelanggan = Pelanggan::with('paket')
            ->where('status', 'approve') // hanya pending
            ->get();

        // Tambahkan nomor urut seperti antrian
        $pelanggan = $pelanggan->values()->map(function ($item, $index) {
            $item->nomor_urut = $index + 1; // mulai dari 1

            return $item;
        });

        return response()->json([
            'data' => $pelanggan,
        ]);
    }

    public function updateSid(Request $request, $nomerid)
    {
        $request->validate([
            'sid' => 'required|string',
        ]);

        // Ambil pelanggan berdasarkan nomerid
        $pelanggan = Pelanggan::where('nomer_id', $nomerid)->first();

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        // Update SID
        $pelanggan->update([
            'webpushr_sid' => $request->sid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SID berhasil disimpan',
            'data' => [
                'nomerid' => $pelanggan->nomerid,
                'sid' => $request->sid,
            ],
        ]);
    }

    // Daftar pelanggan
    public function status()
    {
        $pelanggan = Pelanggan::with('paket')->latest()->get();

        return view('content.apps.marketing-pelanggan.status-pelanggan', compact('pelanggan'));
    }

    // Daftar pelanggan
    public function index()
    {
        $pelanggan = Pelanggan::with('paket')->latest()->get();

        return view('content.apps.Pelanggan.pelanggan', compact('pelanggan'));
    }

    // Halaman tambah pelanggan
    public function create()
    {
        $paket = Paket::all(); // get paket dari tabel paket

        return view('content.apps.Pelanggan.add-pelanggan', compact('paket'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'nullable|string|max:50',
            'no_whatsapp' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',

            // Alamat lengkap
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',

            // Paket
            'paket_id' => 'required|exists:pakets,id',
            'nomer_id' => 'required|string|max:50|unique:pelanggans,nomer_id',

            // Tanggal
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',

            // Lain-lain
            'deskripsi' => 'nullable|string',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {

            // 2?? Upload foto KTP (jika ada)
            $fotoKtpPath = null;
            if ($request->hasFile('foto_ktp')) {
                $fotoKtpPath = $request->file('foto_ktp')->store('foto_ktp', 'public');
            }

            // 3?? Ambil paket & tentukan tanggal langganan
            $paket = Paket::findOrFail($validated['paket_id']);
            $tanggalMulai = $validated['tanggal_mulai'] ?? now();
            $tanggalBerakhir = $validated['tanggal_berakhir'] ?? now()->addDays($paket->masa_pembayaran);

            // 4?? Buat Pelanggan dengan user_id dari user baru
            Pelanggan::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_ktp' => $validated['no_ktp'] ?? null,
                'no_whatsapp' => $validated['no_whatsapp'] ?? null,
                'no_telp' => $validated['no_telp'] ?? null,

                'alamat_jalan' => $validated['alamat_jalan'] ?? null,
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'desa' => $validated['desa'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten' => $validated['kabupaten'] ?? null,
                'provinsi' => $validated['provinsi'] ?? null,
                'kode_pos' => $validated['kode_pos'] ?? null,

                'paket_id' => $paket->id,
                'nomer_id' => $validated['nomer_id'],
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_berakhir' => $tanggalBerakhir,

                'deskripsi' => $validated['deskripsi'] ?? null,
                'foto_ktp' => $fotoKtpPath,
                'status' => 'approve',
            ]);

            DB::commit();

            return redirect()->route('pelanggan')->with('success', '? Pelanggan baru dan akun login berhasil dibuat!');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', '? Terjadi kesalahan: '.$th->getMessage());
        }
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $paket = Paket::all();

        return view('content.apps.Pelanggan.edit-pelanggan', compact('pelanggan', 'paket'));
    }

    public function upload()
    {
        return view('content.apps.Pelanggan.upload');
    }

    // API Get Paket (optional untuk AJAX)

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'nullable|string|max:50',
            'no_whatsapp' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',

            // Alamat lengkap
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',

            // Paket
            'paket_id' => 'required|exists:pakets,id',
            'nomer_id' => 'required|string|max:50|unique:pelanggans,nomer_id,'.$pelanggan->id,

            // Tanggal
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',

            // Lain-lain
            'deskripsi' => 'nullable|string',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'nullable|in:pending,approve,reject',
        ]);

        $paket = Paket::findOrFail($validated['paket_id']);
        $tanggalMulai = $validated['tanggal_mulai'] ?? now();
        $tanggalBerakhir = $validated['tanggal_berakhir'] ?? now()->parse($tanggalMulai)->addDays($paket->masa_pembayaran);

        // Upload foto baru jika ada
        if ($request->hasFile('foto_ktp')) {
            // Hapus foto lama jika ada
            if ($pelanggan->foto_ktp && file_exists(storage_path('app/public/'.$pelanggan->foto_ktp))) {
                unlink(storage_path('app/public/'.$pelanggan->foto_ktp));
            }
            $pelanggan->foto_ktp = $request->file('foto_ktp')->store('foto_ktp', 'public');
        }

        // Update data pelanggan
        $pelanggan->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'no_ktp' => $validated['no_ktp'] ?? null,
            'no_whatsapp' => $validated['no_whatsapp'] ?? null,
            'no_telp' => $validated['no_telp'] ?? null,

            // Alamat lengkap
            'alamat_jalan' => $validated['alamat_jalan'] ?? null,
            'rt' => $validated['rt'] ?? null,
            'rw' => $validated['rw'] ?? null,
            'desa' => $validated['desa'] ?? null,
            'kecamatan' => $validated['kecamatan'] ?? null,
            'kabupaten' => $validated['kabupaten'] ?? null,
            'provinsi' => $validated['provinsi'] ?? null,
            'kode_pos' => $validated['kode_pos'] ?? null,

            // Paket
            'paket_id' => $paket->id,
            'nomer_id' => $validated['nomer_id'],
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_berakhir' => $tanggalBerakhir,

            // Lain-lain
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $validated['status'] ?? $pelanggan->status,
        ]);

        return redirect()->route('pelanggan')->with('success', '? Data pelanggan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        // Tambahkan notifikasi (opsional)
        return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new PelangganImport, $request->file('file'));

        return redirect()->route('pelanggan')->with('success', '? Data Excel berhasil diimport!');
    }
}
