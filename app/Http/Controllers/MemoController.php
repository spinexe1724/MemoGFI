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
     * GM dan Direksi dapat melihat semua memo.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Cek apakah user adalah pihak yang berhak menyetujui (GM atau Direksi)
        $isApprover = in_array($user->role, ['gm', 'direksi']);

        $memos = $isApprover 
            ? Memo::with('approvals')->latest()->get() 
            : $user->memos()->with('approvals')->latest()->paginate(5);
            
        return view('memos.index', compact('memos', 'user'));
    }


 private function getRomanMonth($month) {
        $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romans[$month - 1];
    }
    /**
     * Menampilkan form pembuatan memo baru (Hanya untuk Staff).
     */
    public function create()
    {
        
        if (Auth::user()->role !== 'staff') {
            return redirect()->route('dashboard')->with('error', 'Hanya Staff yang dapat membuat memo.');
        }
         $user = Auth::user();
        $year = date('Y');
        $month = date('n');

        // 1. Hitung jumlah memo di tahun ini untuk reset otomatis setiap tahun
        $count = Memo::whereYear('created_at', $year)->count() + 1;

        // 2. Format urutan menjadi 3 digit (contoh: 007)
        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

        // 3. Ambil bulan romawi
        $monthRoman = $this->getRomanMonth($month);

        // 4. Gabungkan format: {Urutan}/MI/{Divisi}/{BulanRomawi}/{Tahun}
        $autoRef = "{$sequence}/MI/{$user->division}/{$monthRoman}/{$year}";
       return view('memos.create', compact('autoRef'));
    }

    /**
     * Menyimpan memo baru ke database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
           'reference_no' => 'required|string|unique:memos,reference_no',
            'recipient'    => 'required|string',
            'sender'       => 'required|string',
            'subject'      => 'required|string',
            'body_text'    => 'required|string',
            'valid_until'  => 'required|date|after_or_equal:today',
            'cc_list'      => 'nullable|string',
        ]);
        $data = $request->all();
        $data['user_id'] = Auth::id();
        
        Memo::create($data);
        
        return redirect()->route('memos.index')->with('success', 'Memo berhasil dibuat dan siap diproses.');
    }

    /**
     * Menampilkan detail memo (Hanya untuk GM dan Direksi).
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Proteksi: Hanya GM dan Direksi yang bisa melihat detail internal ini
        if (!in_array($user->role, ['gm', 'direksi'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $memo = Memo::with(['approvals', 'user'])->findOrFail($id);
        return view('memos.show', compact('memo'));
    }

    /**
     * Menangani persetujuan dari GM atau Direksi.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['gm', 'direksi'])) {
            abort(403, 'Hanya GM atau Direksi yang dapat menyetujui memo.');
        }

        $memo = Memo::findOrFail($id);
        
        if ($memo->is_rejected) {
            return back()->with('error', 'Memo ini sudah ditolak.');
        }

        // Simpan approval jika belum pernah approve sebelumnya
        if (!$memo->approvals()->where('user_id', $user->id)->exists()) {
            $memo->approvals()->attach($user->id, [
                'note' => $request->input('note') // Mengambil catatan dari popup SweetAlert2
            ]);
        }

        // Jika jumlah pemberi persetujuan mencapai 5, tandai sebagai Fully Approved
        if ($memo->approvals()->count() >= 5) {
            $memo->update(['is_fully_approved' => true]);
        }

        return redirect()->route('memos.index')->with('success', 'Persetujuan berhasil disimpan.');
    }

    /**
     * Menangani penolakan memo oleh GM atau Direksi.
     */
    public function reject($id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['gm', 'direksi'])) {
            abort(403, 'Hanya GM atau Direksi yang dapat menolak memo.');
        }

        $memo = Memo::findOrFail($id);

        // Validasi: Jika user sudah approve, tidak boleh reject
        if ($memo->approvals()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Anda tidak dapat menolak memo yang sudah Anda setujui.');
        }
        
        // Update status menjadi ditolak dan hapus semua approval yang sudah ada (reset)
        $memo->update([
            'is_rejected' => true, 
            'is_fully_approved' => false
        ]);
        
        $memo->approvals()->detach(); 

        return redirect()->route('memos.index')->with('success', 'Memo telah ditolak.');
    }

    /**
     * Generate dan Stream PDF Memo.
     */
   public function download($id) {
    // Muat memo beserta user yang sudah meng-approve
    $memo = Memo::with('approvals')->findOrFail($id);
    
    $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
    $status = $isExpired ? 'TIDAK AKTIF' : 'AKTIF';

    // Kita hanya butuh $memo karena $memo->approvals sudah berisi daftar yang approve
    $pdf = Pdf::loadView('pdf.memo', compact('memo', 'status'));

    $safeFileName = str_replace(['/', '\\'], '-', $memo->reference_no);
    return $pdf->stream("Memo-{$safeFileName}.pdf");
}


  public function edit($id)
    {
        $memo = Memo::with('approvals')->findOrFail($id);

        // PROTEKSI: Jika sudah ada approval atau sudah ditolak, tidak boleh edit
        if ($memo->approvals()->exists() || $memo->is_rejected) {
            return redirect()->route('memos.index')->with('error', 'Memo tidak dapat diubah karena sudah masuk tahap persetujuan atau telah ditolak.');
        }

        // Hanya pemilik memo yang bisa edit
        if ($memo->user_id !== Auth::id()) {
            abort(403);
        }

        return view('memos.edit', compact('memo'));
    }

    /**
     * Memperbarui Data Memo
     */
    public function update(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);

        // PROTEKSI KEDUA (Server-side)
        if ($memo->approvals()->exists() || $memo->is_rejected) {
            return redirect()->route('memos.index')->with('error', 'Perubahan gagal. Memo sudah dalam proses approval.');
        }

        $request->validate([
            'recipient'    => 'required|string',
            'subject'      => 'required|string',
            'body_text'    => 'required|string',
            'valid_until'  => 'required|date|after_or_equal:today',
        ]);

        $memo->update($request->only(['recipient', 'subject', 'body_text', 'valid_until', 'cc_list']));

        return redirect()->route('memos.index')->with('success', 'Memo berhasil diperbarui.');
    }
}
