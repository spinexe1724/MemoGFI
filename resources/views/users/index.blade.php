@extends('layouts.app')

@section('title', 'User Management - Memo System')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwind.min.css">

<div class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        
        <div class="relative bg-gradient-to-r from-red-800 to-red-700 p-8 rounded-3xl text-red overflow-hidden shadow-2xl mb-8">
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight">User Management</h2>
                    <p class="text-red-700 mt-2 opacity-90 flex items-center">
                        <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                        Kelola hak akses, divisi, dan identitas pengguna sistem memo.
                    </p>
                </div>
                <a href="{{ route('users.create') }}" class="bg-white text-red-800 hover:bg-red-50 px-6 py-3 rounded-2xl font-bold shadow-lg transition-all transform hover:-translate-y-1 active:scale-95 flex items-center text-sm">
                    <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                    Tambah Akun Baru
                </a>
            </div>
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        </div>

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
                        <tr class="group hover:bg-gray-50/80 transition-all">
                            <td class="px-4 py-4 bg-white group-hover:bg-transparent rounded-l-2xl border-y border-l border-gray-50 group-hover:border-blue-100 transition-all">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-black font-bold text-sm shadow-md mr-4 shadow-blue-100">
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
                                    <span class="text-[11px] text-blue-600 font-bold uppercase tracking-wider mt-1">
                                        {{ $user->division }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-4 bg-white group-hover:bg-transparent border-y border-gray-50 group-hover:border-blue-100 transition-all text-center">
                                @php
                                    $roleClasses = [
                                        'gm' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        'admin' => 'bg-red-50 text-red-700 border-red-100',
                                        'manager' => 'bg-amber-50 text-amber-700 border-amber-100',
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
                                   
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus Akun">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
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
    /* Custom Styling DataTable agar selaras dengan Tailwind */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 12px !important;
        border: 1px solid #E5E7EB !important;
        padding: 8px 16px !important;
        outline: none !important;
        transition: all 0.2s;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #1E40AF !important;
        color: white !important;
        border: none !important;
        border-radius: 10px !important;
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
            "language": {
                "search": "Cari Pengguna:",
                "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya"
                },
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ pengguna"
            },
            "drawCallback": function() {
                // Render ulang ikon lucide setiap kali tabel berubah (paging/search)
                lucide.createIcons();
            }
        });
    });
</script>
@endpush