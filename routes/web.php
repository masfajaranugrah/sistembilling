<?php
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\apps\Chat;
use App\Http\Controllers\ChatController;

use App\Http\Controllers\CustomerTagihanController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\layouts\CollapsedMenu;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\ContentNavbar;
use App\Http\Controllers\layouts\ContentNavSidebar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Horizontal;
use App\Http\Controllers\layouts\Vertical;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TagihanKwitansiController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketStatusLog;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomBroadcastController;

// Broadcasting authentication with multi-guard support
Route::post('/broadcasting/auth', [CustomBroadcastController::class, 'authenticate'])
    ->middleware(['web']);

// Main Page Route
Route::get('/', function () {
    return redirect()->route('users.member');
});

// locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);

// auth
Route::get('dashboard/auth/login', [AuthController::class, 'indexLogin'])->name('login')->middleware('guest:customer,web'); // tambahkan semua guard yang ingin dicek

Route::get('dashboard/auth/register', [AuthController::class, 'indexRegister'])->name('register');
Route::post('dashboard/auth/login', [AuthController::class, 'login'])->name('login.create');
Route::post('dashboard/auth/register', [AuthController::class, 'register'])->name('register.create');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/customer/logout', [AuthController::class, 'logoutCustomer'])->name('customer.logout');

// auth pelanggan
Route::prefix('/pelanggan/jernihnet')->group(function () {
    Route::get('/login', [AuthController::class, 'loginMember'])->name('users.member')->middleware('guest:customer'); // tambahkan semua guard yang ingin dicek

    Route::post('/login', [AuthController::class, 'loginMem'])->name('login.member.post');
});
// layout
Route::get('/layouts/collapsed-menu', [CollapsedMenu::class, 'index'])->name('layouts-collapsed-menu');
Route::get('/layouts/content-navbar', [ContentNavbar::class, 'index'])->name('layouts-content-navbar');
Route::get('/layouts/content-nav-sidebar', [ContentNavSidebar::class, 'index'])->name('layouts-content-nav-sidebar');
Route::get('/layouts/horizontal', [Horizontal::class, 'index'])->name('dashboard-analytics');
Route::get('/layouts/vertical', [Vertical::class, 'index'])->name('dashboard-analytics');
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// laravel example

Route::middleware(['auth', 'role:administrator,admin'])->group(function () {

    Route::get('/dashboard/admin/employees', [EmployeeController::class, 'index'])->name('karyawan.index');
    Route::get('/dashboard/admin/employees/create', [EmployeeController::class, 'create'])->name('karyawan.create');
    Route::get('/dashboard/admin/employees/data', [EmployeeController::class, 'getDataJson'])->name('employees.data');
    Route::get('/dashboard/admin/employees/upload/data', [EmployeeController::class, 'upload']);
    Route::post('/dashboard/admin/employees/create', [EmployeeController::class, 'store'])->name('employees.create.post');
    Route::get('/dashboard/admin/employees/{id}', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/dashboard/admin/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/dashboard/admin/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    // Route POST untuk proses import
    Route::post('/dashboard/admin/employees/import-excel', [EmployeeController::class, 'importExcel'])->name('karyawan.excel');

    Route::prefix('/dashboard/admin/paket')->group(function () {
        Route::get('/', [PaketController::class, 'index'])->name('paket.index');
        Route::get('/create', [PaketController::class, 'create'])->name('paket.add');
        Route::post('/', [PaketController::class, 'store'])->name('paket.store');
        Route::get('/{id}/edit', [PaketController::class, 'edit'])->name('paket.edit');
        Route::put('/{id}', [PaketController::class, 'update'])->name('paket.update');
        Route::delete('/{id}', [PaketController::class, 'destroy'])->name('paket.destroy');
    });

    Route::prefix('/dashboard/admin/tagihan')->group(function () {
        Route::get('/', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/lunas', [TagihanController::class, 'lunas'])->name('tagihan.lunas');
        Route::get('/proses', [TagihanController::class, 'proses'])->name('tagihan.proses');
        Route::get('/add/tagihan', [TagihanController::class, 'indexAddTagihan'])->name('tagihan.add');
        Route::get('/tagihan/data', [TagihanController::class, 'getData'])->name('tagihan.data');
        Route::put('/{id}/update', [TagihanController::class, 'update'])->name('tagihan.update');
        Route::post('/konfirmasi/{id}', [TagihanController::class, 'konfirmasiBayar']);
        Route::post('/tagihan/store', [TagihanController::class, 'store'])->name('tagihan.store');
        Route::post('/{id}/bayar', [TagihanController::class, 'updateStatus'])->name('tagihan.bayar');
        Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
        Route::post('/{id}/bayar', [TagihanController::class, 'konfirmasiBayar'])->name('tagihan.konfirmasi');
        Route::get('/pdf', [TagihanController::class, 'lihat']);
    });

    Route::prefix('/dashboard/admin/laporan')->group(function () {

        // Laporan
        Route::get('/tagihan', [LaporanController::class, 'tagihan'])->name('laporan.tagihan');
        Route::get('/pembayaran', [LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');

        Route::get('/tagihan/export', [LaporanController::class, 'exportExcel'])->name('laporan.tagihan.export');
    });

    Route::prefix('/dashboard/admin/laporan')->group(function () {
        Route::get('/tagihan/kwitansi', [TagihanKwitansiController::class, 'index'])->name('laporan.kwitansi.index');
        Route::get('/tagihan/kwitansi/export', [TagihanKwitansiController::class, 'exportExcel'])->name('laporan.kwitansi.export');
    });

});

Route::middleware(['auth', 'role:admin,administrator'])->group(function () {

    // pelanggan
    Route::prefix('/dashboard/admin/pelanggan')->group(function () {
        Route::get('/', [PelangganController::class, 'index'])->name('pelanggan');
        Route::get('/status', [PelangganController::class, 'status'])->name('pelanggan.data');
        Route::get('upload/data', [PelangganController::class, 'upload'])->name('pelanggan.data');
        Route::get('/status/data', [PelangganController::class, 'getDataAprove'])->name('pelanggan.data');
        Route::get('/create', [PelangganController::class, 'create'])->name('add-pelanggan');
        Route::post('/store', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::get('/edit/{id}', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::put('/update/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::get('/get-paket', [PelangganController::class, 'getPaket'])->name('pelanggangetPaket');
        Route::delete('/delete/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.delete');
        // ✅ Route POST untuk proses import
        Route::post('/import-excel', [PelangganController::class, 'importExcel'])->name('pelanggan.excel');

    });

    Route::prefix('/dashboard/admin/users')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('users.index'); // list users
        Route::get('/create', [TeamController::class, 'create'])->name('users.create'); // form add user
        Route::post('/store', [TeamController::class, 'register'])->name('users.store'); // simpan user baru
        Route::get('/edit/{user}', [TeamController::class, 'edit'])->name('users.edit'); // edit user
        Route::put('/update/{user}', [TeamController::class, 'update'])->name('users.update'); // update user
        Route::delete('/delete/{user}', [TeamController::class, 'destroy'])->name('users.destroy'); // hapus user
        Route::get('/{id}/edit', [TeamController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [TeamController::class, 'update'])->name('users.update');

    });
});

Route::middleware(['auth', 'role:customer_service'])->group(function () {

    Route::prefix('/dashboard/cs/tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('tickets.indexs');
        Route::get('/tickets/json', [TicketController::class, 'ticketsJson'])->name('tickets.json');
        Route::get('/create', [TicketController::class, 'create'])->name('tickets.creates');
        Route::post('/store', [TicketController::class, 'store'])->name('tickets.stores');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('tickets.shows');
        Route::get('/edit/{ticket}', [TicketController::class, 'edit'])->name('tiket.edit');
        Route::put('/update/{ticket}', [TicketController::class, 'update'])->name('tickets.updates');
        Route::delete('/delete/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroys');
    });
});

Route::prefix('/dashboard/admin/history')->group(function () {
    Route::get('/tickets', [TicketStatusLog::class, 'index'])->name('history.index');
    Route::get('/create', [TicketStatusLog::class, 'create'])->name('tickets.create');
    Route::post('/store', [TicketStatusLog::class, 'store'])->name('tickets.store');
    Route::get('/{ticket}', [TicketStatusLog::class, 'show'])->name('tickets.show');

    // Route::get('/login', [LoginrLogs::class, 'loginLog']); // Commented: Class not found

});

Route::middleware(['auth', 'role:team'])->group(function () {

    Route::prefix('dashboard/teknisi/jobs')->group(function () {
        Route::get('/', [JobsController::class, 'index'])->name('jobs.index');
        Route::get('/create', [JobsController::class, 'create'])->name('jobs.create');
        Route::get('/approved-jobs', [JobsController::class, 'approved'])->name('jobs.approved');
        Route::post('/store', [JobsController::class, 'store'])->name('jobs.store');
        Route::get('/preview-jobs/{ticket}', [JobsController::class, 'show'])->name('jobs.show');
        Route::get('/edit/{ticket}', [JobsController::class, 'edit'])->name('jobs.edit');
        Route::put('/update/{ticket}', [JobsController::class, 'update'])->name('jobs.update');
        Route::delete('/delete/{ticket}', [JobsController::class, 'destroy'])->name('jobs.destroy');
        Route::patch('{id}/auto-update', [JobsController::class, 'autoUpdateStatus'])->name('jobs.autoUpdateStatus');
    });
});

Route::get('/kwitansi/{filename}', function ($filename) {
    $path = storage_path('app/public/kwitansi/'.$filename);

    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});

Route::prefix('dashboard/admin/incomes')->group(function () {
    Route::get('/', [IncomeController::class, 'index'])->name('income.index');
    Route::get('/add', [IncomeController::class, 'create'])->name('income.create');
    Route::post('/', [IncomeController::class, 'store'])->name('income.store');
    Route::get('{id}', [IncomeController::class, 'show'])->name('income.show');
    Route::put('{id}', [IncomeController::class, 'update'])->name('income.upate');
    Route::delete('{id}', [IncomeController::class, 'destroy'])->name('income.delete');
});

// Resource route untuk pengeluaran
Route::prefix('dashboard/admin/expenses')->group(function () {
    Route::get('/', [ExpenseController::class, 'index'])->name('keluar.index');       // List semua pengeluaran
    Route::get('/create', [ExpenseController::class, 'create'])->name('keluar.create');  // Form tambah
    Route::post('/store', [ExpenseController::class, 'store'])->name('keluar.store');    // Simpan pengeluaran
    Route::get('/{id}/edit', [ExpenseController::class, 'edit'])->name('keluar.edit');  // Form edit
    Route::put('/{id}', [ExpenseController::class, 'update'])->name('keluar.update');   // Update pengeluaran
    Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('keluar.destroy');  // Hapus
});

Route::prefix('dashboard/admin/pembukuan')->group(function () {
    Route::get('/masuk', [LedgerController::class, 'index'])->name('pembukuan.index');
    Route::get('/keluar', [LedgerController::class, 'keluar'])->name('pembukuan.keluar');
    Route::get('/total', [LedgerController::class, 'total'])->name('pembukuan.total');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/dashboard/karyawan/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/dashboard/karyawan/data/absensi', [AbsensiController::class, 'getAll'])->name('absensi.indexAll');
    Route::post('/absensi/submit', [AbsensiController::class, 'submit'])->name('absensi.submit');

    Route::post('/absensi/check-out', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
});

 Route::prefix('/dashboard/admin/users')->group(function () {
     Route::get('/', [TeamController::class, 'index'])->name('users.index'); // list users
     Route::get('/create', [TeamController::class, 'create'])->name('users.create'); // form add user
     Route::post('/store', [TeamController::class, 'register'])->name('users.store'); // simpan user baru
     Route::get('/edit/{user}', [TeamController::class, 'edit'])->name('users.edit'); // edit user
//     Route::put('/update/{user}', [TeamController::class, 'update'])->name('users.update'); // update user
//     Route::delete('/delete/{user}', [TeamController::class, 'destroy'])->name('users.destroy'); // hapus user
     Route::get('/{id}/edit', [TeamController::class, 'edit'])->name('users.edit');
     Route::put('/{id}', [TeamController::class, 'update'])->name('users.update');

 });

Route::middleware(['auth:customer'])->group(function () {
    Route::get('dashboard/customer/tagihan/home', [CustomerTagihanController::class, 'indexHome'])->name('customer.tagihan.home');
    Route::get('dashboard/customer/tagihan/selesai', [CustomerTagihanController::class, 'selesai'])->name('customer.tagihan.lunas');
    Route::get('dashboard/customer/tagihan', [CustomerTagihanController::class, 'index'])->name('customer.tagihan');
    Route::get('dashboard/customer/tagihan/json', [CustomerTagihanController::class, 'getTagihanJson'])->name('customer.tagihan.json');
    Route::get('dashboard/customer/tagihan/selesai/json', [CustomerTagihanController::class, 'getInvoiceJson'])->name('customer.tagihan.selesai.json');
    Route::put('dashboard/customer/tagihan/{id}', [CustomerTagihanController::class, 'update'])
        ->name('customer.tagihan.update');
    Route::get('/customer/tagihan/{id}', [CustomerTagihanController::class, 'show'])->name('customer.tagihan.show');
    
    // Chat pelanggan dengan admin
    Route::get('dashboard/customer/chat', [ChatController::class, 'user'])->name('customer.chat');
});

Route::prefix('/pelanggan/jernihnet')->group(function () {
    Route::get('/login', [AuthController::class, 'loginMember'])->name('users.member');
    Route::post('/login', [AuthController::class, 'loginMem'])->name('login.member.post');
});

Route::post('/save-subscription', [PushSubscriptionController::class, 'store']);

// Route::middleware(['auth:customer'])->get('/customer/tagihan/json', [CustomerTagihanController::class, 'getTagihanJson']);

Route::post('/pelanggan/save-player-id', function (Request $request) {

    $request->validate([
        'nomer_id' => 'required',
        'player_id' => 'required',
    ]);

    $pelanggan = \App\Models\Pelanggan::where('nomer_id', $request->nomer_id)->first();

    if ($pelanggan) {
        $pelanggan->update(['player_id' => $request->player_id]);

        return response()->json(['success' => true]);
    }

    return response()->json(['error' => 'not_found'], 404);
});

Route::get('/test-push', function () {

    $playerId = '557a4368-e57a-407b-b479-a33ad32df8a1'; // ganti manual dulu untuk test

    $fields = [
        'app_id' => env('ONESIGNAL_APP_ID'),
        'include_player_ids' => [$playerId],
        'headings' => ['en' => 'Tagihan WIFI'],
        'contents' => ['en' => 'segera dibayar'],
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
});

// json
Route::get('dashboard/admin/tagihan/data', [TagihanController::class, 'indexGetJson'])->name('tagihan.index');
Route::get('dashboard/admin/tagihan/data/{id}', [TagihanController::class, 'getByIdJson'])->name('tagihan.index.id');



Route::post('/dashboard/admin/tagihan/mass-store', [TagihanController::class, 'massStore'])
    ->name('tagihan.massStore');

// news rekeknig

Route::get('/dashboard/admin/rekenings', [RekeningController::class, 'index'])->name('rekenings.index');
Route::get('/dashboard/admin/add/rekenings', [RekeningController::class, 'create'])->name('rekenings.add');
Route::post('/dashboard/admin/rekenings', [RekeningController::class, 'store'])->name('rekenings.create');
Route::get('/dashboard/admin/rekenings/{id}/edit', [RekeningController::class, 'edit'])->name('rekenings.edit');
Route::put('/dashboard/admin/rekenings/{id}', [RekeningController::class, 'update'])->name('rekenings.update');
Route::delete('/dashboard/admin/rekenings/{id}', [RekeningController::class, 'destroy'])->name('rekenings.destroy');

Route::post('/pelanggan/{nomerid}/update-sid', [\App\Http\Controllers\PelangganController::class, 'updateSid'])
    ->middleware('auth:customer');

Route::get('/install', function () {
    return view('content/apps/install');
});

Route::get('/customer/webview-auth', [WebViewController::class, 'loginWithToken']);

Route::prefix('/dashboard/admin/barangs')->group(function () {
    Route::get('/', [BarangController::class, 'index'])->name('barangs');
    Route::get('/create', [BarangController::class, 'create'])->name('add-barang');
    Route::post('/', [BarangController::class, 'store'])->name('post-barang');
    Route::get('{id}', [BarangController::class, 'show'])->name('get-barang');
    Route::put('{id}', [BarangController::class, 'update'])->name('edit-barang');
    Route::delete('{id}', [BarangController::class, 'destroy'])->name('delete-barang');
});

Route::prefix('/dashboard/admin/barang-masuks')->group(function () {
    Route::get('/', [BarangMasukController::class, 'index'])->name('index.barangmasuk');
    Route::get('/create', [BarangMasukController::class, 'create'])->name('create.barangmasuk');
    Route::post('/', [BarangMasukController::class, 'store'])->name('add.barangmasuk');
    Route::get('{id}', [BarangMasukController::class, 'edit'])->name('show.barangmasuk');
    Route::put('{id}', [BarangMasukController::class, 'update'])->name('edit.barangmasuk');
    Route::delete('{id}', [BarangMasukController::class, 'destroy'])->name('delete.barangmasuk');
});
Route::prefix('/dashboard/admin/barang-keluar')->group(function () {
    Route::get('/', [BarangKeluarController::class, 'index'])->name('index.barangkeluar');          // List Barang Keluar
    Route::get('/create', [BarangKeluarController::class, 'create'])->name('add.barangkeluar');     // Form tambah Barang Keluar
    Route::post('/store', [BarangKeluarController::class, 'store'])->name('store.barangkeluar');    // Simpan Barang Keluar
    Route::get('/{id}/edit', [BarangKeluarController::class, 'edit'])->name('edit.barangkeluar');   // Form edit Barang Keluar
    Route::put('/{id}', [BarangKeluarController::class, 'update'])->name('update.barangkeluar');    // Update Barang Keluar
    Route::delete('/{id}', [BarangKeluarController::class, 'destroy'])->name('delete.barangkeluar'); // Hapus Barang Keluar
});


 
// Chat Routes - Real-time messaging
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Chat Admin - untuk admin melihat daftar user
    Route::get('/dashboard/admin/chat', [ChatController::class, 'admin'])->name('chat.admin');
    
    // Chat User - untuk user chat dengan admin
    Route::get('/pelanggan/chat', [ChatController::class, 'pelanggan'])->name('chat.pelanggan');
});

// Chat API endpoints - support both web and customer guards
Route::middleware('auth:web,customer')->group(function () {
    Route::get('/chat/messages/{userId?}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/users', [ChatController::class, 'getUserList'])->name('chat.users');
    Route::post('/chat/mark-read/{userId}', [ChatController::class, 'markRead'])->name('chat.markRead');
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unreadCount');
});
