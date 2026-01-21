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

    class MemoController extends Controller implements HasMiddleware
    {
        public static function middleware(): array
        {
            return [
                new Middleware('auth'),
            ];
        }

        /**
        * DASHBOARD: Filter visibilitas memo secara umum.
        */
        public function index()
        {
            $user = Auth::user();
            if ($user->role === 'superadmin') return redirect()->route('users.index');

            $allMemos = Memo::with(['user', 'approvals', 'approver'])->latest()->get();

            $memos = $allMemos->filter(function($memo) use ($user) {
                if ($memo->user_id == $user->id) return true;
                if ($memo->is_draft) return false;

                $isHO = strtoupper(trim($memo->user->branch ?? '')) === 'HO';

                if ($user->role === 'gm') {
                    return $isHO;
                }

                if (in_array($user->role, ['admin', 'bm'])) {
                    return $memo->user->branch === $user->branch;
                }

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

        /**
        * MENU APPROVAL: Menampilkan daftar memo yang menunggu tanda tangan user.
        */
        public function pendingApprovals()
        {
            $user = Auth::user();
            $allMemos = Memo::with(['user', 'approvals', 'approver'])
                ->where('is_draft', false)
                ->where('is_rejected', false)
                ->latest()
                ->get();

            $memos = $allMemos->filter(function($memo) use ($user) {
                return self::shouldUserApprove($user, $memo);
            });

            return view('memos.index', [
                'memos' => $memos,
                'user' => $user,
                'pageTitle' => 'Menunggu Persetujuan Anda'
            ]);
        }

        /**
        * CENTRAL LOGIC: Menentukan giliran tanda tangan.
        * PERBAIKAN: Ditambahkan pengecekan kadaluarsa agar tidak bisa di-approve jika sudah lewat tanggal berlaku.
        */
        public static function shouldUserApprove($user, $memo)
        {
            // Periksa apakah memo sudah kadaluarsa berdasarkan tanggal valid_until
            $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;

            // Jika sudah final, sudah tanda tangan, ditolak, draf, ATAU KADALUARSA -> return false
            if ($memo->is_final || $memo->approvals->contains('id', $user->id) || $memo->is_rejected || $memo->is_draft || $isExpired) {
                return false;
            }

            $count = $memo->approvals->count();
            $creator = $memo->user;
            $isHO = strtoupper(trim($creator->branch ?? '')) === 'HO';
            $role = strtolower($user->role);
            $userDiv = strtoupper(trim($user->division ?? ''));
            $isGAUser = (str_contains($userDiv, 'GA') || str_contains($userDiv, 'AFFAIR'));

            if (!$isHO) {
                if ($count == 1 && (in_array($role, ['bm', 'manager'])) && $user->branch === $creator->branch) {
                    return true;
                }
                if ($count == 2 && $isGAUser && $role !== 'supervisor') {
                    return true;
                }
                if ($count >= 3 && $role === 'direksi') {
                    return true;
                }
                return false;
            }

            if ($role === 'gm' && $isHO) {
                if (in_array($creator->role, ['manager', 'bm'])) return $count >= 1;
                return $count >= 2;
            }

            if ($role === 'direksi' && $isHO) {
                return $memo->approvals->where('role', 'gm')->count() > 0;
            }

            if ($isGAUser && str_contains(strtoupper(trim($memo->sender ?? '')), 'GA')) {
                if ($count == 1 && $role === 'manager') return true;
                if ($count >= 2 && in_array($role, ['gm', 'direksi'])) return true;
            }

            if ($count == 1 && $isHO) {
                if ($user->id == $memo->approver_id) return true;
                if ($role === 'manager' && $userDiv === strtoupper(trim($creator->division ?? ''))) return true;
            }

            return false;
        }

        /**
        * MENU MEMO AKTIF: Menampilkan arsip memo yang sudah FINAL dan belum kadaluarsa.
        */
        public function myMemos() 
        { 
            $user = Auth::user();
            $userDiv = $user->division;

            $allMemos = Memo::with(['user', 'approvals', 'approver'])
                ->where('is_draft', false)
                ->where('is_rejected', false)
                ->latest()
                ->get();

            $memos = $allMemos->filter(function($memo) use ($user, $userDiv) {
                $ccList = is_array($memo->cc_list) ? $memo->cc_list : [];
                $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
                return $memo->is_final && in_array($userDiv, $ccList) && !$isExpired;
            });

            return view('memos.index', [
                'memos' => $memos,
                'user' => $user,
                'pageTitle' => 'Arsip Memo Aktif (Tembusan)'
            ]);
        }

        /**
        * MENU MEMO DITOLAK / KADALUARSA:
        */
        public function rejectedMemos()
        {
            $user = Auth::user();
            $userDiv = $user->division;

            $allMemos = Memo::with(['user', 'approvals', 'approver'])
                ->where('is_draft', false)
                ->latest()
                ->get();

            $memos = $allMemos->filter(function($memo) use ($user, $userDiv) {
                $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
                $isRejected = $memo->is_rejected;

                if (!$isRejected && !$isExpired) return false;

                if ($memo->user_id == $user->id) return true;
                
                $ccList = is_array($memo->cc_list) ? $memo->cc_list : [];
                if (in_array($userDiv, $ccList)) return true;

                if ($user->level == 3) return true;
                if (in_array($user->role, ['admin', 'bm']) && $memo->user->branch === $user->branch) return true;

                return false;
            });

            return view('memos.index', [
                'memos' => $memos,
                'user' => $user,
                'pageTitle' => 'Arsip Memo Ditolak / Kadaluarsa'
            ]);
        }

        /**
        * SHOW: Menampilkan detail memo.
        */
        public function show($id)
        {
            $user = Auth::user();
            $memo = Memo::with(['approvals' => function($query) { 
                $query->withPivot('note', 'created_at'); 
            }, 'user', 'approver'])->findOrFail($id);
            
            $canApprove = self::shouldUserApprove($user, $memo);

            $isHO = strtoupper(trim($memo->user->branch ?? '')) === 'HO';
            $userDiv = strtoupper(trim($user->division ?? ''));
            $isGAUser = (str_contains($userDiv, 'GA') || str_contains($userDiv, 'AFFAIR'));

            $canView = false;
            if ($user->role === 'superadmin' || $user->level == 3) $canView = true;
            elseif ($user->role === 'gm' && $isHO) $canView = true;
            elseif (in_array($user->role, ['admin', 'bm']) && $memo->user->branch === $user->branch) $canView = true;
            elseif ($isGAUser && (str_contains(strtoupper(trim($memo->sender ?? '')), 'GA') || (!$isHO && $memo->approvals->count() >= 2))) $canView = true;
            elseif ($memo->user_id == $user->id || $memo->approver_id == $user->id) $canView = true;
            elseif ($user->level == 2 && ($memo->user->division == $user->division)) $canView = true;
            
            $ccList = is_array($memo->cc_list) ? $memo->cc_list : [];
            if (in_array($user->division, $ccList)) $canView = true;

            if (!$canView) abort(403);

            return view('memos.show', compact('memo', 'user', 'canApprove'));
        }

        public function pendingApprovalsCount()
        {
            return self::getPendingCount();
        }

        public static function getPendingCount()
        {
            $user = Auth::user();
            if (!$user) return 0;
            $memos = Memo::where('is_draft', false)->where('is_rejected', false)->with(['user', 'approvals'])->get();
            return $memos->filter(fn($m) => self::shouldUserApprove($user, $m))->count();
        }

        public function approve(Request $request, $id)
        {
            $user = Auth::user();
            $memo = Memo::findOrFail($id);
            if ($memo->is_rejected) return back()->with('error', 'Memo sudah ditolak.');
            
            // Pengecekan keamanan melalui shouldUserApprove (mencakup cek kadaluarsa)
            if (!self::shouldUserApprove($user, $memo)) {
                $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
                if ($isExpired) {
                    return back()->with('error', 'Gagal: Memo ini sudah kadaluarsa dan tidak dapat disetujui lagi.');
                }
                return back()->with('error', 'Belum giliran Anda atau kriteria persetujuan tidak terpenuhi.');
            }

            $memo->approvals()->attach($user->id, ['note' => $request->note ?? 'Disetujui secara digital', 'created_at' => now()]);
            return redirect()->route('memos.show', $id)->with('success', 'Persetujuan berhasil disimpan.');
        }

        public function reject(Request $request, $id)
        {
            $user = Auth::user();
            $memo = Memo::findOrFail($id);
            if (!self::shouldUserApprove($user, $memo)) return back()->with('error', 'Otoritas ditolak atau memo sudah kadaluarsa.');
            $memo->update(['is_rejected' => true]);
            return redirect()->route('memos.index')->with('success', 'Memo telah ditolak.');
        }

        public function create() 
        { 
            $user = Auth::user(); $divisions = Division::all(); $managers = User::whereIn('role', ['manager', 'bm'])->get(); 
            if (in_array($user->role, ['bm', 'superadmin'])) return redirect()->route('memos.index');
            $year = date('Y'); $monthRoman = $this->getRomanMonth(date('n')); $memo = new Memo(); 
            if ($user->role === 'admin') { 
                $branch = Branch::where('name', $user->branch)->first(); 
                $refCode = $branch ? $branch->code : ($user->branch ?? 'HO'); 
                $bm = User::where('role', 'bm')->where('branch', $user->branch)->first(); 
                if ($bm) { $memo->recipient = 'Branch Manager - ' . $bm->name; $memo->approver_id = $bm->id; } 
            } else { 
                $division = Division::where('name', $user->division)->first(); 
                $refCode = $division ? $division->initial : ($user->division ?? 'DIV'); 
            } 
            $count = Memo::whereYear('created_at', $year)->where('sender', $user->division)->count() + 1; $sequence = str_pad($count, 3, '0', STR_PAD_LEFT); $autoRef = "{$sequence}/MI/{$refCode}/{$monthRoman}/{$year}"; 
            return view('memos.create', compact('autoRef', 'memo', 'divisions', 'managers')); 
        }

        public function store(Request $request) 
        { 
            $user = Auth::user(); if ($user->role === 'bm') return redirect()->route('memos.index');
            $rules = [ 'reference_no' => 'required|string|unique:memos,reference_no', 'recipient' => 'required', 'subject' => 'required', 'body_text' => 'required', 'valid_until' => 'required|date' ]; 
            if (in_array($user->role, ['supervisor', 'admin'])) $rules['approver_id'] = 'required|exists:users,id'; 
            $request->validate($rules); 
            $memo = Memo::create([ 'user_id' => Auth::id(), 'approver_id' => in_array($user->role, ['supervisor', 'admin']) ? $request->approver_id : null, 'reference_no' => $request->reference_no, 'recipient' => $request->recipient, 'sender' => $user->division, 'subject' => $request->subject, 'body_text' => $request->body_text, 'valid_until' => $request->valid_until, 'cc_list' => $request->cc_list, 'is_draft' => $request->input('action') === 'draft', ]); 
            if (!$memo->is_draft) { $memo->approvals()->attach(Auth::id(), ['note' => 'Memo Diterbitkan', 'created_at' => now()]); } 
            return redirect()->route('memos.index')->with('success', 'Memo berhasil diproses.'); 
        }

        public function drafts() { $memos = Memo::where('user_id', Auth::id())->where('is_draft', true)->latest()->get(); return view('memos.drafts', compact('memos')); }
        
        /**
        * DOWNLOAD / VIEW PDF: Memperbaiki error compact() undefined variable.
        */
        public function download(Request $request, $id) 
        { 
            $memo = Memo::with(['approvals', 'user'])->findOrFail($id); 
            $status = $memo->is_rejected ? 'DITOLAK' : ($memo->is_final ? 'AKTIF' : 'PENDING'); 
            
            // PERBAIKAN: Gunakan string 'status' di dalam compact
            $pdf = Pdf::loadView('pdf.memo', compact('memo', 'status'))->setPaper('a4', 'portrait'); 
            
            return $request->has('download') ? $pdf->download('memo.pdf') : $pdf->stream('memo.pdf'); 
        }
        
        private function getRomanMonth($month) { $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII']; return $romans[$month - 1]; }
    }