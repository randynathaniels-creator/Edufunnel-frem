@extends('layouts.app')

@section('content')
@php
    // Cari platform dengan konversi tertinggi buat AI Insight
    $bestPlatform = collect($data['platforms'])->sortByDesc(function($item) {
        $v = (float)str_replace(',', '', $item['visitors'] ?? $item['visitor'] ?? 1);
        $s = (float)str_replace(',', '', $item['student'] ?? 0);
        return $v > 0 ? ($s / $v) : 0;
    })->first();

    $totalVisitors = collect($data['platforms'])->sum(function($item) {
        return (float)str_replace(',', '', $item['visitors'] ?? $item['visitor'] ?? 0);
    });
@endphp

<div class="max-w-[1400px] mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Reports</h1>
        <p class="text-gray-500 mt-1">Traffic Source Analysis</p>
    </div>
    
    {{-- SEC-1: METRIC CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Best Performing Source</p>
            <h3 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $bestPlatform['sumber'] ?? 'Google Ads' }}</h3>
            @php
                $bv = (float)($bestPlatform['visitor'] ?? 1);
                $bs = (float)($bestPlatform['student'] ?? 0);
                $bConv = $bv > 0 ? round(($bs / $bv) * 100, 1) : 0;
            @endphp
            <p class="text-green-500 text-xs font-bold">{{ $bConv }}% conversion</p>
        </div>

        <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Total Traffic</p>
            <h3 class="text-3xl font-extrabold text-gray-900 mb-2">
                {{ number_format((float) str_replace(',', '', $data['dashboard_summary']['visitors'] ?? 0)) }}
            </h3>
            <p class="text-green-500 text-xs font-bold"></p>
        </div>

        <div class="relative">
            <div class="absolute -top-6 right-4 flex items-center gap-1.5 opacity-70">
                <span class="relative flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                </span>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Real-time Data</span>
            </div>

            <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm transition-all hover:shadow-md h-full">
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Avg. Conversion Rate</p>
                <h3 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $data['dashboard_summary']['conv_rate'] ?? '0%' }}</h3>
                <p class="text-gray-400 text-xs font-bold">Across all sources</p>
            </div>
        </div>
    </div>

    {{-- SEC-2: DUA GRAFIK PERBANDINGAN - MURNI GRAFIK BATANG BERSUSUN KE BAWAH --}}
    <div class="flex flex-col gap-10 mb-10">
        
        {{-- Grafik 1: Perbandingan Tingkat Ketertarikan (Visitor vs Applicant vs Rasio) --}}
        <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm w-full">
            <div class="mb-6">
                <h4 class="text-xl font-bold text-gray-900">Perbandingan Tingkat Ketertarikan</h4>
                <p class="text-xs text-gray-400 mt-1">Menganalisis perbandingan volume traffic masuk, jumlah pendaftar, beserta persentase rasio ketertarikannya</p>
            </div>
            <div class="h-[400px] w-full">
                <canvas id="interestComparisonChart"></canvas>
            </div>
        </div>

        {{-- Grafik 2: Perbandingan Tingkat Kelulusan (Applicant vs Enrolled vs Rasio) --}}
        <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm w-full">
            <div class="mb-6">
                <h4 class="text-xl font-bold text-gray-900">Perbandingan Tingkat Kelulusan & Registrasi</h4>
                <p class="text-xs text-gray-400 mt-1">Menganalisis perbandingan pendaftar masuk, jumlah mahasiswa yang resmi terdaftar, beserta persentase kelulusannya</p>
            </div>
            <div class="h-[400px] w-full">
                <canvas id="passingComparisonChart"></canvas>
            </div>
        </div>
        
    </div>

    {{-- SEC-3: MAIN METRICS SUMMARY TABLE --}}
    <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm mb-10">
        <h4 class="text-xl font-bold text-gray-900 mb-8">Traffic Source Metrics</h4>
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] uppercase font-black text-gray-400 border-b border-gray-50">
                    <th class="pb-6 px-4">Source</th>
                    <th class="pb-6 px-4">Visitors</th>
                    <th class="pb-6 px-4">Applicants</th>
                    <th class="pb-6 px-4 text-center">Enrolled</th>
                    <th class="pb-6 px-4 text-center">Conversion Detail</th>
                    <th class="pb-6 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($data['platforms'] ?? [] as $p)
                @if(strtolower($p['sumber'] ?? '') === 'total')
                    @continue
                @endif
                <tr class="hover:bg-gray-50/50 transition-all">
                    <td class="py-6 px-4 font-bold text-gray-900">{{ $p['sumber'] ?? 'Unknown' }}</td>
                    
                    <td class="py-6 px-4 text-gray-500 font-medium">
                        {{ number_format((float) ($p['visitor'] ?? 0)) }}
                    </td>

                    <td class="py-6 px-4 text-gray-500 font-medium">
                        {{ number_format((float) ($p['lead'] ?? 0)) }}
                    </td>

                    <td class="py-6 px-4 text-center text-gray-500 font-medium">
                        {{ number_format((float) ($p['student'] ?? 0)) }}
                    </td>

                    <td class="py-6 px-4">
                        @php
                            $v = (float) ($p['visitor'] ?? 0);
                            $l = (float) ($p['lead'] ?? 0);
                            $s = (float) ($p['student'] ?? 0);
                            
                            $mainConv = $v > 0 ? round(($s / $v) * 100, 1) : 0;
                            $visToApp = $v > 0 ? round(($l / $v) * 100, 1) : 0;
                            $appToEnr = $l > 0 ? round(($s / $l) * 100, 1) : 0;
                        @endphp
                        <div class="flex flex-col gap-1 items-center justify-center">
                            <span class="px-2.5 py-0.5 bg-green-50 text-green-600 rounded-lg text-xs font-black min-w-[70px] text-center">
                                {{ $mainConv }}% (Total)
                            </span>
                            <div class="flex flex-col text-[10px] text-gray-400 font-medium text-left">
                                <span>Vis ➔ App: <strong class="text-blue-500">{{ $visToApp }}%</strong></span>
                                <span>App ➔ Enr: <strong class="text-purple-500">{{ $appToEnr }}%</strong></span>
                            </div>
                        </div>
                    </td>

                    <td class="py-6 px-4 text-right text-blue-600 font-bold text-sm">
                        <a href="{{ route('detail', ['sumber' => $p['sumber'] ?? 'all']) }}" class="hover:underline flex items-center justify-end">
                            View Details <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- SEC-4: SMART INSIGHT --}}
   <div class="mt-8 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/40 p-5 rounded-2xl flex items-center gap-5 transition-all shadow-sm">
        <div class="bg-blue-600 text-white p-2.5 rounded-xl shadow-md flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-[9px] font-black uppercase tracking-[0.15em] text-blue-600 dark:text-blue-400">Smart Analysis</span>
                <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">• BETA</span>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-snug">
                Berdasarkan data, <span class="font-bold text-gray-900 dark:text-white italic">"{{ $bestPlatform['sumber'] ?? 'Website' }}"</span> paling efisien. Disarankan alokasi budget <span class="font-bold text-blue-600 dark:text-blue-400">tambah 15%</span> ke sini untuk sisa kuarter ini.
            </p>
        </div>
        <div class="hidden md:block">
            <span class="px-3 py-1 bg-white dark:bg-gray-800 border border-blue-100 dark:border-blue-800 rounded-lg text-[10px] font-black text-blue-600 uppercase tracking-tighter shadow-sm">
                Recommended
            </span>
        </div>
    </div>
</div>

<script>
    // Ambil Data Kunci Label Platform Utama
    const platformLabels = @json(collect($data['platforms'] ?? [])->filter(function($p) {
        return strtolower($p['sumber'] ?? '') !== 'total';
    })->pluck('sumber'));

    // ============================================================
    // GRAFIK 1: PERBANDINGAN TINGKAT KETERTARIKAN (VISITOR VS APPLICANT)
    // ============================================================
    const ctxInterest = document.getElementById('interestComparisonChart').getContext('2d');
    new Chart(ctxInterest, {
        type: 'bar', // Tipe Utama Kumpulan Grafik Batang
        data: {
            labels: platformLabels,
            datasets: [
                {
                    label: 'Total Visitors (Orang)',
                    data: @json(collect($data['platforms'] ?? [])->filter(function($p) { return strtolower($p['sumber'] ?? '') !== 'total'; })->pluck('visitor')),
                    backgroundColor: '#1E3A8A', // Biru Tua
                    borderRadius: 4
                },
                {
                    label: 'Total Applicants (Orang)',
                    data: @json(collect($data['platforms'] ?? [])->filter(function($p) { return strtolower($p['sumber'] ?? '') !== 'total'; })->pluck('lead')),
                    backgroundColor: '#60A5FA', // Biru Muda
                    borderRadius: 4
                },
                {
                    label: 'Rasio Ketertarikan (%)',
                    data: @json(collect($data['platforms'] ?? [])->filter(function($p) { return strtolower($p['sumber'] ?? '') !== 'total'; })->map(function($p) {
                        $v = (float) ($p['visitor'] ?? 1); $l = (float) ($p['lead'] ?? 0);
                        return $v > 0 ? round(($l / $v) * 100, 1) : 0;
                    })),
                    backgroundColor: '#EF4444', // DIUBAH JADI BATANG MERAH KONTRAS
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold', size: 11 } } } 
            },
            scales: {
                y: { 
                    type: 'linear',
                    display: true,
                    title: { display: true, text: 'Volume Data / Nilai Rasio (%)', font: { weight: 'bold' } },
                    grid: { borderDash: [5, 5], drawBorder: false }
                },
                x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
            }
        }
    });

    // ============================================================
    // GRAFIK 2: PERBANDINGAN TINGKAT KELULUSAN (APPLICANT VS ENROLLED)
    // ============================================================
    const ctxPassing = document.getElementById('passingComparisonChart').getContext('2d');
    new Chart(ctxPassing, {
        type: 'bar', // Tipe Utama Kumpulan Grafik Batang
        data: {
            labels: platformLabels,
            datasets: [
                {
                    label: 'Total Applicants (Orang)',
                    data: @json(collect($data['platforms'] ?? [])->filter(function($p) { return strtolower($p['sumber'] ?? '') !== 'total'; })->pluck('lead')),
                    backgroundColor: '#4B5563', // Abu-Abu Prospek
                    borderRadius: 4
                },
                {
                    label: 'Total Enrolled Students (Orang)',
                    data: @json(collect($data['platforms'] ?? [])->filter(function($p) { return strtolower($p['sumber'] ?? '') !== 'total'; })->pluck('student')),
                    backgroundColor: '#10B981', // Hijau Sukses
                    borderRadius: 4
                },
                {
                    label: 'Rasio Kelulusan (%)',
                    data: @json(collect($data['platforms'] ?? [])->filter(function($p) { return strtolower($p['sumber'] ?? '') !== 'total'; })->map(function($p) {
                        $l = (float) ($p['lead'] ?? 1); $s = (float) ($p['student'] ?? 0);
                        return $l > 0 ? round(($s / $l) * 100, 1) : 0;
                    })),
                    backgroundColor: '#F59E0B', // DIUBAH JADI BATANG ORANGE KONTRAS
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold', size: 11 } } } 
            },
            scales: {
                y: { 
                    type: 'linear',
                    display: true,
                    title: { display: true, text: 'Volume Data / Nilai Rasio (%)', font: { weight: 'bold' } },
                    grid: { borderDash: [5, 5], drawBorder: false }
                },
                x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
            }
        }
    });
</script>
@endsection