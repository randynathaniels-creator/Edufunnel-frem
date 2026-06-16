@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen w-full transition-colors duration-300 p-10">
    <div class="max-w-[1400px] mx-auto">
        
        {{-- HEADER DASHBOARD --}}
        <div class="mb-10 flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-black text-gray-900">Daftar Mahasiswa</h1>
                <p class="text-gray-400 mt-2">Kelola data mahasiswa dan sinkronisasi statistik secara real-time.</p>
            </div>
            <a href="{{ route('students.create') }}" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                + Tambah Mahasiswa
            </a>
        </div>

        {{-- Form Search --}}
        <form id="search-form" class="mb-6 flex gap-2">
            <input type="text" id="search-input" name="search" value="{{ request('search') }}" 
                placeholder="Cari nama, platform, atau status..." 
                class="w-full md:w-1/3 px-5 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-600">
            
          <button type="button" class="bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-800 transition-all flex items-center justify-center">
    <ion-icon name="search-outline" class="text-xl"></ion-icon>
</button>
        </form>

        {{-- TABEL DATA --}}
        <div class="bg-white border border-gray-200 rounded-[24px] shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-6 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</th>
                        <th class="p-6 text-xs font-black text-gray-400 uppercase tracking-widest">Email</th>
                        <th class="p-6 text-xs font-black text-gray-400 uppercase tracking-widest">Platform</th>
                        <th class="p-6 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="p-6 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="student-table-body" class="divide-y divide-gray-100">
                    @foreach($students as $student)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-6 font-bold text-gray-800">{{ $student->nama_lengkap }}</td>
                        <td class="p-6 text-gray-600">{{ $student->email }}</td>
                        <td class="p-6 text-gray-600 font-semibold">{{ $student->platform_name }}</td>
                        <td class="p-6">
                            <span class="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-full text-xs font-black uppercase tracking-wider">
                                {{ $student->status }}
                            </span>
                        </td>
                        <td class="p-6 text-center flex items-center justify-center gap-4">
                            <a href="{{ route('students.edit', $student->id) }}" class="text-blue-500 hover:text-blue-700 transition-all pr-4 border-r border-gray-300">
                                <ion-icon name="create" class="text-2xl"></ion-icon> 
                            </a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition-all">
                                    <ion-icon name="trash" class="text-2xl"></ion-icon>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div id="no-data-msg" class="p-20 text-center text-gray-400 font-bold hidden">
                Data tidak ditemukan!
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#search-input').on('keyup', function() {
        let value = $(this).val();
        
        $.ajax({
            url: "{{ route('students.index') }}",
            type: "GET",
            data: { 'search': value },
            success: function(data) {
                let tbody = '';
                if (data.length > 0) {
                    $('#no-data-msg').addClass('hidden');
                    data.forEach(function(s) {
                        tbody += `<tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-6 font-bold text-gray-800">${s.nama_lengkap}</td>
                            <td class="p-6 text-gray-600">${s.email}</td>
                            <td class="p-6 text-gray-600 font-semibold">${s.platform_name}</td>
                            <td class="p-6">
                                <span class="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-full text-xs font-black uppercase tracking-wider">${s.status}</span>
                            </td>
                            <td class="p-6 text-center flex items-center justify-center gap-4">
                                <a href="/dashboard/students/${s.id}/edit" class="text-blue-500 hover:text-blue-700 transition-all pr-4 border-r border-gray-300">
                                    <ion-icon name="create" class="text-2xl"></ion-icon> 
                                </a>
                                <form action="/dashboard/students/${s.id}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data?')">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-all">
                                        <ion-icon name="trash" class="text-2xl"></ion-icon>
                                    </button>
                                </form>
                            </td>
                        </tr>`;
                    });
                } else {
                    $('#no-data-msg').removeClass('hidden');
                }
                $('#student-table-body').html(tbody);
            }
        });
    });
</script>
@endsection