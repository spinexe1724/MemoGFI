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
        
        @if(in_array(Auth::user()->role, ['supervisor', 'manager', 'gm', 'direksi']))
    <a href="{{ route('memos.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-lg shadow-red-200 transition-all transform hover:-translate-y-1 active:scale-95 border-b-4 border-red-800">
        <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i>
        BUAT MEMO BARU
    </a>
@endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-4 bg-blue-50 rounded-2xl text-blue-600"><i data-lucide="file-text"></i></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Memo</p><p class="text-2xl font-black text-gray-800">{{ $memos->count() }}</p></div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-4 bg-amber-50 rounded-2xl text-amber-600"><i data-lucide="clock"></i></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pending Review</p><p class="text-2xl font-black text-gray-800">{{ $memos->where('is_fully_approved', false)->where('is_rejected', false)->count() }}</p></div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-4 bg-red-50 rounded-2xl text-red-600"><i data-lucide="alert-circle"></i></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Expired/Rejected</p><p class="text-2xl font-black text-gray-800">{{ $memos->where('is_rejected', true)->count() }}</p></div>
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
                        <th class="pb-4 font-black">Mengetahui</th>
                        <th class="pb-4 font-black">Status</th>
                        <th class="pb-4 font-black text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($memos as $memo)
                    
                    @php
                        $isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($memo->valid_until)) : false;
                        $otherApprovers = $memo->approvals->where('id', '!=', $memo->user_id);
                        $mengetahui = '-';

if ($memo->user->role === 'supervisor') {

    $mengetahui = 'GM ' . ($memo->user->division ?? 'Divisi');

} elseif ($memo->user->role === 'gm') {

    $mengetahui = $memo->user->name;

} else {

    $mengetahui = $memo->user->division;

}                   

                    @endphp
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="py-6 text-center text-gray-400 font-mono text-xs">#{{ $memo->id }}</td>
                        <td class="py-6">
                            <div class="flex flex-col">
                                <span class="font-extrabold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $memo->subject }}</span>
                                <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ $memo->reference_no }}</span>
                                <div class="mt-2 flex items-center text-[10px]">
                                    <i data-lucide="calendar" class="w-3 h-3 mr-1 text-gray-300"></i>
                                    <span class="{{ $isExpired ? 'text-red-500 font-bold' : 'text-gray-400 font-medium' }}">
                                        Exp: {{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('d M Y') : '∞' }}
                                    </span>
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
                        <td class="py-6">
                        {{ $mengetahui }}
                        </td>
                        <td class="py-6">
                           @if($memo->is_draft)
        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-lg border border-amber-200 uppercase">Draf</span>
    @elseif($memo->is_rejected)
        <span class="px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-lg border border-red-200 uppercase">Ditolak</span>
    @elseif($isExpired)
        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-lg border border-gray-200 uppercase">Kadaluarsa</span>
    @else
        <span class="px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-lg border border-green-200 uppercase">Aktif</span>
    @endif

                                <div class="flex -space-x-1 overflow-hidden">
                                    @foreach($otherApprovers as $approver)
                                        <div class="inline-block h-5 w-5 rounded-full ring-2 ring-white bg-blue-600 text-[8px] text-white flex items-center justify-center font-bold" title="{{ $approver->name }}">
                                            {{ substr($approver->name, 0, 5) }}
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

                                @if(Auth::id() == $memo->user_id && $memo->approvals->count() <= 1 && !$memo->is_rejected)
                                    <a href="{{ route('memos.edit', $memo->id) }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-amber-600 hover:border-amber-200 transition-all" title="Edit">
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
        // Initialize Lucide
        lucide.createIcons();

        // Initialize DataTable
        var table = $('#memoTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "",
                searchPlaceholder: "Cari subjek atau nomor memo...",
                lengthMenu: "Tampilkan _MENU_",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ memo",
                paginate: {
                    next: '<i class="lucide-chevron-right"></i>',
                    previous: '<i class="lucide-chevron-left"></i>'
                }
            },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-6 gap-4"f l>rt<"flex flex-col md:flex-row justify-between items-center mt-6 gap-4"i p>',
            drawCallback: function() {
                // Re-initialize icons on table redraw (pagination/search)
                lucide.createIcons();
                // Custom style for pagination
                $('.dataTables_paginate .paginate_button').addClass('rounded-xl border-none font-bold text-xs');
            }
        });

        // Custom Tailwind Styling for DataTables Search Input
        $('.dataTables_filter input').addClass('bg-gray-50 border border-gray-200 rounded-2xl px-5 py-2.5 focus:ring-4 focus:ring-red-50 focus:border-red-800 transition-all outline-none w-72');
        $('.dataTables_length select').addClass('bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 outline-none');
    });
</script>

<style>
    /* Mengatasi konflik CSS DataTables dengan Tailwind */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #991b1b !important; /* Red 800 */
        color: white !important;
        border: none !important;
        border-radius: 0.75rem !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f3f4f6 !important;
        color: #111827 !important;
        border: none !important;
    }
    table.dataTable thead th {
        border-bottom: 1px solid #f3f4f6 !important;
    }
    table.dataTable.no-footer {
        border-bottom: none !important;
    }
</style>
@endsection