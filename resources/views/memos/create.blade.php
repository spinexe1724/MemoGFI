@extends('layouts.app')

@section('title', 'Buat Memo Baru')

@section('content')
<div class="max-w-8xl mx-auto">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="bg-red-800 p-6 flex justify-between items-center text-white">
            <div>
                <h2 class="text-2xl font-bold text-white">Tambah Memo Baru</h2>
                <p class="text-red-100 text-sm opacity-80">Silakan isi formulir di bawah ini untuk menerbitkan memo internal.</p>
            </div>
            <i data-lucide="file-plus" class="text-white opacity-20 w-12 h-12"></i>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 m-8 mb-0">
                <p class="text-red-700 font-bold">Terjadi kesalahan input:</p>
                <ul class="list-disc ml-5 text-red-600 text-sm">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('memos.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Nomor Referensi</label>
                    <input type="text" name="reference_no" value="{{ old('reference_no', $autoRef) }}" readonly class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 font-mono outline-none cursor-not-allowed" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Akhir Berlaku Memo</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until') }}" min="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Perihal / Subjek</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Kegiatan Operasional..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Kepada</label>
                    <input type="text" name="recipient" placeholder="Penerima memo" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Dari (Divisi/Cabang)</label>
                    <input type="text" name="sender" value="{{ Auth::user()->division }}" readonly class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 outline-none cursor-not-allowed" required>
                </div>
                @if(Auth::user()->role === 'admin')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 italic text-red-700 tracking-tight">1. Mengetahui (BM)</label>
                        @php $currentApproverId = old('approver_id', $memo->approver_id); $selectedBM = $managers->firstWhere('id', $currentApproverId); @endphp
                        <input type="text" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 outline-none cursor-not-allowed font-bold" value="{{ $selectedBM ? $selectedBM->name . ' (BM - ' . $selectedBM->branch . ')' : 'BM Tidak Ditemukan' }}" readonly>
                        <input type="hidden" name="approver_id" value="{{ $currentApproverId }}">
                    </div>
                @elseif(Auth::user()->role === 'supervisor')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 italic text-red-700 tracking-tight">1. Mengetahui (Manager)</label>
                        <select name="approver_id" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 p-2.5" required>
                            <option value="">-- Pilih Manager Penyetuju --</option>
                            @foreach($managers->where('role', 'manager') as $manager)
                                <option value="{{ $manager->id }}" {{ old('approver_id', $memo->approver_id) == $manager->id ? 'selected' : '' }}>{{ $manager->name }} - ({{ $manager->division }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="md:col-span-3">
                    <label class="block text-sm font-bold text-red-800 mb-3 uppercase tracking-widest flex items-center">
                        <i data-lucide="shield-check" class="w-5 h-5 mr-2"></i> Persetujuan Lanjutan (Flexible)
                    </label>
                    <div class="bg-red-50/50 border border-red-100 rounded-2xl p-6 space-y-6">
                        @if(Auth::user()->role === 'admin')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase mb-2 tracking-tighter">2. Penyetuju Manager Divisi HO (Hanya 1)</label>
                                    <select name="target_approvers[]" id="manager_ho_select" class="w-full" required>
                                        <option value="">-- Pilih Manager Divisi --</option>
                                        @foreach($managers->where('role', 'manager') as $mHO)
                                            <option value="{{ $mHO->id }}" {{ (is_array(old('target_approvers')) && in_array($mHO->id, old('target_approvers'))) ? 'selected' : '' }}>{{ $mHO->name }} ({{ $mHO->division }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase mb-2 tracking-tighter">3. Penyetuju Direksi (Opsional)</label>
                                    <select name="target_approvers[]" id="direksi_select" class="w-full" multiple="multiple">
                                        <option value="all">-- Pilih Semua Direksi --</option>
                                        @foreach($flexibleApprovers->where('role', 'direksi') as $dir)
                                            <option value="{{ $dir->id }}" {{ (is_array(old('target_approvers')) && in_array($dir->id, old('target_approvers'))) ? 'selected' : '' }}>{{ $dir->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase mb-2 tracking-tighter">Pilih Penyetuju Lanjutan (GM / Direksi)</label>
                                <select name="target_approvers[]" id="approver_select" class="w-full" multiple="multiple">
                                    <option value="all">-- Pilih Semua --</option>
                                    @foreach($flexibleApprovers->whereIn('role', ['direksi', 'gm']) as $fApprover)
                                        <option value="{{ $fApprover->id }}" {{ (is_array(old('target_approvers')) && in_array($fApprover->id, old('target_approvers'))) ? 'selected' : '' }}>{{ $fApprover->name }} ({{ strtoupper($fApprover->role) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Tembusan (CC Divisi)</label>
                    <select name="cc_list[]" id="cc_select" class="w-full" multiple="multiple">
                        <option value="all">-- Pilih Semua Divisi --</option>
                        @foreach($divisions as $div) <option value="{{ $div->name }}" {{ (is_array(old('cc_list')) && in_array($div->name, old('cc_list'))) ? 'selected' : '' }}>{{ $div->name }}</option> @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Isi Pesan Memo</label>
                <textarea name="body_text" id="editor">{{ old('body_text') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('memos.index') }}" class="text-gray-600 hover:text-gray-800 font-medium transition">Batal</a>
                <button type="submit" name="action" value="draft" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl border border-gray-200">Simpan Draf</button>
                <button type="submit" name="action" value="publish" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-10 rounded-xl shadow-lg transition-all transform active:scale-95 flex items-center"><i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan & Terbitkan</button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single { border-color: #D1D5DB; border-radius: 0.5rem; min-height: 42px; }
    .select2-container--default .select2-selection--single { padding: 6px; }
    .ck-editor__editable { min-height: 400px; }
    
    /* Perbaikan visual agar gambar yang di-resize terlihat jelas handle-nya */
    .ck-content .image {
        display: inline-block;
        margin: 10px;
    }
    /* DIUBAH: Styling untuk handle resize gambar agar lebih jelas */
    .ck-content .image.ck-widget_selected .ck-image-resizer {
        display: block !important;  /* Pastikan handle terlihat saat gambar dipilih */
    }
    .ck-image-resizer__handle {
        background: #007cba;  /* Warna handle (biru) */
        border: 2px solid #fff;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .ck-image-resizer__handle:hover {
        background: #005a87;  /* Warna saat hover */
    }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    class MyUploadAdapter {
        constructor(loader) { this.loader = loader; }
        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                const data = new FormData();
                data.append('upload', file);
                $.ajax({
                    url: "{{ route('memos.upload') }}",
                    type: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    success: response => resolve({ default: response.url }),
                    error: (xhr) => reject(xhr.responseJSON?.error?.message || 'Gagal mengunggah gambar.')
                });
            }));
        }
        abort() {}
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
        $('#cc_select').select2({ placeholder: " Klik untuk memilih divisi...", allowClear: true, width: '100%' });
        $('#approver_select').select2({ placeholder: " Pilih GM/Direksi...", allowClear: true, width: '100%' });
        $('#manager_ho_select').select2({ placeholder: " Pilih 1 Manager Divisi (HO)...", allowClear: true, width: '100%' });
        const $direksiSelect = $('#direksi_select').select2({ placeholder: " Pilih Direksi...", allowClear: true, width: '100%' });

        $direksiSelect.on('select2:select', function (e) {
            if (e.params.data.id === 'all') {
                const allIds = $('#direksi_select option').map(function() { return ($(this).val() !== 'all' && $(this).val() !== '') ? $(this).val() : null; }).get();
                $direksiSelect.val(allIds).trigger('change');
            }
        });

        ClassicEditor.create(document.querySelector('#editor'), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            toolbar: { 
                items: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'imageUpload', 'insertTable', 'blockQuote', 'undo', 'redo'] 
            },
            // DIUBAH: KONFIGURASI EDIT GAMBAR (ditambahkan resizeImage dan opsi lebih banyak)
            image: {
                toolbar: [
                    'imageStyle:inline', 
                    'imageStyle:block', 
                    'imageStyle:side', 
                    '|', 
                    'toggleImageCaption', 
                    'imageTextAlternative',
                    '|',  // DIUBAH: Tambahkan separator
                    'resizeImage'  // DIUBAH: TAMBAHKAN INI: Mengaktifkan resize via toolbar dan handle visual
                ],
                // Mengaktifkan fitur resize via toolbar atau handle (jika build mendukung)
                resizeUnit: '%',
                resizeOptions: [
                    { name: 'resizeImage:original', value: null, label: 'Original' },
                    { name: 'resizeImage:25', value: '25', label: '25%' },
                    { name: 'resizeImage:50', value: '50', label: '50%' },
                    { name: 'resizeImage:75', value: '75', label: '75%' },
                    { name: 'resizeImage:100', value: '100', label: '100%' },  // DIUBAH: Tambahkan opsi 100%
                    { name: 'resizeImage:150', value: '150', label: '150%' },  // DIUBAH: Tambahkan opsi lebih besar
                    { name: 'resizeImage:200', value: '200', label: '200%' }   // DIUBAH: Tambahkan opsi lebih besar
                ]
            }
        }).catch(error => console.error(error));
    });
</script>
@endsection