@extends('layouts.app')

@section('title', 'Buat Akun Baru - Memo System')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="bg-white shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-3xl overflow-hidden border border-gray-100">
        
        <div class="relative bg-gradient-to-r from-red-800 to-red-700 p-8 text-white overflow-hidden">
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight">Create New Account</h2>
                    <p class="text-red-100 mt-2 opacity-90 flex items-center">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                        Daftarkan pengguna baru ke dalam sistem manajemen memo.
                    </p>
                </div>
                <div class="hidden md:block bg-white/10 p-4 rounded-2xl backdrop-blur-md">
                    <i data-lucide="shield-check" class="w-10 h-10 text-white"></i>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        <form action="{{ route('users.store') }}" method="POST" id="userForm" class="p-8 md:p-10 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 tracking-wide uppercase flex items-center">
                        <i data-lucide="user" class="w-4 h-4 mr-2 text-red-600"></i> Full Name
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" 
                           class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none shadow-sm" required>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 tracking-wide uppercase flex items-center">
                        <i data-lucide="mail" class="w-4 h-4 mr-2 text-red-600"></i> Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@perusahaan.com" 
                           class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none shadow-sm" required>
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 tracking-wide uppercase">Division</label>
                    <select name="division" class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none shadow-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236B7280%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');" required>
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->name }}" {{ old('division') == $div->name ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 tracking-wide uppercase">Account Role</label>
                    <select name="role" id="role_select" onchange="handleRoleChange()" 
                            class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none shadow-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236B7280%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');" required>
                        <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="gm" {{ old('role') == 'gm' ? 'selected' : '' }}>General Manager (GM)</option>
                        <option value="direksi" {{ old('role') == 'direksi' ? 'selected' : '' }}>Direksi</option>
                    </select>
                </div>

                <div class="space-y-2" id="level_container">
                    <label class="text-sm font-bold text-gray-700 tracking-wide uppercase">Access Level</label>
                    <select name="level" id="level_select" class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none shadow-sm">
                        <option value="2" {{ old('level') == '2' ? 'selected' : '' }}>Level 2 (Divisi)</option>
                        <option value="3" {{ old('level') == '3' ? 'selected' : '' }}>Level 3 (Global)</option>
                    </select>
                    <p id="supervisor_note" class="hidden text-[10px] text-blue-600 mt-1 italic font-medium leading-tight">
                        * Supervisor dikunci ke Level 2 (Hanya akses divisi sendiri).
                    </p>
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 tracking-wide uppercase">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none shadow-sm" required>
                        <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 tracking-wide uppercase">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none shadow-sm" required>
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-end space-y-4 md:space-y-0 md:space-x-6 pt-10 border-t border-gray-100">
                <a href="{{ route('users.index') }}" class="w-full md:w-auto text-center font-semibold text-gray-500 hover:text-gray-800 transition">
                    Batalkan
                </a>
                <button type="submit" id="btnSubmit" class="w-full md:w-auto bg-gradient-to-br from-blue-700 to-blue-800 hover:from-blue-800 hover:to-blue-900 text-white font-bold py-4 px-12 rounded-2xl shadow-xl shadow-blue-200 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                    <i data-lucide="save" class="w-5 h-5 mr-3"></i>
                    Save Account
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    function handleRoleChange() {
        const roleSelect = document.getElementById('role_select');
        const levelSelect = document.getElementById('level_select');
        const note = document.getElementById('supervisor_note');
        const levelContainer = document.getElementById('level_container');

        if (!roleSelect || !levelSelect) return;

        const selectedRole = roleSelect.value;

        if (selectedRole === 'supervisor') {
            levelSelect.value = "2";
            levelSelect.disabled = true;
            levelSelect.classList.add('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
            if (note) note.classList.remove('hidden');

            if (!document.getElementById('hidden_level_input')) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'level';
                hidden.value = '2';
                hidden.id = 'hidden_level_input';
                levelContainer.appendChild(hidden);
            }
        } else {
            levelSelect.disabled = false;
            levelSelect.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
            if (note) note.classList.add('hidden');
            
            const hidden = document.getElementById('hidden_level_input');
            if (hidden) hidden.remove();
        }
    }

    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        handleRoleChange();

        // Loading state on submit
        document.getElementById('userForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin mr-3 h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span> Processing...';
        });
    });
</script>
@endsection