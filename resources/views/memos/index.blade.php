@extends('layouts.app') {{-- layout --}}

@section('title', 'Home Memo System') {{-- title --}}

@section('content') {{-- content section --}}
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, ({{ strtoupper(Auth::user()->name) }})</h2>
</div>

<div class="p-0">
    <div class="bg-red-800 p-6 flex justify-between items-center rounded-t-2xl" style="height:70px;">
        <div>
            <h1 class="text-2xl font-bold text-white">Daftar Memo Gratama</h1>
        </div>
    </div>
    
    <div class="flex justify-between mt-6 mb-6 items-center">
        @if(in_array(Auth::user()->role, ['supervisor', 'gm']))
            <a href="{{ route('memos.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-bold shadow-md transition-all flex items-center">
                <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                + Buat Memo Baru
            </a>
        @endif
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-gray-600">Memo Aktif</th>
                        <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-gray-600">Disetujui Oleh</th>
                        <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-gray-600">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-gray-50">
                    @forelse($memos as $memo)
                        @php
                            $isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($memo->valid_until)) : false;
                        @endphp
                        <tr class="group hover:bg-blue-50/30 transition-all duration-200">
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800 text-lg">{{ $memo->subject }}</span>
                                    <span class="text-xs text-gray-400">No: {{ $memo->reference_no }}</span>
                                    <div class="text-xs {{ $isExpired ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                        Berlaku s/d: {{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('d/m/Y') : 'Tanpa Batas' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @forelse($memo->approvals as $approver)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-50 text-green-700 border border-green-100 text-[9px] font-bold">
                                            ✔ {{ $approver->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 text-[10px] italic">Menunggu Persetujuan</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($memo->is_rejected)
                                    <span class="px-2 py-0.5 bg-red-600 text-white text-[10px] font-bold rounded shadow-sm">DITOLAK</span>
                                @elseif($isExpired)
                                    <span class="px-2 py-0.5 bg-gray-500 text-white text-[10px] font-bold rounded shadow-sm">EXPIRED</span>
                               @else
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-lg border border-green-200 uppercase tracking-tight">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex flex-wrap justify-end items-center gap-2">
                                    {{-- Edit untuk pembuat --}}
                                    @if(Auth::id() == $memo->user_id && $memo->approvals->count() <= 1 && !$memo->is_rejected)
                                        <a href="{{ route('memos.edit', $memo->id) }}" class="inline-flex items-center bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-[11px] font-bold">
                                            Edit
                                        </a>
                                    @endif

                                  
                                    

                                    <a href="{{ route('memos.show', $memo->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-[11px] font-bold">
                                        Detail
                                    </a>

                                    @if(!$memo->is_rejected)
                                        {{-- Tombol View (Buka di Tab Baru) --}}
                                        <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-3 py-1.5 rounded text-[11px] font-bold transition-all">
                                            <i data-lucide="eye" class="w-3.5 h-3.5 mr-1"></i>
                                            View
                                        </a>

                                        {{-- Tombol Download (Unduh Langsung) --}}
                                        <a href="{{ route('memos.pdf', $memo->id) }}?download=1" class="inline-flex items-center bg-red-50 text-red-700 hover:bg-red-600 hover:text-white border border-red-200 px-3 py-1.5 rounded text-[11px] font-bold transition-all group">
                                            <svg class="w-3.5 h-3.5 mr-1 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                            </svg>
                                            Download
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-12 text-center text-gray-500 italic">Belum ada memo yang diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div> 
    
    <div class="mt-6">
        {{ $memos->links() }}
    </div>
</div>

{{-- Memuat Lucide Icons melalui CDN --}}
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    // Inisialisasi Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
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
                    input.value = result.value;
                    form.submit();
                }
            }
        });
    }
</script>
@endsection