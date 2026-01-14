

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Form Tambah Cabang -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <i data-lucide="map-pin" class="w-5 h-5 mr-2 text-blue-600"></i>
                    Tambah Cabang Baru
                </h3>
                <form action="{{ route('branches.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Cabang</label>
                            <input type="text" name="code" placeholder="JKT" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Cabang</label>
                            <input type="text" name="name" placeholder="Jakarta Pusat" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-all shadow-md">
                                Simpan Cabang
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Cabang -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4 font-bold text-gray-600">Kode</th>
                            <th class="p-4 font-bold text-gray-600">Nama Cabang</th>
                            <th class="p-4 font-bold text-gray-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($branches as $branch)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4">
                                <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold border border-indigo-100 uppercase">
                                    {{ $branch->code }}
                                </span>
                            </td>
                            <td class="p-4 font-semibold text-gray-800">{{ $branch->name }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Hapus cabang ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 font-bold text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-400 italic">Belum ada data cabang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
