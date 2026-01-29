<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\Division;
use App\Models\User;
use App\Models\Branch; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;
use App\Notifications\MemoApprovalNotification;
use App\Notifications\MemoRejectedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class MemoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * FUNGSI NOTIFIKASI BERANTAI (Targeted/Flexible)
     */
    private function notifyNextApprover(Memo $memo)
    {
        $memo->load('approvals');
        $count = $memo->approvals->count();
        $nextApprovers = collect();

        // Tahap 1: Ke Manager/BM tujuan (Tanda tangan ke-2)
        if ($count == 1) {
            if ($memo->approver_id) {
                $target = User::find($memo->approver_id);
                if ($target) $nextApprovers->push($target);
            }
        } 
        // Tahap 2: Ke Jajaran GM/Direksi yang dipilih (Tanda tangan ke-3 dan seterusnya)
        elseif ($count >= 2 && !$memo->is_final) {
            $selectedIds = $memo->target_approvers ?? [];
            if (!empty($selectedIds)) {
                $nextApprovers = User::whereIn('id', $selectedIds)->get();
            }
        }

        if ($nextApprovers->count() > 0) {
            Notification::send($nextApprovers, new MemoApprovalNotification($memo, Auth::user()->name));
        }
    }

    /**
     * DASHBOARD: Menampilkan memo yang relevan bagi user.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') return redirect()->route('users.index');

        $allMemos = Memo::with(['user', 'approvals', 'approver'])->latest()->get();

        $memos = $allMemos->filter(function($memo) use ($user) {
            if ($memo->user_id == $user->id) return true;
            if ($memo->is_draft) return false;
            
            // Hak Lihat Global untuk Direksi & GM
            if ($user->level == 3 || $user->role === 'gm') return true;
            
            // Hak Lihat Cabang
            if (in_array($user->role, ['admin', 'bm']) && $memo->user->branch === $user->branch) return true;

            // Hak Lihat Berdasarkan CC (Hanya jika memo sudah Final)
            $ccArray = is_array($memo->cc_list) ? $memo->cc_list : [];
            if (in_array($user->division, $ccArray)) return $memo->is_final;

            return false;
        });

        return view('memos.index', compact('memos', 'user'));
    }

    /**
     * MENU ARSIP MEMO AKTIF: Menampilkan memo yang sudah FINAL dan belum expired.
     */
    public function myMemos() 
    { 
        $user = Auth::user();
        $allMemos = Memo::with(['user', 'approvals', 'approver'])
                        ->where('is_draft', false)
                        ->where('is_rejected', false)
                        ->latest()->get();

        $memos = $allMemos->filter(function($memo) use ($user) {
            $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
            
            if (!$memo->is_final || $isExpired) return false;
            if ($memo->user_id == $user->id) return true;
            if ($user->level == 3 || $user->role === 'gm') return true;
            if (in_array($user->role, ['admin', 'bm']) && $memo->user->branch === $user->branch) return true;
            
            $ccList = is_array($memo->cc_list) ? $memo->cc_list : [];
            return in_array($user->division, $ccList);
        });

        return view('memos.index', ['memos' => $memos, 'user' => $user, 'pageTitle' => 'Arsip Memo Aktif']);
    }

    /**
     * MENU MEMO DITOLAK / KADALUARSA
     */
    public function rejectedMemos()
    {
        $user = Auth::user();
        $allMemos = Memo::with(['user', 'approvals', 'approver'])->where('is_draft', false)->latest()->get();

        $memos = $allMemos->filter(function($memo) use ($user) {
            $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
            if (!$memo->is_rejected && !$isExpired) return false;

            if ($memo->user_id == $user->id || $user->level == 3) return true;
            if (in_array($user->role, ['admin', 'bm']) && $memo->user->branch === $user->branch) return true;
            
            $ccList = is_array($memo->cc_list) ? $memo->cc_list : [];
            return in_array($user->division, $ccList);
        });

        return view('memos.index', ['memos' => $memos, 'user' => $user, 'pageTitle' => 'Memo Ditolak / Kadaluarsa']);
    }

    /**
     * MENU DRAF: Memo milik user yang belum diterbitkan.
     */
    public function drafts() 
    { 
        $memos = Memo::where('user_id', Auth::id())->where('is_draft', true)->latest()->get(); 
        return view('memos.drafts', compact('memos')); 
    }

    /**
     * MENU APPROVAL: Menampilkan daftar memo yang butuh tanda tangan user login.
     */
    public function pendingApprovals()
    {
        $user = Auth::user();
        $allMemos = Memo::with(['user', 'approvals', 'approver'])->where('is_draft', false)->where('is_rejected', false)->latest()->get();
        $memos = $allMemos->filter(fn($m) => self::shouldUserApprove($user, $m));
        return view('memos.index', ['memos' => $memos, 'user' => $user, 'pageTitle' => 'Menunggu Persetujuan Anda']);
    }

    /**
     * FORM EDIT: Digunakan untuk draf atau revisi memo yang ditolak.
     */
    public function edit($id)
    {
        $memo = Memo::with('approvals')->findOrFail($id);
        if ($memo->user_id !== Auth::id()) abort(403);

        // Edit diizinkan jika status Draf atau Ditolak (Revisi)
        if (!$memo->is_draft && !$memo->is_rejected && ($memo->approvals->count() > 1 || $memo->is_final)) {
            return redirect()->route('memos.index')->with('error', 'Memo sedang diproses atau sudah final.');
        }

        $divisions = Division::all();
        $managers = User::whereIn('role', ['manager', 'bm'])->get();
        $flexibleApprovers = User::whereIn('role', ['gm', 'direksi', 'ga'])->get();

        return view('memos.edit', compact('memo', 'divisions', 'managers', 'flexibleApprovers'));
    }

    /**
     * FUNGSI UPDATE: Memperbarui data dan mereset tanda tangan jika ini adalah revisi.
     */
    public function update(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);
        if ($memo->user_id !== Auth::id()) abort(403);
        
        $request->validate([
            'recipient' => 'required', 'subject' => 'required', 
            'body_text' => 'required', 'valid_until' => 'required|date'
        ]);

        $isActionPublish = $request->input('action') === 'publish';
        $wasRejected = $memo->is_rejected;
        $wasDraft = $memo->is_draft;

        $memo->update([
            'recipient'   => $request->recipient,
            'subject'     => $request->subject,
            'body_text'   => $request->body_text,
            'valid_until' => $request->valid_until,
            'cc_list'     => $request->cc_list,
            'is_draft'    => $request->input('action') === 'draft',
            'is_rejected' => false, 
            'approver_id' => $request->approver_id,
            'target_approvers' => $request->target_approvers,
        ]);

        if ($isActionPublish) {
            if ($wasRejected) {
                // Hapus riwayat tanda tangan lama jika revisi agar mulai dari nol lagi
                $memo->approvals()->detach();
                $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Direvisi & Diajukan Kembali', 'created_at' => now()]);
            } elseif ($wasDraft) {
                if (!$memo->approvals()->where('user_id', Auth::id())->exists()) {
                    $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Diterbitkan', 'created_at' => now()]);
                }
            }
            $this->notifyNextApprover($memo);
        }

        return redirect()->route('memos.show', $id)->with('success', 'Memo berhasil diperbarui.');
    }

    public function store(Request $request) 
    { 
        $memo = Memo::create([
            'user_id' => Auth::id(), 
            'reference_no' => $request->reference_no, 
            'recipient' => $request->recipient, 
            'sender' => Auth::user()->division, 
            'subject' => $request->subject, 
            'body_text' => $request->body_text,
            'valid_until' => $request->valid_until, 
            'cc_list' => $request->cc_list, 
            'is_draft' => $request->input('action') === 'draft', 
            'approver_id' => $request->approver_id,
            'target_approvers' => $request->target_approvers,
        ]);

        if (!$memo->is_draft) {
            $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Diterbitkan', 'created_at' => now()]);
            $this->notifyNextApprover($memo);
        }
        return redirect()->route('memos.index')->with('success', 'Memo berhasil diterbitkan.'); 
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user(); $memo = Memo::findOrFail($id);
        if (!self::shouldUserApprove($user, $memo)) return back()->with('error', 'Otoritas ditolak.');
        $memo->approvals()->attach($user->id, ['note' => $request->note ?? 'Disetujui', 'created_at' => now()]);
        $memo->refresh(); 
        if (!$memo->is_final) $this->notifyNextApprover($memo);
        return redirect()->route('memos.show', $id)->with('success', 'Persetujuan berhasil.');
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user(); $memo = Memo::with('user')->findOrFail($id);
        if (!self::shouldUserApprove($user, $memo)) return back()->with('error', 'Otoritas ditolak.');
        $reason = $request->note ?? 'Tanpa alasan spesifik';
        $memo->approvals()->attach($user->id, ['note' => 'Ditolak: ' . $reason, 'created_at' => now()]);
        $memo->update(['is_rejected' => true]);
        if ($memo->user) $memo->user->notify(new MemoRejectedNotification($memo, $user->name, $reason));
        return redirect()->route('memos.index')->with('success', 'Memo ditolak dan dikembalikan ke pembuat.');
    }

    public function destroy($id)
    {
        $memo = Memo::findOrFail($id);
        $user = Auth::user();
        if ($memo->user_id !== $user->id) return back()->with('error', 'Akses ditolak.');
        if (!in_array($user->role, ['supervisor', 'admin'])) return back()->with('error', 'Otoritas tidak cukup.');
        if (!$memo->is_draft && !$memo->is_rejected && $memo->approvals->count() > 1) return back()->with('error', 'Memo tidak dapat dihapus.');
        $memo->delete();
        return redirect()->route('memos.index')->with('success', 'Memo berhasil dihapus.');
    }

    /**
     * LOGIKA APPROVAL FLEXIBLE & SINKRON
     */
    public static function shouldUserApprove($user, $memo) 
    { 
        $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false; 
        if ($memo->is_final || $memo->approvals->contains('id', $user->id) || $memo->is_rejected || $memo->is_draft || $isExpired) return false; 
        
        $count = $memo->approvals->count(); 
        $selectedIds = $memo->target_approvers ?? [];

        // TAHAP 1: Atasan Langsung (Manager/BM)
        if ($count == 1) {
            if ($user->id == $memo->approver_id) return true;
        }

        // TAHAP 2: Penyetuju Lanjutan (Flexible)
        if ($count >= 2) {
            if (in_array($user->id, $selectedIds)) return true;
        }

        return false; 
    }

    /**
     * FUNGSI UPLOAD GAMBAR: Menangani unggahan foto dari CKEditor.
     */
   public function uploadImage(Request $request)
{
    if ($request->hasFile('upload')) {
        $file = $request->file('upload');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('memos', $filename, 'public');
        
        return response()->json([
            'uploaded' => 1, // Kunci utama untuk CKEditor
            'fileName' => $filename,
            'url' => asset('storage/' . $path) // URL publik foto
        ]);
    }
    return response()->json(['uploaded' => 0, 'error' => ['message' => 'Gagal mengunggah foto.']], 400);
}



    public function create() 
    { 
        $user = Auth::user(); $divisions = Division::all(); $managers = User::whereIn('role', ['manager', 'bm'])->get(); 
        $flexibleApprovers = User::whereIn('role', ['gm', 'direksi', 'ga'])->get();
        if (in_array($user->role, ['bm', 'superadmin'])) return redirect()->route('memos.index');
        $divCode = Division::where('name', $user->division)->first()->initial ?? 'DIV';
        $year = date('Y'); $monthRoman = $this->getRomanMonth(date('n'));
        $count = Memo::whereYear('created_at', $year)->where('sender', $user->division)->count() + 1;
        
        if ($user->role === 'admin') {
            $branchData = Branch::where('name', $user->branch)->orWhere('code', $user->branch)->first();
            $refIdentifier = $branchData ? $branchData->code : ($user->branch ?? 'CBG');
        } else {
            $refIdentifier = $divCode;
        }

        $autoRef = str_pad($count, 3, '0', STR_PAD_LEFT) . "/MI/{$refIdentifier}/{$monthRoman}/{$year}";
        $memo = new Memo();
        if ($user->role === 'admin') { 
            $bm = User::where('role', 'bm')->where('branch', $user->branch)->first();
            $memo->approver_id = $bm->id ?? null;
        }
        return view('memos.create', compact('autoRef', 'memo', 'divisions', 'managers', 'flexibleApprovers')); 
    }

    public function show($id) { $user = Auth::user(); $memo = Memo::with(['approvals' => function($query) { $query->withPivot('note', 'created_at'); }, 'user', 'approver'])->findOrFail($id); $canApprove = self::shouldUserApprove($user, $memo); return view('memos.show', compact('memo', 'user', 'canApprove')); }
    public static function getPendingCount() { $user = Auth::user(); if (!$user) return 0; $memos = Memo::where('is_draft', false)->where('is_rejected', false)->with(['user', 'approvals'])->get(); return $memos->filter(fn($m) => self::shouldUserApprove($user, $m))->count(); }
    public function download(Request $request, $id) 
{
    $memo = Memo::with(['approvals', 'user'])->findOrFail($id);
    $status = $memo->is_rejected ? 'DITOLAK' : ($memo->is_final ? 'AKTIF' : 'PENDING');
    
    // Tambahkan setOptions untuk mengizinkan gambar remote dan HTML5
    $pdf = Pdf::loadView('pdf.memo', compact('memo', 'status'))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true, // WAJIB TRUE
            'defaultFont' => 'sans-serif'
        ]);

    return $request->has('download') ? $pdf->download('memo.pdf') : $pdf->stream('memo.pdf');
}
    private function getRomanMonth($month) { $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII']; return $romans[$month - 1]; }
}