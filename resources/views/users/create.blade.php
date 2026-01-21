@extends('layouts.app')

@section('title', 'Buat Akun Baru - Sistem Memo')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-start pt-4 px-6 pb-12">
    
    <div class="w-full max-w-6xl bg-white rounded-[2.5rem] shadow-[0_25px_70px_rgba(0,0,0,0.06)] border border-gray-100 overflow-hidden">
        
        <div class="bg-[#800000] py-6 px-10 flex flex-col md:flex-row md:items-center md:justify-between border-b-4 border-red-900">
            <div class="text-left">
                <h2 class="text-3xl font-black text-white tracking-tight uppercase">Tambah Akun Baru</h2>
                <p class="text-red-200 text-xs mt-1 font-bold uppercase tracking-[0.2em]">Pendaftaran Pengguna Baru Sistem Memo Gratama</p>
            </div>
            <div class="hidden md:block bg-white/10 p-3 rounded-2xl backdrop-blur-sm">
                <i data-lucide="user-plus" class="w-8 h-8 text-white"></i>
            </div>
        </div>

        <form action="{{ route('users.store') }}" method="POST" id="userForm" class="p-8 md:p-12 space-y-8">
            @csrf
            
            <div class="space-y-4">
                <h3 class="text-[11px] font-black text-red-700 uppercase tracking-[0.3em] mb-4 flex items-center">
                    <span class="w-8 h-[2px] bg-red-700 mr-3"></span> Informasi Personal & Lokasi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-2 space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap sesuai identitas" 
                               class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-red-50 focus:border-[#800000] outline-none transition-all text-sm font-bold text-gray-800" required>
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@gratama.com" 
                               class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-red-50 focus:border-[#800000] outline-none transition-all text-sm font-bold text-gray-800" required>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Divisi Kerja</label>
                        <select name="division" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:border-[#800000] outline-none transition-all text-sm font-bold text-gray-800 appearance-none cursor-pointer" required>
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->name }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Branch (Cabang)</label>
                    <select name="branch" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:border-[#800000] outline-none transition-all text-sm font-bold text-gray-800 appearance-none cursor-pointer" required>
                    <option value="">-- Pilih Cabang --</option>
                        @foreach($branches as $branch)
                            {{-- PERBAIKAN: value menggunakan code, tampilan tetap name --}}
                            <option value="{{ $branch->code }}" {{ old('branch') == $branch->code ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('branch') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> 
                    @enderror
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jabatan / Role</label>
                        <select name="role" id="role_select" onchange="handleRoleChange()" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:border-[#800000] outline-none transition-all text-sm font-bold text-gray-800 appearance-none cursor-pointer" required>
                            <option value="admin">Admin</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="bm">Branch Manager</option>
                            <option value="manager">Manager</option>
                            <option value="ga">General Affair</option>
                            <option value="gm">General Manager</option>
                            <option value="direksi">Direksi</option>
                        </select>
                    </div>

                    <div class="space-y-1" id="level_container">
                        <label class="text-[10px] font-black text-blue-900 uppercase tracking-widest ml-1">Level Akses</label>
                        <select name="level" id="level_select" class="w-full px-5 py-3.5 bg-blue-50/50 border border-blue-100 rounded-2xl outline-none text-sm font-bold text-blue-800">
                            <option value="2">Level 2 (Divisi Sendiri)</option>
                            <option value="3">Level 3 (Akses Global)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-6">
                <div class="bg-gray-50 rounded-[2rem] border border-gray-200 p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-white p-2 rounded-lg shadow-sm mr-3">
                            <i data-lucide="lock" class="w-4 h-4 text-[#800000]"></i>
                        </div>
                        <h3 class="text-xs font-black text-gray-700 uppercase tracking-[0.2em]">Kredensial Keamanan</h3>
                        <p id="role_lock_note" class="hidden ml-auto text-[9px] text-blue-600 font-bold italic uppercase tracking-widest bg-blue-100 px-3 py-1 rounded-full">
                            * Level Akses Dikunci untuk Admin/Supervisor
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1 relative">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kata Sandi Baru</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" 
                                       oninput="validatePasswordLength()"
                                       class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:border-[#800000] outline-none text-sm font-bold text-gray-800 transition-all" required>
                                <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#800000]">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                            <p id="pw_hint" class="text-[9px] font-bold text-red-500 mt-1 ml-1 opacity-0 transition-opacity">
                                <i data-lucide="alert-circle" class="w-3 h-3 inline mr-1"></i> Kata sandi minimal 8 huruf/karakter!
                            </p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi" 
                                   class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:border-[#800000] outline-none text-sm font-bold text-gray-800" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex flex-col md:flex-row items-center justify-between gap-6">
                <a href="{{ route('users.index') }}" class="group flex items-center text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-red-700 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                    Batal & Kembali
                </a>
                <button type="submit" id="btnSubmit" class="w-full md:w-auto px-20 py-4 bg-[#800000] hover:bg-black text-white font-black rounded-2xl shadow-2xl shadow-red-200 transition-all transform hover:-translate-y-1 active:scale-95 tracking-[0.2em] uppercase text-xs flex items-center justify-center">
                    <i data-lucide="save" class="w-4 h-4 mr-3"></i> Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
    
    <p class="mt-6 text-[10px] font-bold text-gray-300 uppercase tracking-[0.4em]">Gratama Finance Security Layer v2.0</p>
</div>

<style>
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 1.25rem center; background-size: 0.8rem;
    }
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Fungsi Validasi Panjang Password
    function validatePasswordLength() {
        const pwInput = document.getElementById('password');
        const pwHint = document.getElementById('pw_hint');
        
        if (pwInput.value.length > 0 && pwInput.value.length < 8) {
            pwHint.classList.remove('opacity-0');
            pwInput.classList.add('border-red-500', 'ring-4', 'ring-red-50');
        } else {
            pwHint.classList.add('opacity-0');
            pwInput.classList.remove('border-red-500', 'ring-4', 'ring-red-50');
        }
    }

    function handleRoleChange() {
        const rs = document.getElementById('role_select'), ls = document.getElementById('level_select'), 
              note = document.getElementById('role_lock_note'), lc = document.getElementById('level_container');
        const locked = ['supervisor', 'admin'].includes(rs.value);
        
        ls.disabled = locked;
        if (locked) {
            ls.value = "2";
            ls.classList.add('opacity-60', 'cursor-not-allowed');
            note.classList.remove('hidden');
        } else {
            ls.classList.remove('opacity-60', 'cursor-not-allowed');
            note.classList.add('hidden');
        }

        let hi = document.getElementById('hidden_level_input');
        if (locked && !hi) {
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = 'level'; h.value = '2'; h.id = 'hidden_level_input';
            lc.appendChild(h);
        } else if (!locked && hi) { hi.remove(); }
    }

    function togglePassword(id) {
        const i = document.getElementById(id); i.type = i.type === 'password' ? 'text' : 'password';
    }

    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        handleRoleChange();

        // Validasi Akhir Saat Submit
        document.getElementById('userForm').addEventListener('submit', function(e) {
            const pw = document.getElementById('password').value;
            const confirmPw = document.getElementById('password_confirmation').value;

            if (pw.length < 8) {
                e.preventDefault();
                alert('Gagal: Kata sandi harus minimal 8 karakter!');
                document.getElementById('password').focus();
                return;
            }

            if (pw !== confirmPw) {
                e.preventDefault();
                alert('Gagal: Konfirmasi kata sandi tidak cocok!');
                document.getElementById('password_confirmation').focus();
                return;
            }

            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-3"></span> MEMPROSES...';
        });
    });
</script>
@endsection