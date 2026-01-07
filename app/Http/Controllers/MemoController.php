<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MemoController extends Controller
{
    /**
     * Menampilkan daftar memo berdasarkan role user.
     */
    public function index()
    {
        // Jika GM, tampilkan semua memo untuk diproses. Jika Staff, hanya memo miliknya.
        $memos = (Auth::user()->role === 'gm') 
            ? Memo::with('approvals')->latest()->get() 
            : Auth::user()->memos()->with('approvals')->latest()->get();
            
        return view('memos.index', compact('memos'));
    }

    /**
     * Menampilkan form pembuatan memo baru.
     */
    public function create()
    {
        return view('memos.create');
    }


    /**
     * Menyimpan memo baru ke database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'reference_no' => 'required|string',
            'recipient'    => 'required|string',
            'sender'       => 'required|string',
            'subject'      => 'required|string',
            'body_text'    => 'required|string',
            'valid_until'  => 'required|date|after_or_equal:today',
            'cc_list'      => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();
        
        Memo::create($data);
        
        return redirect()->route('memos.index')->with('success', 'Memo berhasil dibuat dan siap diproses.');
    }
public function show($id) {
    // Proteksi: Hanya GM yang bisa melihat detail internal ini
    if (Auth::user()->role !== 'gm') abort(403, 'Unauthorized action.');

    $memo = Memo::with(['approvals', 'user'])->findOrFail($id);
    return view('memos.show', compact('memo'));
}
    /**
     * Menangani persetujuan dari GM.
     */
    public function approve(Request $request, $id) {
    if (Auth::user()->role !== 'gm') abort(403);
    $memo = Memo::findOrFail($id);
    
    if ($memo->is_rejected) return back()->with('error', 'Memo sudah ditolak.');

    if (!$memo->approvals()->where('user_id', Auth::id())->exists()) {
        // Simpan approval beserta note (nullable)
        $memo->approvals()->attach(Auth::id(), [
            'note' => $request->input('note')
        ]);
    }

    if ($memo->approvals()->count() >= 5) {
        $memo->update(['is_fully_approved' => true]);
    }

    return back()->with('success', 'Persetujuan berhasil.');
    }

    /**
     * Menangani penolakan memo oleh GM.
     */
    public function reject($id)
    {
        if (Auth::user()->role !== 'gm') {
            abort(403, 'Hanya Direktur/GM yang dapat menolak memo.');
        }

        $memo = Memo::findOrFail($id);

        // Validasi: Jika GM sudah approve, dia tidak boleh melakukan reject
        if ($memo->approvals()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'Anda tidak dapat menolak memo yang sudah Anda setujui.');
        }
        
        // Tandai sebagai ditolak, reset status persetujuan penuh, dan hapus approval yang sudah ada
        $memo->update([
            'is_rejected' => true, 
            'is_fully_approved' => false
        ]);
        
        $memo->approvals()->detach(); 

        return back()->with('success', 'Memo telah ditolak dan dibatalkan.');
    }

    /**
     * Generate dan Download PDF Memo.
     */
    public function download($id)
    {
        // Load data memo beserta relasi approvals-nya
        $memo = Memo::with('approvals')->findOrFail($id);
        
        // Ambil daftar semua GM untuk ditampilkan di kotak tanda tangan
        $allGms = User::where('role', 'gm')->orderBy('id', 'asc')->get();
        
        // Logika penentuan status Aktif/Tidak Aktif secara dinamis
        $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
        $status = $isExpired ? 'TIDAK AKTIF' : 'AKTIF';
        
        // Sanitasi nama file: Ganti karakter / atau \ dengan - agar tidak terjadi error sistem file
        $safeFileName = str_replace(['/', '\\'], '-', $memo->reference_no);
        
        $pdf = Pdf::loadView('pdf.memo', compact('memo', 'allGms', 'status'));
        
        // Stream PDF di browser dengan nama file yang sudah dibersihkan
        return $pdf->stream("Memo-{$safeFileName}.pdf");
    }
}