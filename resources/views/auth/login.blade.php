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
        .bg-maroon-pattern {
            background-color: #800000;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
        /* Menghilangkan scrollbar tapi tetap bisa scroll jika diperlukan */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-4 md:p-10">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 bg-white rounded-[3rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] overflow-hidden border border-white min-h-[750px]">
            
            <div class="hidden lg:flex p-16 flex-col justify-center items-center relative overflow-hidden bg-maroon-pattern">
                <div class="z-10 flex flex-col items-center text-center">
                    <div class="px-6 py-4 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-xl mb-10 border border-white/20">
                        <span class="text-white font-bold text-sm tracking-tight">Login Memo Internal Gratama Finance</span> 
                    </div>
                    
                    <div class="p-8 rounded-3xl">
                        <img src="{{ asset('images/gratamaa.PNG') }}" class="h-24 w-auto" alt="Logo">
                    </div>

                    <p class="text-[10px] text-white/50 font-bold tracking-[0.4em] uppercase mt-8">Secure Access Portal</p>
                </div>

                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white rounded-full mix-blend-overlay filter blur-[100px] opacity-20"></div>
                <div class="absolute -top-20 -right-20 w-80 h-80 bg-black rounded-full mix-blend-multiply filter blur-[100px] opacity-30"></div>
            </div>

            <div class="p-8 md:p-16 flex flex-col bg-white min-h-[750px] no-scrollbar">
                
                <div class="my-auto">
                    <div class="mb-12">
                        <div class="lg:hidden mb-8">
                            <span class="text-[#800000] font-black text-3xl italic">GRATAMA</span>
                        </div>
                        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang</h2>
                        <p class="text-slate-400 mt-2 font-medium">Silahkan masuk ke akun Anda</p>
                    </div>

                    {{-- Menampilkan Pesan Error Validasi --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl" role="alert">
                            <div class="text-xs text-red-700 font-bold">
                                {{ $errors->first() }}
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                            <input type="email" name="email" required 
                                class="w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-300 font-semibold" 
                                placeholder="nama@gratama.com">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Kata Sandi</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required 
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all duration-300 outline-none placeholder:text-slate-300 font-semibold pr-12"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-[#800000] transition-colors focus:outline-none">
                                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                          {{-- TAUTAN LUPA PASSWORD --}}
                            <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-[#800000] hover:underline">
                                Lupa Kata Sandi?
                            </a>

                        <div class="pt-2">
                            <button type="submit" 
                                class="w-full py-4 px-6 text-white bg-[#800000] hover:bg-black font-extrabold rounded-2xl text-sm shadow-[0_20px_40px_-12px_rgba(128,0,0,0.3)] transition-all duration-500 active:scale-[0.98] flex items-center justify-center gap-3 group tracking-widest uppercase">
                                <span>Masuk</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mt-8 text-center">
                            <p class="text-sm text-slate-500 font-medium">
                                Belum punya akun? 
                                <a href="{{ route('register') }}" class="text-[#800000] font-bold hover:underline underline-offset-4 transition-all">
                                    Daftar Sekarang
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <div class="mt-8 flex justify-between items-center text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">
                    <p>© 2026 all rights reserved</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>