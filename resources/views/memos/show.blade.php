@extends('layouts.app')

@section('title', 'Detail Memo - ' . $memo->reference_no)

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('memos.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-red-700 transition-colors">
            <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
            Kembali ke Daftar Memo
        </a>
        <div class="flex items-center space-x-3">
            @if(!$memo->is_rejected)
                <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                    <i data-lucide="printer" class="w-4 h-4 mr-2 text-gray-400"></i>
                    Cetak PDF
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Sidebar Informasi --}}
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-8">
            
            {{-- Status Card --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Status Dokumen</h3>
                @php
                    $isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($memo->valid_until)) : false;
                @endphp

                <div class="flex flex-col space-y-4">
                    @if($memo->is_rejected)
                        <div class="flex items-center p-3 bg-red-50 border border-red-100 rounded-2xl">
                            <div class="bg-red-500 p-2 rounded-xl text-white mr-3">
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                            </div>
                            <span class="text-red-700 font-extrabold text-sm uppercase">Dibatalkan / Ditolak</span>
                        </div>
                    @elseif($isExpired)
                        <div class="flex items-center p-3 bg-gray-50 border border-gray-100 rounded-2xl">
                            <div class="bg-gray-500 p-2 rounded-xl text-white mr-3">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <span class="text-gray-700 font-extrabold text-sm uppercase">Kadaluwarsa</span>
                        </div>
                    @elseif($memo->is_final)
                        <div class="flex items-center p-3 bg-green-50 border border-green-100 rounded-2xl">
                            <div class="bg-green-600 p-2 rounded-xl text-white mr-3">
                                <i data-lucide="check-check" class="w-5 h-5"></i>
                            </div>
                            <span class="text-green-700 font-extrabold text-sm uppercase">Aktif / Valid</span>
                        </div>
                    @else
                        <div class="flex items-center p-3 bg-amber-50 border border-amber-100 rounded-2xl">
                            <div class="bg-amber-500 p-2 rounded-xl text-white mr-3">
                                <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                            </div>
                            <span class="text-amber-700 font-extrabold text-sm uppercase">Menunggu Approval</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Meta Info --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 divide-y divide-gray-50">
                <div class="p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Meta Informasi</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">No. Ref</span>
                            <span class="text-sm font-mono font-bold text-gray-800">{{ $memo->reference_no }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Tgl Dibuat</span>
                            <span class="text-sm font-medium text-gray-800">{{ $memo->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Cabang</span>
                            <span class="text-sm font-bold text-red-800 uppercase">{{ $memo->user->branch ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Tembusan (CC)</h3>
                    <div class="flex flex-wrap gap-2">
                        @php $ccItems = is_array($memo->cc_list) ? $memo->cc_list : explode(',', $memo->cc_list); @endphp
                        @foreach($ccItems as $cc)
                            @if(trim($cc))
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-xs font-medium border border-gray-200">
                                    {{ trim($cc) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- LOGIKA APPROVAL PANEL --}}
            @if(!$memo->is_draft && !$memo->is_final && !$memo->is_rejected && !$memo->approvals->contains('id', Auth::id()))
                @php
                    $user = Auth::user();
                    $role = $user->role;
                    $div = $user->division;
                    $count = $memo->approvals->count();
                    $creator = $memo->user;
                    $isHO = strtoupper($creator->branch ?? '') === 'HO';
                    $canApprove = false;

                    // Logika Urutan Approval Cabang NON-HO (Admin/Supervisor)
                    if (!$isHO && in_array($creator->role, ['admin', 'supervisor'])) {
                        // 1. Creator (count=1) -> Menunggu BM
                        if ($count == 1 && ($role === 'bm' || $role === 'manager')) $canApprove = true; 
                        
                        // 2. BM Approved (count=2) -> Menunggu GA
                        if ($count == 2 && $div === 'GA') $canApprove = true;      
                        
                        // 3. GA Approved (count >= 3) -> Menunggu Direksi
                        if ($count >= 3 && $role === 'direksi') $canApprove = true; 
                    } 
                    // Logika HO / Standar
                    else {
                        if (Auth::id() == $memo->approver_id) $canApprove = true;
                        if (in_array($role, ['gm', 'direksi', 'bm'])) $canApprove = true;
                        if ($role === 'manager' && $user->division == $memo->user->division) $canApprove = true;
                    }
                @endphp

                @if($canApprove)
                    <div class="bg-gradient-to-br from-blue-700 to-indigo-800 rounded-3xl shadow-xl p-6 text-white">
                        <h3 class="text-lg font-bold mb-2 flex items-center">
                            <i data-lucide="shield-alert" class="w-5 h-5 mr-2 text-blue-300"></i>
                            Butuh Approval
                        </h3>
                        <p class="text-blue-50 text-sm mb-6 opacity-90">Anda memiliki otoritas sebagai <b>{{ strtoupper($role) }}</b> untuk memverifikasi memo ini.</p>
                        
                        <div class="flex flex-col gap-3">
                            <button onclick="confirmApprove({{ $memo->id }})" class="w-full bg-white text-blue-900 font-black py-3 rounded-2xl hover:bg-blue-50 transition-all flex items-center justify-center shadow-lg">
                                <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-green-600"></i> Berikan Tanda Tangan
                            </button>

                            @if(in_array($role, ['bm', 'manager', 'gm', 'direksi']))
                            <form action="{{ route('memos.reject', $memo->id) }}" method="POST" onsubmit="return confirm('Tolak memo ini?')">
                                @csrf
                                <button type="submit" class="w-full bg-red-900/40 text-red-100 font-bold py-3 rounded-2xl hover:bg-red-600 hover:text-white transition-all border border-red-400/30 flex items-center justify-center">
                                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Reject Dokumen
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Konten Utama Memo --}}
        <div class="lg:col-span-8 space-y-8">
            {{-- ... (bagian konten memo tetap sama) ... --}}
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden relative">
                <div class="h-2 w-full bg-red-800"></div>

                <div class="p-8 md:p-12">
                    <div class="flex justify-between items-start mb-10 pb-8 border-b border-gray-100">
                        <div class="space-y-1">
                            <h1 class="text-3xl font-black text-gray-900 tracking-tighter uppercase italic text-red-800">INTERNAL MEMO</h1>
                            <p class="text-xs font-bold text-gray-400 tracking-widest uppercase">Gratama Management System</p>
                        </div>
                        <div class="text-right">
                            <div class="inline-block p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <i data-lucide="file-text" class="w-8 h-8 text-red-800"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-red-800 uppercase tracking-[0.2em]">Kepada</span>
                            <p class="text-lg font-extrabold text-gray-800">{{ $memo->recipient }}</p>
                        </div>
                        <div class="space-y-1 md:text-right">
                            <span class="text-[10px] font-bold text-red-800 uppercase tracking-[0.2em]">Dari</span>
                            <p class="text-lg font-extrabold text-gray-800">{{ $memo->user->name }}</p>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $memo->sender }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50/50 rounded-2xl p-6 mb-12 border border-gray-100/50">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] block mb-2 text-center">Perihal / Subjek</span>
                        <h2 class="text-xl md:text-2xl font-black text-gray-900 text-center leading-tight uppercase italic">
                            "{{ $memo->subject }}"
                        </h2>
                    </div>

                    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed mb-16 px-2 min-h-[300px]">
                        {!! nl2br($memo->body_text) !!}
                    </div>

                    <div class="border-t-2 border-dashed border-gray-100 pt-12">
                        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-8 text-center italic">Digital Signature Verified</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($memo->approvals as $approver)
                            <div class="relative p-6 bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden flex flex-col items-center">
                                <i data-lucide="check-circle" class="absolute -right-2 -bottom-2 w-16 h-16 text-green-500/5"></i>
                                
                                <span class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-4">Digitally Signed By:</span>
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black mb-3 border border-blue-100">
                                    {{ substr($approver->name, 0, 1) }}
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm text-center line-clamp-1">{{ $approver->name }}</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">{{ strtoupper($approver->role) }}</p>
                                <div class="mt-4 pt-4 border-t border-gray-50 w-full text-center">
                                    <p class="text-[9px] font-mono text-gray-400 tracking-tighter">{{ $approver->pivot->created_at->format('d/m/y H:i:s') }} WIB</p>
                                    @if($approver->pivot->note)
                                        <p class="mt-2 text-[10px] text-blue-600 italic leading-snug">"{{ $approver->pivot->note }}"</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center text-gray-400 text-[10px] font-bold uppercase tracking-widest space-x-4">
                <span>E-Memo ID: {{ $memo->id }}</span>
                <span>•</span>
                <span>Hash ID: {{ md5($memo->id . $memo->reference_no) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Form Tersembunyi untuk Approval --}}
<form id="approve-form-{{ $memo->id }}" action="{{ route('memos.approve', $memo->id) }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="note" id="note-input-{{ $memo->id }}">
</form>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();

    function confirmApprove(memoId) {
        Swal.fire({
            title: 'Konfirmasi Persetujuan',
            text: "Tambahkan catatan instruksi atau keterangan jika diperlukan:",
            input: 'textarea',
            inputPlaceholder: 'Contoh: Lanjutkan ke tahap selanjutnya...',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#1e40af', 
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Verifikasi Digital',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem]',
                confirmButton: 'rounded-xl px-6 py-3 font-bold',
                cancelButton: 'rounded-xl px-6 py-3 font-bold'
            }
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