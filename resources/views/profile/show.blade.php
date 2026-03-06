@extends('layouts.app')

@section('title', 'Profil Saya | E-Memo Gratama')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div>
            <nav class="flex mb-3" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li class="text-[10px] font-black uppercase tracking-widest text-gray-400">Account</li>
                    <li class="text-[10px] font-black uppercase tracking-widest text-red-800">/ Settings</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">
                Informasi <span class="text-red-800">Profile</span>
            </h1>
        </div>
    </div>

    <form action="{{ route('show.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column: Identity Card --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-[3rem] p-8 shadow-2xl shadow-gray-200/50 border border-gray-50 relative overflow-hidden">
                    {{-- Decorative Element --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                    
                    <div class="relative z-10 text-center">
                        <div class="inline-block p-1.5 rounded-full bg-gradient-to-tr from-red-800 to-red-500 mb-6 shadow-xl shadow-red-200">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ffffff&color=b91c1c&size=128&bold=true" 
                                 class="w-28 h-28 rounded-full border-4 border-white object-cover" alt="User Avatar">
                        </div>
                        <h2 class="text-xl font-black text-gray-900 leading-tight uppercase">{{ $user->name }}</h2>
                        <div class="inline-block px-4 py-1.5 bg-red-50 rounded-full mt-3">
                            <p class="text-[10px] font-black text-red-800 uppercase tracking-tighter italic">
                                {{ $user->role }} — {{ $user->division }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 space-y-4 border-t border-dashed border-gray-100 pt-8">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase">Status Akun</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-md uppercase">Aktif</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase">Bergabung</span>
                            <span class="text-[10px] font-bold text-gray-700">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-black rounded-[2rem] p-6 text-white overflow-hidden relative group cursor-pointer">
                    <div class="relative z-10">
                        <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-1">Butuh Bantuan?</p>
                        <p class="text-xs font-medium text-gray-400">Hubungi tim IT jika Anda mengalami kendala akses data.</p>
                    </div>
                    <i data-lucide="help-circle" class="absolute right-[-10px] bottom-[-10px] w-20 h-20 text-white/5 transform -rotate-12 group-hover:rotate-0 transition-transform"></i>
                </div>
            </div>

            {{-- Right Column: Form Fields --}}
            <div class="lg:col-span-8 space-y-8">
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-3xl flex items-center animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center mr-3 shadow-lg shadow-emerald-200">
                        <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    </div>
                    <span class="text-xs font-black uppercase italic">{{ session('success') }}</span>
                </div>
                @endif

                <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-8 flex items-center">
                        <span class="w-8 h-1 bg-red-800 mr-3 rounded-full"></span>
                        Informasi Dasar
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Email Address</label>
                            <div class="relative">
                                <i data-lucide="mail" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                                <input type="text" value="{{ $user->email }}" disabled 
                                       class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none text-gray-400 text-sm rounded-2xl cursor-not-allowed font-bold">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Full Name</label>
                            <div class="relative">
                                <i data-lucide="user" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-red-800"></i>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 text-gray-900 text-sm rounded-2xl focus:ring-4 focus:ring-red-500/5 focus:border-red-800 transition-all outline-none font-bold shadow-sm">
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-8 mt-12 flex items-center">
                        <span class="w-8 h-1 bg-red-800 mr-3 rounded-full"></span>
                        Keamanan Kata Sandi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">New Password</label>
                            <div class="relative">
                                <i data-lucide="lock" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                                <input type="password" name="password" placeholder="••••••••"
                                       class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 text-gray-900 text-sm rounded-2xl focus:ring-4 focus:ring-red-500/5 focus:border-red-800 transition-all outline-none font-bold shadow-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Confirm Password</label>
                            <div class="relative">
                                <i data-lucide="shield-check" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                                <input type="password" name="password_confirmation" placeholder="••••••••"
                                       class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 text-gray-900 text-sm rounded-2xl focus:ring-4 focus:ring-red-500/5 focus:border-red-800 transition-all outline-none font-bold shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex items-center justify-between p-6 bg-red-50/50 rounded-[2rem] border border-red-100/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-800 flex items-center justify-center text-white">
                                <i data-lucide="info" class="w-5 h-5"></i>
                            </div>
                            <p class="text-[10px] font-bold text-red-900 leading-tight uppercase">
                                Pastikan data sudah benar sebelum <br>menekan tombol simpan.
                            </p>
                        </div>
                        <button type="submit" 
                                class="px-8 py-4 bg-red-800 hover:bg-black text-white font-black rounded-2xl text-[10px] shadow-xl shadow-red-200 transition-all duration-300 active:scale-[0.95] flex items-center gap-3 uppercase tracking-widest italic">
                            <span>Update Profile</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection