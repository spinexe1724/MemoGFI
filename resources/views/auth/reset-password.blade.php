<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setel Ulang Kata Sandi | E - Memo Gratama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-maroon-pattern {
            background-color: #800000;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-4 md:p-10">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 bg-white rounded-[3rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] overflow-hidden border border-white">
            
            {{-- Sisi Kiri: Branding (Sama dengan Login/Register) --}}
            <div class="hidden lg:flex p-16 flex-col justify-center items-center relative overflow-hidden bg-maroon-pattern">
                <div class="z-10 flex flex-col items-center text-center">
                    <div class="px-6 py-4 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-xl mb-10 border border-white/20">
                        <span class="text-white font-bold text-sm tracking-tight">Pemulihan Akun E-Memo Gratama</span> 
                    </div>
                    
                    <div class="p-8 rounded-3xl">
                        <img src="{{ asset('images/gratamaa.PNG') }}" class="h-24 w-auto" alt="Logo" onerror="this.src='https://via.placeholder.com/200x80?text=GRATAMA'">
                    </div>

                    <p class="text-[10px] text-white/50 font-bold tracking-[0.4em] uppercase mt-8">Secure Password Recovery</p>
                </div>

                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white rounded-full mix-blend-overlay filter blur-[100px] opacity-20"></div>
                <div class="absolute -top-20 -right-20 w-80 h-80 bg-black rounded-full mix-blend-multiply filter blur-[100px] opacity-30"></div>
            </div>

            {{-- Sisi Kanan: Form Reset Password --}}
            <div class="p-8 md:p-16 flex flex-col justify-center bg-white">
                <div class="mb-10">
                    <div class="lg:hidden mb-6">
                        <span class="text-[#800000] font-black text-3xl italic">GRATAMA</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Kata Sandi Baru</h2>
                    <p class="text-slate-400 mt-2 font-medium">Buat kata sandi yang kuat untuk keamanan akun Anda</p>
                </div>

                {{-- Validasi Error --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl">
                        <ul class="text-xs text-red-600 font-bold space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    
                    <div class="space-y-1">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                            class="w-full px-6 py-4 bg-slate-100 border border-slate-200 text-slate-500 text-sm rounded-2xl cursor-not-allowed font-semibold outline-none" 
                            placeholder="nama@gratama.com">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Kata Sandi Baru</label>
                        <input type="password" name="password" required autofocus
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold"
                            placeholder="••••••••">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full py-4 px-6 text-white bg-[#800000] hover:bg-black font-extrabold rounded-2xl text-sm shadow-[0_20px_40px_-12px_rgba(128,0,0,0.3)] transition-all duration-500 active:scale-[0.98] flex items-center justify-center gap-3 group tracking-widest uppercase">
                            <span>Simpan Kata Sandi</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-12 flex justify-between items-center text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">
                    <p>© 2026 GRATAMA all rights reserved</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>