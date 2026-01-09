
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Riwayat Pembuatan Memo (Seluruh Divisi)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="p-4 font-bold text-gray-600 text-xs uppercase">No. Referensi</th>
                                    <th class="p-4 font-bold text-gray-600 text-xs uppercase">Perihal</th>
                                    <th class="p-4 font-bold text-gray-600 text-xs uppercase">Pembuat</th>
                                    <th class="p-4 font-bold text-gray-600 text-xs uppercase">Divisi</th>
                                    <th class="p-4 font-bold text-gray-600 text-xs uppercase">Role</th>
                                    <th class="p-4 font-bold text-gray-600 text-xs uppercase">Tgl Dibuat</th>
                                    <th class="p-4 font-bold text-gray-600 text-xs uppercase text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($memos as $memo)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-sm font-semibold text-blue-600">{{ $memo->reference_no }}</td>
                                    <td class="p-4 text-sm text-gray-800">{{ $memo->subject }}</td>
                                    <td class="p-4 text-sm text-gray-800 font-medium">{{ $memo->user->name ?? 'User Terhapus' }}</td>
                                    <td class="p-4 text-sm text-gray-600">{{ $memo->sender }}</td>
                                    <td class="p-4 text-sm">
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase border">
                                            {{ $memo->user->role ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs text-gray-500">
                                        {{ $memo->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('memos.show', $memo->id) }}" class="text-blue-600 hover:underline text-xs font-bold">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-500 italic">Belum ada data memo yang tercatat di sistem.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $memos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
