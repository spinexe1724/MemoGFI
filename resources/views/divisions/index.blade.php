@extends('layouts.app')

@section('title', 'Daftar Divisi')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwind.min.css">

<div class="py-12 px-4">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Manajemen Divisi</h2>
                <p class="text-gray-500 mt-1">Kelola daftar divisi dan inisial untuk penomoran memo.</p>
            </div>
            <a href="{{ route('divisions.create') }}" class="w-full md:w-auto bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-Black px-6 py-3 rounded-xl font-bold shadow-lg shadow-green-100 transition-all transform hover:-translate-y-1 flex items-center justify-center">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i>
                Tambah Divisi Baru
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden p-6">
            <table id="divisionTable" class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-400 uppercase text-xs tracking-wider border-b border-gray-100">
                        <th class="px-4 py-4 font-bold">ID</th>
                        <th class="px-4 py-4 font-bold">Nama Divisi</th>
                        <th class="px-4 py-4 font-bold">Inisial</th>
                        <th class="px-4 py-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($divisions as $division)
                    <tr class="hover:bg-gray-50/50 transition-colors border-b border-gray-50 last:border-0">
                        <td class="px-4 py-4 font-mono text-sm text-gray-400">#{{ $division->id }}</td>
                        <td class="px-4 py-4">
                            <span class="font-semibold text-gray-800">{{ $division->name }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-xs font-bold border border-blue-100 uppercase tracking-wide">
                                {{ $division->initial }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex justify-center items-center space-x-3">
                                <a href="{{ route('divisions.destroy', $division->id) }}" class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                
                                <form action="{{ route('divisions.destroy', $division->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi {{ $division->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
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

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwind.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi Lucide Icons
        lucide.createIcons();

        // Inisialisasi DataTable
        $('#divisionTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json', // Bahasa Indonesia
                searchPlaceholder: "Cari divisi...",
                search: ""
            },
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: 3 } // Nonaktifkan sortir pada kolom Aksi
            ],
            drawCallback: function() {
                // Re-render icons after table redraw (pagination/search)
                lucide.createIcons();
                
                // Styling kustom untuk input search DataTable agar lebih cantik
                $('.dataTables_filter input').addClass('px-4 py-2 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all ml-2 text-sm');
                $('.dataTables_length select').addClass('px-3 py-1 border border-gray-200 rounded-lg outline-none text-sm mx-2');
            }
        });
    });
</script>

<style>
    /* Menghilangkan border default datatable yang mengganggu */
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1.5rem;
    }
    table.dataTable {
        border-collapse: collapse !important;
    }
</style>
@endsection