<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E - Memo Gratama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-[#F1F5F9] text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-4 md:p-8">
        <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-2 bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-white">
            
        <div class="hidden lg:flex bg-gradient-to-br from-red-950 via-red-900 to-red-950 p-12 flex-col justify-between relative overflow-hidden">
    <div class="z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 text-xs font-bold tracking-widest uppercase mb-8">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
            </span>
            Memo Internal Gratama Finance
        </div>
        <h1 class="text-5xl font-extrabold text-white leading-tight">
            Hubungkan <br> <span class="text-red-500">Divisi</span> Anda.
        </h1>

        <br>
        <br>
        <br>
        <div class="mt-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg transform -rotate-6">
                <span class="text-red-900 font-black text-2xl">G</span> 
            </div>
            <div>
            <img src="{{ asset('images/gratam.PNG') }}" 
                     class="h-16 w-auto object-contain transform scale-110 transition-transform" 
                     alt="Logo" />
                <p class="text-red-500 text-xs font-medium tracking-[0.2em] mt-1">TRUSTED PARTNER</p>
            </div>
        </div>
    </div>

    <div class="z-10 flex items-center gap-4 text-slate-500 text-sm italic font-medium">
    </div>
    
    <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-600 rounded-full mix-blend-screen filter blur-[100px] opacity-20"></div>
</div>

            <div class="p-8 md:p-16 flex flex-col justify-center">
                <div class="mb-10">
                    <h2 class="text-4xl font-black text-slate-900 tracking-tighter">Login</h2>
                    <p class="text-slate-500 mt-2 font-medium">Selamat datang kembali</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Work Email</label>
                        <input type="email" name="email" required 
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 text-slate-900 text-sm rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-400 font-medium" 
                            placeholder="admin@perusahaan.com">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2 px-1">
                            <label class="text-sm font-bold text-slate-700">Password</label>
                        </div>
                        <input type="password" name="password" required 
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 text-slate-900 text-sm rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-400 font-medium"
                            placeholder="••••••••">
                    </div>

                    <div class="flex items-center px-1 py-2">
                        <label class="relative flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="sr-only peer">
                            
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 px-6 text-white bg-slate-900 hover:bg-indigo-600 font-bold rounded-2xl text-lg shadow-xl shadow-slate-200 transition-all duration-500 active:scale-95 flex items-center justify-center gap-2 group">
                        <span>Masuk</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                    <p>E - MEMO</p>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-slate-900">Privacy</a>
                        <a href="#" class="hover:text-slate-900">Help</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>