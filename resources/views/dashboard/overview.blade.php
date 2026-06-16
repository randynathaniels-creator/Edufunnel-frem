@extends('layouts.app')

@section('content')
<div class="max-w-[1400px] mx-auto">
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Overview</h1>
        <p class="text-gray-500 mt-1">Admissions growth intelligence and funnel performance overview.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 font-bold text-lg">
                    👥
                </div>
                <span class="text-green-500 text-xs font-bold"></span>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Visitors</p>
            <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $data['dashboard_summary']['visitors'] ?? '0' }}</h3>
        </div>

        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 font-bold text-lg">
                    👤+
                </div>
                <span class="text-green-500 text-xs font-bold"></span>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Interested Applicants</p>
            <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $data['dashboard_summary']['applicants'] ?? '0' }}</h3>
        </div>
        
        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 font-bold text-lg">
                    🎯
                </div>
                <span class="text-green-500 text-xs font-bold"></span>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Conversion Rate</p>
            <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $data['dashboard_summary']['conv_rate'] ?? '0%' }}</h3>
        </div>

        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 font-bold text-lg">
                    📉
                </div>
                <span class="text-red-500 text-xs font-bold"></span>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Enrolled Rate</p>
            <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $data['dashboard_summary']['enrolled'] ?? '0' }}</h3>
        </div>
    </div>
    
        <div class="relative">
            <div class="absolute -top-6 right-4 flex items-center gap-1.5 opacity-70">
                <span class="relative flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                </span>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Real-time Data</span>
            </div>
        </div>
        

    <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm mb-10">
        <div class="flex justify-between items-center mb-10">
            <h4 class="text-xl font-bold text-gray-900">Source Performance Comparison</h4>
            <a href="{{ route('reports') }}" class="text-blue-600 font-bold text-sm flex items-center hover:underline">
                View details <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </a>
        </div>
        <div class="h-[450px]">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h4 class="text-xl font-bold text-gray-900">Weekly Application Trend</h4>
                <p class="text-xs text-gray-400">Daily volume tracking for current month</p>
            </div>
            <div class="flex items-center space-x-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                <div class="flex items-center"><span class="w-3 h-3 bg-blue-100 rounded-full mr-2"></span> Target Visitors</div>
                <div class="flex items-center"><span class="w-3 h-3 bg-blue-600 rounded-full mr-2"></span> Current Week</div>
            </div>
        </div>
        <div class="h-[400px]">
            <canvas id="weeklyTrendChart"></canvas>
        </div>
    </div>
</div>

<script>
    // PERFORMANCE CHART
    const perfCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(perfCtx, {
        type: 'bar',
        data: {
            labels: @json(collect($data['platforms'] ?? [])->filter(function($p) {
                return strtolower($p['sumber'] ?? '') !== 'total';
            })->pluck('sumber')),
            datasets: [
                {
                    label: 'Conversion Rate (%)',
                    data: @json(collect($data['platforms'] ?? [])->filter(function($p) {
                        return strtolower($p['sumber'] ?? '') !== 'total';
                    })->map(function($p) {
                        $v = (float)($p['visitor'] ?? 1);
                        $s = (float)($p['student'] ?? 0);
                        return $v > 0 ? round(($s / $v) * 100, 1) : 0;
                    })),
                    backgroundColor: '#3B82F6',
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.4,
                },
                {
                    label: 'Enrolled Students',
                    data: @json(collect($data['platforms'] ?? [])->filter(function($p) {
                        return strtolower($p['sumber'] ?? '') !== 'total';
                    })->pluck('student')),
                    backgroundColor: '#10B981',
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        usePointStyle: true, 
                        padding: 30,
                        font: { weight: 'bold', size: 11 }
                    } 
                } 
            },
            scales: {
                y: { 
                    type: 'linear', 
                    display: true, 
                    position: 'left', 
                    grid: { borderDash: [5, 5], drawBorder: false } 
                },
                y1: { 
                    type: 'linear', 
                    display: true, 
                    position: 'right', 
                    grid: { display: false } 
                },
                x: { grid: { display: false } }
            }
        }
    });

    // WEEKLY TREND CHART
    const weekCtx = document.getElementById('weeklyTrendChart').getContext('2d');
    new Chart(weekCtx, {
        type: 'bar',
        data: {
            labels: @json($data['trends']['weekly']['labels'] ?? []),
            datasets: [
                {
                    label: 'Target Visitors',
                    data: @json($data['trends']['weekly']['past_week'] ?? []),
                    backgroundColor: '#DBEAFE',
                    borderRadius: 4
                },
                {
                    label: 'Current Week',
                    data: @json($data['trends']['weekly']['current_week'] ?? []),
                    backgroundColor: '#2563EB',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false },
                x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
            }
        }
    });
</script>
@endsection