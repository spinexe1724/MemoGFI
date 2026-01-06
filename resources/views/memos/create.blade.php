
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Memo Internal Baru</title>
    <!-- Tailwind CSS Play CDN: URL Bersih -->
    <script src="[https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)"></script>
    <style>
        /* Mengatur tinggi minimum editor agar nyaman digunakan */
        .ck-editor__editable {
            min-height: 300px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen py-10 px-4">

    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <!-- Header Panel -->
        <div class="bg-blue-800 p-6">
            <h2 class="text-2xl font-bold text-white">Memo Internal System</h2>
            <p class="text-blue-100 text-sm">Silakan isi detail memo di bawah ini untuk menghasilkan PDF otomatis.</p>
        </div>

        <form action="{{ route('memos.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Baris 1 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Referensi</label>
                    <input type="text" name="reference_no" placeholder="Contoh: 783/DIR/GFI/OL/11/2025" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kepada</label>
                    <input type="text" name="recipient" placeholder="Contoh: Seluruh Karyawan" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>

                <!-- Baris 2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari</label>
                    <input type="text" name="sender" placeholder="Contoh: Direksi" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cc (Pisahkan dengan koma)</label>
                    <input type="text" name="cc_list" placeholder="Contoh: Finance, HRD, GA" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                </div>
            </div>

            <!-- Perihal -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Perihal / Subjek</label>
                <input type="text" name="subject" placeholder="Contoh: Kegiatan Operasional HO di Hari Sabtu" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
            </div>
            
            <!-- Editor -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan Memo</label>
                <textarea name="body_text" id="editor"></textarea>
            </div>

            <!-- Aksi -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('memos.index') }}" class="text-gray-600 hover:text-gray-800 font-medium transition">Batal</a>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-blue-200 transition-all transform active:scale-95">
                    Simpan & Generate
                </button>
            </div>
        </form>
    </div>

    <!-- CKEditor Script: URL Bersih -->
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
</body>
</html>