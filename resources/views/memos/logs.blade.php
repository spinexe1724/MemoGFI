@extends('layouts.app')

@section('title', 'Riwayat Memo')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwind.min.css">

<div class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Riwayat Pembuatan Memo</h2>
                <p class="text-gray-500 mt-1 flex items-center">
                    <i data-lucide="layers" class="w-4 h-4 mr-2"></i>
                    Daftar seluruh memo internal yang telah diterbitkan di sistem.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="bg-blue-50 p-3 rounded-xl text-blue-600">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Memo</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $memos->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-gray-100 overflow-hidden">
            <div class="p-6 md:p-8">
                <table id="memoTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-400 uppercase text-[11px] tracking-widest border-b border-gray-100">
                            <th class="px-4 py-4 font-bold">No. Referensi</th>
                            <th class="px-4 py-4 font-bold">Perihal</th>
                            <th class="px-4 py-4 font-bold">Pembuat</th>
                            <th class="px-4 py-4 font-bold">Divisi</th>
                            <th class="px-4 py-4 font-bold">Role</th>
                            <th class="px-4 py-4 font-bold">Tgl Dibuat</th>
                            <th class="px-4 py-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y divide-gray-50">
                        @foreach($memos as $memo)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-4 py-4">
                                <span class="font-mono text-sm font-bold text-blue-600 bg-blue-50/50 px-2 py-1 rounded">
                                    {{ $memo->reference_no }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-gray-800 line-clamp-1">{{ $memo->subject }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 text-xs font-bold text-gray-500 border border-gray-200 uppercase">
                                        {{ substr($memo->user->name ?? '?', 0, 2) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $memo->user->name ?? 'User Terhapus' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-gray-600">{{ $memo->sender }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 bg-white text-gray-500 rounded-lg text-[10px] font-bold uppercase border border-gray-200 shadow-sm">
                                    {{ $memo->user->role ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-xs text-gray-500 tabular-nums">
                                    {{ $memo->created_at->format('d M Y') }}
                                    <span class="block text-[10px] opacity-60">{{ $memo->created_at->format('H:i') }} WIB</span>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('memos.show', $memo->id) }}" class="inline-flex items-center justify-center p-2 bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm group-hover:scale-110">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwind.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    $(document).ready(function() {
        lucide.createIcons();

        $('#memoTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                searchPlaceholder: "Cari nomor, perihal, atau pembuat...",
                search: ""
            },
            pageLength: 10,
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"f l>rtip',
            drawCallback: function() {
                lucide.createIcons();
                // Styling kustom untuk search box
                $('.dataTables_filter input').addClass('w-full md:w-80 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all text-sm');
                $('.dataTables_length select').addClass('px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm');
                $('.dataTables_paginate').addClass('mt-6');
            }
        });
    });
</script>

<style>
    /* Styling tambahan untuk integrasi Tailwind */
    .dataTables_wrapper .dataTables_info {
        font-size: 0.875rem;
        color: #6B7280;
        padding-top: 1.5rem;
    }
    table.dataTable thead th {
        border-bottom: 1px solid #F3F4F6 !important;
    }
</style>
@endsection