<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Divisi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Form Tambah Divisi -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <h3 class="text-lg font-bold mb-4">Tambah Divisi Baru</h3>
                <form action="{{ route('divisions.store') }}" method="POST" class="flex gap-4">
                    @csrf
                    <div class="flex-1">
                        <input type="text" name="name" placeholder="Nama Divisi (contoh: IT, HRD, Marketing)" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500" required>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">
                        Tambah
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Divisi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4 font-bold text-gray-600">ID</th>
                            <th class="p-4 font-bold text-gray-600">Nama Divisi</th>
                            <th class="p-4 font-bold text-gray-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($divisions as $division)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 text-gray-400">#{{ $division->id }}</td>
                            <td class="p-4 font-semibold text-gray-800">{{ $division->name }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('divisions.destroy', $division->id) }}" method="POST" 
                                      onsubmit="return confirm('Hapus divisi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline text-sm font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500 italic">Belum ada divisi yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>