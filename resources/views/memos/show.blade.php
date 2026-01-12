@extends('layouts.app') {{-- layout --}}

@section('title', 'Detail Memo - ' . $memo->reference_no)

@section('content')

<div class="max-w-6xl mx-auto space-y-6">
<!-- Header Panel -->
<div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
<div class="bg-red-800 p-6 flex justify-between items-center">
<div>
<h2 class="text-2xl font-bold text-white">Detail Memo</h2>
<p class="text-red-100 text-sm opacity-80">{{ $memo->reference_no }}</p>
</div>
<div>
@php
$isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($memo->valid_until)) : false;
@endphp

            @if($memo->is_rejected)
                <span class="px-4 py-2 bg-red-600 text-white rounded-full text-xs font-bold uppercase shadow-lg border border-red-500">Ditolak / Dibatalkan</span>
            @elseif($isExpired)
                <span class="px-4 py-2 bg-gray-600 text-white rounded-full text-xs font-bold uppercase shadow-lg border border-gray-500">Kadaluwarsa</span>
            @elseif($memo->is_fully_approved)
                <span class="px-4 py-2 bg-green-600 text-white rounded-full text-xs font-bold uppercase shadow-lg border border-green-500">Status: Final</span>
            @else
                <span class="px-4 py-2 bg-yellow-500 text-white rounded-full text-xs font-bold uppercase shadow-lg border border-yellow-400 text-shadow-sm">Status: Pending</span>
            @endif
        </div>
    </div>

    <!-- Notifikasi -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 m-8 mb-0">
            <p class="text-green-700 font-bold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="p-8 space-y-8">
        <!-- Grid Data Memo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Nomor Referensi</label>
                <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 font-medium">
                    {{ $memo->reference_no }}
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Akhir Berlaku</label>
                <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 font-medium {{ $isExpired ? 'text-red-600 font-bold' : '' }}">
                    {{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('d F Y') : 'Tanpa Batas Waktu' }}
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Kepada</label>
                <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700">
                    {{ $memo->recipient }}
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Dari (Divisi)</label>
                <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700">
                    {{ $memo->user->name }} ({{ $memo->sender }})
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Tembusan (CC)</label>
                <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700">
                   {{ is_array($memo->cc_list) ? implode(', ', $memo->cc_list) : $memo->cc_list }}
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Perihal</label>
                <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 font-bold text-lg">
                    {{ $memo->subject }}
                </div>
            </div>
        </div>

        <!-- Isi Memo -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Isi Pesan Memo</label>
            <div class="w-full p-8 bg-white border border-gray-200 rounded-2xl text-gray-700 prose prose-blue max-w-none shadow-inner min-h-[200px]">
                {!! nl2br($memo->body_text) !!}
            </div>
        </div>

        <!-- DAFTAR PERSETUJUAN & CATATAN -->
        <div class="pt-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-2">
                    <i data-lucide="check-square" class="w-4 h-4"></i>
                </span>
                Riwayat Persetujuan Digital
            </h3>
            
            <div class="overflow-hidden border border-gray-100 rounded-xl shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase">Penyetuju</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase">Jabatan</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase">Waktu</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase">Catatan (Note)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($memo->approvals as $approver)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3 text-sm font-bold text-gray-800">{{ $approver->name }}</td>
                                <td class="px-4 py-3 text-[10px] uppercase font-semibold text-gray-500">{{ $approver->role }}</td>
                                <td class="px-4 py-3 text-[10px] text-gray-400 italic">
                                    {{ \Carbon\Carbon::parse($approver->pivot->created_at)->format('d/m/y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-xs text-blue-600 font-medium">
                                    {{ $approver->pivot->note ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic text-sm">Belum ada persetujuan digital yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tombol Navigasi & Aksi -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-8 border-t border-gray-100">
            <a href="{{ route('memos.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali ke Daftar
            </a>

            <div class="flex items-center space-x-3">
                {{-- Aksi Approval Khusus GM/Direksi --}}
                @if(in_array(Auth::user()->role, ['gm', 'direksi']) && !$memo->is_rejected && !$memo->is_fully_approved)
                    @if(!$memo->approvals->contains('id', Auth::id()))
                        <form action="{{ route('memos.reject', $memo->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK memo ini?')">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white font-bold rounded-xl transition-all border border-red-200">
                                Reject Memo
                            </button>
                        </form>

                        <button type="button" onclick="confirmApprove({{ $memo->id }})" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all transform active:scale-95 flex items-center">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                            Berikan Persetujuan
                        </button>

                        {{-- Form Hidden untuk Approval via SweetAlert --}}
                        <form id="approve-form-{{ $memo->id }}" action="{{ route('memos.approve', $memo->id) }}" method="POST" style="display:none;">
                            @csrf
                            <input type="hidden" name="note" id="note-input-{{ $memo->id }}">
                        </form>
                    @endif
                @endif

                @if(!$memo->is_rejected)
                    <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-red-700 hover:bg-red-800 text-white font-bold rounded-xl shadow-lg shadow-red-100 transition-all">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                        Download PDF
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>


</div>

<!-- Scripts -->

<script src="https://unpkg.com/lucide@latest"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
lucide.createIcons();

function confirmApprove(memoId) {
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