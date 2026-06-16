@extends('layouts.app')

@section('content')
@php
    $platforms = collect($data['platforms'] ?? [])->filter(function($p) {
        return strtolower($p['sumber'] ?? '') !== 'total';
    })->values()->all();
@endphp

<div class="max-w-[1400px] mx-auto">
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Alat Analisis Statistik</h1>
        <p class="text-gray-500 mt-1">Penerapan konsep Probabilitas dan Uji Hipotesis Statistik pada data Funnel Rekrutmen.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- 1. AB TESTING SIGNIFICANCE CALCULATOR -->
        <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-xl font-bold text-gray-900">Kalkulator Uji Signifikansi A/B (Z-Test)</h4>
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">Hipotesis Dua Arah</span>
                </div>
                <p class="text-xs text-gray-400 mb-8">Uji apakah perbedaan Conversion Rate (CR) antara dua platform signifikan secara statistik dengan tingkat kepercayaan 95% (&alpha; = 0.05).</p>

                <!-- Input Selection -->
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Platform A (Kontrol)</label>
                        <select id="platformA" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($platforms as $index => $p)
                                <option value="{{ $index }}" {{ $index == 0 ? 'selected' : '' }}>{{ $p['sumber'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Platform B (Variasi)</label>
                        <select id="platformB" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($platforms as $index => $p)
                                <option value="{{ $index }}" {{ $index == 1 ? 'selected' : '' }}>{{ $p['sumber'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Custom Override Values -->
                <div class="grid grid-cols-2 gap-6 mb-8 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                    <div class="space-y-4">
                        <p class="text-xs font-bold text-blue-600">Data Platform A</p>
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase">Pengunjung (N<sub>A</sub>)</label>
                            <input type="number" id="visitorsA" class="w-full bg-white border border-gray-200 rounded-lg p-2.5 mt-1 text-xs font-bold text-gray-700">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase">Terdaftar (X<sub>A</sub>)</label>
                            <input type="number" id="enrolledA" class="w-full bg-white border border-gray-200 rounded-lg p-2.5 mt-1 text-xs font-bold text-gray-700">
                        </div>
                    </div>
                    <div class="space-y-4 border-l border-gray-200 pl-6">
                        <p class="text-xs font-bold text-green-600">Data Platform B</p>
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase">Pengunjung (N<sub>B</sub>)</label>
                            <input type="number" id="visitorsB" class="w-full bg-white border border-gray-200 rounded-lg p-2.5 mt-1 text-xs font-bold text-gray-700">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase">Terdaftar (X<sub>B</sub>)</label>
                            <input type="number" id="enrolledB" class="w-full bg-white border border-gray-200 rounded-lg p-2.5 mt-1 text-xs font-bold text-gray-700">
                        </div>
                    </div>
                </div>

                <!-- Math Outputs -->
                <div class="space-y-3 mb-8 text-xs font-medium text-gray-600">
                    <div class="flex justify-between">
                        <span>Tingkat Konversi A (p<sub>A</sub>):</span>
                        <span id="crA" class="font-bold text-gray-900">0.0%</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tingkat Konversi B (p<sub>B</sub>):</span>
                        <span id="crB" class="font-bold text-gray-900">0.0%</span>
                    </div>
                    <div class="flex justify-between border-t border-dashed border-gray-100 pt-3">
                        <span>Proporsi Gabungan (p<sub>pool</sub>):</span>
                        <span id="pPool" class="font-bold text-gray-900">0.0%</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Galat Baku (Standard Error - SE):</span>
                        <span id="seVal" class="font-bold text-gray-900">0.0000</span>
                    </div>
                    <div class="flex justify-between border-t border-dashed border-gray-100 pt-3">
                        <span>Nilai Z (Z-Score):</span>
                        <span id="zScore" class="font-black text-blue-600 text-sm">0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Nilai p (p-value):</span>
                        <span id="pValue" class="font-black text-blue-600 text-sm">0.0000</span>
                    </div>
                </div>
            </div>

            <!-- Verdict Card -->
            <div id="verdictCard" class="p-6 rounded-[20px] transition-all flex gap-4 items-center">
                <div id="verdictIcon" class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-lg">
                    ?
                </div>
                <div>
                    <h5 id="verdictTitle" class="font-black text-sm text-gray-900">Keputusan Uji</h5>
                    <p id="verdictDesc" class="text-xs text-gray-500 mt-1 leading-relaxed">Pilih platform atau sesuaikan angka di atas untuk menghitung hasil Z-test.</p>
                </div>
            </div>
        </div>

        <!-- 2. FUNNEL TARGET FORECASTING SIMULATOR -->
        <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-xl font-bold text-gray-900">Simulasi Prediksi Target (Funnel Forecasting)</h4>
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">Probabilitas Bersyarat</span>
                </div>
                <p class="text-xs text-gray-400 mb-8">Tentukan target mahasiswa baru terdaftar, dan sistem akan memprediksi volume traffic minimum yang dibutuhkan di tiap tahap berdasarkan probabilitas saat ini.</p>

                <!-- Input Selection -->
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Pilih Platform</label>
                        <select id="forecastPlatform" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 mt-1 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($platforms as $index => $p)
                                <option value="{{ $index }}" {{ $index == 0 ? 'selected' : '' }}>{{ $p['sumber'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Target Terdaftar (Mahasiswa)</label>
                        <input type="number" id="targetStudents" value="100" class="w-full bg-white border border-gray-200 rounded-xl p-3 mt-1 text-gray-900 font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Probability Information -->
                <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100 mb-8 space-y-3">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Probabilitas Konversi Tahap</p>
                    <div class="grid grid-cols-2 gap-4 text-xs font-medium text-gray-600">
                        <div>P(Berminat | Pengunjung): <span id="probLead" class="font-bold text-gray-900">0%</span></div>
                        <div>P(Ujian | Berminat): <span id="probExam" class="font-bold text-gray-900">0%</span></div>
                        <div>P(Daftar Ulang | Ujian): <span id="probRegis" class="font-bold text-gray-900">0%</span></div>
                        <div>P(Terdaftar | Daftar Ulang): <span id="probStudent" class="font-bold text-gray-900">0%</span></div>
                    </div>
                    <div class="border-t border-blue-100 pt-3 flex justify-between text-xs font-bold text-blue-800">
                        <span>Probabilitas Total P(Terdaftar | Pengunjung):</span>
                        <span id="probTotal">0.00%</span>
                    </div>
                </div>

                <!-- Forecast Output Funnel Visualizer -->
                <div class="space-y-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Estimasi Kebutuhan Tiap Tahapan Funnel</p>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                                <span>Pengunjung Website (Visitors)</span>
                                <span id="forecastVisitor" class="text-blue-600">0</span>
                            </div>
                            <div class="w-full bg-gray-50 h-3 rounded-full overflow-hidden border border-gray-100">
                                <div id="barVisitor" class="bg-blue-600 h-full" style="width: 100%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                                <span>Berminat Daftar (Leads)</span>
                                <span id="forecastLead" class="text-blue-500">0</span>
                            </div>
                            <div class="w-full bg-gray-50 h-3 rounded-full overflow-hidden border border-gray-100">
                                <div id="barLead" class="bg-blue-500 h-full" style="width: 70%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                                <span>Peserta Ujian (Exam Takers)</span>
                                <span id="forecastExam" class="text-blue-400">0</span>
                            </div>
                            <div class="w-full bg-gray-50 h-3 rounded-full overflow-hidden border border-gray-100">
                                <div id="barExam" class="bg-blue-400 h-full" style="width: 50%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                                <span>Daftar Ulang (Registered)</span>
                                <span id="forecastRegis" class="text-blue-300">0</span>
                            </div>
                            <div class="w-full bg-gray-50 h-3 rounded-full overflow-hidden border border-gray-100">
                                <div id="barRegis" class="bg-blue-300 h-full" style="width: 30%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-bold text-gray-700 mb-1">
                                <span>Terdaftar (Enrolled)</span>
                                <span id="forecastStudent" class="text-blue-800 font-extrabold">0</span>
                            </div>
                            <div class="w-full bg-gray-50 h-3 rounded-full overflow-hidden border border-gray-100">
                                <div id="barStudent" class="bg-blue-600 h-full animate-pulse" style="width: 10%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 border-t border-gray-100 pt-6 text-[10px] text-gray-400 leading-snug font-medium">
                * Estimasi didasarkan pada proporsi konversi historis dan mengasumsikan pola probabilitas yang konstan.
            </div>
        </div>
    </div>
</div>

<script>
    // Load historical platforms data from PHP
    const platformsData = @json($platforms);

    // ==========================================
    // 1. AB TESTING LOGIC
    // ==========================================

    const selectA = document.getElementById('platformA');
    const selectB = document.getElementById('platformB');

    const inputVisA = document.getElementById('visitorsA');
    const inputEnrA = document.getElementById('enrolledA');
    const inputVisB = document.getElementById('visitorsB');
    const inputEnrB = document.getElementById('enrolledB');

    function populateABData() {
        const platA = platformsData[selectA.value];
        const platB = platformsData[selectB.value];

        inputVisA.value = platA.visitor;
        inputEnrA.value = platA.student;
        inputVisB.value = platB.visitor;
        inputEnrB.value = platB.student;

        calculateZTest();
    }

    // Normal Cumulative Distribution Function (CDF) approximation
    function normalCDF(z) {
        var t = 1 / (1 + 0.2316419 * Math.abs(z));
        var d = 0.3989422804 * Math.exp(-z * z / 2);
        var p = d * t * (0.31938153 + t * (-0.356563782 + t * (1.781477937 + t * (-1.821255978 + t * 1.330274429))));
        if (z > 0) {
            return 1 - p;
        } else {
            return p;
        }
    }

    function calculateZTest() {
        const nA = parseInt(inputVisA.value) || 0;
        const xA = parseInt(inputEnrA.value) || 0;
        const nB = parseInt(inputVisB.value) || 0;
        const xB = parseInt(inputEnrB.value) || 0;

        if (nA === 0 || nB === 0) {
            return;
        }

        const pA = xA / nA;
        const pB = xB / nB;

        document.getElementById('crA').innerText = (pA * 100).toFixed(2) + '%';
        document.getElementById('crB').innerText = (pB * 100).toFixed(2) + '%';

        // Pooled Proportion
        const pPool = (xA + xB) / (nA + nB);
        document.getElementById('pPool').innerText = (pPool * 100).toFixed(2) + '%';

        // Standard Error
        const seVal = Math.sqrt(pPool * (1 - pPool) * ((1 / nA) + (1 / nB)));
        document.getElementById('seVal').innerText = seVal.toFixed(4);

        if (seVal === 0) {
            document.getElementById('zScore').innerText = '0.00';
            document.getElementById('pValue').innerText = '1.0000';
            setVerdict(0, 1);
            return;
        }

        // Z-Score
        const zScore = (pB - pA) / seVal;
        document.getElementById('zScore').innerText = (zScore > 0 ? '+' : '') + zScore.toFixed(2);

        // p-value (two-tailed)
        const pVal = 2 * (1 - normalCDF(Math.abs(zScore)));
        document.getElementById('pValue').innerText = pVal.toFixed(4);

        setVerdict(zScore, pVal);
    }

    function setVerdict(zScore, pValue) {
        const card = document.getElementById('verdictCard');
        const icon = document.getElementById('verdictIcon');
        const title = document.getElementById('verdictTitle');
        const desc = document.getElementById('verdictDesc');

        if (pValue < 0.05) {
            // Significant
            card.className = "p-6 rounded-[20px] bg-green-50 border border-green-100 flex gap-4 items-center";
            icon.className = "w-10 h-10 rounded-full flex items-center justify-center bg-green-600 text-white font-bold text-lg";
            icon.innerText = "✓";
            title.className = "font-black text-sm text-green-950";
            title.innerText = "Perbedaan Signifikan";
            const betterPlat = zScore > 0 ? selectB.options[selectB.selectedIndex].text : selectA.options[selectA.selectedIndex].text;
            desc.innerText = `Dengan p-value = ${pValue.toFixed(4)} (< 0.05), hipotesis nol ditolak. Performa ${betterPlat} secara signifikan lebih baik secara statistik.`;
        } else {
            // Not significant
            card.className = "p-6 rounded-[20px] bg-gray-50 border border-gray-100 flex gap-4 items-center";
            icon.className = "w-10 h-10 rounded-full flex items-center justify-center bg-gray-400 text-white font-bold text-lg";
            icon.innerText = "!";
            title.className = "font-black text-sm text-gray-900";
            title.innerText = "Tidak Ada Signifikansi";
            desc.innerText = `Dengan p-value = ${pValue.toFixed(4)} (>= 0.05), perbedaan performa ini mungkin hanya merupakan fluktuasi acak (noise).`;
        }
    }

    [selectA, selectB].forEach(el => el.addEventListener('change', populateABData));
    [inputVisA, inputEnrA, inputVisB, inputEnrB].forEach(el => el.addEventListener('input', calculateZTest));


    // ==========================================
    // 2. FORECASTING LOGIC
    // ==========================================

    const forecastPlat = document.getElementById('forecastPlatform');
    const inputTarget = document.getElementById('targetStudents');

    function calculateForecast() {
        const plat = platformsData[forecastPlat.value];
        const target = parseInt(inputTarget.value) || 0;

        const vis = parseFloat(plat.visitor) || 1;
        const lead = parseFloat(plat.lead) || 0;
        const exam = parseFloat(plat.exam) || 0;
        const regis = parseFloat(plat.regis) || 0;
        const student = parseFloat(plat.student) || 0;

        // Transition Probabilities
        const pL_V = vis > 0 ? (lead / vis) : 0;
        const pE_L = lead > 0 ? (exam / lead) : 0;
        const pR_E = exam > 0 ? (regis / exam) : 0;
        const pS_R = regis > 0 ? (student / regis) : 0;
        const pS_V = vis > 0 ? (student / vis) : 0;

        document.getElementById('probLead').innerText = (pL_V * 100).toFixed(1) + '%';
        document.getElementById('probExam').innerText = (pE_L * 100).toFixed(1) + '%';
        document.getElementById('probRegis').innerText = (pR_E * 100).toFixed(1) + '%';
        document.getElementById('probStudent').innerText = (pS_R * 100).toFixed(1) + '%';
        document.getElementById('probTotal').innerText = (pS_V * 100).toFixed(2) + '%';

        // Calculate required numbers backward
        const reqVis = pS_V > 0 ? Math.round(target / pS_V) : 0;
        const reqLead = pL_V > 0 ? Math.round(reqVis * pL_V) : 0;
        const reqExam = pE_L > 0 ? Math.round(reqLead * pE_L) : 0;
        const reqRegis = pR_E > 0 ? Math.round(reqExam * pR_E) : 0;

        document.getElementById('forecastVisitor').innerText = reqVis.toLocaleString();
        document.getElementById('forecastLead').innerText = reqLead.toLocaleString();
        document.getElementById('forecastExam').innerText = reqExam.toLocaleString();
        document.getElementById('forecastRegis').innerText = reqRegis.toLocaleString();
        document.getElementById('forecastStudent').innerText = target.toLocaleString();

        // Adjust Funnel Bar Widths (relative to historical platform visitor capacity so it changes dynamically as target changes)
        const maxScale = plat.visitor || 1;
        document.getElementById('barVisitor').style.width = Math.min(100, Math.max(5, (reqVis / maxScale) * 100)) + '%';
        document.getElementById('barLead').style.width = Math.min(100, Math.max(5, (reqLead / maxScale) * 100)) + '%';
        document.getElementById('barExam').style.width = Math.min(100, Math.max(5, (reqExam / maxScale) * 100)) + '%';
        document.getElementById('barRegis').style.width = Math.min(100, Math.max(5, (reqRegis / maxScale) * 100)) + '%';
        document.getElementById('barStudent').style.width = Math.min(100, Math.max(5, (target / maxScale) * 100)) + '%';
    }

    forecastPlat.addEventListener('change', calculateForecast);
    inputTarget.addEventListener('input', calculateForecast);

    // Initial load
    populateABData();
    calculateForecast();
</script>
@endsection
