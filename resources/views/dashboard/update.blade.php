@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen w-full transition-colors duration-300 p-10">
    <div class="max-w-[1400px] mx-auto">
        
        {{-- HEADER DASHBOARD --}}
        <div class="mb-10 flex justify-between items-center">
            <div>
                <a href="{{ route('reports') }}" class="text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Dashboard
                </a>
                <h1 class="text-4xl font-black text-gray-900">Data Management</h1>
            </div>
            <div class="bg-blue-100 p-3 rounded-2xl">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
        </div>

        {{-- NOTIFIKASI FLASH MESSAGE --}}
        @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex gap-3 items-center text-emerald-800 text-sm font-semibold animate-fade-in">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- INFORMASI --}}
        <div class="mb-10 bg-blue-50 border border-blue-100 p-6 rounded-[24px] flex gap-4 items-center">
            <span class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg">!</span>
            <p class="text-sm text-blue-800 leading-relaxed">
                Perubahan data di sini akan langsung memperbarui **Grafik Performa**, **Tabel Metrik**, **Visualisasi Funnel**, dan **Analisa AI** di seluruh dashboard secara real-time.
            </p>
        </div>

        {{-- FORM 1: UPDATE KONTRIBUSI PER SUMBER PLATFORM --}}
        <div class="mb-14">
            <div class="border-b border-gray-200 pb-3 mb-6">
                <h2 class="text-xl font-extrabold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
                    Kontribusi Per Platform Sumber Trafik
                </h2>
                <p class="text-xs text-gray-400 mt-1">Perbarui metrik volume corong (Funneling) dari setiap media kampanye</p>
            </div>

            <form action="{{ route('admin.save') }}" method="POST">
                @csrf
                @foreach($data['platforms'] ?? [] as $index => $platform)
                    @if(strtolower($platform['sumber'] ?? '') === 'total') @continue @endif
                    
                    <div class="bg-gray-50 p-8 rounded-2xl mb-6 border border-gray-200 shadow-sm">
                        <input type="hidden" name="platforms[{{ $index }}][sumber]" value="{{ $platform['sumber'] }}">

                        <h3 class="font-black uppercase tracking-widest mb-4 text-gray-800 text-sm flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                            {{ $platform['sumber'] }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Visitors</label>
                                <input type="number" name="platforms[{{ $index }}][visitors]" value="{{ $platform['visitor'] }}" class="w-full bg-white border border-gray-300 rounded-xl p-3 mt-1 text-gray-900 font-semibold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Applicants</label>
                                <input type="number" name="platforms[{{ $index }}][lead]" value="{{ $platform['lead'] }}" class="w-full bg-white border border-gray-300 rounded-xl p-3 mt-1 text-gray-900 font-semibold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tes / Wawancara</label>
                                <input type="number" name="platforms[{{ $index }}][exam_taker]" value="{{ $platform['exam_taker'] }}" class="w-full bg-white border border-gray-300 rounded-xl p-3 mt-1 text-gray-900 font-semibold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Daftar Ulang</label>
                                <input type="number" name="platforms[{{ $index }}][registered]" value="{{ $platform['registered'] }}" class="w-full bg-white border border-gray-300 rounded-xl p-3 mt-1 text-gray-900 font-semibold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Enrolled Students</label>
                                <input type="number" name="platforms[{{ $index }}][student]" value="{{ $platform['student'] }}" class="w-full bg-white border border-gray-300 rounded-xl p-3 mt-1 text-gray-900 font-semibold">
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-blue-700 transition-all shadow-lg mt-4">
                    Deploy Platform Updates
                </button>
            </form>
        </div>

       {{-- FORM 2: UPDATE GRAFIK TREN BULANAN --}}
{{-- FORM: UPDATE TREND MINGGUAN --}}
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mt-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Update Data Mingguan</h3>
    <form action="{{ route('admin.save-weekly') }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">Pilih Minggu Target</label>
            <select name="week_label" class="w-full bg-white border border-gray-200 rounded-xl p-3 font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Week 1">Week 1</option>
                <option value="Week 2">Week 2</option>
                <option value="Week 3">Week 3</option>
                <option value="Week 4">Week 4</option>
            </select>
        </div>
        
      <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
    <div class="space-y-2">
        <label class="text-[10px] font-black text-gray-400 uppercase">{{ $day }}</label>
        
        <div class="relative">
            <input type="number" name="days[{{ $day }}]" 
                   value="{{ $data['weekly_details'][$day]->current_value ?? 0 }}" 
                   class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm font-bold text-center focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                   placeholder="Aktual">
        </div>
        
        <input type="number" name="targets[{{ $day }}]" 
               value="{{ $data['weekly_details'][$day]->comparison_value ?? 0 }}" 
               placeholder="Target"
               class="w-full bg-white border border-dashed border-gray-300 rounded-lg p-2 text-[12px] font-bold text-center text-gray-500 focus:border-blue-400 focus:text-blue-600 transition-all">
    </div>
    @endforeach
</div>

<button type="submit" 
        onclick="return confirm('Apakah Anda yakin ingin mengupdate target bulanan berdasarkan data minggu ini?')"
        class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-blue-200">
    Hitung & Update Grafik Bulanan
</button>
    </form>
</div>
@endsection