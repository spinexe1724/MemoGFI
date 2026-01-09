@extends('layouts.app') {{--  layout --}}

@section('title', 'Home Memo System') {{-- title --}}

@section('content') {{-- content section --}}
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-xl shadow-lg border">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Create New Account</h3>
                
                <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-semibold mb-1">Full Name</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Email Address</label>
                        <input type="email" name="email" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    </div>
                   <div>
        <label class="block font-semibold mb-1">Division</label>
        <select name="division" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" >
        <option value="">-- Pilih Divisi --</option>
        @foreach($divisions as $div)
            <option value="{{ $div->name }}" {{ old('division') == $div->name ? 'selected' : '' }}>
                {{ $div->name }}
            </option>
        @endforeach
        </select>
    </div>
                    <div>
                        <label class="block font-semibold mb-1">Account Role</label>
                       <select name="role" id="role_select" onchange="handleRoleChange()" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="gm" {{ old('role') == 'gm' ? 'selected' : '' }}>General Manager (GM)</option>
                                <option value="direksi" {{ old('role') == 'direksi' ? 'selected' : '' }}>Direksi</option>
                            </select>
                    </div>
                    <div class="mt-4">
    <label class="block font-medium text-sm text-gray-700">Tingkat Akses (Level)</label>
      <select name="level" id="level_select" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="2" {{ old('level') == '2' ? 'selected' : '' }}>Level 2 (Akses Divisi)</option>
                                    <option value="3" {{ old('level') == '3' ? 'selected' : '' }}>Level 3 (Akses Global)</option>
                                </select>
      <p id="supervisor_note" class="hidden text-[11px] text-blue-600 mt-1 italic font-medium">
                                * Supervisor otomatis dikunci ke Level 2.
                            </p>
</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold mb-1">Password</label>
                            <input type="password" name="password" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end space-x-2">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 text-gray-500">Cancel</a>
                        <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold shadow-lg">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   <script>
        /**
         * Mengatur perubahan tampilan dan nilai Level berdasarkan Role yang dipilih.
         */
        function handleRoleChange() {
            const roleSelect = document.getElementById('role_select');
            const levelSelect = document.getElementById('level_select');
            const note = document.getElementById('supervisor_note');
            const levelContainer = document.getElementById('level_container');

            if (!roleSelect || !levelSelect) return;

            const selectedRole = roleSelect.value;

            if (selectedRole === 'supervisor') {
                // Set ke Level 2 dan kunci input
                levelSelect.value = "2";
                levelSelect.disabled = true;
                levelSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
                
                if (note) note.classList.remove('hidden');

                // Tambahkan hidden input agar data 'level' tetap terkirim ke server 
                // karena input yang disabled tidak akan dikirim oleh form HTML.
                if (!document.getElementById('hidden_level_input')) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'level';
                    hidden.value = '2';
                    hidden.id = 'hidden_level_input';
                    levelContainer.appendChild(hidden);
                }
            } else {
                // Kembalikan ke keadaan normal
                levelSelect.disabled = false;
                levelSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
                
                if (note) note.classList.add('hidden');
                
                // Hapus hidden input jika ada
                const hidden = document.getElementById('hidden_level_input');
                if (hidden) {
                    hidden.remove();
                }
            }
        }

        // Gunakan DOMContentLoaded agar skrip berjalan segera setelah struktur HTML siap.
        document.addEventListener('DOMContentLoaded', function() {
            handleRoleChange();
        });
    </script>

@endsection
