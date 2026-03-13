@extends('layouts.app')

@section('title', request('from') == 'verification' ? 'Aktivasi Pengguna Baru' : 'Edit Akun Pengguna')

@section('content')
<div class="py-10 px-4">
    <div class="max-w-3xl mx-auto">
        
        {{-- Breadcrumb & Back Link --}}
        <div class="mb-6">
            <a href="{{ request('from') == 'verification' ? route('users.verification') : route('users.index') }}" 
               class="inline-flex items-center text-xs font-black uppercase tracking-widest text-gray-400 hover:text-red-800 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali ke {{ request('from') == 'verification' ? 'Antrean Verifikasi' : 'Daftar Pengguna' }}
            </a>
        </div>

        {{-- Header Card --}}
        @php $isVerification = request('from') == 'verification'; @endphp
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="{{ $isVerification ? 'bg-amber-600' : 'bg-red-800' }} p-8 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black italic uppercase tracking-tight">
                        {{ $isVerification ? 'Verifikasi & Aktivasi' : 'Perbarui Profil' }} 
                        <span class="{{ $isVerification ? 'text-amber-900' : 'text-red-200' }}">Pengguna</span>
                    </h2>
                    <p class="text-sm opacity-80 mt-1 font-medium italic">
                        {{ $isVerification ? 'Tentukan role dan divisi agar user dapat mengakses dashboard.' : 'Kelola informasi identitas dan hak akses pengguna.' }}
                    </p>
                </div>
                <i data-lucide="{{ $isVerification ? 'shield-check' : 'user-cog' }}" class="w-12 h-12 opacity-20"></i>
            </div>

            <form action="{{ route('users.update', $user->id) }}" method="POST" class="p-8 md:p-12 space-y-8">
                @csrf
                @method('PUT')

                {{-- Hidden flag to handle redirect after update --}}
                @if($isVerification)
                    <input type="hidden" name="from_verification" value="1">
                @endif

                {{-- Section 1: Data Identitas (Read-only for verification focus) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-red-800 transition-all outline-none font-bold">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-red-800 transition-all outline-none font-bold">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor Telepon</label>
                        <div class="relative">
                            <i data-lucide="phone" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
                                   class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-red-800 transition-all outline-none font-bold">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cabang Penempatan</label>
                        <div class="relative">
                            <i data-lucide="map-pin" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                            <select name="branch" required class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-red-800 transition-all outline-none font-bold appearance-none">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($branches as $br)
                                    <option value="{{ $br->code }}" {{ old('branch', $user->branch) == $br->code ? 'selected' : '' }}>
                                        {{ $br->name }} ({{ $br->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Penetapan Akses (Highlight for Verification) --}}
                <div class="p-8 rounded-[2rem] {{ $isVerification ? 'bg-amber-50 border-2 border-dashed border-amber-200' : 'bg-slate-50 border border-slate-100' }}">
                    <h3 class="text-xs font-black {{ $isVerification ? 'text-amber-800' : 'text-slate-500' }} uppercase tracking-[0.2em] mb-6 flex items-center">
                        <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                        {{ $isVerification ? 'Lengkapi Hak Akses Untuk Aktivasi' : 'Konfigurasi Hak Akses' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Role / Jabatan</label>
                            <select name="role" required class="w-full px-6 py-4 bg-white border border-gray-100 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold appearance-none shadow-sm">
                                <option value="pending" disabled {{ $user->role == 'pending' ? 'selected' : '' }}>-- Pilih Role --</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Branch)</option>
                                <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="bm" {{ old('role', $user->role) == 'bm' ? 'selected' : '' }}>Branch Manager</option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="direksi" {{ old('role', $user->role) == 'direksi' ? 'selected' : '' }}>Direksi</option>
                                <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Divisi Kerja</label>
                            <select name="division" required class="w-full px-6 py-4 bg-white border border-gray-100 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold appearance-none shadow-sm">
                                <option value="Pending" disabled {{ $user->division == 'Pending' ? 'selected' : '' }}>-- Pilih Divisi --</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->name }}" {{ old('division', $user->division) == $div->name ? 'selected' : '' }}>
                                        {{ $div->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Level Akses</label>
                            <select name="level" required class="w-full px-6 py-4 bg-white border border-gray-100 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold appearance-none shadow-sm">
                                <option value="0" disabled {{ $user->level == 0 ? 'selected' : '' }}>-- Pilih Level --</option>
                                <option value="2" {{ old('level', $user->level) == '2' ? 'selected' : '' }}>Level 2 (Akses Divisi)</option>
                                <option value="3" {{ old('level', $user->level) == '3' ? 'selected' : '' }}>Level 3 (Akses Global)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Keamanan --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ubah Kata Sandi (Opsional)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                            <input type="password" name="password" placeholder="••••••••"
                                   class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-red-800 transition-all outline-none font-bold">
                        </div>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Sandi Baru"
                                   class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-red-800 transition-all outline-none font-bold">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 italic ml-1 mt-1 font-medium">* Kosongkan jika tidak ingin mengganti kata sandi pengguna.</p>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                    <div class="flex items-center text-amber-600 font-bold text-xs uppercase italic">
                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                        Pastikan Role & Divisi sesuai penempatan.
                    </div>
                    
                    <button type="submit" 
                            class="px-10 py-4 {{ $isVerification ? 'bg-amber-600 hover:bg-black shadow-amber-100' : 'bg-red-800 hover:bg-black shadow-red-100' }} text-white font-black rounded-2xl text-[11px] shadow-xl transition-all duration-300 active:scale-95 flex items-center gap-3 uppercase tracking-widest italic">
                        <span>{{ $isVerification ? 'Verifikasi & Aktifkan Akun' : 'Simpan Perubahan' }}</span>
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endsection