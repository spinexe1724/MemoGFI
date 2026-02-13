<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E - Memo Gratama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-pattern {
            background-color: #800000;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-2 bg-white rounded-[3rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] overflow-hidden border border-white">
            
            <div class="hidden lg:flex bg-pattern p-16 flex-col justify-between relative overflow-hidden">
                <div class="z-10">
                    <div class="w-26 h-16 bg-white rounded-2xl flex items-center justify-center shadow-2xl mb-10">
                        <span class="text-[#800000] font-black text-1xl">Login Memo Internal Gratama Finance</span> 
                    </div>
                    
                    <img src="{{ asset('images/gratam.PNG') }}" class="h-23 w-auto brightness-0 invert opacity-80" alt="Logo">
                    <p class="text-[10px] text-red-300/40 font-bold tracking-[0.4em] uppercase mt-4"></p>
                    <p class="text-red-200/60 mt-6 text-lg leading-relaxed max-w-xs">
                        Platform komunikasi terintegrasi untuk efisiensi operasional Gratama Finance.
                    </p>
                </div>


                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-red-500 rounded-full mix-blend-soft-light filter blur-[100px] opacity-20"></div>
            </div>

            <div class="p-10 md:p-20 flex flex-col justify-center bg-white">
                <div class="mb-12">
                    <div class="lg:hidden mb-8">
                        <span class="text-[#800000] font-black text-4xl italic">GRATAMA</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-slate-400 mt-2 font-medium">Silahkan masuk ke akun Anda</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" name="email" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:ring-4 focus:ring-red-500/5 focus:border-[#800000] focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-300 font-semibold" 
                            placeholder="nama@gratama.com">
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Kata Sandi</label>
                        </div>
                        <input type="password" name="password" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:ring-4 focus:ring-red-500/5 focus:border-[#800000] focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-300 font-semibold"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full py-4 px-6 text-white bg-[#800000] hover:bg-black font-extrabold rounded-2xl text-sm shadow-[0_20px_40px_-12px_rgba(128,0,0,0.3)] transition-all duration-500 active:scale-[0.98] flex items-center justify-center gap-3 group tracking-widest uppercase">
                            <span>Masuk</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-16 flex justify-between items-center text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">
                    <p>© 2026 all rights reserved</p>
                    <div class="flex gap-6">
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>