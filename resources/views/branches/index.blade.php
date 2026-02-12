@extends('layouts.app')

@section('title', 'Manajemen Cabang - Gratama System')

@section('content')
    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Data Cabang</h2>
                    <p class="text-slate-500 mt-1">Kelola seluruh kantor cabang operasional dalam satu sistem.</p>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-2xl border border-blue-100">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                    </span>
                    <span class="text-sm font-bold text-blue-700 uppercase tracking-widest">{{ count($branches) }} Cabang Aktif</span>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-10">
                <h3 class="text-lg font-bold mb-6 flex items-center text-slate-800">
                    <div class="p-2 bg-blue-600 rounded-xl mr-3 shadow-lg shadow-blue-200">
                        <i data-lucide="map-pin" class="w-5 h-5 text-white"></i>
                    </div>
                    Registrasi Cabang Baru
                </h3>
                <form action="{{ route('branches.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kode Cabang</label>
                            <input type="text" name="code" placeholder="Cth: JKT" 
                                   class="w-full border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all py-3.5" required>
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Lokasi</label>
                            <input type="text" name="name" placeholder="Cth: Jakarta Pusat" 
                                   class="w-full border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all py-3.5" required>
                        </div>
                        <div class="md:col-span-3 flex items-end">
                            <button type="submit" class="w-full bg-slate-900 text-white px-6 py-4 rounded-2xl font-bold hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95 flex items-center justify-center group">
                                <i data-lucide="save" class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform"></i> 
                                Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <table id="branchTable" class="w-full text-left display border-none">
                    <thead>
                        <tr class="text-slate-400 text-[11px] font-black uppercase tracking-[0.2em]">
                            <th class="p-4 border-b border-slate-50">Identity</th>
                            <th class="p-4 border-b border-slate-50">Branch Name</th>
                            <th class="p-4 border-b border-slate-50 text-center">Operational</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($branches as $branch)
                        <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                            <td class="p-4">
                                <span class="bg-slate-100 text-slate-700 px-3 py-1.5 rounded-xl text-[11px] font-black border border-slate-200 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all uppercase tracking-tighter">
                                    {{ $branch->code }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 tracking-tight">{{ $branch->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium">Gratama Branch Unit</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-center items-center">
                                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Hapus cabang ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2.5 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-2xl transition-all" title="Hapus">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
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

<script>
    $(document).ready(function() {
        $('#branchTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "",
                searchPlaceholder: "Cari cabang...",
                lengthMenu: "Tampilkan _MENU_",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                paginate: {
                    next: "→",
                    previous: "←"
                }
            }
        });
        
        // Memastikan Lucide icons dirender setelah DataTable dimuat
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush