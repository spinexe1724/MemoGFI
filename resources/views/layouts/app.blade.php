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
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Custom Scrollbar untuk Sidebar */
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #b91c1c !important; /* red-700 */
            color: white !important;
            border-radius: 8px !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <aside id="sidebar" class="fixed top-0 left-0 z-50 w-68 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white border-r border-gray-100 shadow-sm overflow-y-auto">
        
        <div class="sticky top-0 bg-white z-10 px-6 py-8">
            <div class="flex items-center justify-center p-2 rounded-2xl bg-gray-50 border border-gray-100 shadow-inner">
                <img src="{{ asset('images/Capture.PNG') }}" class="h-12 w-auto object-contain" alt="Logo Gratama" />
            </div>
        </div>

        <nav class="px-4 pb-6 space-y-6">
            
            <div>
                <p class="px-4 mb-3 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Menu Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('memos.index') }}" class="flex items-center px-4 py-3 text-red-700 rounded-xl bg-red-50 font-bold border border-red-100/50 transition-all duration-200 group shadow-sm shadow-red-100/50">
                        <i data-lucide="layout-grid" class="w-5 h-5 mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="#" class="flex items-center px-4 py-3 text-gray-500 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group">
                        <i data-lucide="mail" class="w-5 h-5 mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Memo Saya</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="px-4 mb-3 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Sistem</p>
                <div class="space-y-1">
                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-500 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all duration-200 group">
                            <i data-lucide="log-out" class="w-5 h-5 mr-3 transition-transform group-hover:translate-x-1"></i>
                            <span class="font-medium">Keluar Aplikasi</span>
                        </button>
                    </form>
                </div>
            </div>

        </nav>

        <div class="absolute bottom-6 left-0 w-full px-8">
            <div class="p-4 bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-xl shadow-gray-200 text-center">
                <p class="text-[10px] text-gray-400 font-medium">Versi Aplikasi</p>
                <p class="text-xs text-white font-bold">Gratama v2.0.1</p>
            </div>
        </div>
    </aside>

    <div class="sm:ml-68 min-h-screen flex flex-col">
        
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
                    <p class="text-[10px] font-bold text-red-600 mt-1 uppercase tracking-tighter tracking-widest">
                         {{ Auth::user()->role }} 
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

        <main class="flex-grow">
            @yield('content')
        </main>

        <footer class="p-6 text-center text-gray-400 text-[11px] font-medium border-t border-gray-100">
            &copy; 2026 PT. Gratama. All rights reserved.
        </footer>
    </div>

    <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Dropdown Toggle (Opsional jika dibutuhkan nanti)
        function toggleDropdown(id) {
            const element = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id);
            element.classList.toggle('hidden');
            if(arrow) arrow.classList.toggle('rotate-180');
        }
    </script>
    @stack('scripts')
</body>
</html>