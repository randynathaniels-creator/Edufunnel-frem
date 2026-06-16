@extends('layouts.app')

@section('content')
<div class="max-w-[1400px] mx-auto">
    <div class="mb-6">
        <a href="{{ route('reports') }}" class="text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Campaign Details
        </a>
    </div>

    <div class="flex justify-between items-start mb-10">
        <div>
            <h1 class="text-4xl font-black text-gray-900 mb-2">{{ $sumber }} Performance Detail</h1>
            <p class="text-gray-500 font-medium">Performance breakdown for Q3 Undergraduate Recruitment Campaign</p>
        </div>
        <a href="{{ route('reports') }}" class="px-8 py-3 bg-white border border-gray-200 rounded-full font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all">
            Back to Report
        </a>
    </div>

    @php
        $currentVisitor = (float)($detail['visitor'] ?? $detail['visitors'] ?? 0);
        $currentApplicant = (float)($detail['lead'] ?? $detail['applicants'] ?? 0);
        $currentEnrolled = (float)($detail['student'] ?? $detail['enrolled'] ?? 0);
        $currentTest = (float)($detail['exam_taker'] ?? $detail['exam'] ?? 0);
        $currentRegis = (float)($detail['registered'] ?? $detail['regis'] ?? 0);
        
        $conv = $currentVisitor > 0 ? round(($currentEnrolled / $currentVisitor) * 100, 1) : 0;

        $totalVisitors = collect($data['platforms'] ?? [])->sum(function($item) {
            return (float)($item['visitor'] ?? $item['visitors'] ?? 0);
        });
        $otherVisitors = max(0, $totalVisitors - $currentVisitor);
        $contributionPercent = $totalVisitors > 0 ? round(($currentVisitor / $totalVisitors) * 100, 1) : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm">
         <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">👥</div>
            <p class="text-gray-400 text-xs font-bold mb-4">Total Visitors</p>
            <h3 class="text-4xl font-black text-gray-900 mb-2">{{ number_format($currentVisitor) }}</h3>
            <p class="text-xs font-bold text-green-500 text-opacity-80"></p>
        </div>
        <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm">
         <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">👤+</div>
            <p class="text-gray-400 text-xs font-bold mb-4">Applicants</p>
            <h3 class="text-4xl font-black text-gray-900 mb-2">{{ number_format($currentApplicant) }}</h3>
            <p class="text-xs font-bold text-green-500 text-opacity-80"></p>
        </div>
        <div class="bg-blue-50 p-8 rounded-[24px] border border-blue-100 shadow-sm">
         <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">🎯</div>
            <p class="text-blue-400 text-xs font-bold mb-4">Enrolled Students</p>
            <h3 class="text-4xl font-black text-gray-900 mb-2">{{ number_format($currentEnrolled) }}</h3>
            <p class="text-xs font-bold text-blue-600"></p>
        </div>
        <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm">
         <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center font-bold">📉</div>
            <p class="text-gray-400 text-xs font-bold mb-4">Conversion Rate</p>
            <h3 class="text-4xl font-black text-gray-900 mb-2">{{ $conv }}%</h3>
            <p class="text-xs font-bold text-gray-400"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
            <h4 class="text-xl font-bold text-gray-900 mb-12">Conversion Funnel Visualizer</h4>
            
            <div class="space-y-4 flex flex-col items-center">
                <div class="w-full max-w-2xl bg-blue-100 rounded-full py-4 px-10 flex justify-between items-center text-blue-900 font-bold shadow-sm">
                    <span>Visitors</span>
                    <span>{{ number_format($currentVisitor) }}</span>
                </div>
                
                <div class="w-[85%] max-w-xl bg-blue-200 rounded-full py-4 px-10 flex justify-between items-center text-blue-900 font-bold shadow-sm">
                    <span>Applicants</span>
                    <span>{{ number_format($currentApplicant) }}</span>
                </div>

                <div class="w-[70%] max-w-lg bg-blue-300 rounded-full py-4 px-10 flex justify-between items-center text-blue-900 font-bold shadow-sm">
                    <span>Test / Interview</span>
                    <span>{{ number_format($currentTest) }}</span>
                </div>

                <div class="w-[50%] max-w-md bg-blue-400 rounded-full py-4 px-10 flex justify-between items-center text-white font-bold shadow-sm">
                    <span>Re-registration</span>
                    <span>{{ number_format($currentRegis) }}</span>
                </div>

                <div class="w-[30%] max-w-xs bg-blue-600 rounded-full py-4 px-10 flex justify-between items-center text-white font-bold shadow-sm">
                    <span>Enrolled</span>
                    <span>{{ number_format($currentEnrolled) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
            <h4 class="text-xl font-bold text-gray-900 mb-2">Traffic Contribution</h4>
            <p class="text-gray-400 text-sm mb-12">Source breakdown for total visits</p>
            
            <div class="relative h-[250px] mb-12">
                <canvas id="contributionChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-4xl font-black text-gray-900">{{ $contributionPercent }}%</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ strtoupper($sumber) }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-blue-600"></div>
                        <span class="text-sm font-bold text-gray-700">{{ $sumber }}</span>
                    </div>
                    <span class="text-sm font-black text-gray-900">{{ number_format($currentVisitor) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-gray-200"></div>
                        <span class="text-sm font-bold text-gray-500">Other Sources</span>
                    </div>
                    <span class="text-sm font-black text-gray-900">{{ number_format($otherVisitors) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('contributionChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['{{ $sumber }}', 'Other Sources'],
            datasets: [{
                data: [{{ $contributionPercent }}, {{ max(0, 100 - $contributionPercent) }}],
                backgroundColor: ['#2563EB', '#F3F4F6'],
                borderWidth: 0,
                cutout: '85%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection

