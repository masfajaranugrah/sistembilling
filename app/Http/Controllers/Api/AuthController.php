<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Rekening;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Login & Logout",
 *     description="API untuk autentikasi pelanggan"
 * )
 * @OA\Tag(
 *     name="Tagihan",
 *     description="API untuk halaman tagihan pelanggan"
 * )
 */
class AuthController extends Controller
{
    /**
     * Login Pelanggan (Flutter)
     *
     * @OA\Post(
     *     path="/api/pelanggan/jernihnet/login",
     *     tags={"Login & Logout"},
     *     summary="Login pelanggan menggunakan nomor WhatsApp",
     *     description="Endpoint untuk login pelanggan dan mendapatkan token",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"no_whatsapp"},
     *
     *             @OA\Property(property="no_whatsapp", type="string", example="62812345678", description="Nomor WhatsApp pelanggan")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login berhasil"),
     *             @OA\Property(property="token", type="string", example="1|HgzMzrBuTgurTt8hVN417o7rJ41qsdcatVdVY0Hxafc6cae2"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="string", example="a07caaef-51e1-4522-8ba4-67286c3fedb3"),
     *                 @OA\Property(property="nama_lengkap", type="string", example="MAS GANTENG"),
     *                 @OA\Property(property="no_whatsapp", type="string", example="62812345678"),
     *                 @OA\Property(property="nomer_id", type="string", example="JMK.1234"),
     *                 @OA\Property(property="paket_id", type="string", example="e5f153f9-03b3-4378-8ae5-b0b5f1f893a4"),
     *                 @OA\Property(property="kabupaten", type="string", example="SUKOHARJO"),
     *                 @OA\Property(property="foto_ktp_url", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validasi gagal"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="no_whatsapp", type="array",
     *
     *                     @OA\Items(type="string", example="Nomor WhatsApp wajib diisi.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function loginMem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_whatsapp' => 'required|string|exists:pelanggans,no_whatsapp',
        ], [
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.exists' => 'Nomor WhatsApp tidak terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pelanggan = Pelanggan::where('no_whatsapp', $request->no_whatsapp)->first();
        $token = $pelanggan->createToken('pelanggan_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $pelanggan->id,
                'nama_lengkap' => $pelanggan->nama_lengkap,
                'no_whatsapp' => $pelanggan->no_whatsapp,
                'nomer_id' => $pelanggan->nomer_id,
                'paket_id' => $pelanggan->paket_id,
                'kabupaten' => $pelanggan->kabupaten,
                'foto_ktp_url' => $pelanggan->foto_ktp_url,
            ],
        ], 200);
    }

    /**
     * Logout Pelanggan (Flutter)
     *
     * @OA\Post(
     *     path="/api/pelanggan/jernihnet/logout",
     *     tags={"Login & Logout"},
     *     summary="Logout pelanggan",
     *     description="Hapus token pelanggan saat ini",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logout berhasil",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Logout berhasil")
     *         )
     *     )
     * )
     */
    public function logoutMem(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
        ], 200);
    }

    /**
     * Tampilkan halaman tagihan pelanggan
     *
     * @OA\Get(
     *     path="/dashboard/customer/tagihan",
     *     tags={"Tagihan"},
     *     summary="Tampilkan halaman tagihan pelanggan",
     *     description="Endpoint ini menampilkan halaman tagihan pelanggan. Token dikirim sebagai query parameter.",
     *
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=true,
     *         description="Token akses pelanggan",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Halaman tagihan berhasil ditampilkan"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token tidak valid / pelanggan belum login",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Silakan login terlebih dahulu.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $token = $request->query('token');
        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $tokenModel ? $tokenModel->tokenable : null;

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        $tagihans = Tagihan::with('pelanggan.user')
            ->where('pelanggan_id', $user->id)
            ->whereIn('status_pembayaran', ['proses_verifikasi', 'belum bayar'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $rekenings = Rekening::all();

        return view('content.apps.Customer.tagihan.tagihan', compact('tagihans', 'rekenings'));
    }

    /**
     * Tampilkan tagihan yang sudah lunas
     *
     * @OA\Get(
     *     path="/dashboard/customer/tagihan/selesai",
     *     tags={"Tagihan"},
     *     summary="Tampilkan tagihan pelanggan yang sudah lunas",
     *     description="Endpoint ini menampilkan tagihan pelanggan dengan status 'lunas'. Token dikirim sebagai query parameter.",
     *
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=true,
     *         description="Token akses pelanggan",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Halaman tagihan lunas berhasil ditampilkan"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token tidak valid / pelanggan belum login",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Silakan login terlebih dahulu.")
     *         )
     *     )
     * )
     */
    public function tagihanSelesai(Request $request)
    {
        $token = $request->query('token');
        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $tokenModel ? $tokenModel->tokenable : null;

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        $tagihans = Tagihan::with(['pelanggan.user', 'rekening'])
            ->where('pelanggan_id', $user->id)
            ->where('status_pembayaran', 'lunas')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('content.apps.Customer.tagihan.lunas-tagihan', compact('tagihans'));
    }
}
