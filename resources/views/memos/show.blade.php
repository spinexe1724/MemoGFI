@extends('layouts.app') {{--  layout --}}

@section('title', 'Home Memo System') {{-- title --}}

@section('content') {{-- content section --}}
     <div class="max-w-8xl mx-auto bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <!-- Header Panel -->
        <div class="bg-red-800 p-6">
            <h2 class="text-2xl font-bold text-white">Detail Memo</h2>
         </div>

        <!-- Tampilkan Pesan Error Global jika ada -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 m-8 mb-0">
                <p class="text-red-700 font-bold">Terjadi kesalahan input:</p>
                <ul class="list-disc ml-5 text-red-600 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('memos.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Baris 1: No Referensi & Masa Berlaku -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Referensi</label>
                    <input type="text" name="reference_no" value="{{ $memo->reference_no }}" disabled placeholder="Contoh: 783/DIR/GFI/OL/11/2025" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Akhir Berlaku Memo</label>
                    <input type="date" 
           name="valid_until" 
           value="{{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('Y-m-d') : '' }}" 
           min="{{ date('Y-m-d') }}" disabled
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                    <p class="text-xs text-gray-400 mt-1 italic">* Status akan otomatis TIDAK AKTIF setelah tanggal ini.</p>
                </div>

                <!-- Baris 2: Kepada & Dari -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kepada</label>
                    <input type="text" name="recipient" value="{{ $memo->recipient }}" disabled placeholder="Contoh: Seluruh Karyawan" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari</label>
                    <input type="text" name="sender" value="{{ $memo->sender }}" disabled placeholder="Contoh: Direksi" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>

                <!-- Baris 3: Cc -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tembusan</label>
                    <input type="text" name="cc_list" value="{{ $memo->cc_list ?? '-' }}" disabled placeholder="Contoh: Finance, HRD, GA" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                </div>
            </div>

            <!-- Perihal -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Perihal</label>
                <input type="text" name="subject" value="{{ $memo->subject }}" disabled placeholder="Contoh: Kegiatan Operasional HO di Hari Sabtu" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
            </div>
            
            <!-- Editor -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan Memo</label>
                <div class="w-full p-6 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 prose prose-blue max-w-none italic">
                    {!! $memo->body_text !!}
                </div>
            </div>

            <!-- Aksi -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('memos.index') }}" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-blue-200 transition-all transform active:scale-95">Kembali</a>
                
            </div>
        </form>
    </div>

    <!-- CKEditor Script -->
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
                })
                .catch(error => {
                    console.error('CKEditor Error:', error);
                });
        });
    </script>
  </main>
  <script>
  function toggleDropdown(menuId, arrowId) {
    const menu = document.getElementById(menuId);
    const arrow = document.getElementById(arrowId);
    
    // Toggle class hidden
    menu.classList.toggle('hidden');
    
    // Putar arrow icon
    arrow.classList.toggle('rotate-180');
  }
</script>
@endsection