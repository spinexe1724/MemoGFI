<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\Division;
use App\Models\User;
use App\Models\Branch; 
use App\Models\MemoAttachment;
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
use Illuminate\Support\Facades\DB;

class MemoController extends Controller implements HasMiddleware
{
    /**
     * Mengatur Middleware untuk kontrol akses.
     */
    public static function middleware(): array
    {
        return [new Middleware('auth')];
    }

    /**
     * FUNGSI NOTIFIKASI BERANTAI
     * Mengirim notifikasi ke tahap approval berikutnya secara otomatis.
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
        // Tahap 2: Ke Jajaran GM/Direksi yang dipilih (Tanda tangan ke-3 dst)
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
     * DASHBOARD UTAMA
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') return redirect()->route('users.index');

        // Pastikan relasi user, approvals, dan approver menggunakan withTrashed()
        $allMemos = Memo::with([
            'user' => function($query) { $query->withTrashed(); },
            'approvals' => function($query) { $query->withTrashed()->withPivot('note', 'created_at'); },
            'approver' => function($query) { $query->withTrashed(); }
        ])->latest()->get();

        $memos = $allMemos->filter(function($memo) use ($user) {
            // User pembuat selalu bisa melihat memonya sendiri (meskipun draf)
            if ($memo->user_id == $user->id) return true;
            
            // Sembunyikan draf dari orang lain
            if ($memo->is_draft) return false;
            
            // Akses Level 3 (GM/Direksi) atau Superadmin bisa melihat semua
            if ($user->level == 3 || $user->role === 'gm') return true;
            
            // Akses Cabang (Admin/BM)
            if (in_array($user->role, ['admin', 'bm']) && optional($memo->user)->branch === $user->branch) return true;

            // Logika Tembusan (CC) - Hanya jika sudah final
            $ccArray = is_array($memo->cc_list) ? $memo->cc_list : [];
            if (in_array($user->division, $ccArray)) return $memo->is_final;

            return false;
        });

        return view('memos.index', compact('memos', 'user'));
    }

    /**
     * MENU: MEMO SAYA
     * Menampilkan semua memo yang dibuat oleh user sendiri.
     */
    public function myOwnMemos()
    {
        $user = Auth::user();
        $memos = Memo::with(['user', 'approvals', 'approver'])
                    ->where('user_id', $user->id)
                    ->where('is_draft', false)
                    ->latest()
                    ->get();

        return view('memos.index', [
            'memos' => $memos, 
            'user' => $user, 
            'pageTitle' => 'Daftar Memo Saya'
        ]);
    }

    /**
     * MENU: MEMO AKTIF (ARSIP PERUSAHAAN)
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

        return view('memos.index', ['memos' => $memos, 'user' => $user, 'pageTitle' => 'Database Memo Aktif']);
    }

    /**
     * MENU: MEMO DITOLAK / KADALUARSA
     */
      public function rejectedMemos(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'rejected'); // Default ke 'rejected'
        
        $allMemos = Memo::with(['user', 'approvals', 'approver'])
                        ->where('is_draft', false)
                        ->latest()->get();

        $memos = $allMemos->filter(function($memo) use ($user, $filter) {
            $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
            
            // Logika Pemisahan Strik (Eksklusif)
            if ($filter === 'expired') {
                // Menu Kadaluarsa: Hanya tampilkan yang benar-benar expired
                if (!$isExpired) return false;
            } else {
                // Menu Ditolak: Hanya tampilkan yang ditolak DAN belum kadaluarsa
                if (!$memo->is_rejected || $isExpired) return false;
            }

            // Kontrol Akses
            if ($memo->user_id == $user->id || $user->level == 3) return true;
            if (in_array($user->role, ['admin', 'bm']) && $memo->user->branch === $user->branch) return true;
            
            $ccList = is_array($memo->cc_list) ? $memo->cc_list : [];
            return in_array($user->division, $ccList);
        });

        $pageTitle = $filter === 'expired' ? 'Daftar Memo Kadaluarsa' : 'Daftar Memo Ditolak';

        return view('memos.index', ['memos' => $memos, 'user' => $user, 'pageTitle' => $pageTitle]);
    }

    /**
     * MENU: DRAF
     */
    public function drafts() 
    { 
        $memos = Memo::where('user_id', Auth::id())->where('is_draft', true)->latest()->get(); 
        return view('memos.drafts', compact('memos')); 
    }

    /**
     * MENU: PERSETUJUAN (PENDING APPROVAL)
     */
    public function pendingApprovals()
    {
        $user = Auth::user();
        $allMemos = Memo::with(['user', 'approvals', 'approver'])->where('is_draft', false)->where('is_rejected', false)->latest()->get();
        $memos = $allMemos->filter(fn($m) => self::shouldUserApprove($user, $m));
        return view('memos.index', ['memos' => $memos, 'user' => $user, 'pageTitle' => 'Menunggu Persetujuan Anda']);
    }

    /**
     * FORM: BUAT MEMO
     */
    public function create() 
    { 
        $user = Auth::user();
        $divisions = Division::all();
        $managers = User::whereIn('role', ['manager', 'bm'])->get(); 
        $flexibleApprovers = User::whereIn('role', ['gm', 'direksi', 'ga'])->get();

        if (in_array($user->role, ['bm', 'superadmin'])) return redirect()->route('memos.index');

        $divCode = Division::where('name', $user->division)->first()->initial ?? 'DIV';
        $year = date('Y');
        $monthRoman = $this->getRomanMonth(date('n'));
        $count = Memo::whereYear('created_at', $year)->where('sender', $user->division)->count() + 1;
        
        if ($user->role === 'admin') {
            $branchData = Branch::where('code', $user->branch)->first();
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

    /**
     * FUNGSI: SIMPAN MEMO (STORE)
     */
    public function store(Request $request) 
    { 
        $request->validate([
            'reference_no' => 'required',
            'attachments.*' => 'nullable|mimes:docx,xlsx,pdf,pptx,zip,png,jpg|max:10240',
        ]);

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

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('memo_attachments', 'public');
                $memo->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        if (!$memo->is_draft) {
            $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Diterbitkan', 'created_at' => now()]);
            $this->notifyNextApprover($memo);
        }
        return redirect()->route('memos.index')->with('success', 'Memo berhasil diproses.'); 
    }

    /**
     * FORM: EDIT MEMO
     */
    public function edit($id)
    {
        $memo = Memo::with(['approvals', 'attachments'])->findOrFail($id);
        if ($memo->user_id !== Auth::id()) abort(403);

        $divisions = Division::all();
        $managers = User::whereIn('role', ['manager', 'bm'])->get();
        $flexibleApprovers = User::whereIn('role', ['gm', 'direksi', 'ga'])->get();

        return view('memos.edit', compact('memo', 'divisions', 'managers', 'flexibleApprovers'));
    }

    /**
     * FUNGSI: UPDATE MEMO
     * PERBAIKAN: Menangani transisi Draf ke Publik agar tanda tangan pembuat muncul.
     */
    public function update(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);
        if ($memo->user_id !== Auth::id()) abort(403);
        
        $request->validate([
            'recipient' => 'required', 'subject' => 'required', 
            'body_text' => 'required', 'valid_until' => 'required|date',
            'attachments.*' => 'nullable|mimes:docx,xlsx,xls,png,jpg,pdf,pptx,zip|max:10240',
        ]);

        $isActionPublish = $request->input('action') === 'publish';
        $wasRejected = $memo->is_rejected;
        $wasDraft = $memo->is_draft; // <--- Simpan status awal draf sebelum update

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

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('memo_attachments', 'public');
                $memo->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        if ($isActionPublish) {
            // Jika sebelumnya ditolak, reset semua approval dan pasang tanda tangan revisi
            if ($wasRejected) {
                $memo->approvals()->detach();
                $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Direvisi & Diajukan Kembali', 'created_at' => now()]);
            } 
            // PERBAIKAN: Jika sebelumnya draf, pasang tanda tangan penerbitan pertama kali
            elseif ($wasDraft) {
                if (!$memo->approvals()->where('user_id', Auth::id())->exists()) {
                    $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Diterbitkan', 'created_at' => now()]);
                }
            }
            
            $this->notifyNextApprover($memo);
        }

        return redirect()->route('memos.show', $id)->with('success', 'Memo berhasil diperbarui.');
    }

    /**
     * DETAIL MEMO (SHOW)
     */
  public function show($id) { 
    $user = Auth::user(); 
    
    // Kita gunakan withTrashed() agar data user yang sudah di-soft-delete 
    // tetap bisa ditarik informasinya untuk tampilan tanda tangan digital.
    $memo = Memo::with([
        'approvals' => function($query) { 
            $query->withTrashed()->withPivot('note', 'created_at'); 
        }, 
        'user' => function($query) {
            $query->withTrashed();
        },
        'approver' => function($query) {
            $query->withTrashed();
        },
        'attachments' 
    ])->findOrFail($id); 
    
    $canApprove = self::shouldUserApprove($user, $memo); 
    return view('memos.show', compact('memo', 'user', 'canApprove')); 
}

    /**
     * LOGIKA: APAKAH USER BERHAK MENYETUJUI?
     */
    public static function shouldUserApprove($user, $memo) {
        $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
        if ($memo->is_final || $memo->approvals->contains('id', $user->id) || $memo->is_rejected || $memo->is_draft || $isExpired) return false;
        
        $count = $memo->approvals->count();
        $selectedIds = $memo->target_approvers ?? [];
        
        if ($count == 1 && $user->id == $memo->approver_id) return true;
        if ($count >= 2 && in_array($user->id, $selectedIds)) return true;
        
        return false;
    }

    /**
     * FUNGSI: APPROVE (Tanda Tangan)
     */
    public function approve(Request $request, $id)
    {
       return DB::transaction(function () use ($id, $request) {
            $user = Auth::user();
            $memo = Memo::lockForUpdate()->findOrFail($id);
            if (!self::shouldUserApprove($user, $memo)) return back()->with('error', 'Otoritas ditolak.');
            
            // Menggunakan 'Approved' sebagai nilai fallback jika note kosong
            $memo->approvals()->attach($user->id, [
                'note' => $request->note ?? 'Approved', 
                'created_at' => now()
            ]);
            
            $memo->refresh(); 
            
            if (!$memo->is_final) $this->notifyNextApprover($memo);
            return redirect()->route('memos.show', $id)->with('success', 'Persetujuan berhasil.');
        });
    }

    /**
     * FUNGSI: REJECT (Penolakan)
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user(); $memo = Memo::with('user')->findOrFail($id);
        if (!self::shouldUserApprove($user, $memo)) return back()->with('error', 'Otoritas ditolak.');
        
        $reason = $request->note ?? 'Tanpa alasan spesifik';
        $memo->approvals()->attach($user->id, ['note' => 'Ditolak: ' . $reason, 'created_at' => now()]);
        $memo->update(['is_rejected' => true]);
        
        if ($memo->user) $memo->user->notify(new MemoRejectedNotification($memo, $user->name, $reason));
        return redirect()->route('memos.index')->with('success', 'Memo ditolak.');
    }

    /**
     * FUNGSI: HAPUS PERMANEN
     */
    public function destroy($id)
    {
        $memo = Memo::findOrFail($id);
        if ($memo->user_id !== Auth::id()) abort(403);
        
        foreach($memo->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $memo->delete();
        return redirect()->route('memos.index')->with('success', 'Memo berhasil dihapus.');
    }

    /**
     * DOWNLOAD: LAMPIRAN
     */
    public function downloadAttachment($id)
    {
        $attachment = MemoAttachment::findOrFail($id);
        if (!Storage::disk('public')->exists($attachment->file_path)) return back()->with('error', 'File tidak ditemukan.');
        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    /**
     * HAPUS: LAMPIRAN
     */
   public function destroyAttachment($id)
{
    // CARI DI TABEL ATTACHMENT, BUKAN TABEL MEMO
    $attachment = \App\Models\MemoAttachment::findOrFail($id);
    
    // Cek apakah user adalah pemilik memo dari lampiran ini
    if ($attachment->memo->user_id !== Auth::id()) abort(403);

    // Hapus file fisik
    if (Storage::disk('public')->exists($attachment->file_path)) {
        Storage::disk('public')->delete($attachment->file_path);
    }

    $attachment->delete();

    return back()->with('success', 'Lampiran berhasil dihapus.');
}

    /**
     * CKEDITOR: UPLOAD GAMBAR
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $path = $request->file('upload')->store('memos', 'public');
            return response()->json(['uploaded' => 1, 'url' => asset('storage/' . $path)]);
        }
        return response()->json(['uploaded' => 0], 400);
    }

    /**
     * PDF EXPORT
     */
    public function download(Request $request, $id) 
    {
        $memo = Memo::with(['approvals', 'user', 'attachments'])->findOrFail($id);
        $status = $memo->is_rejected ? 'DITOLAK' : ($memo->is_final ? 'AKTIF' : 'PENDING');
        $pdf = Pdf::loadView('pdf.memo', compact('memo', 'status'))->setPaper('a4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        return $request->has('download') ? $pdf->download('memo.pdf') : $pdf->stream('memo.pdf');
    }

    /**
     * LOG GLOBAL (SUPERADMIN)
     */
    public function allLogs()
    {
        $memos = Memo::with('user')->latest()->paginate(10);
        return view('memos.logs', compact('memos'));
    }

    public static function getPendingCount() {
        $user = Auth::user();
        if (!$user) return 0;
        return Memo::where('is_draft', false)->where('is_rejected', false)->get()->filter(fn($m) => self::shouldUserApprove($user, $m))->count();
    }

    private function getRomanMonth($month) {
        $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romans[$month - 1];
    }


    
    
}