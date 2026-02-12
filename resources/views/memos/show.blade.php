@extends('layouts.app')

@section('title', 'Detail Memo - ' . $memo->reference_no)

@section('content')
<style>
    /* CSS Tambahan untuk menangani konten dari CKEditor */
    .ck-content table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 1.5rem 0 !important;
        table-layout: auto !important;
    }
    .ck-content table td, 
    .ck-content table th {
        border: 1px solid #d1d5db !important;
        padding: 12px 15px !important;
        min-width: 2em !important;
    }
    .ck-content table th {
        background-color: #f9fafb !important;
        font-weight: bold !important;
        text-align: left !important;
    }
    .ck-content .image-style-align-left { float: left; margin-right: 1.5rem; }
    .ck-content .image-style-align-right { float: right; margin-left: 1.5rem; }
    .ck-content .image-style-block-align-center { margin-left: auto; margin-right: auto; }

    /* Custom Style untuk Signbox yang lebih jelas */
    .signature-card {
        transition: all 0.3s ease;
        border: 1px solid #eef2f6;
    }
    .signature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        border-color: #d1d5db;
    }
    .approval-note {
        position: relative;
        background: #f8fafc;
        border-radius: 12px;
        padding: 10px;
        font-size: 11px;
        line-height: 1.4;
        color: #475569;
        margin-top: 12px;
        border: 1px solid #e2e8f0;
    }
    .approval-note::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid #e2e8f0;
    }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('memos.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-red-700 transition-colors">
            <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
            Kembali ke Daftar Memo
        </a>
        <div class="flex items-center space-x-3">
            @php
                $currentSignCount = $memo->approvals ? $memo->approvals->count() : 0;
            @endphp

            @if(Auth::id() == $memo->user_id && ($memo->is_draft || $memo->is_rejected || (!$memo->is_final && $currentSignCount <= 1)))
                <a href="{{ route('memos.edit', $memo->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-bold rounded-xl shadow-sm hover:bg-amber-700 transition-all">
                    <i data-lucide="edit-3" class="w-4 h-4 mr-2"></i>
                    Edit / Revisi Memo
                </a>
            @endif

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
        <div class="lg:col-span-3 space-y-6 lg:sticky lg:top-8">
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
                        @php 
                            $ccList = $memo->cc_list;
                            $ccItems = is_array($ccList) ? $ccList : (is_string($ccList) ? explode(',', $ccList) : []); 
                        @endphp
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

            @if($canApprove)
                <div class="bg-white rounded-3xl shadow-xl shadow-red-100 border-2 border-red-800 overflow-hidden">
                    <div class="bg-red-800 px-6 py-3 flex items-center">
                        <i data-lucide="shield-check" class="w-5 h-5 mr-2 text-white"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Otoritas Approval</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-900 text-base font-bold leading-relaxed mb-1">
                            Halo, {{ Auth::user()->name }}
                        </p>
                        <p class="text-gray-600 text-sm mb-6">
                            Dokumen ini memerlukan verifikasi Anda sebagai <span class="text-red-800 font-black underline decoration-red-200 decoration-2">{{ strtoupper(Auth::user()->role) }}</span>.
                        </p>
                        
                        <div class="flex flex-col gap-3">
                            <button type="button" onclick="confirmApprove({{ $memo->id }})" class="w-full bg-red-800 text-white font-black py-4 rounded-2xl hover:bg-red-900 transition-all flex items-center justify-center shadow-lg shadow-red-200 group">
                                <i data-lucide="pen-tool" class="w-5 h-5 mr-2 transition-transform group-hover:rotate-12"></i> 
                                VERIFIKASI & TANDA TANGAN
                            </button>

                            <button type="button" onclick="confirmReject({{ $memo->id }})" class="w-full bg-white text-red-600 font-bold py-3 rounded-2xl hover:bg-red-50 transition-all border border-red-200 flex items-center justify-center">
                                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Reject Dokumen
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Konten Utama --}}
        <div class="lg:col-span-9 space-y-8">
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
                            <p class="text-lg font-extrabold text-gray-800">{{ $memo->user->name ?? 'User' }}</p>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $memo->sender }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50/50 rounded-2xl p-6 mb-12 border border-gray-100/50 text-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] block mb-2">Perihal / Subjek</span>
                        <h2 class="text-xl md:text-2xl font-black text-gray-900 leading-tight uppercase italic">
                            "{{ $memo->subject }}"
                        </h2>
                    </div>

                    <div class="ck-content prose prose-lg max-w-none text-gray-700 leading-relaxed mb-12 px-2 min-h-[300px]">
                        {!! $memo->body_text !!}
                    </div>

                    {{-- SEKSI LAMPIRAN --}}
                    @if($memo->attachments && $memo->attachments->count() > 0)
                        <div class="mb-16 bg-slate-50 rounded-3xl p-6 md:p-8 border border-slate-100">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center">
                                <i data-lucide="paperclip" class="w-4 h-4 mr-2"></i> Lampiran Dokumen
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($memo->attachments as $file)
                                    <div class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                                        <div class="flex items-center min-w-0">
                                            <div class="p-3 bg-blue-50 rounded-xl text-blue-600 mr-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                                @php
                                                    $ext = strtolower($file->file_type);
                                                    $icon = 'file-text';
                                                    if(in_array($ext, ['xlsx', 'xls', 'csv'])) $icon = 'file-spreadsheet';
                                                    elseif($ext == 'pdf') $icon = 'file-type-2';
                                                @endphp
                                                <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                                            </div>
                                            <div class="truncate">
                                                <p class="text-sm font-bold text-slate-700 truncate" title="{{ $file->file_name }}">
                                                    {{ $file->file_name }}
                                                </p>
                                                <p class="text-[10px] font-medium text-slate-400 uppercase tracking-tighter">
                                                    {{ strtoupper($ext) }} Dokumen
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('memos.attachment.download', $file->id) }}" 
                                           class="ml-4 p-2.5 bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white rounded-xl transition-all">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- TANDA TANGAN (SIGNBOX) --}}
                    <div class="border-t-2 border-dashed border-gray-100 pt-12">
                        <div class="text-center mb-10">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] italic mb-2">Digital Signature Verified</h3>
                            <div class="h-1 w-20 bg-red-800 mx-auto rounded-full"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($memo->approvals as $approver)
                            <div class="signature-card relative p-6 bg-white rounded-[2rem] flex flex-col items-center overflow-hidden">
                                {{-- Role Header --}}
                                <div class="w-full text-center mb-4">
                                    <span class="px-4 py-1 bg-gray-900 text-white text-[9px] font-black uppercase tracking-widest rounded-full">
                                        {{ strtoupper($approver->role) }}
                                    </span>
                                </div>

                                {{-- Avatar with Status Icon --}}
                                <div class="relative mb-4">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-50 to-slate-100 text-blue-600 flex items-center justify-center text-xl font-black border border-slate-200 shadow-inner">
                                        {{ substr($approver->name, 0, 1) }}
                                    </div>
                                    <div class="absolute -right-2 -bottom-2 bg-green-500 text-white p-1.5 rounded-lg shadow-lg border-2 border-white">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    </div>
                                </div>

                                {{-- Name & Division --}}
                                <div class="text-center mb-2">
                                    <h4 class="font-black text-gray-900 text-sm tracking-tight">{{ strtoupper($approver->name) }}</h4>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $approver->division }}</p>
                                </div>

                                {{-- Date/Time --}}
                                <div class="flex items-center text-[9px] font-mono text-slate-400 bg-slate-50 px-3 py-1 rounded-full mb-2">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1.5"></i>
                                    {{ $approver->pivot->created_at->format('d/m/Y H:i') }}
                                </div>

                                {{-- Approval Note with Speech Bubble Style --}}
                                @if($approver->pivot->note)
                                    <div class="w-full">
                                        <div class="approval-note text-center italic">
                                            @php
                                                $isRejected = str_contains(strtolower($approver->pivot->note), 'tolak');
                                            @endphp
                                            <span class="{{ $isRejected ? 'text-red-600 font-bold' : 'text-blue-700' }}">
                                                "{{ $approver->pivot->note }}"
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Subtle BG Icon --}}
                                <i data-lucide="shield-check" class="absolute -right-4 -bottom-4 w-20 h-20 text-slate-50 -z-10"></i>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center text-gray-400 text-[10px] font-bold uppercase tracking-widest space-x-4">
                <span>E-Memo ID: {{ $memo->id }}</span>
                <span>•</span>
                <span>Hash: {{ substr(md5($memo->id . $memo->reference_no), 0, 8) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Forms --}}
<form id="approve-form-{{ $memo->id }}" action="{{ route('memos.approve', $memo->id) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="note" id="note-input-{{ $memo->id }}">
</form>

<form id="reject-form-{{ $memo->id }}" action="{{ route('memos.reject', $memo->id) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="note" id="reject-note-input-{{ $memo->id }}">
</form>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });

    function confirmApprove(memoId) {
        Swal.fire({
            title: 'Verifikasi Digital',
            text: "Tambahkan catatan jika diperlukan:",
            input: 'textarea',
            inputPlaceholder: 'Opsional: Berikan instruksi atau catatan...',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#1e40af', 
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Tanda Tangani',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('approve-form-' + memoId);
                const input = document.getElementById('note-input-' + memoId);
                if (form && input) {
                    input.value = result.value || 'Disetujui secara digital';
                    form.submit();
                }
            }
        });
    }

    function confirmReject(memoId) {
        Swal.fire({
            title: 'Tolak Dokumen',
            text: "Wajib memberikan alasan penolakan untuk revisi:",
            input: 'textarea',
            inputPlaceholder: 'Contoh: Perbaiki rincian biaya atau lampiran tidak lengkap...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#991b1b',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) return 'Alasan penolakan wajib diisi!'
            },
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('reject-form-' + memoId);
                const input = document.getElementById('reject-note-input-' + memoId);
                if (form && input) {
                    input.value = result.value;
                    form.submit();
                }
            }
        });
    }
</script>
@endsection