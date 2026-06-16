<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFunnel | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F9FAFB] text-[#1F2937]">

    <div class="flex min-h-screen">
        <aside class="w-72 bg-white border-r border-gray-100 flex flex-col fixed h-full z-10">
            <div class="p-8">
                <div class="flex items-center space-x-3 text-blue-600 mb-12">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-gray-900">EduFunnel</span>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('overview') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ Request::is('overview') ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                        <span class="mr-3">📊</span> Overview
                    </a>
                    <a href="{{ route('funnel') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ Request::is('funnel') ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                        <span class="mr-3">🎯</span> Funnel Analytics
                    </a>
                    <a href="{{ route('reports') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ Request::is('reports') ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                        <span class="mr-3">📈</span> Source Reports
                    </a>
                    <a href="{{ route('statistics') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ Request::is('statistics') ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                        <span class="mr-3">🧮</span> Alat Statistik
                    </a>
                    <a href="{{ route('admin.update') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ Request::is('update') ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                        <span class="mr-3">⚙️</span> Update Data
                    </a>
                     <a href="{{ route('students.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ Request::is('dashboard/students/index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                        <span class="mr-3">🎓</span> Student 
                    </a>
                </nav>
            </div>
            
            <div class="mt-auto p-8">
                <a href="{{ route('logout') }}" class="flex items-center justify-center space-x-2 px-4 py-3 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100 transition-all">
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <div class="flex-1 ml-72">
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-0 px-10 flex items-center justify-between">
                <h2 class="font-semibold text-gray-400 uppercase tracking-widest text-xs">Admin System</h2>
                
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        @php
                            // Ambil nama lengkap user aktif dari session login
                            $namaAdmin = session('admin_nama', 'Guest Admin');
                        @endphp
                        <p class="text-sm font-bold text-gray-900">{{ $namaAdmin }}</p>
                        <p class="text-[10px] text-gray-500">Administrator</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-600 rounded-full border-2 border-white shadow-sm flex items-center justify-center text-white font-bold text-sm uppercase">
                        {{ substr($namaAdmin, 0, 2) }}
                    </div>
                </div>
            </header>

            <div class="p-10">
                @yield('content')
            </div>
        </div>
    </div>

</body>
</html>