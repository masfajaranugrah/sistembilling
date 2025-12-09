<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    public function indexLogin()
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('content.apps.Auth.login', ['pageConfigs' => $pageConfigs]);
    }

    public function loginMember()
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('content.apps.Auth.member.login', ['pageConfigs' => $pageConfigs]);
    }

    // public function loginMem(Request $request)
    // {

    //     $request->validate([
    //         'nomer_id' => 'required|exists:pelanggans,nomer_id',
    //     ]);

    //     // Ambil user berdasarkan nomer_id
    //     $pelanggan = Pelanggan::where('nomer_id', $request->nomer_id)->first();

    //     // Login user pakai guard default (web)
    //     Auth::guard('customer')->login($pelanggan);

    //     // Redirect ke dashboard member
    //     return redirect('dashboard/customer/tagihan')->with('success', 'Registrasi berhasil! Selamat datang, ');
    // }

 
	    public function loginMem(Request $request)
    {
        $request->validate([
            'no_whatsapp' => 'required|exists:pelanggans,no_whatsapp',
        ]);

        // Cari pelanggan berdasarkan no_whatsapp
        $pelanggan = Pelanggan::where('no_whatsapp', $request->no_whatsapp)->first();

        // Login guard customer
        Auth::guard('customer')->login($pelanggan);

        // Redirect ke dashboard
        return redirect()->route('customer.tagihan.home');
    }


    public function indexRegister()
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('content.apps.Auth.register', ['pageConfigs' => $pageConfigs]);
    }

    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Set default role 'marketing' karena form tidak ada pilihan role
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Auto login setelah register
        Auth::login($user);

        return redirect('/dashboard/admin/tagihan')->with('success', 'Registrasi berhasil! Selamat datang, '.$user->name);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {

            $request->session()->regenerate();

            // Log aktivitas login
            $agent = new Agent;
            LoginLog::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
            ]);

            $user = Auth::user();

            // Redirect berdasarkan role
            switch ($user->role) {

                case 'administrator':
                    return redirect('/dashboard/admin/tagihan');

                case 'admin':
                    return redirect('/dashboard/admin/tagihan');

                case 'marketing':
                    return redirect('/dashboard/marketing/pelanggan');

                case 'customer_service':
                    return redirect('/dashboard/cs/tickets');

                case 'team':
                    return redirect('/dashboard/teknisi/jobs');

                case 'karyawan':
                    return redirect('/dashboard/karyawan/absensi');

                default:
                    Auth::logout();

                    return redirect()->route('login')->withErrors([
                        'email' => 'Role tidak valid.',
                    ]);
            }
        }

        return redirect()->route('login')
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah']);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/dashboard/auth/login')->with('success', 'Berhasil logout.');
    }

    // Logout customer (guard 'customer')
    public function logoutCustomer(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/pelanggan/jernihnet/login')->with('success', 'Berhasil logout.');
    }
}
