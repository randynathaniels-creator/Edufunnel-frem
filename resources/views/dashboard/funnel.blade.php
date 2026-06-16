@extends('layouts.app')

@section('content')
<div class="max-w-[1400px] mx-auto">
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Analisis Funnel</h1>
        <p class="text-gray-500 mt-1">Pantau tahapan konversi pendaftar dan identifikasi titik penurunan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm">
            <div class="flex justify-between mb-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">👥</div>
                <span class="text-green-500 text-xs font-bold"></span>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Pengunjung</p>
            <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($data['overall_funnel']['step_1']) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm">
            <div class="flex justify-between mb-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">👤+</div>
                <span class="text-green-500 text-xs font-bold"></span>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Pendaftar</p>
            <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($data['overall_funnel']['step_2']) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm">
            <div class="flex justify-between mb-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">🎯</div>
                <span class="text-red-500 text-xs font-bold"></span>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Tingkat Konversi</p>
            <h3 class="text-3xl font-extrabold text-gray-900">{{ $data['dashboard_summary']['conv_rate'] }}</h3>
        </div>
        
        <div class="bg-blue-600 p-6 rounded-[24px] shadow-lg shadow-blue-200">
            <div class="flex justify-between mb-4 text-white">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center font-bold">📉</div>
                <span class="text-[10px] opacity-80 uppercase font-black"></span>
            </div>
            <p class="text-blue-100 text-[10px] font-black uppercase tracking-widest mb-1">Attrition Rate</p>
            @php
                $convVal = (float)rtrim($data['dashboard_summary']['conv_rate'] ?? '0%', '%');
                $attritionRate = number_format(100 - $convVal, 1);
            @endphp
            <h3 class="text-3xl font-extrabold text-white">{{ $attritionRate }}%</h3>
        </div>

    
    </div>

    @php
        $step1 = (float)($data['overall_funnel']['step_1'] ?? 1);
        $step2 = (float)($data['overall_funnel']['step_2'] ?? 0);
        $step3 = (float)($data['overall_funnel']['step_3'] ?? 0);
        $step4 = (float)($data['overall_funnel']['step_4'] ?? 0);
        $step5 = (float)($data['overall_funnel']['step_5'] ?? 0);

        $efficiencyScore = $step1 > 0 ? round(($step5 / $step1) * 100, 1) : 0;

        $steps = [
            ['label' => 'Pengunjung Website', 'val' => $step1, 'color' => 'bg-blue-600', 'p' => '100% Reach'],
            ['label' => 'Berminat Daftar', 'val' => $step2, 'color' => 'bg-blue-500', 'p' => ($step1 > 0 ? round(($step2 / $step1) * 100, 1) : 0) . '% Conversion'],
            ['label' => 'Tes / Wawancara', 'val' => $step3, 'color' => 'bg-blue-400', 'p' => ($step1 > 0 ? round(($step3 / $step1) * 100, 1) : 0) . '% Conversion'],
            ['label' => 'Daftar Ulang', 'val' => $step4, 'color' => 'bg-blue-300', 'p' => ($step1 > 0 ? round(($step4 / $step1) * 100, 1) : 0) . '% Conversion'],
            ['label' => 'Mahasiswa Terdaftar', 'val' => $step5, 'color' => 'bg-blue-200', 'p' => ($step1 > 0 ? round(($step5 / $step1) * 100, 1) : 0) . '% Net Enrolled'],
        ];

        // Find the top source
        $totalVisitors = collect($data['platforms'] ?? [])->sum(function($item) {
            return (float)($item['visitor'] ?? $item['visitors'] ?? 0);
        });
        $topPlatform = collect($data['platforms'] ?? [])->sortByDesc(function($p) {
            return (float)($p['visitor'] ?? $p['visitors'] ?? 0);
        })->first();
        $topSourceName = $topPlatform['sumber'] ?? 'Unknown';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <div class="lg:col-span-2 bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
            <div class="flex justify-between items-center mb-12">
                <h4 class="text-xl font-bold text-gray-900">Visualisasi Funnel Pendaftaran</h4>
                <div class="text-right">
                    <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest leading-none">Efficiency Score</p>
                    <p class="text-3xl font-black text-blue-600">{{ $efficiencyScore }}%</p>
                </div>
            </div>
            <div class="space-y-10">
                @foreach($steps as $index => $step)
                    <div class="relative">
                        <div class="flex justify-between text-sm font-bold mb-2 text-gray-700">
                            <span>{{ $step['label'] }}</span>
                            <span>{{ number_format($step['val']) }}</span>
                        </div>
                        <div class="w-full bg-gray-50 rounded-full h-12 overflow-hidden border border-gray-100">
                            <div class="{{ $step['color'] }} h-full flex items-center justify-center text-white text-[10px] font-black uppercase tracking-tighter" style="width: {{ $step1 > 0 ? max(20, round(($step['val'] / $step1) * 100, 1)) : 20 }}%">
                                {{ $step['p'] }}
                            </div>
                        </div>
                        @if(!$loop->last)
                            @php
                                $nextVal = $steps[$index + 1]['val'];
                                $currVal = $step['val'];
                                $dropOff = $currVal > 0 ? round((1 - ($nextVal / $currVal)) * 100, 1) : 0;
                            @endphp
                            <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-red-400 text-[10px] font-black">↓ -{{ $dropOff }}% Drop</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
            <h4 class="text-xl font-bold text-gray-900 mb-10">Kontribusi Sumber</h4>
         <div class="relative h-64 w-full flex items-center justify-center mb-10" style="min-height: 256px;">
    <canvas id="donutChart"></canvas>
               <div class="absolute inset-0 flex flex-col items-center justify-center mt-4 pointer-events-none">
    <p class="text-2xl font-black text-gray-900 leading-none">Top</p>
    <p class="text-[10px] font-black text-blue-600 uppercase">{{ $topSourceName }}</p>
</div>
            </div>
            <div class="space-y-4">
                @foreach($data['platforms'] ?? [] as $p)
                @if(strtolower($p['sumber'] ?? '') === 'total')
                    @continue
                @endif
                @php
                    $v = (float)($p['visitor'] ?? 0);
                    $pct = $totalVisitors > 0 ? round(($v / $totalVisitors) * 100, 1) : 0;
                @endphp
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-blue-600 opacity-{{ 100 - ($loop->index * 20) }} mr-3"></div>
                        <span class="text-gray-500 font-bold">{{ $p['sumber'] }}</span>
                    </div>
                    <span class="font-black text-gray-900">{{ $pct }}%</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm mb-10">
        <div class="flex justify-between items-center mb-8">
            <h4 class="text-xl font-bold text-gray-900">Tren Pendaftar Mingguan</h4>
            <div class="flex items-center space-x-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                <div class="flex items-center"><span class="w-3 h-3 bg-blue-600 rounded-full mr-2"></span> Target</div>
                <div class="flex items-center"><span class="w-3 h-3 bg-blue-100 rounded-full mr-2"></span> Aktual</div>
            </div>
        </div>
        <div class="h-[300px]">
            <canvas id="funnelTrendChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-10">
            <h4 class="text-xl font-bold text-gray-900">Analisis Titik Penurunan Terbesar</h4>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Real-time Data</span>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] uppercase font-black text-gray-400 border-b border-gray-50">
                    <th class="pb-6 px-4">Transisi Tahapan</th>
                    <th class="pb-6 px-4">Jumlah Drop-Off</th>
                    <th class="pb-6 px-4 text-center">Persentase</th>
                    <th class="pb-6 px-4 text-right">Tingkat Keparahan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php
                    $drop1 = $step1 - $step2;
                    $drop1_pct = $step1 > 0 ? round(($drop1 / $step1) * 100, 1) : 0;

                    $drop2 = $step2 - $step3;
                    $drop2_pct = $step2 > 0 ? round(($drop2 / $step2) * 100, 1) : 0;

                    $drop3 = $step3 - $step4;
                    $drop3_pct = $step3 > 0 ? round(($drop3 / $step3) * 100, 1) : 0;

                    $drop4 = $step4 - $step5;
                    $drop4_pct = $step4 > 0 ? round(($drop4 / $step4) * 100, 1) : 0;

                    $transitions = [
                        [
                            'name' => 'Pengunjung → Berminat',
                            'drop' => $drop1,
                            'pct' => $drop1_pct,
                        ],
                        [
                            'name' => 'Berminat → Tes/Wawancara',
                            'drop' => $drop2,
                            'pct' => $drop2_pct,
                        ],
                        [
                            'name' => 'Tes → Daftar Ulang',
                            'drop' => $drop3,
                            'pct' => $drop3_pct,
                        ],
                        [
                            'name' => 'Daftar Ulang → Terdaftar',
                            'drop' => $drop4,
                            'pct' => $drop4_pct,
                        ]
                    ];

                    // Sort transitions by drop percentage descending to list the biggest drops
                    $transitions = collect($transitions)->sortByDesc('pct')->all();
                @endphp

                @foreach($transitions as $t)
                <tr class="hover:bg-gray-50 transition-all">
                    <td class="py-6 px-4 font-bold text-gray-900">{{ $t['name'] }}</td>
                    <td class="py-6 px-4 text-gray-500 font-medium">{{ number_format($t['drop']) }} Siswa</td>
                    <td class="py-6 px-4 text-center text-red-500 font-black">{{ $t['pct'] }}%</td>
                    <td class="py-6 px-4 text-right">
                        @if($t['pct'] >= 50)
                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-tighter">● Tinggi</span>
                        @elseif($t['pct'] >= 35)
                            <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-tighter">● Sedang</span>
                        @else
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-tighter">● Rendah</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
   // Funnel Trend Chart (Target vs Actual Bulanan)
    const ftCtx = document.getElementById('funnelTrendChart').getContext('2d');
    new Chart(ftCtx, {
        type: 'bar',
        data: {
            labels: @json($data['trends']['monthly']['labels'] ?? []), // Sekarang datanya sudah ada!
            datasets: [
                {
                    label: 'Target',
                    data: @json($data['trends']['monthly']['target'] ?? []),
                    backgroundColor: '#2563EB',
                    borderRadius: 4,
                    barPercentage: 0.2,
                    categoryPercentage: 0.5
                },
                {
                    label: 'Aktual',
                    data: @json($data['trends']['monthly']['current_month'] ?? []),
                    backgroundColor: '#DBEAFE',
                    borderRadius: 4,
                    barPercentage: 0.2,
                    categoryPercentage: 0.5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false },
                x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 11 } } }
            }
        }
    });

// Donut Chart (Kontribusi Sumber) - Teroptimasi
const dcCtx = document.getElementById('donutChart').getContext('2d');
new Chart(dcCtx, {
    type: 'doughnut',
    data: {
        labels: @json(collect($data['platforms'] ?? [])->where('sumber', '!=', 'Total')->pluck('sumber')),
        datasets: [{
            data: @json(collect($data['platforms'] ?? [])->where('sumber', '!=', 'Total')->pluck('visitor')),
            backgroundColor: ['#2563EB', '#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE'],
            borderWidth: 0,
            cutout: '80%',
            borderRadius: 5, // Memberikan sedikit kesan membulat pada ujung segmen
            hoverOffset: 10  // Efek sedikit menonjol saat di-hover
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
        padding: 20 // Memberikan ruang di sisi luar grafik
    },
        // Meningkatkan akurasi deteksi mouse
        hover: {
            mode: 'nearest',
            intersect: true
        },
        plugins: { 
            legend: { display: false },
            tooltip: {
                enabled: true,
                mode: 'nearest',
                intersect: true,
                padding: 10,
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: { weight: 'bold' },
                bodyFont: { size: 12 }
            }
        },
        // Memastikan interaksi mouse tidak terhalang
        events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove']
    }
});
</script>
@endsection