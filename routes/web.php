<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StudentController;

// ==========================================
// 1. HALAMAN LOGIN & PROSES AUTENTIKASI
// ==========================================

// Tampilan Halaman Login Awal
Route::get('/', function () {
    if (session('is_logged_in') === true) {
        return redirect()->route('overview');
    }
    return view('auth.login');
})->name('login');

// Fungsi Proses Verifikasi Akun Login (Membaca dari Database Supabase)
Route::post('/login-process', function (Request $request) {
    $email = $request->input('email');
    $password = $request->input('password');

    // MENCARI USER LANGSUNG KE TABEL USERS SUPABASE
    $userFound = DB::table('users')
        ->where('email', $email)
        ->where('password', $password) // Mencari yang password-nya cocok persis
        ->first();

    // JIKA USER DITEMUKAN DAN COCOK
    if ($userFound !== null) {
        session([
            'is_logged_in' => true,
            'admin_email'  => $userFound->email,
            'admin_nama'   => $userFound->name // Mengambil kolom 'name' dari database
        ]);

        session()->save(); // Simpan session saat ini juga

        return redirect()->route('overview');
    }

    // JIKA TIDAK COCOK / ASAL-ASALAN, BLOKIR DAN KEMBALIKAN
    return redirect()->route('login')->with('error', 'Email atau kata sandi Anda salah!');
})->name('login.process');

// Fungsi Keluar Sistem (Logout)
Route::get('/logout', function () {
    session()->forget(['is_logged_in', 'admin_email', 'admin_nama']);
    session()->flush(); 
    return redirect()->route('login');
})->name('logout');


// ==========================================
// 2. HELPER UTILITY DATA (MURNI SUPABASE - BEBAS JSON)
// ==========================================

function getMasterData() {
    $platformsData = DB::table('platforms')->get();
    
    $platforms = $platformsData->map(function($p) {
        $visitor = (int) $p->visitor;
        $student = (int) $p->enrolled;
        
        $rateStudent = $visitor > 0 ? round(($student / $visitor) * 100, 2) : 0;

        return [
            'sumber'     => $p->sumber,
            'visitor'    => $visitor,
            'lead'       => (int) $p->lead,
            'exam_taker' => (int) ($p->exam_taker ?? 0), // PENTING: Key ini untuk form
            'registered' => (int) ($p->registered ?? 0), // PENTING: Key ini untuk form
            'student'    => $student,
            'final_conv_rate' => number_format($rateStudent, 2) . '%',
        ];
    })->toArray();

    $totalVisitors = array_sum(array_column($platforms, 'visitor'));
    $totalApplicants = array_sum(array_column($platforms, 'lead'));
    $totalExam = array_sum(array_column($platforms, 'exam_taker'));
    $totalRegis = array_sum(array_column($platforms, 'registered'));
    $totalEnrolled = array_sum(array_column($platforms, 'student'));

    $convRate = $totalVisitors > 0 ? ($totalEnrolled / $totalVisitors) * 100 : 0;
    $targetEnrolled = 745;
    $pctEnrolled = $targetEnrolled > 0 ? round(($totalEnrolled / $targetEnrolled) * 100) : 100;

    $platforms[] = [
        'sumber' => 'Total',
        'visitor' => $totalVisitors,
        'lead' => $totalApplicants,
        'exam_taker' => $totalExam,
        'registered' => $totalRegis,
        'student' => $totalEnrolled,
        'final_conv_rate' => number_format($convRate, 2) . '%',
    ];

    $trendsData = DB::table('trends')->orderBy('id', 'asc')->get();
    
    return [
        'platforms' => $platforms,
        'dashboard_summary' => [
            'visitors' => number_format($totalVisitors),
            'applicants' => number_format($totalApplicants),
            'enrolled' => number_format($totalEnrolled),
            'conv_rate' => number_format($convRate, 2) . '%',
            'enrolled_sub' => $pctEnrolled . '% of target reached'
        ],
        'overall_funnel' => [
            'step_1' => $totalVisitors,
            'step_2' => $totalApplicants,
            'step_3' => $totalExam,
            'step_4' => $totalRegis,
            'step_5' => $totalEnrolled,
        ],
        'trends' => [
            'monthly' => [
                'labels' => $trendsData->where('type', 'monthly')->pluck('label')->toArray(),
                'current_month' => $trendsData->where('type', 'monthly')->pluck('current_value')->map(fn($v) => (int)$v)->toArray(),
                'target' => $trendsData->where('type', 'monthly')->pluck('comparison_value')->map(fn($v) => (int)$v)->toArray(),
            ],
            
            'weekly' => [
        'labels'       => $trendsData->where('type', 'weekly')->pluck('label')->toArray(),
        'past_week'    => $trendsData->where('type', 'weekly')->pluck('comparison_value')->map(fn($v) => (int)$v)->toArray(),
        'current_week' => $trendsData->where('type', 'weekly')->pluck('current_value')->map(fn($v) => (int)$v)->toArray(),
       
            ]
        ],
         'weekly_details' => $trendsData->where('type', 'weekly')->keyBy('label')->toArray()
    ];
}


// ==========================================
// 3. GERBANG DASHBOARD (PROTEKSI KETAT)
// ==========================================

Route::get('/overview', function () {
    if (session('is_logged_in') !== true) {
        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu!');
    }
    return view('dashboard.overview', ['data' => getMasterData()]);
})->name('overview');

Route::get('/funnel', function () {
    if (session('is_logged_in') !== true) {
        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu!');
    }
    return view('dashboard.funnel', ['data' => getMasterData()]);
})->name('funnel');

Route::get('/reports', function () {
    if (session('is_logged_in') !== true) {
        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu!');
    }
    return view('dashboard.reports', ['data' => getMasterData()]);
})->name('reports');

Route::get('/details/{sumber}', function ($sumber) {
    if (session('is_logged_in') !== true) {
        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu!');
    }
    
    $allData = getMasterData();
    $detail = collect($allData['platforms'] ?? [])->first(function($item) use ($sumber) {
        return strtolower($item['sumber']) == strtolower($sumber);
    });

    return view('dashboard.details', [
        'sumber' => $sumber, 
        'detail' => $detail,
        'data'   => $allData
    ]);
})->name('detail');

Route::get('/update', function () {
    if (session('is_logged_in') !== true) {
        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu!');
    }
    return view('dashboard.update', ['data' => getMasterData()]);
    dd($data['weekly_details']);
})->name('admin.update');

Route::get('/statistics', function () {
    if (session('is_logged_in') !== true) {
        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu!');
    }
    return view('dashboard.statistics', ['data' => getMasterData()]);
})->name('statistics');


// ==========================================
// 4. PROSES SIMPAN EDIT DATA KE SUPABASE
// ==========================================

// Form Aksi 1: Menyimpan Kontribusi Per Platform
Route::post('/admin/save-data', function (Request $request) {
    if (session('is_logged_in') !== true) {
        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu!');
    }
    
    // Tarik data asli database sebagai referensi
    $originalPlatforms = DB::table('platforms')->get()->toArray();

    // Looping data input dari form admin update
    foreach ($request->platforms as $index => $val) {
        
        $sumber = $val['sumber'] ?? $originalPlatforms[$index]->sumber ?? null;
        
        if (!$sumber || strtolower($sumber) === 'total') {
            continue;
        }

        // UPDATE SESUAI DENGAN NAME INPUT DI BLADE
        DB::table('platforms')->updateOrInsert(
            ['sumber' => $sumber],
            [
                'visitor'    => (int) ($val['visitors'] ?? 0),
                'lead'       => (int) ($val['lead'] ?? 0),
                'exam_taker' => (int) ($val['exam_taker'] ?? 0), // PENTING: Harus sesuai dengan name di Blade
                'registered' => (int) ($val['registered'] ?? 0), // PENTING: Harus sesuai dengan name di Blade
                'enrolled'   => (int) ($val['student'] ?? 0),    // PENTING: Sesuai dengan name 'student' di Blade
                'updated_at' => now()
            ]
        );
    }

    return redirect()->route('admin.update')->with('success', 'Data performa platform berhasil diperbarui!');
})->name('admin.save');


// Form Aksi 2: Menyimpan Tren Pendaftaran Bulanan
Route::post('/admin/save-trends', function (Request $request) {
    if (session('is_logged_in') !== true) {
        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu!');
    }

    $trendsInput = $request->input('trends', []);

    foreach ($trendsInput as $data) {
        $label = $data['label'] ?? null;

        if ($label) {
            // Update angka Target dan Aktual ke tabel 'trends' berdasarkan type 'monthly' dan nama labelnya
            DB::table('trends')
                ->where('type', 'monthly')
                ->where('label', $label)
                ->update([
                    'current_value'    => (int) ($data['current'] ?? 0),
                    'comparison_value' => (int) ($data['target'] ?? 0),
                    'updated_at'       => now()
                ]);
        }
    }

    return redirect()->route('admin.update')->with('success', 'Angka grafik tren bulanan berhasil diperbarui!');
})->name('admin.save-trends');

// Tambahkan rute ini di akhir file
Route::post('/admin/save-weekly', function (Request $request) {
    if (session('is_logged_in') !== true) return redirect()->route('login');

    $weekLabel = $request->week_label;
    $dailyValues = $request->days;
    $dailyTargets = $request->targets; 
    
    // 1. HITUNG TOTAL AKTUAL DAN TOTAL TARGET
    $totalMingguanAktual = array_sum($dailyValues);
    $totalMingguanTarget = array_sum($dailyTargets); // <--- TAMBAHKAN INI

    // 2. Update total aktual DAN total target ke tabel monthly
    DB::table('trends')
        ->where('type', 'monthly')
        ->where('label', $weekLabel)
        ->update([
            'current_value'    => $totalMingguanAktual,
            'comparison_value' => $totalMingguanTarget, // <--- UPDATE TARGET BULANAN
            'updated_at'       => now()
        ]);

    // 3. Simpan/Update detail per hari (Aktual + Target)
    foreach($dailyValues as $day => $val) {
        DB::table('trends')
            ->where('type', 'weekly')
            ->where('label', $day)
            ->update([
                'current_value'    => (int)($val ?? 0),
                'comparison_value' => (int)($dailyTargets[$day] ?? 0),
                'updated_at'       => now()
            ]);
    }

    return redirect()->route('admin.update')->with('success', "Data $weekLabel, target harian, dan target bulanan berhasil diperbarui!");
})->name('admin.save-weekly');

// STUDENT DETAILS//

// Halaman utama daftar mahasiswa
Route::get('/dashboard/students', [StudentController::class, 'index'])->name('students.index');

// Halaman form tambah mahasiswa
Route::get('/dashboard/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/dashboard/students', [StudentController::class, 'store'])->name('students.store');
Route::delete('/dashboard/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
// edit data mahasiswwa
Route::put('/dashboard/students/{id}', [StudentController::class, 'update'])->name('students.update');
Route::get('/dashboard/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');