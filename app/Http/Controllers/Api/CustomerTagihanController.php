<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerTagihanController extends Controller
{
    // Ambil tagihan aktif
    public function getTagihanJson()
    {
        $pelanggan = auth()->user();

        if (! $pelanggan) {
            return response()->json(['status' => 'error', 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $tagihans = Tagihan::with('paket')
            ->where('pelanggan_id', $pelanggan->id)
            ->whereIn('status_pembayaran', ['proses_verifikasi', 'belum bayar'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $tagihans]);
    }

    // Ambil tagihan selesai
    public function getTagihanSelesaiJson()
    {
        $pelanggan = auth()->user();

        if (! $pelanggan) {
            return response()->json(['status' => 'error', 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $tagihans = Tagihan::with('paket')
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status_pembayaran', 'lunas')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $tagihans]);
    }

    // Detail tagihan tertentu
    public function showJson($id)
    {
        $pelanggan = auth()->user();

        $tagihan = Tagihan::with('paket')
            ->where('pelanggan_id', $pelanggan->id)
            ->findOrFail($id);

        return response()->json(['status' => 'success', 'data' => $tagihan]);
    }

    // Upload bukti pembayaran
    public function uploadJson(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $pelanggan = auth()->user();

        $tagihan = Tagihan::where('pelanggan_id', $pelanggan->id)->findOrFail($id);

        try {
            if ($tagihan->bukti_pembayaran && Storage::disk('public')->exists($tagihan->bukti_pembayaran)) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }

            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

            $tagihan->update([
                'bukti_pembayaran' => $path,
                'status_pembayaran' => 'proses_verifikasi',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diupload! Status menunggu verifikasi admin.',
                'data' => ['path' => $path],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal upload: '.$e->getMessage(),
            ], 500);
        }
    }
}
