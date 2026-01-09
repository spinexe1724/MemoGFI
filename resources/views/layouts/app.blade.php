<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    @vite('resources/css/app.css')
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>White Clean Dashboard</title>
    <!-- Tailwind CSS CDN sebagai fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Mengatur tinggi minimum editor agar nyaman digunakan */
        .ck-editor__editable {
            min-height: 300px;
        }
        /* Style untuk validasi error */
        .error-text {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-50">

    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white border-r border-gray-200">
        
        <div class="flex items-center px-6 py-5 bg-gray shadow-md">
        <a href="" class="flex items-center">
         <img src="{{ asset('images/Capture.PNG')}}" class="" style="height:auto;"/>
         <span class="self-center text-lg text-heading font-semibold whitespace-nowrap"></span>
      </a>
        </div>

        <nav class="mt-6 px-3 space-y-2">
            <a href="#" class="flex items-center p-3 text-blue-600 rounded-xl bg-blue-50 font-medium group">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span class="ml-3">Dashboard</span>
            </a>
            <!-- <div class="space-y-1">
                <button onclick="toggleDropdown('ecommerce-drop')" class="flex items-center justify-between w-full p-3 text-gray-600 rounded-xl hover:bg-gray-50 transition group">
                    <div class="flex items-center">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        <span class="ml-3">E-Commerce</span>
                    </div>
                    <i data-lucide="chevron-down" id="arrow-ecommerce-drop" class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div id="ecommerce-drop" class="hidden pl-11 pr-3 py-1 space-y-1">
                    <a href="#" class="block p-2 text-sm text-gray-500 hover:text-blue-600 rounded-lg">Products</a>
                    <a href="#" class="block p-2 text-sm text-gray-500 hover:text-blue-600 rounded-lg">Orders</a>
                </div>
            </div> -->
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                
            <div class="pt-4 mt-4 border-t border-gray-100">
            <button type="submit" class="flex items-center p-3 text-gray-500 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all group">Logout</button>
            @csrf
                
            </div>
            </form>
        </nav>
    </aside>

    <div class="sm:ml-64">
        
        <nav class="sticky top-0 z-30 flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200">
            <button onclick="toggleSidebar()" class="p-2 text-gray-600 rounded-lg sm:hidden hover:bg-gray-100">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <p>Memo Internal System Gratama</p>

            <div class="flex items-center space-x-4">
                
                <div class="h-8 w-[1px] bg-gray-200"></div>
                <div class="flex items-center gap-3 cursor-pointer">
                    <div class="text-right hidden md:block">
                        <p class="text-xl font-semibold text-black-900 leading-none"> {{ Auth::user()->name }} </p>
                        <p class="text-[13px] text-black-500 mt-1">({{ strtoupper(Auth::user()->role) }})</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=AJ&background=0D8ABC&color=fff" class="w-9 h-9 rounded-full border border-gray-200 shadow-sm" alt="User">
                </div>
            </div>
        </nav>

        <main class="p-8">
            @yield('content')
        </main>
    </div>

</body>
</html>