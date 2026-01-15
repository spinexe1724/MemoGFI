@extends('layouts.app')

@section('title', 'Master Divisi - Memo System')

@section('content')
{{-- justify-start membuat konten naik ke atas, pt-12 memberikan jarak dari top --}}
<div class="min-h-screen bg-[#F8FAFC] flex flex-col items-center justify-start pt-12 md:pt-20 p-6 overflow-hidden">
    
    <div class="text-center mb-8">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">
            Tambah <span class="text-red-700">Divisi Baru</span>
        </h1>
    </div>

    <div class="w-full max-w-2xl bg-white shadow-[0_20px_50px_rgba(0,0,0,0.04)] rounded-[2.5rem] border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12">
            
            <form action="{{ route('divisions.store') }}" method="POST" id="divForm" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Divisi</label>
                        <div class="relative group">
                            <i data-lucide="building" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-red-600 transition-colors"></i>
                            <input type="text" name="name" placeholder="Information Technology" 
                                   class="w-full pl-11 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-red-700/5 focus:border-red-700 outline-none transition-all font-medium text-slate-700" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Inisial</label>
                        <input type="text" name="initial" placeholder="IT" 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-red-700/5 focus:border-red-700 outline-none transition-all font-bold text-slate-800 uppercase text-center" required>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-6 pt-2">
                    <button type="submit" id="btnSubmit" class="w-full md:w-72 bg-slate-900 hover:bg-red-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-slate-200 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center tracking-widest uppercase text-xs">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Simpan Departemen
</button>
                </div>
            </form>
        </div>
    </div>

    <a href="{{ route('divisions.index') }}" class="mt-8 text-sm font-bold text-slate-400 hover:text-red-700 transition-colors flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Daftar Divisi
    </a>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        
        document.getElementById('divForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin mr-2 h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span> MEMPROSES...';
        });
    });
</script>
@endsection