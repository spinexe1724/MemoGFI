@extends('layouts.app')

@section('title', 'Verifikasi Pendaftar Baru')

{{-- CSS Kustom agar DataTables tidak merusak desain --}}
<style>
    /* Gunakan ID tabel agar lebih spesifik */
    #userTable_wrapper .dataTables_filter input {
        background-color: #f3f4f6 !important; /* bg-gray-100 */
        border: none !important;
        border-radius: 1rem !important;
        padding: 0.75rem 1.25rem !important;
        outline: none !important;
    }

    #userTable thead th {
        background-color: #f3f4f6 !important; /* Abu-abu */
        color: #800000 !important; /* Maroon */
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        font-size: 10px !important;
        border-bottom: none !important;
    }

    /* Pagination Merah */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #800000 !important;
        color: white !important;
        border: none !important;
        border-radius: 12px !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #fecaca !important; /* red-200 */
        color: #800000 !important;
        border: none !important;
    }
</style>

@section('content')
<div class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header Section Tetap Sama --}}
        <div class="bg-[#800000] p-8 rounded-3xl text-white shadow-xl mb-8 flex justify-between items-center border border-white/10 relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-black italic uppercase tracking-tight">
                    Antrean <span class="text-red-200">Verifikasi</span>
                </h2>
                <p class="text-white/80 mt-1 font-medium opacity-90 italic">
                    Daftar pengguna baru yang menunggu penetapan Role, Divisi, & Level Akses.
                </p>
            </div>
            <div class="relative z-10 p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner text-center min-w-[140px]">
                <p class="text-[10px] font-black uppercase tracking-widest text-white/70 mb-1">Total Menunggu</p>
                <p class="text-3xl font-black text-white">{{ $pendingUsers->count() }}</p>
            </div>
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-2xl flex items-center shadow-sm animate-in fade-in slide-in-from-top-4 duration-500">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 mr-3"></i>
                <span class="text-sm font-bold text-emerald-800 uppercase italic">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                @if($pendingUsers->count() > 0)
                    <div class="overflow-x-auto">
                        {{-- TAMBAHKAN ID "userTable" DAN HAPUS BORDER SEPARATE --}}
                        <table id="userTable" class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-gray-50">
                                    <th class="px-6 py-4">Informasi Pendaftar</th>
                                    <th class="px-6 py-4">Kontak</th>
                                    <th class="px-6 py-4 text-center">Cabang</th>
                                    <th class="px-6 py-4 text-center">Level Akses</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach($pendingUsers as $user)
                                <tr class="hover:bg-amber-50/50 transition-all group border-b border-gray-50 last:border-none">
                                    <td class="px-6 py-6">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black mr-4 border-2 border-white shadow-sm shrink-0">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 uppercase italic tracking-tight">{{ $user->name }}</p>
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[9px] font-black rounded uppercase tracking-tighter">Status: Pending</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-700">{{ $user->email }}</span>
                                            <span class="text-xs text-gray-400 font-medium">{{ $user->phone_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="px-4 py-1.5 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-white shadow-sm">
                                            {{ $user->branch ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-black {{ $user->level == 0 ? 'text-red-500' : 'text-emerald-600' }}">
                                                LEVEL {{ $user->level }}
                                            </span>
                                            @if($user->level == 0)
                                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter italic">Belum Aktif</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-right">
                                        <button 
                                            onclick="openVerifyModal('{{ $user->id }}', '{{ $user->name }}')"
                                            class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-black text-white text-[10px] font-black rounded-xl transition-all shadow-lg shadow-amber-200 uppercase italic tracking-widest">
                                            <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i> Verifikasi
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Empty State Tetap Sama --}}
                    <div class="py-20 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="user-check" class="w-10 h-10 text-gray-200"></i>
                        </div>
                        <p class="text-gray-400 font-bold italic uppercase tracking-widest">Tidak ada antrean verifikasi saat ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="verifyModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-300">
            <div class="bg-[#800000] p-8 text-white relative">
                <h3 class="text-2xl font-black italic uppercase tracking-tight" id="modalUserName">Verifikasi User</h3>
                <p class="text-white/70 text-[10px] font-black uppercase tracking-[0.2em] mt-1">Aktivasi Hak Akses & Penempatan Divisi</p>
                <button onclick="closeVerifyModal()" class="absolute top-8 right-8 text-white/50 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form id="verifyForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="p-8 md:p-10 space-y-6">
                    {{-- Role --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Pilih Role / Jabatan</label>
                        <select name="role" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 transition-all outline-none appearance-none cursor-pointer">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin Cabang</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="manager">Manager</option>
                            <option value="bm">Branch Manager</option>
                            <option value="direksi">Direksi</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>

                    {{-- Divisi --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Pilih Divisi Kerja</label>
                        <select name="division" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 transition-all outline-none appearance-none cursor-pointer">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->name }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TAMBAHAN FIELD LEVEL AKSES --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Level Akses Dashboard</label>
                        <select name="level" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-amber-500/10 focus:border-amber-600 transition-all outline-none appearance-none cursor-pointer">
                            <option value="">-- Pilih Level --</option>
                            @foreach($levels as $key => $val)
                                <option value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-gray-400 font-bold italic ml-1">* User tidak bisa login jika level tetap 0.</p>
                    </div>
                </div>

                <div class="p-8 md:p-10 pt-0 flex gap-4">
                    <button type="button" onclick="closeVerifyModal()" class="flex-1 px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-500 text-[10px] font-black rounded-2xl transition-all uppercase tracking-widest italic">
                        Batal
                    </button>
                    <button type="submit" class="flex-[2] px-6 py-4 bg-amber-600 hover:bg-black text-white text-[10px] font-black rounded-2xl transition-all shadow-xl shadow-amber-200 uppercase tracking-widest italic flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Aktifkan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#userTable').DataTable({
            "language": {
                "search": "",
                "searchPlaceholder": "CARI DATA PENDAFTAR...",
                "lengthMenu": "_MENU_",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ antrean",
                "paginate": {
                    "next": "Berikutnya",
                    "previous": "Kembali"
                }
            },
            "dom": '<"flex flex-col md:flex-row justify-between items-center mb-2"f l>rt<"flex flex-col md:flex-row justify-between items-center mt-4"i p>',
            "drawCallback": function() {
                if (window.lucide) { lucide.createIcons(); }
            }
        });
    });

    function openVerifyModal(userId, userName) {
        const modal = document.getElementById('verifyModal');
        const form = document.getElementById('verifyForm');
        const nameDisplay = document.getElementById('modalUserName');

        // URL action untuk route verify
        form.action = `/users/${userId}/verify`; 
        
        nameDisplay.innerText = `Verifikasi: ${userName}`;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeVerifyModal() {
        const modal = document.getElementById('verifyModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('verifyModal');
        if (event.target == modal.querySelector('.fixed.inset-0.bg-black\\/60')) {
            closeVerifyModal();
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endsection