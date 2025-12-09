<?php

namespace App\Http\Controllers;

use App\Models\CustomerTagihan;
use App\Models\Pelanggan;
use App\Models\Rekening;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // pastikan import model Rekening

class CustomerTagihanController extends Controller
{
    public function update(Request $request, $id)
    {
        // Validasi request
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'type_pembayaran' => 'required|exists:rekenings,id',
        ]);

        $pelanggan = Auth::guard('customer')->user();

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        // Pastikan tagihan milik pelanggan
        $tagihan = Tagihan::where('pelanggan_id', $pelanggan->id)->findOrFail($id);

        try {
            // Upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {

                // Hapus file lama jika ada
                if ($tagihan->bukti_pembayaran && Storage::disk('public')->exists($tagihan->bukti_pembayaran)) {
                    Storage::disk('public')->delete($tagihan->bukti_pembayaran);
                }

                $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

                // Update tagihan
                $tagihan->update([
                    'bukti_pembayaran' => $path,
                    'type_pembayaran' => $request->type_pembayaran, // ID rekening
                    'status_pembayaran' => 'proses_verifikasi',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Bukti pembayaran berhasil diupload! Status menunggu verifikasi admin.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File bukti pembayaran tidak ditemukan.',
            ], 400);

        } catch (\Exception $e) {
            // Debugging untuk mengetahui penyebab error
            \Log::error('Tagihan update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal upload: '.$e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        // Ambil pelanggan yang sedang login via guard 'customer'
        $pelanggan = Auth::guard('customer')->user();

        if (! $pelanggan) {
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu.');
        }

        // Ambil tagihan pelanggan berdasarkan status tertentu
        $tagihans = Tagihan::with('pelanggan.user')
            ->where('pelanggan_id', $pelanggan->id)
            ->whereIn('status_pembayaran', ['proses_verifikasi', 'belum bayar'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Ambil semua rekening untuk ditampilkan di form pembayaran
        $rekenings = Rekening::all();

        return view('content.apps.Customer.tagihan.tagihan', compact('tagihans', 'rekenings'));
    }

    public function indexHome()
    {
        // Ambil pelanggan yang sedang login via guard 'customer'
        $pelanggan = Auth::guard('customer')->user();

        if (! $pelanggan) {
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu.');
        }

        // Ambil tagihan pelanggan berdasarkan status tertentu
        $tagihans = Tagihan::with('pelanggan.user')
            ->where('pelanggan_id', $pelanggan->id)
            ->whereIn('status_pembayaran', ['proses_verifikasi', 'belum bayar'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('content.apps.Customer.tagihan.home', compact('tagihans'));
    }

    public function selesai()
    {
        // Ambil pelanggan yang sedang login via guard 'customer'
        $pelanggan = Auth::guard('customer')->user();

        if (! $pelanggan) {
            return redirect()->route('login')
                ->with('warning', 'Silakan login terlebih dahulu.');
        }

        // Ambil tagihan pelanggan dengan status 'lunas'
        $tagihans = Tagihan::with(['pelanggan.user', 'rekening'])
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status_pembayaran', 'lunas')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('content.apps.Customer.tagihan.lunas-tagihan', compact('tagihans'));
    }

    public function getInvoiceJson()
    {
        $pelanggan = Auth::guard('customer')->user();

        if (! $pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        $tagihans = Tagihan::with(['pelanggan', 'paket', 'rekening'])
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status_pembayaran', 'lunas')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Mapping supaya data lebih rapi dan termasuk tanggal pembayaran + type pembayaran
        $tagihansData = $tagihans->map(function ($tagihan) {
            return [
                'id' => $tagihan->id,
                'pelanggan_id' => $tagihan->pelanggan_id,
                'nama_pelanggan' => $tagihan->pelanggan->nama_lengkap ?? null,
                'nomer_id' => $tagihan->pelanggan->nomer_id ?? null,

                'harga' => $tagihan->paket->harga,

                'tanggal_mulai' => $tagihan->tanggal_mulai,
                'tanggal_berakhir' => $tagihan->tanggal_berakhir,
                'tanggal_pembayaran' => $tagihan->tanggal_pembayaran,

                // AMBIL NAMA BANK DARI RELASI REKENING
                'type_pembayaran' => $tagihan->rekening->nama_bank ?? null,

                'kwitansi' => $tagihan->kwitansi,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $tagihansData,
        ]);
    }

    public function getTagihanJson()
    {
        // Ambil pelanggan yang sedang login via guard 'customer'
        $pelanggan = Auth::guard('customer')->user();

        if (! $pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        // Ambil tagihan pelanggan yang belum lunas / proses_verifikasi
        // sekaligus relasi pelanggan dan paket
        $tagihans = Tagihan::with(['pelanggan', 'paket']) // pastikan relasi paket ada di model Tagihan
            ->where('pelanggan_id', $pelanggan->id)
            ->whereIn('status_pembayaran', ['proses_verifikasi', 'belum bayar'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Bisa kita map supaya data lebih rapi
        $tagihansData = $tagihans->map(function ($tagihan) {
            return [
                'id' => $tagihan->id,
                'pelanggan_id' => $tagihan->pelanggan_id,
                'paket_id' => $tagihan->paket_id,
                'nama_paket' => $tagihan->paket->nama ?? null,
                'harga' => $tagihan->paket->harga,
                'kecepatan' => $tagihan->paket->kecepatan ?? null,
                'masa_pembayaran' => $tagihan->masa_pembayaran,
                'tanggal_mulai' => $tagihan->tanggal_mulai,
                'tanggal_berakhir' => $tagihan->tanggal_berakhir,
                'status_pembayaran' => $tagihan->status_pembayaran,
                'tanggal_pembayaran' => $tagihan->tanggal_pembayaran,
                'catatan' => $tagihan->catatan,
                'bukti_pembayaran' => $tagihan->bukti_pembayaran,
                'kwitansi' => $tagihan->kwitansi,
                'pelanggan' => [
                    'id' => $tagihan->pelanggan->id,
                    'nama_lengkap' => $tagihan->pelanggan->nama_lengkap,
                    'no_ktp' => $tagihan->pelanggan->no_ktp,
                    'no_whatsapp' => $tagihan->pelanggan->no_whatsapp,
                    'alamat_jalan' => $tagihan->pelanggan->alamat_jalan,
                    'nomer_id' => $tagihan->pelanggan->nomer_id,
                    // Tambahkan field lain yang dibutuhkan
                ],
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $tagihansData,
        ]);
    }

    public function show($id)
    {
        $tagihan = CustomerTagihan::with('tagihan.paket')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('content.customer.tagihan.show', compact('tagihan'));
    }
}
