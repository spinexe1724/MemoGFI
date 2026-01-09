@extends('layouts.app') {{--  layout --}}

@section('title', 'Home Memo System') {{-- title --}}

@section('content') {{-- content section --}}
<script src="https://cdn.tailwindcss.com"></script>
    <div class="py-12">

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
           
            <div class="flex justify-between mt-6 mb-6 items-center">
            <a href="{{ route('divisions.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-bold shadow-md transition-all flex items-center">
                
                + Buat Memo Baru
            </a>
    </div>

            <!-- Tabel Daftar Divisi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4 font-bold text-gray-600">ID</th>
                            <th class="p-4 font-bold text-gray-600">Nama Divisi</th>
                            <th class="p-4 font-bold text-gray-600">Inisial</th>
                            <th class="p-4 font-bold text-gray-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($divisions as $division)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-gray-400">#{{ $division->id }}</td>
                            <td class="p-4 font-semibold text-gray-800">{{ $division->name }}</td>
                            <td class="p-4">
                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold border border-blue-100 uppercase">
                                    {{ $division->initial }}
                                </span>
                            </td>
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
                            <td colspan="4" class="p-8 text-center text-gray-500 italic">Belum ada divisi yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection