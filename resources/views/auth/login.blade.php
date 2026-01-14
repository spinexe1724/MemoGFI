<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Memo System Pro</title>
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
            
            <div class="hidden lg:flex bg-slate-900 p-12 flex-col justify-between relative overflow-hidden">
                <div class="z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 text-xs font-bold tracking-widest uppercase mb-8">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                        </span>
                        System v2.0
                    </div>
                    <h1 class="text-5xl font-extrabold text-white leading-tight">
                        Hubungkan <br> <span class="text-indigo-500">Divisi</span> Anda.
                    </h1>
                    <p class="text-slate-400 mt-6 text-lg max-w-sm leading-relaxed">
                        Sistem korespondensi memo digital yang dirancang untuk efisiensi maksimal dan transparansi data.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-8 opacity-40">
                    <div class="h-32 bg-slate-800 rounded-3xl p-4 border border-slate-700">
                        <div class="h-2 w-12 bg-slate-700 rounded-full mb-2"></div>
                        <div class="h-2 w-8 bg-slate-700 rounded-full"></div>
                    </div>
                    <div class="h-32 bg-indigo-600 rounded-3xl p-4 flex items-end justify-end">
                         <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>

                <div class="z-10 flex items-center gap-4 text-slate-500 text-sm italic font-medium">
                    <div class="flex -space-x-2">
                        <img class="w-8 h-8 rounded-full border-2 border-slate-900" src="https://ui-avatars.com/api/?name=A&background=random" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-slate-900" src="https://ui-avatars.com/api/?name=B&background=random" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-slate-900" src="https://ui-avatars.com/api/?name=C&background=random" alt="">
                    </div>
                    <span>Terpercaya oleh 20+ Divisi</span>
                </div>
                
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-600 rounded-full mix-blend-screen filter blur-[100px] opacity-20"></div>
            </div>

            <div class="p-8 md:p-16 flex flex-col justify-center">
                <div class="mb-10">
                    <h2 class="text-4xl font-black text-slate-900 tracking-tighter">Sign In</h2>
                    <p class="text-slate-500 mt-2 font-medium">Selamat datang kembali! Silakan login.</p>
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
                            <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Forgot Password?</a>
                        </div>
                        <input type="password" name="password" required 
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 text-slate-900 text-sm rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-400 font-medium"
                            placeholder="••••••••">
                    </div>

                    <div class="flex items-center px-1 py-2">
                        <label class="relative flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="sr-only peer">
                            <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-indigo-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-semibold text-slate-600">Remember session</span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 px-6 text-white bg-slate-900 hover:bg-indigo-600 font-bold rounded-2xl text-lg shadow-xl shadow-slate-200 transition-all duration-500 active:scale-95 flex items-center justify-center gap-2 group">
                        <span>Get Started</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                    <p>Memo Digital v2.0</p>
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