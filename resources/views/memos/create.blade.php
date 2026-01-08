<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Memo Internal Baru</title>
    
    <!-- Menggunakan Vite untuk Asset Laravel -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS CDN sebagai fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Mengatur tinggi minimum editor agar nyaman digunakan */
        .ck-editor__editable {
            min-height: 300px;
        }
        /* Style untuk validasi error */
        .error-text {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen bg-gray-100">

  <div class="relative flex h-screen w-full max-w-[18rem] flex-col bg-white p-4 text-gray-700 shadow-xl">
    <div class="p-4 mb-2">
    <a href="" class="flex items-center">
         <img src="{{ asset('images/Capture.PNG')}}" class="" style="height:auto;"/>
         <span class="self-center text-lg text-heading font-semibold whitespace-nowrap"></span>
      </a>
    </div>
    
    <div class="garis-vertical"></div>
    <aside class="relative flex h-screen w-full max-w-[20rem] flex-col bg-white p-4 text-gray-700">
    <div class="p-4 mb-2">
      <h5 class="text-xl font-semibold text-blue-gray-900">Sidebar Menu</h5>
    </div>
    
    <nav class="flex flex-col gap-1 p-2 text-base font-normal">
      
      <div class="relative w-full">
        <button onclick="toggleDropdown('dashboard-menu', 'arrow-icon')" 
          class="flex items-center justify-between w-full p-3 transition-all rounded-lg hover:bg-blue-gray-50 hover:text-blue-gray-900">
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="currentColor" viewBox="0 0 24 24"><path d="M2.25 2.25a.75.75 0 000 1.5H3v10.5a3 3 0 003 3h1.21l-1.172 3.513a.75.75 0 001.424.474l.329-.987h8.418l.33.987a.75.75 0 001.422-.474l-1.17-3.513H18a3 3 0 003-3V3.75h.75a.75.75 0 000-1.5H2.25z"/></svg>
            <span>Dashboard</span>
          </div>
          <svg id="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 transition-transform duration-200">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
          </svg>
        </button>
        
        <div id="dashboard-menu" class="hidden overflow-hidden pl-9 mt-1 transition-all duration-300">
          <a href="#" class="block p-2 text-sm rounded-lg hover:bg-gray-100">Analytics</a>
          <a href="#" class="block p-2 text-sm rounded-lg hover:bg-gray-100">Reporting</a>
          <a href="#" class="block p-2 text-sm rounded-lg hover:bg-gray-100">Projects</a>
        </div>
      </div>

      <div role="button" class="flex items-center w-full p-3 rounded-lg hover:bg-blue-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6.912 3a3 3 0 00-2.868 2.118l-2.411 7.838a3 3 0 00-.133.882V18a3 3 0 003 3h15a3 3 0 003-3v-4.162c0-.299-.045-.596-.133-.882l-2.412-7.838A3 3 0 0017.088 3H6.912z"/></svg>
        Inbox
      </div>

      
      <div role="button" action="submit" class="flex items-center w-full p-3 rounded-lg hover:bg-red-50 text-red-600 mt-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
        Log Out
      </div>
    </nav>
  </aside>
    <nav class="flex min-w-[240px] flex-col gap-1 p-2 font-sans text-base font-normal text-blue-gray-700">
      </nav>
  </div>

  <main class="flex-1 p-8 overflow-y-auto">
    <header class="mb-8">
      <h1 class="text-3xl font-bold text-blue-gray-900">Dashboard Overview</h1>
      <p class="text-gray-600">Selamat datang kembali, admin.</p>
    </header>

    <div class="max-w-8xl mx-auto bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <!-- Header Panel -->
        <div class="bg-blue-800 p-6">
            <h2 class="text-2xl font-bold text-white">Memo Internal System Gratama</h2>
            <p class="text-blue-100 text-sm">Silakan isi detail memo di bawah ini untuk menghasilkan PDF otomatis.</p>
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
                    <input type="text" name="reference_no" value="{{ $autoRef }}" readonly 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Akhir Berlaku Memo</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until') }}" min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                    <p class="text-xs text-gray-400 mt-1 italic">* Status akan otomatis TIDAK AKTIF setelah tanggal ini.</p>
                </div>

                <!-- Baris 2: Kepada & Dari -->
                <div>
                    <label class="p block text-sm font-semibold text-gray-700 mb-2">Kepada</label>
                    <input type="text" name="recipient" value="{{ old('recipient') }}" placeholder="Contoh: Seluruh Karyawan" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari</label>
                    <input type="text" name="sender" value="{{ old('sender', Auth::user()->division) }}" readonly 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
                </div>

                <!-- Baris 3: Cc -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tembusan</label>
                    <input type="text" name="cc_list" value="{{ old('cc_list') }}" placeholder="Contoh: Finance, HRD, GA" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                </div>
            </div>

            <!-- Perihal -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Perihal</label>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Kegiatan Operasional HO di Hari Sabtu" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" required>
            </div>
            
            <!-- Editor -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan Memo</label>
                <textarea name="body_text" id="editor">{{ old('body_text') }}</textarea>
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
</div>
</body>
<body class="bg-gray-50 min-h-screen py-10 px-4">

 
</body>
</html>