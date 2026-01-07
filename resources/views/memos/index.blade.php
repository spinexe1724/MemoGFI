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
<body class="bg-gray-50 py-10 px-4">
    
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
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
    </div>
</body>
</html>