@extends('layouts.app')

@section('title', 'User Management - Memo System')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwind.min.css">

<div class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header Panel --}}
        <div class="relative bg-gradient-to-r from-red-800 to-red-700 p-8 rounded-3xl text-red overflow-hidden shadow-2xl mb-8">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight">User Management</h2>
                    <p class="text-red-700 mt-2 opacity-90 flex items-center">
                        <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                        Kelola hak akses, divisi, dan identitas pengguna sistem memo.
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center justify-center gap-3">
                    {{-- Toggle Filter: Aktif vs Terhapus --}}
                    @if(request('show_deleted'))
                        <a href="{{ route('users.index') }}" class="bg-white/10 hover:bg-white/20 text-red px-5 py-3 rounded-2xl font-bold backdrop-blur-md border border-white/20 transition-all flex items-center text-sm">
                            <i data-lucide="user-check" class="w-4 h-4 mr-2 text-red-400"></i> Lihat User Aktif
                        </a>
                    @else
                        <a href="{{ route('users.index', ['show_deleted' => 1]) }}" class="bg-white-500/20 hover:bg-amber-500/30 text-red-400 px-5 py-3 rounded-2xl font-bold backdrop-blur-md border border-amber-500/30 transition-all flex items-center text-sm">
                            <i data-lucide="archive" class="w-4 h-4 mr-2 text-red-400"></i> Lihat Arsip (Nonaktif)
                        </a>
                    @endif

                    <a href="{{ route('users.create') }}" class="bg-white text-red-800 hover:bg-red-50 px-6 py-3 rounded-2xl font-bold shadow-lg transition-all transform hover:-translate-y-1 active:scale-95 flex items-center text-sm">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Tambah Akun Baru
                    </a>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        @if (session('success'))
            <div class="mb-6 flex items-center p-4 text-green-800 border-t-4 border-green-500 bg-green-50 rounded-2xl shadow-sm" role="alert">
                <i data-lucide="check-circle" class="w-5 h-5 mr-3"></i>
                <div class="text-sm font-bold">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 flex items-center p-4 text-red-800 border-t-4 border-red-500 bg-red-50 rounded-2xl shadow-sm" role="alert">
                <i data-lucide="alert-triangle" class="w-5 h-5 mr-3"></i>
                <div class="text-sm font-bold">{{ session('error') }}</div>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="p-6 md:p-8">
                <table id="userTable" class="w-full text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-gray-400 uppercase text-[11px] tracking-widest border-b border-gray-100">
                            <th class="px-4 py-4 font-bold">Pengguna</th>
                            <th class="px-4 py-4 font-bold">Kontak & Divisi</th>
                            <th class="px-4 py-4 font-bold text-center">Role</th>
                            <th class="px-4 py-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach($users as $user)
                        <tr class="group hover:bg-gray-50/80 transition-all {{ $user->trashed() ? 'opacity-60' : '' }}">
                            <td class="px-4 py-4 bg-white group-hover:bg-transparent rounded-l-2xl border-y border-l border-gray-50 group-hover:border-blue-100 transition-all">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br {{ $user->trashed() ? 'from-gray-400 to-gray-600' : 'from-blue-500 to-indigo-600' }} flex items-center justify-center text-black font-bold text-sm shadow-md mr-4 shadow-blue-100">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 tracking-tight">{{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono tracking-tighter italic">ID: #USR-{{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 bg-white group-hover:bg-transparent border-y border-gray-50 group-hover:border-blue-100 transition-all">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-600 flex items-center">
                                        <i data-lucide="mail" class="w-3 h-3 mr-1.5 opacity-40"></i> {{ $user->email }}
                                    </span>
                                    <div class="flex items-center mt-1 space-x-2">
                                        <span class="text-[11px] text-blue-600 font-bold uppercase tracking-wider">{{ $user->division }}</span>
                                        <span class="text-[10px] bg-red-50 text-red-700 px-2 py-0.5 rounded font-black uppercase">{{ $user->branch ?? 'HO' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 bg-white group-hover:bg-transparent border-y border-gray-50 group-hover:border-blue-100 transition-all text-center">
                                @php
                                    $roleClasses = [
                                        'superadmin' => 'bg-black text-white',
                                        'gm' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        'admin' => 'bg-red-50 text-red-700 border-red-100',
                                        'manager' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'bm' => 'bg-orange-50 text-orange-700 border-orange-100',
                                        'user' => 'bg-blue-50 text-blue-700 border-blue-100'
                                    ];
                                    $class = $roleClasses[strtolower($user->role)] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                                @endphp
                                <span class="px-4 py-1.5 rounded-xl text-[10px] font-extrabold uppercase border shadow-sm {{ $class }}">
                                    {{ $user->role }}
                                </span>
                            </td>

                            <td class="px-4 py-4 bg-white group-hover:bg-transparent rounded-r-2xl border-y border-r border-gray-50 group-hover:border-blue-100 transition-all text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    @if($user->trashed())
                                        {{-- AKSI RESTORE --}}
                                        <form action="{{ route('users.restore', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Pulihkan Akun">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        </form>

                                        {{-- AKSI FORCE DELETE --}}
                                        <form action="{{ route('users.force_delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('HAPUS PERMANEN? Data tidak dapat dikembalikan!')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus Permanen">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{-- AKSI EDIT --}}
                                        <a href="{{ route('users.edit', $user->id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Akun">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        
                                        {{-- AKSI SOFT DELETE --}}
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan akun {{ $user->name }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Nonaktifkan (Soft Delete)">
                                                    <i data-lucide="user-minus" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
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
</div>

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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwind.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    $(document).ready(function() {
        lucide.createIcons();

        if ($.fn.DataTable.isDataTable('#userTable')) {
            $('#userTable').DataTable().destroy();
        }

        $('#userTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true,
            "pageLength": 10,
            "dom": '<"flex flex-col md:flex-row justify-between items-center mb-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-6"ip>',
            "language": {
                "search": "",
                "searchPlaceholder": "Cari Pengguna...",
                "lengthMenu": "Tampilkan _MENU_",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ user",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": {
                    "previous": "<i data-lucide='chevron-left' class='w-4 h-4'></i>",
                    "next": "<i data-lucide='chevron-right' class='w-4 h-4'></i>"
                }
            },
            "drawCallback": function() {
                // Render ulang icon Lucide setiap kali ganti halaman/filter
                lucide.createIcons();
            }
        });
    });
</script>
@endpush