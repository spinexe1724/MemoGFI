<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <style>
        #sidebar::-webkit-scrollbar { width: 4px; }
        #sidebar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .nav-link-active { 
            @apply bg-red-50 text-red-700 font-bold border border-red-100/50 shadow-sm shadow-red-100/50;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <aside id="sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white border-r border-gray-100 shadow-sm overflow-y-auto">
        
        <div class="sticky top-0 bg-white z-10 px-6 py-8">
            <div class="flex items-center justify-center p-1 rounded-2xl bg-gray-50 border border-gray-100 shadow-inner">
                <img src="{{ asset('images/gratam.PNG') }}" 
                     class="h-16 w-auto object-contain transform scale-110 transition-transform" 
                     alt="Logo" />
            </div>
        </div>

        <nav class="px-4 pb-6 space-y-8">
            @if(Auth::user()->role === 'superadmin')
                {{-- Kelompok Superadmin --}}
                <div>
                    <p class="px-4 mb-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Management</p>
                    <div class="space-y-1">
                        <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('users.*') ? 'bg-red-800 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">
                            <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                            <span class="font-bold text-sm">Manajemen User</span>
                        </a>
                        <a href="{{ route('memos.logs') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('memos.logs') ? 'bg-red-800 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">
                            <i data-lucide="file-clock" class="w-5 h-5 mr-3"></i>
                            <span class="font-bold text-sm">Log Memo Global</span>
                        </a>
                        <a href="{{ route('divisions.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('divisions.*') ? 'bg-red-800 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">
                            <i data-lucide="layers" class="w-5 h-5 mr-3"></i>
                            <span class="font-bold text-sm">Manajemen Divisi</span>
                        </a>
                        <a href="{{ route('branches.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('branches.*') ? 'bg-red-800 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">
                            <i data-lucide="map-pin" class="w-5 h-5 mr-3"></i>
                            <span class="font-bold text-sm">Manajemen Cabang</span>
                        </a>
                    </div>
                </div>
            @else
                {{-- Kelompok 1: Navigasi Utama --}}
                <div>
                    <p class="px-4 mb-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Utama</p>
                    <div class="space-y-1">
                        <a href="{{ route('memos.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('memos.index') ? 'bg-red-800 text-white shadow-lg shadow-red-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="layout-grid" class="w-5 h-5 mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="font-bold text-sm">Dashboard</span>
                        </a>

                        @php
                            $user = Auth::user();
                            // Pastikan peran 'bm' (Branch Manager) termasuk dalam pemberi persetujuan
                            $isApprover = in_array($user->role, ['manager', 'bm', 'gm', 'direksi']) || strtoupper($user->division) === 'GA';
                            
                            // Hitung jumlah memo yang menunggu persetujuan user ini
                            $approvalCount = $isApprover ? \App\Http\Controllers\MemoController::getPendingCount() : 0;
                        @endphp

                        @if($isApprover)
                        <a href="{{ route('memos.approvals') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('memos.approvals') ? 'bg-red-800 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="signature" class="w-5 h-5 mr-3 transition-transform group-hover:scale-110"></i>
                            <span class="font-bold text-sm">Persetujuan</span>
                            @if($approvalCount > 0)
                                <span class="ml-auto bg-amber-400 text-red-900 text-[10px] px-2 py-0.5 rounded-full font-black animate-bounce shadow-sm">
                                    {{ $approvalCount }}
                                </span>
                            @endif
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Kelompok 2: Dokumen Saya --}}
                <div>
                    <p class="px-4 mb-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Dokumen Saya</p>
                    <div class="space-y-1">
                        @if(in_array($user->role, ['supervisor', 'manager', 'admin']))
                        <a href="{{ route('memos.create') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-red-50 mb-1">
                            <i data-lucide="plus-circle" class="w-5 h-5 mr-3 text-red-600 transition-transform group-hover:scale-110"></i>
                            <span class="font-bold text-sm text-red-600">Buat Memo Baru</span>
                        </a>
                        @endif
                        
                        <a href="{{ route('memos.own') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('memos.own') ? 'bg-gray-100 text-gray-900 font-bold border-l-4 border-red-800' : 'text-gray-500 hover:bg-gray-50' }}">
                            <i data-lucide="user-check" class="w-5 h-5 mr-3"></i>
                            <span class="text-sm">Memo Saya</span>
                        </a>

                        <a href="{{ route('memos.rejectedMemos') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('memos.rejectedMemos') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-500 hover:bg-gray-50' }}">
                            <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                            <span class="text-sm">Memo Ditolak</span>
                        </a>

                        <a href="{{ route('memos.drafts') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('memos.drafts') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-500 hover:bg-gray-50' }}">
                            <i data-lucide="archive" class="w-5 h-5 mr-3"></i>
                            <span class="text-sm">Memo Draf</span>
                            @php $draftCount = \App\Models\Memo::where('user_id', Auth::id())->where('is_draft', true)->count(); @endphp
                            @if($draftCount > 0)
                                <span class="ml-auto text-[10px] font-bold text-gray-400 px-2 py-0.5 bg-gray-200 rounded-full">{{ $draftCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                {{-- Kelompok 3: Referensi Global --}}
                <div>
                    <p class="px-4 mb-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Referensi</p>
                    <div class="space-y-1">
                        <a href="{{ route('memos.my_memos') }}" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('memos.my_memos') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-500 hover:bg-gray-50' }}">
                            <i data-lucide="database" class="w-5 h-5 mr-3 transition-transform group-hover:scale-110 text-slate-400"></i>
                            <span class="font-bold text-sm">Memo Aktif</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Bagian Sistem --}}
            <div class="pt-4 border-t border-gray-50">
                <p class="px-4 mb-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Sistem</p>
                <div class="space-y-1">
                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-500 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all duration-200 group">
                            <i data-lucide="log-out" class="w-5 h-5 mr-3 transition-transform group-hover:translate-x-1"></i>
                            <span class="font-bold text-sm">Keluar Aplikasi</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="absolute bottom-6 left-0 w-full px-8">
            <div class="p-4 bg-gray-900 rounded-2xl shadow-xl text-center">
                <p class="text-[10px] text-gray-400 font-medium tracking-widest uppercase mb-1">Versi Aplikasi</p>
                <p class="text-xs text-white font-black tracking-tighter italic">Gratama <span class="text-red-500 font-bold">v.1.0</span></p>
            </div>
        </div>
    </aside>

    <div class="sm:ml-64 min-h-screen flex flex-col">
        <nav class="sticky top-0 z-40 flex items-center justify-between px-6 py-4 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
            <div class="flex items-center">
                <button onclick="toggleSidebar()" class="p-2 mr-3 text-gray-600 rounded-xl sm:hidden hover:bg-gray-100 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div class="hidden sm:block">
                    <h2 class="text-sm font-bold text-gray-800 tracking-tight italic uppercase">Internal <span class="text-red-800">Memo System</span></h2>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden md:flex flex-col text-right mr-1">
                    <p class="text-sm font-black text-gray-900 leading-none"> {{ Auth::user()->name }} </p>
                    <p class="text-[10px] font-bold text-red-600 mt-1 uppercase tracking-widest italic leading-none">
                         {{ Auth::user()->role }} - {{ Auth::user()->division }} 
                    </p>
                </div>
                
                <div class="relative group">
                    <div class="p-0.5 rounded-full bg-gradient-to-tr from-red-600 to-amber-400 shadow-md">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=fff&color=b91c1c&bold=true" 
                             class="w-10 h-10 rounded-full border-2 border-white object-cover" 
                             alt="User Profile">
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow p-6">
            @yield('content')
        </main>

        <footer class="p-6 text-center text-gray-400 text-[11px] font-medium border-t border-gray-100">
            &copy; {{ date('Y') }} PT. Gratama. All rights reserved.
        </footer>
    </div>

    <script>
        lucide.createIcons();
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
    </script>
    @stack('scripts')
</body>
</html>