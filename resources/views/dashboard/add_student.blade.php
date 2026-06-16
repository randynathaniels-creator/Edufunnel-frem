@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen w-full p-10">
    <div class="max-w-[1400px] mx-auto">
        
        {{-- HEADER --}}
        <div class="mb-10">
            <h1 class="text-4xl font-black text-gray-900">Tambah Data Mahasiswa</h1>
            <p class="text-gray-400 mt-2">Masukkan informasi mahasiswa baru ke dalam sistem.</p>
        </div>

        {{-- FORM CARD --}}
        <div class="bg-white p-10 rounded-[24px] border border-gray-200 shadow-sm">
            <form action="{{ route('students.store') }}" method="POST" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Masukkan nama..." 
                            class="w-full px-5 py-4 rounded-2xl border {{ $errors->has('nama_lengkap') ? 'border-red-500' : 'border-gray-200' }} focus:ring-2 focus:ring-blue-600 outline-none font-bold text-gray-800" required>
                        @error('nama_lengkap') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@gmail.com" 
                            class="w-full px-5 py-4 rounded-2xl border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} focus:ring-2 focus:ring-blue-600 outline-none font-bold text-gray-800" required>
                        @error('email') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Asal Platform --}}
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Asal Platform</label>
                        <select name="asal_platform_id" class="w-full px-5 py-4 rounded-2xl border {{ $errors->has('asal_platform_id') ? 'border-red-500' : 'border-gray-200' }} focus:ring-2 focus:ring-blue-600 outline-none font-bold text-gray-800 bg-white">
                            @foreach($platforms as $p)
                                <option value="{{ $p->id }}" {{ old('asal_platform_id') == $p->id ? 'selected' : '' }}>{{ $p->sumber }}</option>
                            @endforeach
                        </select>
                        @error('asal_platform_id') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Status</label>
                        <select name="status" class="w-full px-5 py-4 rounded-2xl border {{ $errors->has('status') ? 'border-red-500' : 'border-gray-200' }} focus:ring-2 focus:ring-blue-600 outline-none font-bold text-gray-800 bg-white">
                            <option value="Lead" {{ old('status') == 'Lead' ? 'selected' : '' }}>Lead</option>
                            <option value="Test/Interview" {{ old('status') == 'Test/Interview' ? 'selected' : '' }}>Test/Interview</option>
                            <option value="Registered" {{ old('status') == 'Registered' ? 'selected' : '' }}>Registered</option>
                            <option value="Enrolled" {{ old('status') == 'Enrolled' ? 'selected' : '' }}>Enrolled</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="flex gap-4 pt-6 border-t border-gray-100">
                    <button type="submit" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                        Simpan 
                    </button>
                    <a href="{{ route('students.index') }}" class="px-10 py-4 rounded-2xl font-black text-gray-600 hover:bg-gray-100 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection