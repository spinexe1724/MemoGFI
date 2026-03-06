<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E - Memo Gratama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        /* Animasi mengambang untuk logo */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        /* Efek hover halus pada input */
        .input-focus-effect:focus {
            box-shadow: 0 0 0 4px rgba(128, 0, 0, 0.1);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="text-slate-900 overflow-x-hidden">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-2 bg-white/80 backdrop-blur-xl rounded-[3rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.15)] overflow-hidden border border-white/50">
            
<div class="hidden lg:flex p-16 flex-col items-center justify-center relative overflow-hidden bg-[rgb(128,0,0)] border-r border-slate-100">        
    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-red-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-10"></div>
    <div class="absolute top-0 right-0 w-40 h-40 bg-blue-400 rounded-full mix-blend-multiply filter blur-[80px] opacity-5"></div>

    <div class="z-10 flex flex-col items-center text-center animate-float">
        <div class="inline-block px-6 py-3 bg-white rounded-2xl shadow-xl shadow-red-900/5 mb-8 border border-slate-50">
            <span class="text-[#800000] font-bold text-xs tracking-tight">Login Memo Internal Gratama Finance</span> 
        </div>
        
        <div class="relative group">
            <img src="{{ asset('images/gratam.png') }}" class="h-23 w-auto drop-shadow-2xl transition-transform duration-500 group-hover:scale-105" alt="Logo">
            <div class="w-16 h-1 bg-[#800000] rounded-full mt-6 mx-auto opacity-50"></div>
        </div>
    </div>

    <div class="absolute bottom-10 text-[9px] font-bold text-slate-300 uppercase tracking-widest">
        Secure Access Portal
    </div>
</div>

            <div class="p-10 md:p-20 flex flex-col justify-center bg-white relative">
                <div class="mb-10">
                    <div class="lg:hidden mb-8">
                        <span class="text-[#800000] font-black text-3xl italic tracking-tighter">GRATAMA</span>
                    </div>
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Selamat Datang</h2>
                    <p class="text-slate-500 font-medium italic">Silahkan masuk ke akun Anda</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Email Corporate</label>
                        <input type="email" name="email" required 
                            class="input-focus-effect w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-300 font-semibold" 
                            placeholder="nama@gratama.com">
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kata Sandi</label>
                        </div>
                        <input type="password" name="password" required 
                            class="input-focus-effect w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-300 font-semibold"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full py-4 px-6 text-white bg-[#800000] hover:bg-[#600000] hover:shadow-[0_20px_40px_-10px_rgba(128,0,0,0.4)] font-extrabold rounded-2xl text-sm transition-all duration-300 active:scale-[0.97] flex items-center justify-center gap-3 group tracking-widest uppercase">
                            <span>Masuk Sekarang</span>
                            <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-16 flex justify-between items-center text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em]">
                    <p>© 2026 Gratama Finance. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
