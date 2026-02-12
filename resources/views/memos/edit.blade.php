@extends('layouts.app')

@section('title', 'Edit Memo - ' . $memo->subject)

@section('content')
<div class="max-w-8xl mx-auto">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        
        <!-- Header Panel -->
        <div class="bg-red-800 p-6 flex justify-between items-center text-white">
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Memo: {{ $memo->subject }}</h2>
                <p class="text-red-100 text-sm opacity-80">Perbarui informasi memo, lampiran, dan ajukan kembali untuk proses persetujuan.</p>
            </div>
            <i data-lucide="edit-3" class="text-white opacity-20 w-12 h-12"></i>
        </div>

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

        <form action="{{ route('memos.update', $memo->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Baris 1: No Referensi & Masa Berlaku -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Nomor Referensi</label>
                    <input type="text" name="reference_no" value="{{ $memo->reference_no }}" readonly 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 font-mono outline-none cursor-not-allowed" required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Akhir Berlaku Memo</label>
                    <input type="date" name="valid_until" 
                           value="{{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('Y-m-d') : '' }}" 
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Perihal / Subjek</label>
                    <input type="text" name="subject" value="{{ old('subject', $memo->subject) }}" placeholder="Contoh: Kegiatan Operasional..." 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                </div>

                <!-- Baris 2: Kepada & Dari -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Kepada</label>
                    <input type="text" name="recipient" value="{{ old('recipient', $memo->recipient) }}" placeholder="Penerima memo" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Dari (Divisi/Cabang)</label>
                    <input type="text" name="sender" value="{{ $memo->sender }}" readonly 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 outline-none cursor-not-allowed" required>
                </div>

                <!-- Penyetuju Tahap 1 -->
                @if(Auth::user()->role === 'admin')
                <div>
                    <label class="block text-sm font-semibold text-red-800 mb-2 italic tracking-tight">1. Mengetahui (BM)</label>
                    @php 
                        $currentApproverId = old('approver_id', $memo->approver_id);
                        $bm = $managers->firstWhere('id', $currentApproverId);
                    @endphp
                    <input type="text" value="{{ $bm ? $bm->name . ' (BM - ' . $bm->branch . ')' : 'BM Tidak Terdeteksi' }}" 
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 font-bold outline-none cursor-not-allowed" readonly>
                    
                    <input type="hidden" name="approver_id" value="{{ $currentApproverId }}">
                </div>
                @elseif(Auth::user()->role === 'supervisor')
                <div>
                    <label class="block text-sm font-semibold text-red-800 mb-2 italic tracking-tight">1. Mengetahui (Manager)</label>
                    <select name="approver_id" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 p-2.5" required>
                        <option value="">-- Pilih Manager Penyetuju --</option>
                        @foreach($managers->where('role', 'manager') as $manager)
                            <option value="{{ $manager->id }}" {{ old('approver_id', $memo->approver_id) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }} - ({{ $manager->division }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Persetujuan Lanjutan (Flexible) -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-bold text-red-800 mb-3 uppercase tracking-widest flex items-center">
                        <i data-lucide="shield-check" class="w-5 h-5 mr-2"></i> Persetujuan Lanjutan (Flexible)
                    </label>
                    <div class="bg-red-50/50 border border-red-100 rounded-2xl p-6 space-y-6">
                        @php
                            $savedTargets = $memo->target_approvers;
                            if (is_string($savedTargets)) {
                                $savedTargets = json_decode($savedTargets, true) ?? [];
                            }
                            $currentTargets = (array) old('target_approvers', $savedTargets);
                        @endphp

                        @if(Auth::user()->role === 'admin')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase mb-2 tracking-tighter">2. Penyetuju Manager Divisi HO (Hanya 1)</label>
                                    <select name="target_approvers[]" id="manager_ho_select" class="w-full" required>
                                        <option value="">-- Pilih Manager Divisi --</option>
                                        @foreach($managers->where('role', 'manager') as $mHO)
                                            <option value="{{ $mHO->id }}" {{ in_array($mHO->id, $currentTargets) ? 'selected' : '' }}>
                                                {{ $mHO->name }} ({{ $mHO->division }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase mb-2 tracking-tighter">3. Penyetuju Direksi (Opsional)</label>
                                    <select name="target_approvers[]" id="direksi_select" class="w-full" multiple="multiple">
                                        <option value="all">-- Pilih Semua Direksi --</option>
                                        @foreach($flexibleApprovers->where('role', 'direksi') as $dir)
                                            <option value="{{ $dir->id }}" {{ in_array($dir->id, $currentTargets) ? 'selected' : '' }}>
                                                {{ $dir->name }}
                                            </option>
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
                                        <option value="{{ $fApprover->id }}" {{ in_array($fApprover->id, $currentTargets) ? 'selected' : '' }}>
                                            {{ $fApprover->name }} ({{ strtoupper($fApprover->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tembusan (CC) -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Tembusan (CC Divisi)</label>
                    @php
                        $selectedCC = old('cc_list', $memo->cc_list) ?? [];
                        if (is_string($selectedCC)) {
                            $decoded = json_decode($selectedCC, true);
                            $selectedCC = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : array_map('trim', explode(',', $selectedCC));
                        }
                        $selectedCC = array_filter((array)$selectedCC);
                    @endphp
                    <select name="cc_list[]" id="cc_select" class="w-full" multiple="multiple">
                        <option value="all">-- Pilih Semua Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->name }}" {{ in_array($div->name, $selectedCC) ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bagian Lampiran -->
                <div class="md:col-span-3">
                    <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6">
                        <label class="block text-sm font-bold text-blue-800 mb-3 uppercase tracking-widest flex items-center">
                            <i data-lucide="paperclip" class="w-5 h-5 mr-2"></i> Lampiran Dokumen
                        </label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <input type="file" name="attachments[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                            </div>

                            @if($memo->attachments && $memo->attachments->count() > 0)
                            <div class="space-y-2">
                                <p class="text-xs font-bold text-gray-500 uppercase">File Terlampir:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($memo->attachments as $file)
                                    <div class="flex items-center bg-white border border-gray-200 rounded-lg px-3 py-1.5 shadow-sm">
                                        <i data-lucide="file" class="w-3 h-3 mr-2 text-blue-500"></i>
                                        <span class="text-xs font-medium text-gray-700 truncate max-w-[150px]">{{ $file->file_name }}</span>
                                        
                                        <!-- FORM DELETE LAMPIRAN (Sangat Penting: Pastikan rutenya benar) -->
                                        <button type="button" onclick="deleteAttachment({{ $file->id }})" class="ml-2 text-gray-300 hover:text-red-500 transition-colors">
                                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- CKEditor -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 tracking-tight">Isi Pesan Memo</label>
                <textarea name="body_text" id="editor">{{ old('body_text', $memo->body_text) }}</textarea>
            </div>

            <!-- Aksi -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('memos.index') }}" class="text-gray-600 hover:text-gray-800 font-medium transition">Batal</a>
                    
                    @if(Auth::id() == $memo->user_id && ($memo->is_draft || $memo->is_rejected || (!$memo->is_final && $memo->approvals->count() <= 1)))
                        <button type="button" onclick="confirmDelete()" class="text-red-600 hover:text-red-800 font-bold px-4 py-2 rounded-xl transition flex items-center hover:bg-red-50">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            Hapus Memo
                        </button>
                    @endif
                </div>

                <div class="flex items-center space-x-4">
                    <button type="submit" name="action" value="draft" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl border border-gray-200 transition-all flex items-center">
                        <i data-lucide="archive" class="w-4 h-4 mr-2"></i> Simpan Draf
                    </button>
                    <button type="submit" name="action" value="publish" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-10 rounded-xl shadow-lg transition-all transform active:scale-95 flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan & Terbitkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- FORM TERSEMBUNYI (PENTING: Harus di luar <form> utama agar tidak bentrok) --}}
<form id="delete-attachment-form" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form> 

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single { border-color: #D1D5DB; border-radius: 0.5rem; min-height: 42px; }
    .select2-container--default .select2-selection--single { padding: 6px; }
    .ck-editor__editable { min-height: 400px; }
    .ck-content .image { display: inline-block; margin: 10px; }
    .ck-content .image.ck-widget_selected .ck-image-resizer { display: block !important; }
    .ck-image-resizer__handle { background: #007cba; border: 2px solid #fff; width: 10px; height: 10px; border-radius: 50%; }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    function confirmDelete() {
        Swal.fire({
            title: 'Hapus Memo?',
            text: "Data memo akan dihapus secara permanen dan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Permanen',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        });
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
            }
        }).catch(error => console.error(error));
    });
     function deleteAttachment(attachmentId) {
        Swal.fire({
            title: 'Hapus Lampiran?',
            text: "Hanya file ini yang akan dihapus, bukan memo Anda.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus File',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-attachment-form');
                // Set action URL secara dinamis ke rute penghapusan LAMPIRAN
                form.action = `/attachments/${attachmentId}`; 
                form.submit();
            }
        });
    }
</script>
@endsection