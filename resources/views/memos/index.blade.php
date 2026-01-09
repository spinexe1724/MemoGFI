@extends('layouts.app') {{--  layout --}}

@section('title', 'Home Memo System') {{-- title --}}

@section('content') {{-- content section --}}
<div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, ({{ strtoupper(Auth::user()->name) }})</h2>
</div>
<div class="p-">
<div class="bg-red-800 p-6 flex justify-between items-center" style="height:70px;">
            <div>
                <h1 class="text-2xl font-bold text-white">Daftar Memo Gratama </h1>
                <p class="text-blue-100 italic text-sm"></p>
                
            </div>
            <hr>
               
            </form>
        </div>
        <br>
        <div class="flex justify-between mb-6 items-center">
                @if(Auth::user()->role === 'staff')
                    <a href="{{ route('memos.create') }}" class="bg-green-600 hover:bg-blue-800 text-white px-4 py-2 rounded font-bold shadow-md transition-all">
                        + Buat Memo Baru
                    </a>
                @endif
            </div>
<div class="w-full overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-gray-50/50">
          <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-gray-600">Memo Aktif</th>
          <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-gray-600">Progress</th>
          <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-gray-600">Status</th>
          <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-gray-600 text-right">Lihat</th>
        </tr>
      </thead>
      
      <tbody class="divide-y divide-gray-50">
                @foreach($memos as $memo)
                        @php
                            $isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt($memo->valid_until) : false;
                        @endphp
        <tr class="group hover:bg-blue-50/30 transition-all duration-200">
          <td class="px-6 py-5">
            <div class="flex flex-col">
              <span class="font-bold text-gray-800 text-lg">{{ $memo->subject }}</span>
              <span class="text-xs text-gray-400">No: {{ $memo->reference_no }}</span>
              <div class="text-xs {{ $isExpired ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                    Berlaku s/d: {{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('d/m/Y') : 'Tanpa Batas' }}
            </div>
            </div>
          </td>
          <td class="px-6 py-5">
            <div class="w-full max-w-[100px]">
            <div class="flex items-center">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-bold mr-2">
                                            {{ $memo->approvals->count() }}/5
                                        </span>
                                        
                                        
                                    </div>
                                    
                                    <!-- Progress Bar Sederhana -->
                                    <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                        <div class="bg-blue-600 h-1 rounded-full" style="width: {{ ($memo->approvals->count() / 5) * 100 }}%"></div>
                                    </div>
            </div>
          </td>
          <td class="px-6 py-5">
                <!-- Label Status Dinamis -->
                                        @if($memo->is_rejected)
                                            <span class="px-2 py-0.5 bg-red-600 text-white text-[10px] font-bold rounded shadow-sm">DITOLAK</span>
                                        @elseif($isExpired)
                                            <span class="px-2 py-0.5 bg-gray-500 text-white text-[10px] font-bold rounded shadow-sm">EXPIRED</span>
                                        @elseif($memo->is_fully_approved)
                                            <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-bold rounded shadow-sm">FINAL</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-yellow-500 text-white text-[10px] font-bold rounded shadow-sm">PENDING</span>
                                        @endif
          </td>
          <td class="px-6 py-5 text-right">
          @if(Auth::id() == $memo->user_id && $memo->approvals->count() == 0 && !$memo->is_rejected)
                                        <a href="{{ route('memos.edit', $memo->id) }}" class="inline-flex items-center bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold shadow-sm transition-all transform active:scale-95">
                                            <i data-lucide="pencil" class="w-3 h-3 mr-1"></i> Edit
                                        </a>
                                    @endif
                                <!-- Aksi khusus GM -->
                                @if(in_array(Auth::user()->role, ['gm', 'direksi']) && !$memo->is_rejected && !$memo->is_fully_approved)
        @if(!$memo->approvals->contains('id', Auth::id()))
                                   <button type="button" onclick="handleApprove({{ $memo->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-[11px] font-bold transition-all">
    Approve
</button>
     
 
                    <form id="approve-form-{{ $memo->id }}" action="{{ route('memos.approve', $memo->id) }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="note" id="note-input-{{ $memo->id }}">
                    </form>

                                            <form action="{{ route('memos.reject', $memo->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak memo ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-[11px] font-bold transition-all">
                                            Reject
                                        </button>
                                    </form>
                                @endif
                                    @if(Auth::user()->role === 'gm')
        <a href="{{ route('memos.show', $memo->id) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-[11px] font-bold transition-all shadow-sm">
            Detail
        </a>
    @endif

                                    @endif
                                    
                                

                                <!-- Link View PDF -->
                                <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" 
                                   class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-[11px] font-bold border border-gray-300 transition-all">
                                    View PDF
                                </a>
                               
          </td>
        </tr>

        
        @endforeach
        @if($memos->isEmpty())
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500 italic">Belum ada memo yang dibuat.</td>
                        </tr>
                        @endif
   
      </tbody>
      
    </table>
  </div>
  
</div>   
<br>
{{$memos->links()}}
</div>


  
            <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();

        // Toggle Sidebar Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
        

  function toggleDropdown(menuId, arrowId) {
    const menu = document.getElementById(menuId);
    const arrow = document.getElementById(arrowId);
    
    // Toggle class hidden
    menu.classList.toggle('hidden');
    
    // Putar arrow icon
    arrow.classList.toggle('rotate-180');
  }
  function confirmApprove(memoId) {
    Swal.fire({
        title: 'Konfirmasi Persetujuan',
        text: "Apakah Anda yakin ingin menyetujui memo ini?",
        input: 'textarea',
        inputLabel: 'Catatan (Opsional)',
        inputPlaceholder: 'Tulis pesan atau catatan di sini...',
        inputAttributes: {
            'aria-label': 'Tulis catatan di sini'
        },
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal',
        preConfirm: (note) => {
            // Kita bisa melakukan validasi di sini jika diperlukan
            return note;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Jalankan fungsi pengiriman form
            submitApprovalForm(memoId, result.value);
        }
    });
}

  function handleApprove(memoId) {
        Swal.fire({
            title: 'Konfirmasi Persetujuan',
            text: "Tambahkan catatan jika diperlukan (opsional):",
            input: 'textarea',
            inputPlaceholder: 'Tulis catatan di sini...',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Approve!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('approve-form-' + memoId);
                const input = document.getElementById('note-input-' + memoId);
                
                if (form && input) {
                    // Set nilai note ke hidden input
                    input.value = result.value;
                    // Submit form secara manual
                    form.submit();
                } else {
                    console.error('Form atau Input tidak ditemukan untuk ID: ' + memoId);
                }
            }
        });
    }
    </script>
    
@endsection

       
   