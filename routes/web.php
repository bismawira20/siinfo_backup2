<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Http\Middleware\IsUser;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BukuTamuController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PassphraseController;
use App\Http\Controllers\AdminBidangController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\PostDashboardController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('home',[
        "title" => 'Home',
        "active" => "home",
    ]);
});
Route::get('/about', function () {
    return view('about', [
        "title" => 'About',
        "active" => "about",
    ]);
});


Route::get('/berita', [PostController::class,'index']);

//halaman single post
Route::get('posts/{post:slug}',  [PostController::class,'show']);

Route::get('/categories', function(){
    return view('categories',[
        "title" => 'Kategori Berita',
        "active"=> 'categories',
        "categories" => Category::all(),
    ]);
});

Route::get('/login',[LoginController::class,'index'])->name('login')->middleware('guest');
Route::post('/login',[LoginController::class,'authenticate']);
Route::post('/logout',[LoginController::class,'logout']);

Route::get('/register',[RegisterController::class,'index'])->middleware('guest');
Route::post('/register',[RegisterController::class,'store']);

Route::get('/dashboard',function(){
    return view('dashboard.index');})->middleware('auth');

// Route untuk Bagian Admin
Route::middleware(IsAdmin::class)->resource('/dashboard/categories', AdminCategoryController::class)
->except('show');    
Route::middleware(IsAdmin::class)->resource('/dashboard/bidangs', AdminBidangController::class)
->except('show');
   
Route::middleware(['auth', IsUser::class])->group(function () {
    // Route untuk Buku Tamu
    Route::prefix('dashboard/bukutamu')->group(function () {
        Route::get('/', [BukuTamuController::class, 'index'])->name('bukutamu.index');
        Route::get('/create', [BukuTamuController::class, 'create'])->name('bukutamu.create');
        Route::post('/store', [BukuTamuController::class, 'store'])->name('bukutamu.store');
        Route::delete('/{bukutamu}/destroy', [BukuTamuController::class, 'destroy'])->name('bukutamu.destroy');
        Route::put('/{bukutamu}/update', [BukuTamuController::class, 'update'])->name('bukutamu.update');
        Route::get('/{bukutamu}/edit', [BukuTamuController::class, 'edit'])->name('bukutamu.edit');
    });
    //Route untuk Berita
    Route::prefix('dashboard/post')->group(function(){
        Route::get('/',[PostDashboardController::class, 'index'])->name('post.index');
        Route::get('/create',[PostDashboardController::class, 'create'])->name('post.create');
        Route::post('/store', [PostDashboardController::class, 'store'])->name('post.store');
        Route::get('/{post}/tampil',[PostDashboardController::class, 'tampil'])->name('post.tampil');
        Route::delete('/{post}/destroy',[PostDashboardController::class, 'destroy'])->name('post.destroy');
        Route::put('/{post}/update', [PostDashboardController::class, 'update'])->name('post.update');
        Route::get('/{post}/edit', [PostDashboardController::class, 'edit'])->name('post.edit');
    });
    //Route untuk Pengajuan TTE
    Route::prefix('dashboard/pengajuan')->group(function () {
        Route::get('/', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/store', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::delete('/{pengajuan}/destroy',[PengajuanController::class, 'destroy'])->name('pengajuan.destroy');
        Route::put('/{pengajuan}/update', [PengajuanController::class, 'update'])->name('pengajuan.update');
        Route::get('/{pengajuan}/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
    });
    //Route untuk Passphrase TTE
    Route::prefix('dashboard/passphrase')->group(function () {
        Route::get('/', [PassphraseController::class, 'index'])->name('passphrase.index');
        Route::get('/create', [PassphraseController::class, 'create'])->name('passphrase.create');
        Route::post('/store', [PassphraseController::class, 'store'])->name('passphrase.store');
        Route::delete('/{passphrase}/destroy',[PassphraseController::class, 'destroy'])->name('passphrase.destroy');
        Route::put('/{passphrase}/update', [PassphraseController::class, 'update'])->name('passphrase.update');
        Route::get('/{passphrase}/edit', [PassphraseController::class, 'edit'])->name('passphrase.edit');
    });
});

Route::middleware(['auth', IsAdmin::class])->group(function (){
    // Route untuk Buku Tamu  
    Route::prefix('dashboard/bukutamu/admin')->group(function () {
        Route::get('/',[BukuTamuController::class, 'adminIndex'])->name('bukutamu.admin.index');
        Route::put('/{bukutamu}/setuju',[BukuTamuController::class,'setuju'])->name('bukutamu.admin.setuju');
        Route::put('/{bukutamu}/tolak',[BukuTamuController::class,'tolak'])->name('bukutamu.admin.tolak');
        Route::put('/setujuSemua',[BukuTamuController::class,'setujuSemua'])->name('bukutamu.admin.setujuSemua');
    });
    //Route untuk Berita
    Route::prefix('dashboard/post/admin')->group(function(){
        Route::get('/',[PostDashboardController::class, 'adminIndex'])->name('post.admin.index');
        Route::get('/create',[PostDashboardController::class, 'adminCreate'])->name('post.admin.create');
        Route::post('/store', [PostDashboardController::class, 'adminStore'])->name('post.admin.store');
        Route::get('/{post}/show',[PostDashboardController::class, 'adminShow'])->name('post.admin.show');
        Route::delete('/{post}/destroy',[PostDashboardController::class, 'adminDestroy'])->name('post.admin.destroy');
        Route::put('/{post}/update', [PostDashboardController::class, 'adminUpdate'])->name('post.admin.update');
        Route::get('/{post}/edit', [PostDashboardController::class, 'adminEdit'])->name('post.admin.edit');
    });
    //Route Pengajuan TTE
    Route::prefix('dashboard/pengajuan/admin')->group(function () {
        Route::get('/',[PengajuanController::class, 'adminIndex'])->name('pengajuan.admin.index');
        Route::put('/{pengajuan}/selesai',[PengajuanController::class,'selesai'])->name('pengajuan.admin.selesai');
        // Route::put('/{pengajuan}/tolak',[PengajuanController::class,'tolak'])->name('pengajuan.admin.tolak');
        Route::put('/selesaiSemua',[PengajuanController::class,'selesaiSemua'])->name('pengajuan.admin.selesaiSemua');
    });
    //Route untuk Passphrase TTE
    Route::prefix('dashboard/passphrase/admin')->group(function () {
        Route::get('/',[PassphraseController::class, 'adminIndex'])->name('passphrase.admin.index');
        Route::put('/{passphrase}/selesai',[PassphraseController::class,'selesai'])->name('passphrase.admin.selesai');
        // Route::put('/{passphrase}/tolak',[PassphraseController::class,'tolak'])->name('passphrase.admin.tolak');
        Route::put('/selesaiSemua',[PassphraseController::class,'selesaiSemua'])->name('passphrase.admin.selesaiSemua');
    });
});

