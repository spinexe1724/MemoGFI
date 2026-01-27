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

class MemoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * FUNGSI NOTIFIKASI BERANTAI (Next Approver)
     */
    private function notifyNextApprover(Memo $memo)
    {
        $memo->load('approvals');
        $count = $memo->approvals->count();
        $creator = $memo->user;
        $isHO = strtoupper(trim($creator->branch ?? '')) === 'HO';
        $nextApprovers = collect();

        if ($count == 1) {
            if ($memo->approver_id) {
                $target = User::find($memo->approver_id);
                if ($target) $nextApprovers->push($target);
            }
        } 
        elseif ($count == 2) {
            if ($isHO) {
                $nextApprovers = User::where('role', 'gm')->get();
            } else {
                $nextApprovers = User::where('division', 'LIKE', '%GA%')
                                    ->where('role', '!=', 'supervisor')
                                    ->get();
            }
        }
        elseif ($count >= 3 && !$memo->is_final) {
            $nextApprovers = User::where('role', 'direksi')->get();
        }

        if ($nextApprovers->count() > 0) {
            Notification::send($nextApprovers, new MemoApprovalNotification($memo, Auth::user()->name));
        }
    }

    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') return redirect()->route('users.index');

        $allMemos = Memo::with(['user', 'approvals', 'approver'])->latest()->get();

        $memos = $allMemos->filter(function($memo) use ($user) {
            if ($memo->user_id == $user->id) return true;
            if ($memo->is_draft) return false;
            $isHO = strtoupper(trim($memo->user->branch ?? '')) === 'HO';
            if ($user->role === 'gm') return $isHO;
            if (in_array($user->role, ['admin', 'bm'])) return $memo->user->branch === $user->branch;
            $userDiv = strtoupper(trim($user->division ?? ''));
            $isGAUser = (str_contains($userDiv, 'GA') || str_contains($userDiv, 'AFFAIR'));
            if ($isGAUser) {
                if (str_contains(strtoupper(trim($memo->sender ?? '')), 'GA')) return true;
                if (!$isHO && $memo->approvals->count() >= 2) return true;
            }
            if ($user->level == 3) return true;
            if ($user->level == 2) {
                if ($memo->sender == $user->division || $memo->approver_id == $user->id) return true;
            }
            $ccArray = is_array($memo->cc_list) ? $memo->cc_list : [];
            if (in_array($user->division, $ccArray)) return $memo->is_final;
            return false;
        });

        return view('memos.index', compact('memos', 'user'));
    }

    public function pendingApprovals()
    {
        $user = Auth::user();
        $allMemos = Memo::with(['user', 'approvals', 'approver'])->where('is_draft', false)->where('is_rejected', false)->latest()->get();
        $memos = $allMemos->filter(fn($m) => self::shouldUserApprove($user, $m));
        return view('memos.index', ['memos' => $memos, 'user' => $user, 'pageTitle' => 'Menunggu Persetujuan Anda']);
    }

    /**
     * CREATE: Menyiapkan form memo baru dengan nomor referensi otomatis.
     * PERBAIKAN: Menggunakan KODE CABANG untuk Admin, bukan Nama Cabang.
     */
    public function create() 
    { 
        $user = Auth::user(); 
        $divisions = Division::all(); 
        $managers = User::whereIn('role', ['manager', 'bm'])->get(); 
        
        if (in_array($user->role, ['bm', 'superadmin'])) return redirect()->route('memos.index');
        
        $year = date('Y'); 
        $monthRoman = $this->getRomanMonth(date('n'));
        $count = Memo::whereYear('created_at', $year)->where('sender', $user->division)->count() + 1;
        
        if ($user->role === 'admin') {
            // Mencari data cabang berdasarkan string yang tersimpan di user->branch
            // Hal ini untuk memastikan kita mendapatkan 'code' (JKT) meskipun di user tersimpan 'Jakarta'
            $branchData = Branch::where('name', $user->branch)
                                ->orWhere('code', $user->branch)
                                ->first();
                                
            $refIdentifier = $branchData ? $branchData->code : ($user->branch ?? 'CBG');
        } else {
            // Untuk peran selain Admin (HO), gunakan inisial divisi
            $refIdentifier = Division::where('name', $user->division)->first()->initial ?? 'DIV';
        }

        // Format: 001/MI/JKT/I/2026
        $autoRef = str_pad($count, 3, '0', STR_PAD_LEFT) . "/MI/{$refIdentifier}/{$monthRoman}/{$year}";
        
        $memo = new Memo();
        if ($user->role === 'admin') { 
            // Otomatis mencari BM di cabang yang sama untuk dijadikan approver awal
            $bm = User::where('role', 'bm')->where('branch', $user->branch)->first();
            $memo->approver_id = $bm->id ?? null;
        }
        return view('memos.create', compact('autoRef', 'memo', 'divisions', 'managers')); 
    }

    public function edit($id)
    {
        $memo = Memo::with('approvals')->findOrFail($id);
        if ($memo->user_id !== Auth::id()) abort(403);

        if (!$memo->is_draft && !$memo->is_rejected && ($memo->approvals->count() > 1 || $memo->is_final)) {
            return redirect()->route('memos.index')->with('error', 'Memo sedang diproses atau sudah final.');
        }

        $divisions = Division::all();
        $managers = User::whereIn('role', ['manager', 'bm'])->get();
        return view('memos.edit', compact('memo', 'divisions', 'managers'));
    }

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
            'approver_id' => in_array(Auth::user()->role, ['supervisor', 'admin']) ? $request->approver_id : $memo->approver_id,
        ]);

        if ($wasRejected && $isActionPublish) {
            $memo->approvals()->detach();
            $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Direvisi & Diajukan Kembali', 'created_at' => now()]);
            $this->notifyNextApprover($memo);
        }
        elseif ($wasDraft && $isActionPublish) {
            if (!$memo->approvals()->where('user_id', Auth::id())->exists()) {
                $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Diterbitkan', 'created_at' => now()]);
            }
            $this->notifyNextApprover($memo);
        }

        return redirect()->route('memos.show', $id)->with('success', 'Memo berhasil diperbarui.');
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

    public function store(Request $request) 
    { 
        $user = Auth::user(); if ($user->role === 'bm') return redirect()->route('memos.index');
        $memo = Memo::create([
            'user_id' => Auth::id(), 'reference_no' => $request->reference_no, 
            'recipient' => $request->recipient, 'sender' => $user->division, 
            'subject' => $request->subject, 'body_text' => $request->body_text,
            'valid_until' => $request->valid_until, 'cc_list' => $request->cc_list, 
            'is_draft' => $request->input('action') === 'draft', 'approver_id' => $request->approver_id,
        ]);
        if (!$memo->is_draft) {
            $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Diterbitkan', 'created_at' => now()]);
            $this->notifyNextApprover($memo);
        }
        return redirect()->route('memos.index')->with('success', 'Memo berhasil diterbitkan.'); 
    }

    public function myMemos() 
    { 
        $user = Auth::user();
        $allMemos = Memo::with(['user', 'approvals', 'approver'])->where('is_draft', false)->where('is_rejected', false)->latest()->get();
        $memos = $allMemos->filter(function($memo) use ($user) {
            if (!$memo->is_final || ($memo->valid_until && Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)))) return false;
            if ($memo->user_id == $user->id) return true;
            if ($user->level == 3) return true;
            if (in_array($user->role, ['admin', 'bm']) && $memo->user->branch === $user->branch) return true;
            return in_array($user->division, (array)$memo->cc_list);
        });
        return view('memos.index', ['memos' => $memos, 'user' => $user, 'pageTitle' => 'Arsip Memo Aktif']);
    }

    public function show($id) { $user = Auth::user(); $memo = Memo::with(['approvals' => function($query) { $query->withPivot('note', 'created_at'); }, 'user', 'approver'])->findOrFail($id); $canApprove = self::shouldUserApprove($user, $memo); return view('memos.show', compact('memo', 'user', 'canApprove')); }
    
    public static function shouldUserApprove($user, $memo) { 
        $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false; 
        if ($memo->is_final || $memo->approvals->contains('id', $user->id) || $memo->is_rejected || $memo->is_draft || $isExpired) return false; 
        $count = $memo->approvals->count(); $creator = $memo->user;
        $isHO = strtoupper(trim($creator->branch ?? '')) === 'HO'; $role = strtolower($user->role); 
        if (!$isHO) { 
            if ($count == 1 && in_array($role, ['bm', 'manager']) && $user->branch === $creator->branch) return true; 
            if ($count == 2 && str_contains(strtoupper($user->division), 'GA')) return true; 
            if ($count >= 3 && $role === 'direksi') return true; 
            return false; 
        } 
        if ($role === 'gm' && $isHO) return (in_array($creator->role, ['manager', 'bm'])) ? $count >= 1 : $count >= 2; 
        if ($role === 'direksi' && $isHO) return $memo->approvals->where('role', 'gm')->count() > 0; 
        if ($count == 1 && $isHO) { 
            if ($user->id == $memo->approver_id) return true; 
            if ($role === 'manager' && strtoupper($user->division) === strtoupper($creator->division)) return true; 
        } 
        return false; 
    }

    public function drafts() { $memos = Memo::where('user_id', Auth::id())->where('is_draft', true)->latest()->get(); return view('memos.drafts', compact('memos')); }
    public static function getPendingCount() { $user = Auth::user(); if (!$user) return 0; $memos = Memo::where('is_draft', false)->where('is_rejected', false)->with(['user', 'approvals'])->get(); return $memos->filter(fn($m) => self::shouldUserApprove($user, $m))->count(); }
    public function download(Request $request, $id) { $memo = Memo::with(['approvals', 'user'])->findOrFail($id); $status = $memo->is_rejected ? 'DITOLAK' : ($memo->is_final ? 'AKTIF' : 'PENDING'); $pdf = Pdf::loadView('pdf.memo', compact('memo', 'status'))->setPaper('a4', 'portrait'); return $request->has('download') ? $pdf->download('memo.pdf') : $pdf->stream('memo.pdf'); }
    
    private function getRomanMonth($month) { $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII']; return $romans[$month - 1]; }
}