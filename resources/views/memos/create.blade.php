@extends('layouts.app')

@section('title', 'Buat Memo Baru')

@section('content')
<div class="max-w-12xl mx-auto">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <!-- Header Panel -->
        <div class="bg-red-800 p-6 flex justify-between items-center text-white">
            <div>
                <h2 class="text-2xl font-bold">Tambah Memo Baru</h2>
                <p class="text-red-100 text-sm opacity-80">Silakan isi formulir di bawah ini untuk menerbitkan memo internal.</p>
            </div>
            <i data-lucide="file-plus" class="text-white opacity-20 w-12 h-12"></i>
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
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Baris 1: No Referensi & Masa Berlaku -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Referensi</label>
                    <input type="text" name="reference_no" value="{{ $autoRef }}" readonly 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 font-mono outline-none cursor-not-allowed" required>
                    <p class="text-[10px] text-gray-400 mt-1 italic">* Dibuat otomatis berdasarkan divisi Anda.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Akhir Berlaku Memo</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until') }}" min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                    <p class="text-[10px] text-gray-400 mt-1 italic">* Status otomatis TIDAK AKTIF setelah tanggal ini.</p>
                </div>

                <!-- Perihal (Dipindah ke baris atas agar row tetap penuh) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Perihal / Subjek</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Kegiatan Operasional HO..." 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                </div>

                <!-- Baris 2: Kepada & Dari -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kepada</label>
                    <input type="text" name="recipient" value="{{ old('recipient') }}" placeholder="Contoh: Seluruh Karyawan" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari (Divisi)</label>
                    <input type="text" name="sender" value="{{ old('sender', Auth::user()->division) }}" readonly  
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 outline-none" required>
                </div>

                <!-- Field Menyetujui (Khusus Supervisor) -->
                @if(Auth::user()->role === 'supervisor')
                <div id="approver_field">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 italic text-red-700">Menyetujui (Manager Divisi)</label>
                    <select name="approver_id" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 p-2.5" required>
                        <option value="">-- Pilih Manager Penyetuju --</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}">
                                {{ $manager->name }} - ({{ $manager->division }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">* Wajib diisi oleh Supervisor untuk diteruskan ke Manager.</p>
                </div>
                @endif

                <!-- Baris 3: Tembusan (CC) -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tembusan (CC Divisi)</label>
                    <select name="cc_list[]" id="cc_select" class="w-full" multiple="multiple">
                        <option value="all">-- Pilih Semua Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->name }}" {{ (is_array(old('cc_list')) && in_array($div->name, old('cc_list'))) ? 'selected' : '' }}>
                                {{ $div->name }} ({{ $div->initial }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1 italic">* Pilih divisi dari daftar. Anda bisa memilih lebih dari satu.</p>
                </div>
            </div>

            <!-- Editor -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan Memo</label>
                <textarea name="body_text" id="editor">{{ old('body_text') }}</textarea>
            </div>

            <!-- Aksi -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('memos.index') }}" class="text-gray-600 hover:text-gray-800 font-medium transition">Batal</a>
                
                <button type="submit" name="action" value="draft" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl transition-all flex items-center border border-gray-200">
                    <i data-lucide="archive" class="w-4 h-4 mr-2"></i>
                    Simpan Draf
                </button>

                <button type="submit" name="action" value="publish" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-blue-200 transition-all transform active:scale-95 flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan & Terbitkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Styles & Scripts Area -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border-color: #D1D5DB;
        border-radius: 0.5rem;
        padding: 4px;
        min-height: 42px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3B82F6;
    }
    .ck-editor__editable {
        min-height: 300px;
    }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();

        // Inisialisasi Select2
        const $ccSelect = $('#cc_select').select2({
            placeholder: " Klik untuk memilih divisi...",
            allowClear: true,
            width: '100%'
        });

        // Logika "Pilih Semua"
        $ccSelect.on('select2:select', function (e) {
            if (e.params.data.id === 'all') {
                const allDivisions = $('#cc_select option').map(function() {
                    return ($(this).val() !== 'all' && $(this).val() !== '') ? $(this).val() : null;
                }).get();
                $ccSelect.val(allDivisions).trigger('change');
            }
        });

        // Inisialisasi CKEditor 5
        ClassicEditor
            .create(document.querySelector('#editor'), {
                ckfinder: {
                },
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'imageUpload', 'undo', 'redo' ]
            })
            .catch(error => console.error(error));
    });
</script>
@endsection