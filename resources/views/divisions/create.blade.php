@extends('layouts.app') {{--  layout --}}

@section('title', 'Home Memo System') {{-- title --}}

@section('content') {{-- content section --}}<!-- Form Tambah Divisi -->
 <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <h3 class="text-lg font-bold mb-4">Tambah Divisi Baru</h3>
                <form action="{{ route('divisions.store') }}" method="POST">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-[2]">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Divisi</label>
                            <input type="text" name="name" placeholder="Contoh: Information Technology" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500" required>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Inisial</label>
                            <input type="text" name="initial" placeholder="Contoh: IT" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500" required>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                                Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </div>
@endsection