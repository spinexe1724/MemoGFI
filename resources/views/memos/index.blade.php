@extends('layouts.app')

@section('title', 'Daftar Memo - Gratama System')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Selamat Datang, 
                <span class="text-red-800">{{ strtoupper(Auth::user()->name) }}</span>
            </h2>
            <p class="text-gray-500 mt-1">Kelola dan pantau seluruh sirkulasi memo internal di sini.</p>
        </div>
        
        @if(in_array(Auth::user()->role, ['supervisor', 'manager','admin']))
            <a href="{{ route('memos.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-lg shadow-red-200 transition-all transform hover:-translate-y-1 active:scale-95 border-b-4 border-red-800">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i>
                BUAT MEMO BARU
            </a>
        @endif
    </div>

    {{-- Dashboard Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-4 bg-blue-50 rounded-2xl text-blue-600"><i data-lucide="file-text"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Memo</p>
                <p class="text-2xl font-black text-gray-800">{{ $memos->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-4 bg-amber-50 rounded-2xl text-amber-600"><i data-lucide="clock"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pending Review</p>
                <p class="text-2xl font-black text-gray-800">
                    {{ $memos->filter(fn($m) => !$m->is_final && !$m->is_draft && !$m->is_rejected)->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-4 bg-red-50 rounded-2xl text-red-600"><i data-lucide="alert-circle"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Expired/Rejected</p>
                <p class="text-2xl font-black text-gray-800">{{ $memos->where('is_rejected', true)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
        <div class="bg-red-800 px-8 py-6 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="layers" class="w-6 h-6 mr-3 opacity-80"></i>
                Database Memo Gratama
            </h3>
        </div>

        <div class="p-8">
            <table id="memoTable" class="w-full text-left display nowrap" style="width:100%">
                <thead>
                    <tr class="text-gray-400 text-[11px] uppercase tracking-[0.2em] border-b border-gray-100">
                        <th class="pb-4 font-black text-center w-10">ID</th>
                        <th class="pb-4 font-black">Memo Aktif</th>
                        <th class="pb-4 font-black">Pembuat</th>
                        <th class="pb-4 font-black text-center">Tgl Dibuat</th>
                        <th class="pb-4 font-black text-center">Mengetahui</th>
                        <th class="pb-4 font-black text-center">Penyetuju</th>
                        <th class="pb-4 font-black text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($memos as $memo)
                        @php
                            
                            // Logika "Mengetahui"
                            $targetApproverId = $memo->approver_id;
                            $hasSigned = $memo->approvals->contains('id', $targetApproverId);
                            
                            if (strtolower($memo->user->role ?? '') === 'manager') {
                                $mengetahui = '<span class="text-red-800 font-bold">' . $memo->user->name . '</span>';
                            } else {
                                if ($hasSigned && $memo->approver) {
                                    $mengetahui = '<span class="text-red-800 font-bold">' . $memo->approver->name . '</span>';
                                } else {
                                    $mengetahui = '-';
                                }
                            }

                            // Logika Target Approval
                            $target = 5;
                            if ($memo->user->role === 'supervisor') $target = 5;
                            elseif ($memo->user->role === 'manager') $target = 4;
                            elseif (in_array($memo->user->role, ['gm', 'direksi'])) $target = 2;

                            $currentSignCount = $memo->approvals->count();
                            
                            $excludedIds = [$memo->user_id];
                            if (strtolower($memo->user->role ?? '') !== 'manager' && $memo->approver_id) {
                                $excludedIds[] = $memo->approver_id;
                            }

                            $otherApprovers = $memo->approvals->whereNotIn('id', $excludedIds);
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="py-6 text-center text-gray-400 font-mono text-xs">{{ $loop->iteration }}</td>
                            <td class="py-6">
                                <div class="flex flex-col">
                                    <span class="font-extrabold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $memo->subject }}</span>
                                    <span class="text-[15px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ $memo->reference_no }}</span>
                                    
                                    <div class="mt-2 flex items-center text-[10px]">
                                        <i data-lucide="calendar-days" class="w-3 h-3 mr-1 text-gray-300"></i>
                                       
                                    </div>
                                </div>
                            </td>
                            <td class="py-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-red-50 text-red-700 flex items-center justify-center font-bold text-xs border border-red-100">
                                        {{ substr($memo->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700">{{ $memo->user->name }}</span>
                                        <span class="text-[9px] text-gray-400 uppercase font-black">{{ $memo->sender }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 text-center" data-order="{{ $memo->created_at->timestamp }}">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-bold text-gray-700">{{ $memo->created_at->format('d M Y') }}</span>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $memo->created_at->format('H:i') }} WIB</span>
                                </div>
                            </td>
                            <td class="py-6 text-sm text-center">
                                {!! $mengetahui !!}
                            </td>
                            <td class="py-6">
                                <div class="flex flex-col items-center gap-2">
                                    @if($memo->is_deactivated)
                                        <span class="px-3 py-1 bg-slate-800 text-white text-[10px] font-black rounded-full border border-slate-900 uppercase tracking-tighter">Non-aktif</span>
                                    @elseif($memo->is_draft)
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black rounded-full border border-amber-200 uppercase tracking-tighter">Draf</span>
                                    @elseif($memo->is_rejected)
                                        <div class="flex flex-col items-center">
                                            <span class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black rounded-full border border-red-200 uppercase tracking-tighter">Ditolak</span>
                                            @php
                                                $rejectionNote = $memo->approvals->where('note', '!=', 'Memo Diterbitkan')->last()->pivot->note ?? null;
                                            @endphp
                                            
                                        </div>
                                   
                                    @elseif($memo->is_final)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full border border-green-200 uppercase tracking-tighter">Aktif</span>
                                    @else
                                        <div class="flex flex-col items-center">
                                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black rounded-full border border-blue-200 uppercase tracking-tighter inline-block w-fit">Pending</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex flex-wrap justify-center gap-1.5 mt-1 max-w-[200px]">
                                        @foreach($otherApprovers as $approver)
                                            <div class="group/name flex items-center bg-slate-50 border border-slate-200 rounded-md px-2 py-0.5" title="Telah Disetujui">
                                                <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 animate-pulse"></div>
                                                <span class="text-[10px] font-bold text-slate-700 whitespace-nowrap">
                                                    {{ $approver->name }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('memos.show', $memo->id) }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 hover:border-blue-200 hover:shadow-sm transition-all" title="Detail">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                    
                                    @if(!$memo->is_rejected)
                                        <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-red-600 hover:border-red-200 transition-all" title="View PDF">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                        </a>
                                    @endif
 {{-- TOMBOL NONAKTIFKAN: Hanya jika Aktif/Final dan User memiliki Otoritas --}}
                                    @php
                                        $approverIds = is_array($memo->target_approvers) ? $memo->target_approvers : [];
                                        if ($memo->approver_id) { $approverIds[] = $memo->approver_id; }
                                        $hasAuthority = Auth::id() == $memo->user_id || in_array(Auth::id(), $approverIds);
                                    @endphp

                                    @if($memo->is_final && !$memo->is_rejected && $hasAuthority)
                                        <button type="button" onclick="confirmDeactivate({{ $memo->id }})" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all" title="Nonaktifkan Memo">
                                            <i data-lucide="power" class="w-4 h-4 text-red-500"></i>
                                        </button>
                                        <form id="deactivate-form-{{ $memo->id }}" action="{{ route('memos.deactivate', $memo->id) }}" method="POST" class="hidden">
                                            @csrf
                                            <input type="hidden" name="note" id="deactivate-note-input-{{ $memo->id }}">
                                        </form>
                                    @endif
                                    {{-- PERBAIKAN: Izinkan edit jika draf, ditolak, atau masih pending (baru tanda tangan pembuat saja) --}}
                                    @if(Auth::id() == $memo->user_id && ($memo->is_draft || $memo->is_rejected || (!$memo->is_final && $memo->approvals->count() <= 1)))
                                        <a href="{{ route('memos.edit', $memo->id) }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-amber-600 hover:border-amber-200 transition-all" title="Revisi / Edit">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    $(document).ready(function() {
        lucide.createIcons();

        var table = $('#memoTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[3, 'desc']], 
            language: {
                search: "",
                searchPlaceholder: "Cari subjek atau nomor memo...",
                lengthMenu: "Tampilkan _MENU_",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ memo",
                paginate: {
                    next: '<i class="lucide-chevron-right w-4 h-4"></i>',
                    previous: '<i class="lucide-chevron-left w-4 h-4"></i>'
                }
            },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-6 gap-4"f l>rt<"flex flex-col md:flex-row justify-between items-center mt-6 gap-4"i p>',
            drawCallback: function() {
                lucide.createIcons();
                $('.dataTables_paginate .paginate_button').addClass('rounded-xl border-none font-bold text-xs');
            }
        });

        $('.dataTables_filter input').addClass('bg-gray-50 border border-gray-200 rounded-2xl px-5 py-2.5 focus:ring-4 focus:ring-red-50 focus:border-red-800 transition-all outline-none w-72');
        $('.dataTables_length select').addClass('bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 outline-none');
    });
     function confirmDeactivate(memoId) {
        Swal.fire({
            title: 'Nonaktifkan Memo?',
            text: "Memo yang sudah aktif akan dibatalkan masa berlakunya. Berikan alasan:",
            input: 'textarea',
            inputPlaceholder: 'Tulis alasan penonaktifan...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#111827',
            confirmButtonText: 'Ya, Nonaktifkan',
            cancelButtonText: 'Batal',
            inputValidator: (value) => { if (!value) return 'Alasan wajib diisi!' },
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deactivate-form-' + memoId);
                const input = document.getElementById('deactivate-note-input-' + memoId);
                if (form && input) {
                    input.value = result.value;
                    form.submit();
                } else {
                    console.error('Form penonaktifan tidak ditemukan untuk ID: ' + memoId);
                }
            }
        });
    }
</script>

<style>
    /* Filter Search Customization */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 12px !important;
        border: 1px solid #E5E7EB !important;
        padding: 8px 16px !important;
        outline: none !important;
        transition: all 0.2s;
        margin-left: 10px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #ef4444 !important; /* Warna Merah sesuai tema */
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
    }

    /* Pagination Styling */
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 1.5rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 4px !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid #E5E7EB !important;
        border-radius: 10px !important;
        padding: 5px 12px !important;
        background: white !important;
        color: #4B5563 !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        transition: all 0.2s !important;
        cursor: pointer !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #F9FAFB !important;
        border-color: #D1D5DB !important;
        color: #111827 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #991b1b !important; /* Merah pekat (Red-800) */
        color: white !important;
        border-color: #991b1b !important;
        box-shadow: 0 4px 12px rgba(153, 27, 27, 0.2) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }

    /* Info text styling */
    .dataTables_wrapper .dataTables_info {
        padding-top: 1.5rem !important;
        font-size: 13px !important;
        color: #6B7280 !important;
    }
</style>
@endsection