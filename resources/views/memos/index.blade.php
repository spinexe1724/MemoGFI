<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Daftar Memo Internal</h2>
        <a href="{{ route('memos.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">Buat Memo Baru</a>
    </div>

    <div class="bg-white shadow overflow-hidden rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perihal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($memos as $memo)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $memo->reference_no }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $memo->subject }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $memo->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Download PDF</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
