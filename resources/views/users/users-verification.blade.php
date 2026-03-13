@extends('layouts.app')

@section('title', 'Verifikasi Pendaftar Baru')

@section('content')
<div class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        
        <div class="bg-[#800000] p-8 rounded-3xl text-white shadow-xl mb-8 flex justify-between items-center border border-white/10">
            <div>
                <h2 class="text-3xl font-black italic uppercase tracking-tight text-white">
                    Antrean <span class="text-red-200">Verifikasi</span>
                </h2>
                <p class="text-white/80 mt-1 font-medium opacity-90 italic">
                    Daftar pengguna baru yang menunggu penetapan Role & Divisi.
                </p>
            </div>
            <div class="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner text-center">
                <p class="text-[10px] font-black uppercase tracking-widest text-white/70 mb-1">Total Menunggu</p>
                <p class="text-3xl font-black text-white">{{ $pendingUsers->count() }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-2xl flex items-center shadow-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 mr-3"></i>
                <span class="text-sm font-bold text-emerald-800 uppercase italic">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                @if($pendingUsers->count() > 0)
                    <table class="w-full text-left border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-gray-400 uppercase text-[10px] font-black tracking-[0.2em]">
                                <th class="px-6 py-4">Informasi Pendaftar</th>
                                <th class="px-6 py-4">Kontak</th>
                                <th class="px-6 py-4 text-center">Cabang</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @foreach($pendingUsers as $user)
                            <tr class="hover:bg-amber-50/50 transition-all">
                                <td class="px-6 py-6 bg-white border-y border-l border-gray-50 rounded-l-3xl">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black mr-4 border-2 border-white shadow-sm">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-900 uppercase italic tracking-tight">{{ $user->name }}</p>
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[9px] font-black rounded uppercase tracking-tighter">Status: Pending</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 bg-white border-y border-gray-50">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700">{{ $user->email }}</span>
                                        <span class="text-xs text-gray-400 font-medium">{{ $user->phone_number }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 bg-white border-y border-gray-50 text-center">
                                    <span class="px-4 py-1.5 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-white shadow-sm">
                                        {{ $user->branch ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 bg-white border-y border-r border-gray-50 rounded-r-3xl text-right">
                                    <button 
                                        onclick="openVerifyModal('{{ $user->id }}', '{{ $user->name }}')"
                                        class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-black text-white text-[10px] font-black rounded-xl transition-all shadow-lg shadow-amber-200 uppercase italic tracking-widest">
                                        <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i> Verifikasi
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="py-20 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="user-check" class="w-10 h-10 text-gray-200"></i>
                        </div>
                        <p class="text-gray-400 font-bold italic uppercase tracking-widest">Tidak ada antrean verifikasi saat ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="verifyModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
            <div class="bg-[#800000] p-6 text-white">
                <h3 class="text-xl font-black italic uppercase tracking-tight" id="modalUserName">Verifikasi User</h3>
                <p class="text-white/70 text-xs font-medium uppercase italic">Tetapkan peran dan divisi kerja</p>
            </div>

            <form id="verifyForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Pilih Role</label>
                        <select name="role" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all outline-none">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Pilih Divisi</label>
                        <select name="division" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all outline-none">
                            <option value="">-- Pilih Divisi --</option>
                            <option value="IT">Information Technology</option>
                            <option value="HRD">Human Resources</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Finance">Finance</option>
                        </select>
                    </div>
                </div>

                <div class="p-8 pt-0 flex gap-3">
                    <button type="button" onclick="closeVerifyModal()" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 text-[10px] font-black rounded-xl transition-all uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-amber-600 hover:bg-black text-white text-[10px] font-black rounded-xl transition-all shadow-lg shadow-amber-200 uppercase tracking-widest">
                        Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openVerifyModal(userId, userName) {
        const modal = document.getElementById('verifyModal');
        const form = document.getElementById('verifyForm');
        const nameDisplay = document.getElementById('modalUserName');

        // Set action URL (Sesuaikan dengan nama route update Anda)
        form.action = `/users/${userId}/verify`; 
        
        // Set nama di header modal
        nameDisplay.innerText = `Verifikasi: ${userName}`;

        // Tampilkan modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Lock scroll
    }

    function closeVerifyModal() {
        const modal = document.getElementById('verifyModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Unlock scroll
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('verifyModal');
        if (event.target == modal.querySelector('.fixed.inset-0.bg-black\\/50')) {
            closeVerifyModal();
        }
    }
</script>
@endsection