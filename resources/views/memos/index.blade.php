<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    @vite('resources/css/app.css')
    <title>Dashboard Memo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>White Clean Dashboard</title>
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
            <a href="#" class="flex items-center p-3 text-gray-500 rounded-xl hover:bg-gray-100 hover:text-gray-900 transition-all group">
                <i data-lucide="shopping-bag" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                <span class="ml-3">Products</span>
            </a>
            <div class="space-y-1">
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
            </div>
            <a href="#" class="flex items-center p-3 text-gray-500 rounded-xl hover:bg-gray-100 hover:text-gray-900 transition-all group">
                <i data-lucide="mail" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                <span class="ml-3">Messages</span>
            </a>
            
            <div class="pt-4 mt-4 border-t border-gray-100">
                <a href="#" class="flex items-center p-3 text-gray-500 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all group">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="ml-3">Sign Out</span>
                </a>
            </div>
        </nav>
    </aside>

    <div class="sm:ml-64">
        
        <nav class="sticky top-0 z-30 flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200">
            <button onclick="toggleSidebar()" class="p-2 text-gray-600 rounded-lg sm:hidden hover:bg-gray-100">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <div class="hidden sm:flex items-center bg-gray-100 px-3 py-2 rounded-lg border border-transparent focus-within:border-blue-400 focus-within:bg-white transition-all">
                <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                <input type="text" class="ml-2 bg-transparent outline-none text-sm w-64" placeholder="Search anything...">
            </div>

            <div class="flex items-center space-x-4">
                
                <div class="h-8 w-[1px] bg-gray-200"></div>
                <div class="flex items-center gap-3 cursor-pointer">
                    <div class="text-right hidden md:block">
                        <p class="text-xs font-semibold text-gray-900 leading-none"> User: {{ Auth::user()->name }} </p>
                        <p class="text-[10px] text-gray-500 mt-1">({{ strtoupper(Auth::user()->role) }})</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=AJ&background=0D8ABC&color=fff" class="w-9 h-9 rounded-full border border-gray-200 shadow-sm" alt="User">
                </div>
            </div>
        </nav>

        <main class="p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Memo({{ strtoupper(Auth::user()->role) }})</h2>
                <p class="text-gray-500">List Memo Internal Gratama Finance</p>
            </div>

            <div class="max-w-9xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header Panel -->
        <div class="bg-red-800 p-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Internal Memos</h1>
                <p class="text-blue-100 italic text-sm"></p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-bold transition-all transform active:scale-95">Logout</button>
            </form>
        </div>

        <div class="p-6">
            <div class="flex justify-between mb-6 items-center">
                <h2 class="text-xl font-bold text-gray-800">Daftar Memo Internal</h2>
                @if(Auth::user()->role === 'staff')
                    <a href="{{ route('memos.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-bold shadow-md transition-all">
                        + Buat Memo Baru
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="p-3 border text-sm font-bold text-gray-600">Detail Memo</th>
                            <th class="p-3 border text-sm font-bold text-gray-600">Status & Progres</th>
                            <th class="p-3 border text-center text-sm font-bold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($memos as $memo)
                        @php
                            // Perbaikan: Tambahkan pengecekan jika valid_until bernilai null
                            $isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt($memo->valid_until) : false;
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $isExpired || $memo->is_rejected ? 'bg-red-50' : '' }}">
                            <td class="p-3 border">
                                <div class="font-bold text-gray-800">{{ $memo->subject }}</div>
                                <div class="text-xs text-gray-500">No: {{ $memo->reference_no }}</div>
                                <div class="text-xs {{ $isExpired ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                    Berlaku s/d: {{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('d/m/Y') : 'Tanpa Batas' }}
                                </div>
                            </td>
                            <td class="p-3 border">
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold mr-2">
                                            {{ $memo->approvals->count() }}/5 GM
                                        </span>
                                        
                                        <!-- Label Status Dinamis -->
                                        @if($memo->is_rejected)
                                            <span class="px-2 py-0.5 bg-red-600 text-white text-[10px] font-bold rounded shadow-sm">DITOLAK</span>
                                        @elseif($isExpired)
                                            <span class="px-2 py-0.5 bg-gray-500 text-white text-[10px] font-bold rounded shadow-sm">EXPIRED</span>
                                        @elseif($memo->is_fully_approved)
                                            <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-bold rounded shadow-sm">FINAL</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-yellow-500 text-white text-[10px] font-bold rounded shadow-sm">PENDING</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Progress Bar Sederhana -->
                                    <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                        <div class="bg-blue-600 h-1 rounded-full" style="width: {{ ($memo->approvals->count() / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 border text-center space-x-1">
                                <!-- Aksi khusus GM -->
                                @if(Auth::user()->role === 'gm' && !$memo->is_rejected && !$memo->is_fully_approved && !$isExpired)
                                    @if(!$memo->approvals->contains(Auth::user()))
                                   <button type="button" onclick="handleApprove({{ $memo->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-[11px] font-bold transition-all">
    Approve
</button>


 <!-- Hidden Form Approval -->
                    <form id="approve-form-{{ $memo->id }}" action="{{ route('memos.approve', $memo->id) }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="note" id="note-input-{{ $memo->id }}">
                    </form>

                                            <form action="{{ route('memos.reject', $memo->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak memo ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-[11px] font-bold transition-all">
                                            Reject
                                        </button>
                                    </form>
                                @endif
                                    @if(Auth::user()->role === 'gm')
        <a href="{{ route('memos.show', $memo->id) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-[11px] font-bold transition-all shadow-sm">
            Detail
        </a>
    @endif

                                    @endif
                                    
                                

                                <!-- Link View PDF -->
                                <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" 
                                   class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-[11px] font-bold border border-gray-300 transition-all">
                                    View PDF
                                </a>
                               
                            </td>
                            

                        </tr>
                        @endforeach
                        
                        @if($memos->isEmpty())
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500 italic">Belum ada memo yang dibuat.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
            </div>
        </main>
    </div>

    <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();

        // Toggle Sidebar Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
        

  function toggleDropdown(menuId, arrowId) {
    const menu = document.getElementById(menuId);
    const arrow = document.getElementById(arrowId);
    
    // Toggle class hidden
    menu.classList.toggle('hidden');
    
    // Putar arrow icon
    arrow.classList.toggle('rotate-180');
  }
  function confirmApprove(memoId) {
    Swal.fire({
        title: 'Konfirmasi Persetujuan',
        text: "Apakah Anda yakin ingin menyetujui memo ini?",
        input: 'textarea',
        inputLabel: 'Catatan (Opsional)',
        inputPlaceholder: 'Tulis pesan atau catatan di sini...',
        inputAttributes: {
            'aria-label': 'Tulis catatan di sini'
        },
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal',
        preConfirm: (note) => {
            // Kita bisa melakukan validasi di sini jika diperlukan
            return note;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Jalankan fungsi pengiriman form
            submitApprovalForm(memoId, result.value);
        }
    });
}

  function handleApprove(memoId) {
        Swal.fire({
            title: 'Konfirmasi Persetujuan',
            text: "Tambahkan catatan jika diperlukan (opsional):",
            input: 'textarea',
            inputPlaceholder: 'Tulis catatan di sini...',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Approve!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('approve-form-' + memoId);
                const input = document.getElementById('note-input-' + memoId);
                
                if (form && input) {
                    // Set nilai note ke hidden input
                    input.value = result.value;
                    // Submit form secara manual
                    form.submit();
                } else {
                    console.error('Form atau Input tidak ditemukan untuk ID: ' + memoId);
                }
            }
        });
    }
    </script>
    
</body>
</html>