<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - EduFunnel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white">

    <div class="flex h-screen w-full overflow-hidden">
        <div class="hidden lg:flex w-1/2 bg-[#0052D4] p-12 flex-col justify-between relative">
            <div class="z-10 flex items-center space-x-2 text-white">
                <div class="w-8 h-8 bg-white/20 rounded flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <span class="text-xl font-bold tracking-tight">EduFunnel</span>
            </div>

            <div class="z-10">
                <p class="text-blue-200 text-sm font-semibold tracking-widest uppercase mb-4">Admissions Intelligence</p>
                <h1 class="text-5xl font-bold text-white leading-tight mb-6">
                    Selamat Datang di <br> EduFunnel
                </h1>
                <p class="text-blue-100 text-lg max-w-md leading-relaxed">
                    Pantau corong penerimaan, sumber trafik, dan analitik konversi mahasiswa secara real-time.
                </p>
            </div>

            <div class="z-10 bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-2xl max-w-sm shadow-2xl">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-600 rounded-lg shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold italic">Predictive Analytics</h4>
                        <p class="text-blue-200 text-[10px]">Model kecerdasan untuk estimasi yield mahasiswa.</p>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-[-10%] right-[-10%] opacity-10">
                <svg width="500" height="500" viewBox="0 0 24 24" fill="none" class="text-white"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="0.5"/></svg>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-24 lg:px-32 bg-white overflow-y-auto">
            <div class="max-w-md w-full mx-auto py-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Masuk</h2>
                <p class="text-gray-500 mb-8">Akses dasbor admin Anda</p>

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl flex items-center text-red-700 text-sm font-semibold">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('login.process') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                            </span>
                            <input type="email" name="email" required 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all" 
                                placeholder="admin@edufunnel.id">
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2">
                            <label class="block text-sm font-semibold text-gray-700">Kata Sandi</label>
                            <a href="#" class="text-xs font-bold text-blue-600 hover:underline">Lupa Kata Sandi?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input type="password" id="password" name="password" required 
                                class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all" 
                                placeholder="••••••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600 font-medium">Ingat Saya</label>
                    </div>

                    <button type="submit" class="w-full bg-[#0052D4] text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-[0.98] transition-all uppercase tracking-wider">
                        Masuk
                    </button>

                    <div class="relative flex items-center justify-center py-2">
                        <div class="border-t border-gray-200 w-full"></div>
                        <div class="border-t border-gray-200 w-full"></div>
                    </div>
                </form>

                <p class="mt-12 text-center text-[10px] text-gray-400 font-medium uppercase tracking-widest">
                    © 2026 Frem Karya rendi gemink
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
            } else {
                pwInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
    </script>
</body>
</html>