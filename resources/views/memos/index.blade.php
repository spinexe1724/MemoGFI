
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    @vite('resources/css/app.css')
    <title>Dashboard Memo</title>
    <script src="[https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)"></script>
</head>
<body class="bg-gray-50 py-10 px-4">
    
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-800 p-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Internal Memos</h1>
                <p class="text-blue-100 italic">User: {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-bold">Logout</button>
            </form>
        </div>

        <div class="p-6">
            <div class="flex justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Daftar Memo</h2>
                @if(Auth::user()->role === 'staff')
                    <a href="{{ route('memos.create') }}" class="bg-green-600 text-white px-4 py-2 rounded font-bold">+ Buat Memo Baru</a>
                @endif
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-3 border">Subjek</th>
                        <th class="p-3 border">Status Persetujuan</th>
                        <th class="p-3 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($memos as $memo)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 border">{{ $memo->subject }}</td>
                        <td class="p-3 border">
                            <div class="flex items-center">
                                <span class="bg-gray-200 px-2 py-1 rounded text-xs mr-2">{{ $memo->approvals->count() }}/5 GM</span>
                                @if($memo->is_fully_approved)
                                    <span class="text-green-600 font-bold text-sm">FINAL</span>
                                @else
                                    <span class="text-yellow-600 text-sm italic">Pending</span>
                                @endif
                            </div>
                        </td>
                        <td class="p-3 border text-center space-x-2">
                            @if(Auth::user()->role === 'gm' && !$memo->approvals->contains(Auth::user()))
                                <form action="{{ route('memos.approve', $memo->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">Approve Now</button>
                                </form>
                            @endif
                            <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-bold">View PDF</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>