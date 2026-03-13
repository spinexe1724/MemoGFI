<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | E - Memo Gratama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-maroon-pattern {
            background-color: #800000;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }

        /* Gaya Kustom untuk Dropdown (Select) */
        .custom-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1.25rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 3.5rem !important;
        }
        .custom-select:focus {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23800000' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-4 md:p-10">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 bg-white rounded-[3rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] overflow-hidden border border-white">
            
            <div class="hidden lg:flex p-16 flex-col justify-center items-center relative overflow-hidden bg-maroon-pattern">
                <div class="z-10 flex flex-col items-center text-center">
                    <div class="px-6 py-4 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-xl mb-10 border border-white/20">
                        <span class="text-white font-bold text-sm tracking-tight">Pendaftaran Akun E-Memo Gratama</span> 
                    </div>
                    
                    <div class="p-8 rounded-3xl">
                        <img src="{{ asset('images/gratamaa.PNG') }}" class="h-24 w-auto" alt="Logo">
                    </div>

                    <p class="text-[10px] text-white/50 font-bold tracking-[0.4em] uppercase mt-8">Secure Registration Portal</p>
                </div>

                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white rounded-full mix-blend-overlay filter blur-[100px] opacity-20"></div>
                <div class="absolute -top-20 -right-20 w-80 h-80 bg-black rounded-full mix-blend-multiply filter blur-[100px] opacity-30"></div>
            </div>

            <div class="p-8 md:p-16 flex flex-col justify-center bg-white max-h-[90vh]">
                <div class="mb-8">
                    <div class="lg:hidden mb-6">
                        <span class="text-[#800000] font-black text-3xl italic">GRATAMA</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Buat Akun</h2>
                    <p class="text-slate-400 mt-2 font-medium">Lengkapi data diri Anda untuk mendaftar</p>
                </div>

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                        <input type="text" name="name" required 
                            class="w-full px-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold" 
                            placeholder="Input nama lengkap">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" name="email" required 
                            class="w-full px-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold" 
                            placeholder="nama@gratama.com">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Kata Sandi --}}
    <div class="space-y-1 relative">
        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Kata Sandi</label>
        <div class="relative">
            <input type="password" name="password" id="password" required 
                class="w-full px-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold"
                placeholder="••••••••" minlength="8">
            <button type="button" onclick="togglePassword('password', 'eye-icon-1')" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#800000]">
                <svg id="eye-icon-1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
        <p id="pass-min-msg" class="hidden text-[10px] text-red-600 font-bold ml-1 italic mt-1 uppercase tracking-wider">Minimal harus 8 karakter!</p>
    </div>

    {{-- Konfirmasi Sandi --}}
    <div class="space-y-1 relative">
        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi Sandi</label>
        <div class="relative">
            <input type="password" name="password_confirmation" id="password_confirmation" required 
                class="w-full px-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold"
                placeholder="••••••••">
            <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#800000]">
                <svg id="eye-icon-2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
        <p id="match-msg" class="hidden text-[10px] text-red-600 font-bold ml-1 italic mt-1 uppercase tracking-wider">Konfirmasi sandi tidak sesuai!</p>
    </div>
</div>
                     {{-- Input Nomor Telepon (Menggantikan Jabatan dan Divisi) --}}
{{-- Nomor Telepon --}}
<div class="space-y-1">
    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Telepon</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-6 pointer-events-none">
            <span class="text-slate-400 text-sm font-bold">+62</span>
        </div>
        <input type="text" name="phone_number" id="phone" required 
            class="w-full pl-16 pr-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold" 
            placeholder="812xxxxxxx">
    </div>
    <p id="phone-error" class="hidden text-[10px] text-red-600 font-bold ml-1 italic mt-1 uppercase tracking-wider">Hanya boleh memasukkan angka!</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-1 gap-4">
    {{-- DROPDOWN CABANG (Tetap Dipertahankan) --}}
    <div class="space-y-1">
        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Cabang</label>
        <select name="branch" required 
            class="w-full px-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold appearance-none custom-select">
            <option value="">-- Pilih Cabang --</option>
            @foreach($branches as $br)
                <option value="{{ $br->code }}" {{ old('branch') == $br->code ? 'selected' : '' }}>{{ $br->name }} ({{ $br->code }})</option>
            @endforeach
        </select>
    </div>
</div>
                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full py-4 px-6 text-white bg-[#800000] hover:bg-black font-extrabold rounded-2xl text-sm shadow-[0_20px_40px_-12px_rgba(128,0,0,0.3)] transition-all duration-500 active:scale-[0.98] flex items-center justify-center gap-3 group tracking-widest uppercase">
                            <span>Daftar Akun</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-slate-500 font-medium">
                            Sudah memiliki akun? 
                            <a href="{{ route('login') }}" class="text-[#800000] font-bold hover:underline underline-offset-4 transition-all">
                                Masuk di sini
                            </a>
                        </p>
                    </div>
                </form>

                <div class="mt-12 flex justify-between items-center text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">
                    <p>© 2026 all rights reserved</p>
                </div>
            </div>
        </div>
    </div>

</body>
<script>
    // Fungsi Show/Hide Password
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.add('text-[#800000]');
        } else {
            input.type = 'password';
            icon.classList.remove('text-[#800000]');
        }
    }

    // Validasi Password & Konfirmasi
    const password = document.getElementById('password');
    const confirmPass = document.getElementById('password_confirmation');
    const matchMsg = document.getElementById('match-msg');
    const minMsg = document.getElementById('pass-min-msg');

    function validate() {
        // Validasi Minimal 8 Karakter
        if (password.value.length > 0 && password.value.length < 8) {
            minMsg.classList.remove('hidden');
        } else {
            minMsg.classList.add('hidden');
        }

        // Validasi Kesesuaian
        if (confirmPass.value.length > 0) {
            if (password.value !== confirmPass.value) {
                matchMsg.classList.remove('hidden');
                confirmPass.classList.add('border-red-600');
            } else {
                matchMsg.classList.add('hidden');
                confirmPass.classList.remove('border-red-600');
            }
        }
    }

    password.addEventListener('input', validate);
    confirmPass.addEventListener('input', validate);

    // Validasi Nomor Telepon (Hanya Angka)
const phoneInput = document.getElementById('phone');
const phoneError = document.getElementById('phone-error');

phoneInput.addEventListener('input', function(e) {
    // Menghapus karakter yang bukan angka
    const sanitizedValue = this.value.replace(/[^0-9]/g, '');
    
    if (this.value !== sanitizedValue) {
        // Tampilkan pesan error jika ada karakter non-angka
        phoneError.classList.remove('hidden');
        this.classList.add('border-red-600');
        
        // Paksa nilai input hanya menjadi angka
        this.value = sanitizedValue;
    } else {
        phoneError.classList.add('hidden');
        this.classList.remove('border-red-600');
    }
});
</script>

</html>