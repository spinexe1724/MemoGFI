<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    @vite('resources/css/app.css')
    <title>Dashboard Memo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
<div class="flex min-h-screen bg-gray-100">

  <div class="relative flex h-screen w-full max-w-[18rem] flex-col bg-white p-4 text-gray-700 shadow-xl">
    <div class="p-4 mb-2">
    <a href="" class="flex items-center">
         <img src="{{ asset('images/Capture.PNG')}}" class="" style="height:auto;"/>
         <span class="self-center text-lg text-heading font-semibold whitespace-nowrap"></span>
      </a>
    </div>
    <div class="garis-vertical"></div>

    <aside class="relative flex h-screen w-full max-w-[20rem] flex-col bg-white p-4 text-gray-700">
    <div class="p-4 mb-2">
      <h5 class="text-xl font-semibold text-blue-gray-900">Sidebar Menu</h5>
    </div>
    
    <nav class="flex flex-col gap-1 p-2 text-base font-normal">
      
      <div class="relative w-full">
        <button onclick="toggleDropdown('dashboard-menu', 'arrow-icon')" 
          class="flex items-center justify-between w-full p-3 transition-all rounded-lg hover:bg-blue-gray-50 hover:text-blue-gray-900">
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="currentColor" viewBox="0 0 24 24"><path d="M2.25 2.25a.75.75 0 000 1.5H3v10.5a3 3 0 003 3h1.21l-1.172 3.513a.75.75 0 001.424.474l.329-.987h8.418l.33.987a.75.75 0 001.422-.474l-1.17-3.513H18a3 3 0 003-3V3.75h.75a.75.75 0 000-1.5H2.25z"/></svg>
            <span>Dashboard</span>
          </div>
          <svg id="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 transition-transform duration-200">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
          </svg>
        </button>
        
        <div id="dashboard-menu" class="hidden overflow-hidden pl-9 mt-1 transition-all duration-300">
          <a href="#" class="block p-2 text-sm rounded-lg hover:bg-gray-100">Analytics</a>
          <a href="#" class="block p-2 text-sm rounded-lg hover:bg-gray-100">Reporting</a>
          <a href="#" class="block p-2 text-sm rounded-lg hover:bg-gray-100">Projects</a>
        </div>
      </div>

      <div role="button" class="flex items-center w-full p-3 rounded-lg hover:bg-blue-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6.912 3a3 3 0 00-2.868 2.118l-2.411 7.838a3 3 0 00-.133.882V18a3 3 0 003 3h15a3 3 0 003-3v-4.162c0-.299-.045-.596-.133-.882l-2.412-7.838A3 3 0 0017.088 3H6.912z"/></svg>
        Inbox
      </div>

      
      <div role="button" action="submit" class="flex items-center w-full p-3 rounded-lg hover:bg-red-50 text-red-600 mt-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
        Log Out
      </div>
    </nav>
  </aside>
    <nav class="flex min-w-[240px] flex-col gap-1 p-2 font-sans text-base font-normal text-blue-gray-700">
      </nav>
  </div>

  <main class="flex-1 p-8 overflow-y-auto">
    <header class="mb-8">
      <h1 class="text-3xl font-bold text-blue-gray-900">Dashboard Overview</h1>
      <p class="text-gray-600">Selamat datang kembali, admin.</p>
    </header>

    <div class="max-w-9xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header Panel -->
        <div class="bg-blue-800 p-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Internal Memos</h1>
                <p class="text-blue-100 italic text-sm">User: {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</p>
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
                                        <form action="{{ route('memos.approve', $memo->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-[11px] font-bold transition-all">
                                                Approve
                                            </button>
                                        </form>
                                            <form action="{{ route('memos.reject', $memo->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak memo ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-[11px] font-bold transition-all">
                                            Reject
                                        </button>
                                    </form>
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
  </main>
  <script>
  function toggleDropdown(menuId, arrowId) {
    const menu = document.getElementById(menuId);
    const arrow = document.getElementById(arrowId);
    
    // Toggle class hidden
    menu.classList.toggle('hidden');
    
    // Putar arrow icon
    arrow.classList.toggle('rotate-180');
  }
</script>
</div>
</body>
</html>