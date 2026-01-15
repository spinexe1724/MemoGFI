@extends('layouts.app')

@section('title', 'Buat Akun - Memo System')

@section('content')
<div class="h-screen bg-[#FDFDFD] flex flex-col items-center justify-start pt-8 md:pt-12 p-6 overflow-hidden">
    
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-slate-900 rounded-2xl mb-3 shadow-xl shadow-slate-200">
            <i data-lucide="user-plus" class="w-5 h-5 text-white"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Akun Baru</h2>
        <p class="text-slate-400 text-[11px] mt-1 font-bold uppercase tracking-widest">Registrasi</p>
    </div>

    <div class="w-full max-w-3xl bg-white rounded-[2.5rem] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.06)] border border-slate-100 p-8 md:p-12 relative overflow-hidden">
        
        <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -mr-12 -mt-12 blur-3xl opacity-50"></div>

        <form action="{{ route('users.store') }}" method="POST" id="userForm" class="relative z-10 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                <div class="space-y-0.5 group">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" 
                           class="w-full py-2 bg-transparent border-b-2 border-slate-100 focus:border-red-600 outline-none transition-all text-sm font-bold text-slate-800 placeholder:text-slate-200" required>
                </div>

                <div class="space-y-0.5 group">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="john@company.com" 
                           class="w-full py-2 bg-transparent border-b-2 border-slate-100 focus:border-red-600 outline-none transition-all text-sm font-bold text-slate-800 placeholder:text-slate-200" required>
                </div>

                <div class="space-y-0.5 group">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Divisi</label>
                    <select name="division" class="w-full py-2 bg-transparent border-b-2 border-slate-100 focus:border-red-600 outline-none transition-all text-sm font-bold text-slate-800 appearance-none cursor-pointer" required>
                        <option value="" disabled selected>Pilih Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->name }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-0.5 group">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Cabang</label>
                    <select name="branch" class="w-full py-2 bg-transparent border-b-2 border-slate-100 focus:border-red-600 outline-none transition-all text-sm font-bold text-slate-800 appearance-none cursor-pointer" required>
                        <option value="" disabled selected>Pilih Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->name }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-red-900 rounded-2xl p-6 flex flex-col md:flex-row items-center gap-6 shadow-lg shadow-slate-100">
                <div class="flex-1 w-full">
                    <label class="text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 block">Role</label>
                    <select name="role" id="role_select" onchange="handleRoleChange()" class="w-full bg-transparent text-white text-base font-black outline-none border-none cursor-pointer">
                        <option value="supervisor" class="text-slate-900">Supervisor</option>
                        <option value="gm" class="text-slate-900">General Manager</option>
                        <option value="direksi" class="text-slate-900">Direksi</option>
                    </select>
                </div>
                <div class="w-px h-8 bg-white/10 hidden md:block"></div>
                <div class="flex-1 w-full" id="level_container">
                    <label class="text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 block">Access Level</label>
                    <select name="level" id="level_select" class="w-full bg-transparent text-white text-base font-black outline-none border-none cursor-pointer">
                        <option value="2" class="text-slate-900">Level 2</option>
                        <option value="3" class="text-slate-900">Level 3</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                <div class="space-y-0.5 relative group">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Password</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" 
                           class="w-full py-2 bg-transparent border-b-2 border-slate-100 focus:border-red-600 outline-none transition-all text-sm font-bold text-slate-800 placeholder:text-slate-200" required>
                    <button type="button" onclick="togglePassword('password')" class="absolute right-0 top-6 text-slate-300 hover:text-red-600"><i data-lucide="eye" class="w-3.5 h-3.5"></i></button>
                </div>

                <div class="space-y-0.5">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirm</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" 
                           class="w-full py-2 bg-transparent border-b-2 border-slate-100 focus:border-red-600 outline-none transition-all text-sm font-bold text-slate-800 placeholder:text-slate-200" required>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-slate-50">
                <a href="{{ route('users.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-red-600 transition-colors">
                    Cancel
                </a>
                <button type="submit" id="btnSubmit" class="px-10 py-3.5 bg-red-600 hover:bg-slate-900 text-white font-black rounded-xl shadow-lg shadow-red-200 transition-all transform hover:-translate-y-1 active:scale-95 tracking-widest uppercase text-[10px]">
                    Create Account
                </button>
            </div>
        </form>
    </div>

    <p class="mt-6 text-[9px] font-bold text-slate-300 uppercase tracking-[0.5em]">
        Gratama Finance Protocol
    </p>
</div>

<style>
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23e2e8f0'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0px center;
        background-size: 0.7rem;
    }
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    function handleRoleChange() {
        const rs = document.getElementById('role_select'), ls = document.getElementById('level_select'), lc = document.getElementById('level_container');
        if (rs.value === 'supervisor') {
            ls.value = "2"; ls.disabled = true;
            lc.style.opacity = "0.3";
        } else {
            ls.disabled = false;
            lc.style.opacity = "1";
        }
    }
    function togglePassword(id) {
        const i = document.getElementById(id); 
        i.type = i.type === 'password' ? 'text' : 'password';
    }
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        handleRoleChange();
        document.getElementById('userForm').addEventListener('submit', function() {
            const b = document.getElementById('btnSubmit');
            b.disabled = true;
            b.innerHTML = '<span class="animate-spin h-3 w-3 border-2 border-white border-t-transparent rounded-full"></span>';
        });
    });
</script>
@endsection