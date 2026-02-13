@extends('layouts.app')

@section('title', 'Detail Memo - ' . $memo->reference_no)

@section('content')
<style>
    /* CSS Konten CKEditor agar tabel memiliki garis */
    .ck-content table { width: 100% !important; border-collapse: collapse !important; margin: 1.5rem 0 !important; }
    .ck-content table td, .ck-content table th { border: 1px solid #d1d5db !important; padding: 12px 15px !important; }
    
    /* Style Signbox Profesional */
    .signature-card { 
        transition: all 0.3s ease; 
        border: 1px solid #eef2f6; 
        min-height: 280px; 
        display: flex; 
        flex-direction: column; 
        align-items: center;
        padding: 1.5rem;
    }
    .signature-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); 
    }
    
    /* Gelembung Catatan di ATAS (pointing down) */
    .bubble-note-top {
        position: relative;
        background: #f8fafc;
        border-radius: 12px;
        padding: 10px;
        font-size: 11px;
        line-height: 1.4;
        color: #1e293b;
        margin-bottom: 18px;
        border: 1px solid #e2e8f0;
        width: 100%;
        text-align: center;
        font-style: italic;
    }
    .bubble-note-top::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid #e2e8f0;
    }

    .central-note-box {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        border: 2px solid #f1f5f9;
        margin-bottom: 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('memos.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-red-700 transition-colors">
            <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Kembali ke Daftar Memo
        </a>
        <div class="flex items-center space-x-3">
            @if(!$memo->is_rejected)
                <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                    <i data-lucide="printer" class="w-4 h-4 mr-2 text-gray-400"></i> Cetak PDF
                </a>
            @endif

            {{-- TOMBOL REVISI: Muncul jika User adalah Pembuat dan statusnya Ditolak --}}
            @if(Auth::id() == $memo->user_id && $memo->is_rejected)
                <a href="{{ route('memos.edit', $memo->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white text-sm font-black rounded-xl shadow-lg hover:bg-amber-600 transition-all">
                    <i data-lucide="edit-3" class="w-4 h-4 mr-2"></i> REVISI MEMO
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Sidebar Informasi --}}
        <div class="lg:col-span-3 space-y-6 lg:sticky lg:top-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Status Dokumen</h3>
                <div class="flex flex-col space-y-4">
                    @if($memo->is_rejected)
                        <div class="flex items-center p-3 bg-red-50 border border-red-100 rounded-2xl">
                            <div class="bg-red-500 p-2 rounded-xl text-white mr-3"><i data-lucide="x-circle" class="w-5 h-5"></i></div>
                            <span class="text-red-700 font-extrabold text-sm uppercase">Dibatalkan / Perlu Revisi</span>
                        </div>
                    @elseif($memo->is_final)
                        <div class="flex items-center p-3 bg-green-50 border border-green-100 rounded-2xl">
                            <div class="bg-green-600 p-2 rounded-xl text-white mr-3"><i data-lucide="check-check" class="w-5 h-5"></i></div>
                            <span class="text-green-700 font-extrabold text-sm uppercase">Aktif / Valid</span>
                        </div>
                    @else
                        <div class="flex items-center p-3 bg-amber-50 border border-amber-100 rounded-2xl">
                            <div class="bg-amber-500 p-2 rounded-xl text-white mr-3"><i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i></div>
                            <span class="text-amber-700 font-extrabold text-sm uppercase">Menunggu Approval</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Meta Informasi --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Meta Informasi</h3>
    <div class="space-y-3">
        <div>
            <p class="text-[10px] text-gray-400 uppercase font-black">No. Referensi</p>
            <p class="text-sm font-mono font-bold text-gray-800">{{ $memo->reference_no }}</p>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 uppercase font-black">Tgl Dibuat</p>
            <p class="text-sm font-bold text-gray-800">{{ $memo->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 uppercase font-black">Berlaku Sampai</p>
            <p class="text-sm font-bold text-red-800">
                {{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('d M Y') : 'Tanpa Batas' }}
            </p>
        </div>

        {{-- TAMBAHKAN SEKSI CC DI SINI --}}
        <div class="pt-3 border-t border-gray-100 mt-2">
            <p class="text-[10px] text-gray-400 uppercase font-black mb-1">Tembusan (CC):</p>
            <div class="flex flex-wrap gap-1">
                @if(isset($memo->carbon_copies) && count($memo->carbon_copies) > 0)
                    @foreach($memo->carbon_copies as $cc)
                        <span class="px-2 py-0.5 bg-slate-50 border border-slate-200 text-slate-600 text-[10px] font-bold rounded-md">
                            {{ $cc->name }}
                        </span>
                    @endforeach
                @else
                    <span class="text-xs font-bold text-gray-500 italic">Seluruh Karyawan</span>
                @endif
            </div>
        </div>
    </div>
</div>

            @if($canApprove)
                <div class="bg-white rounded-3xl shadow-xl shadow-red-100 border-2 border-red-800 overflow-hidden p-6">
                    <h3 class="text-sm font-black text-red-800 uppercase tracking-widest mb-4">Otoritas Approval</h3>
                    <div class="flex flex-col gap-3">
                        <button type="button" onclick="confirmApprove({{ $memo->id }})" class="w-full bg-red-800 text-white font-black py-4 rounded-2xl hover:bg-red-900 transition-all flex items-center justify-center">
                            <i data-lucide="pen-tool" class="w-5 h-5 mr-2"></i> VERIFIKASI & TTD
                        </button>
                        <button type="button" onclick="confirmReject({{ $memo->id }})" class="w-full bg-white text-red-600 font-bold py-3 rounded-2xl hover:bg-red-50 border border-red-200 flex items-center justify-center">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Reject
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Konten Utama --}}
        <div class="lg:col-span-9 space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden p-8 md:p-12">
                <div class="bg-gray-50/50 rounded-2xl p-6 mb-12 border border-gray-100/50 text-center">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] block mb-2">Perihal</span>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900 uppercase italic">"{{ $memo->subject }}"</h2>
                </div>

                <div class="ck-content prose prose-lg max-w-none text-gray-700 leading-relaxed mb-12 min-h-[300px]">
                    {!! $memo->body_text !!}
                </div>

                {{-- LAMPIRAN DOKUMEN --}}
                @if($memo->attachments && $memo->attachments->count() > 0)
                    <div class="mb-12 p-6 bg-blue-50/50 rounded-3xl border border-blue-100">
                        <h4 class="text-xs font-black text-blue-800 uppercase tracking-[0.2em] mb-4 flex items-center">
                            <i data-lucide="paperclip" class="w-4 h-4 mr-2"></i> Lampiran Dokumen ({{ $memo->attachments->count() }})
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($memo->attachments as $file)
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" 
                                   class="flex items-center p-4 bg-white rounded-2xl border border-blue-200/50 hover:border-blue-400 hover:shadow-md transition-all group">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                        @if(in_array($file->file_type, ['pdf']))
                                            <i data-lucide="file-text"></i>
                                        @elseif(in_array($file->file_type, ['docx', 'doc']))
                                            <i data-lucide="file-type-2"></i>
                                        @elseif(in_array($file->file_type, ['xlsx', 'xls']))
                                            <i data-lucide="file-spreadsheet"></i>
                                        @else
                                            <i data-lucide="file"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $file->file_name }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">{{ strtoupper($file->file_type) }} • Klik untuk mengunduh</p>
                                    </div>
                                    <i data-lucide="download" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 ml-2"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="border-t-2 border-dashed border-gray-100 pt-12">
                    {{-- SEKSI CATATAN TERPUSAT --}}
                    @php
                        $specialNotes = $memo->approvals->filter(function($a) {
                            $n = strtolower($a->pivot->note);
                            return !empty($n) && $n !== 'memo diterbitkan' && $n !== 'approved';
                        });
                    @endphp

                    @if($specialNotes->count() > 0)
                        <div class="central-note-box">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                                <i data-lucide="message-square" class="w-4 h-4 mr-2"></i> Instruksi & Catatan Penting:
                            </h4>
                            <div class="space-y-4">
                                @foreach($specialNotes as $noteApprover)
                                    <div class="flex items-start space-x-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-sm font-black text-blue-600 shadow-sm">
                                            {{ substr($noteApprover->name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">
                                                {{ $noteApprover->name ?? 'User' }} ({{ strtoupper($noteApprover->role ?? 'N/A') }})
                                            </p>
                                            <div class="text-sm text-slate-800 italic leading-relaxed break-words">
                                                "{!! nl2br(e($noteApprover->pivot->note)) !!}"
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="text-center mb-10">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] italic mb-2">Digital Signature Verified</h3>
                        <div class="h-1 w-20 bg-red-800 mx-auto rounded-full"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($memo->approvals as $approver)
                            @php
                                $rawNote = $approver->pivot->note;
                                $isDefaultNote = (strtolower($rawNote) === 'approved' || strtolower($rawNote) === 'memo diterbitkan');
                            @endphp
                            <div class="signature-card relative bg-white rounded-[2rem] flex flex-col items-center">
                                {{-- 1. ROLE BADGE --}}
                                <span class="px-4 py-1 bg-gray-900 text-white text-[9px] font-black uppercase tracking-widest rounded-full mb-4">
                                    {{ strtoupper($approver->role ?? 'N/A') }}
                                </span>

                                {{-- 2. POSISI ATAS: Jika Note KUSTOM --}}
                                <div class="w-full min-h-[50px] flex items-end">
                                    @if(!$isDefaultNote && !empty($rawNote))
                                        <div class="bubble-note-top">
                                            "{{ Str::limit($rawNote, 50) }}"
                                        </div>
                                    @endif
                                </div>

                                {{-- 3. AVATAR --}}
                                <div class="relative mb-4">
                                    <div class="w-20 h-20 rounded-2xl bg-slate-50 text-blue-600 flex items-center justify-center text-2xl font-black border border-slate-200 shadow-inner">
                                        {{ substr($approver->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="absolute -right-2 -bottom-2 bg-green-500 text-white p-2 rounded-lg border-2 border-white shadow-lg">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </div>
                                </div>

                                {{-- 4. INFO NAMA --}}
                                <div class="text-center w-full">
                                    <h4 class="font-black text-gray-900 text-sm tracking-tight leading-none">
                                        {{ strtoupper($approver->name ?? 'User') }}
                                    </h4>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">{{ $approver->division ?? '-' }}</p>
                                    
                                    {{-- 5. POSISI BAWAH: Jika Note DEFAULT --}}
                                    @if($isDefaultNote)
                                        <p class="text-[10px] text-green-600 font-extrabold uppercase mt-2 tracking-widest">
                                            {{ $rawNote == 'Memo Diterbitkan' ? 'Pembuat' : 'APPROVED' }}
                                        </p>
                                    @endif

                                    {{-- 6. TIMESTAMP --}}
                                    <div class="mt-3 flex items-center justify-center text-[8px] font-mono text-slate-400 bg-slate-50 py-1 px-3 rounded-full border border-slate-100 inline-flex">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1.5"></i>
                                        {{ $approver->pivot->created_at->format('d/m/y H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-center text-gray-400 text-[10px] font-bold uppercase tracking-widest space-x-4 mt-8">
                <span>E-Memo ID: {{ $memo->id }}</span>
                <span>•</span>
                <span>Hash: {{ substr(md5($memo->id . $memo->reference_no), 0, 8) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Forms & Scripts --}}
<form id="approve-form-{{ $memo->id }}" action="{{ route('memos.approve', $memo->id) }}" method="POST" class="hidden">@csrf<input type="hidden" name="note" id="note-input-{{ $memo->id }}"></form>
<form id="reject-form-{{ $memo->id }}" action="{{ route('memos.reject', $memo->id) }}" method="POST" class="hidden">@csrf<input type="hidden" name="note" id="reject-note-input-{{ $memo->id }}"></form>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
    function confirmApprove(memoId) {
        Swal.fire({
            title: 'Verifikasi Digital',
            text: "Tambahkan instruksi kustom atau kosongkan untuk default 'Approved':",
            input: 'textarea',
            inputPlaceholder: 'Tulis pesan di sini...',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#1e40af', 
            confirmButtonText: 'Tanda Tangani',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('approve-form-' + memoId);
                const input = document.getElementById('note-input-' + memoId);
                if (form && input) {
                    input.value = result.value || 'Approved';
                    form.submit();
                }
            }
        });
    }

    function confirmReject(memoId) {
        Swal.fire({
            title: 'Tolak Memo?',
            text: "Berikan alasan penolakan agar pembuat dapat merevisi:",
            input: 'textarea',
            inputPlaceholder: 'Tulis alasan penolakan...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#991b1b',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            inputValidator: (value) => { if (!value) return 'Alasan wajib diisi!' },
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