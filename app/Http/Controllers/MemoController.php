<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;

class MemoController extends Controller implements HasMiddleware
{
    /**
     * Registrasi Middleware (Standar Laravel 11)
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * DASHBOARD: Menampilkan memo berdasarkan akses level.
     * Perbaikan Logika: Approver dan Pengirim diprioritaskan sebelum aturan CC.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Redirect Superadmin langsung ke Manajemen User
        if ($user->role === 'superadmin') {
            return redirect()->route('users.index');
        }

        // Query dasar
        $allMemos = Memo::with(['user', 'approvals'])->latest()->get();

        // Filter visibilitas berdasarkan aturan CC dan Level
        $memos = $allMemos->filter(function($memo) use ($user) {
            // Pembuat selalu bisa melihat
            if ($memo->user_id == $user->id) return true;

            // Jangan tampilkan draf milik orang lain
            if ($memo->is_draft) return false;

            // Akses Level 3 (Global) bisa melihat semua yang bukan draf
            if ($user->level == 3) return true;

            // PRIORITAS: Akses Level 2 (Penyetuju yang ditunjuk atau Pengirim Divisi)
            // Manager harus bisa melihat memo untuk approve meskipun divisinya di-CC
            if ($user->level == 2) {
                if ($memo->sender == $user->division || $memo->approver_id == $user->id) {
                    return true;
                }
            }

            // LOGIKA CC: Jika hanya sebagai CC (bukan penyetuju/pengirim), tunggu sampai FINAL
            $ccArray = is_array($memo->cc_list) ? $memo->cc_list : [];
            $isCCed = in_array($user->division, $ccArray);
            if ($isCCed) {
                return $memo->is_final;
            }

            return false;
        });

        return view('memos.index', compact('memos', 'user'));
    }

    /**
     * MEMO SAYA: Menampilkan memo milik user yang sudah AKTIF (Published).
     */
    public function myMemos()
    {
        $memos = Memo::where('user_id', Auth::id())
                     ->where('is_draft', false)
                     ->latest()
                     ->get();
                     
        return view('memos.my_memos', compact('memos'));
    }

    /**
     * DRAF SAYA
     */
    public function drafts()
    {
        $memos = Memo::where('user_id', Auth::id())
                     ->where('is_draft', true)
                     ->latest()
                     ->get();
                     
        return view('memos.drafts', compact('memos'));
    }

    /**
     * DETAIL MEMO: Proteksi akses agar CC tidak bisa melihat sebelum final.
     */
    public function show($id)
    {
        $user = Auth::user();
        $memo = Memo::with(['approvals' => function($query) {
            $query->withPivot('note', 'created_at');
        }, 'user'])->findOrFail($id);
        
        $canView = false;
        $ccArray = is_array($memo->cc_list) ? $memo->cc_list : [];
        $isCCed = in_array($user->division, $ccArray);

        // 1. Superadmin & Level 3
        if ($user->role === 'superadmin' || $user->level == 3) {
            $canView = true;
        } 
        // 2. Pemilik Memo & Penyetuju yang ditunjuk (Prioritas Utama)
        elseif ($memo->user_id == $user->id || $memo->approver_id == $user->id) {
            $canView = true;
        }
        // 3. Level 2 (Divisi Sama atau CC yang sudah Final)
        elseif ($user->level == 2) {
            if ($memo->user->division == $user->division) {
                $canView = true;
            } elseif ($isCCed && $memo->is_final) {
                $canView = true;
            }
        }

        if (!$canView) {
            abort(403, 'Akses ditolak. Memo ini belum mencapai status final untuk divisi Anda (CC).');
        }

        return view('memos.show', compact('memo', 'user'));
    }

    /**
     * PUBLISH DARI DRAF
     */
    public function publish($id)
    {
        $memo = Memo::findOrFail($id);
        if ($memo->user_id !== Auth::id()) abort(403);

        $memo->update(['is_draft' => false]);

        if ($memo->approvals()->count() == 0) {
            $memo->approvals()->attach(Auth::id(), [
                'note' => 'Memo Diterbitkan dari Draf',
                'created_at' => now()
            ]);
        }

        return redirect()->route('memos.show', $id)->with('success', 'Memo berhasil diterbitkan.');
    }

    /**
     * LOG GLOBAL SUPERADMIN
     */
    public function allLogs()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses khusus Superadmin.');
        }

        $memos = Memo::with('user')->latest()->paginate(20);
        return view('memos.logs', compact('memos'));
    }

    /**
     * FORM BUAT MEMO
     */
    public function create()
    {
        $user = Auth::user();
        $divisions = Division::all();
        $managers = User::where('role', 'manager')->get(); // Diperlukan untuk field 'Menyetujui'
        
        $allowedRoles = ['supervisor', 'manager'];
        if (!in_array($user->role, $allowedRoles)) {
            return redirect()->route('memos.index')->with('error', 'Hanya Supervisor dan Manager yang boleh membuat memo.');
        }
        
        $year = date('Y');
        $division = Division::where('name', $user->division)->first();
        $divCode = $division ? $division->initial : $user->division;

        $count = Memo::whereYear('created_at', $year)
                     ->where('sender', $user->division)
                     ->count() + 1;

        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);
        $monthRoman = $this->getRomanMonth(date('n'));
        $autoRef = "{$sequence}/MI/{$divCode}/{$monthRoman}/{$year}";
        
        $memo = new Memo();
        return view('memos.create', compact('autoRef', 'memo', 'divisions', 'managers'));
    }

    /**
     * SIMPAN MEMO
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'reference_no' => 'required|string|unique:memos,reference_no',
            'recipient'    => 'required|string',
            'subject'      => 'required|string',
            'body_text'    => 'required|string',
            'valid_until'  => 'required|date|after_or_equal:today',
            'cc_list'      => 'nullable|array',
        ];

        // Jika Supervisor, wajib memilih Manager sebagai penyetuju
        if ($user->role === 'supervisor') {
            $rules['approver_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $isDraft = $request->input('action') === 'draft';

        $memo = Memo::create([
            'user_id'      => Auth::id(),
            'approver_id'  => ($user->role === 'supervisor') ? $request->approver_id : null,
            'reference_no' => $request->reference_no,
            'recipient'    => $request->recipient,
            'sender'       => $user->division, 
            'subject'      => $request->subject,
            'body_text'    => $request->body_text,
            'valid_until'  => $request->valid_until,
            'cc_list'      => $request->cc_list,
            'is_draft'     => $isDraft,
        ]);

        if (!$isDraft) {
            $memo->approvals()->attach(Auth::id(), [
                'note' => 'Memo Diterbitkan',
                'created_at' => now()
            ]);
        }

        $msg = $isDraft ? 'Memo disimpan sebagai draf.' : 'Memo berhasil diterbitkan.';
        return redirect()->route('memos.index')->with('success', $msg);
    }

    /**
     * EDIT MEMO
     */
    public function edit($id)
    {
        $divisions = Division::all();
        $managers = User::where('role', 'manager')->get();
        $memo = Memo::with('approvals')->findOrFail($id);

        if (($memo->approvals()->count() > 1 && !$memo->is_draft) || $memo->is_rejected) {
            return redirect()->route('memos.index')->with('error', 'Memo tidak dapat diubah karena sudah masuk tahap persetujuan.');
        }

        if ($memo->user_id !== Auth::id()) {
            abort(403);
        }

        return view('memos.edit', compact('memo', 'divisions', 'managers'));
    }

    /**
     * UPDATE MEMO
     */
    public function update(Request $request, $id)
    {
         $memo = Memo::findOrFail($id);
        $user = Auth::user();

        if (($memo->approvals()->count() > 1 && !$memo->is_draft) || $memo->is_rejected) {
            return redirect()->route('memos.index')->with('error', 'Perubahan gagal. Memo sudah dalam proses approval.');
        }

        $rules = [
            'recipient'    => 'required|string',
            'subject'      => 'required|string',
            'body_text'    => 'required|string',
            'valid_until'  => 'required|date|after_or_equal:today',
        ];

        if ($user->role === 'supervisor') {
            $rules['approver_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $isDraft = $request->input('action') === 'draft';

        $memo->update([
            'recipient'    => $request->recipient,
            'subject'      => $request->subject,
            'body_text'    => $request->body_text,
            'valid_until'  => $request->valid_until,
            'cc_list'      => $request->cc_list,
            'is_draft'     => $isDraft,
            'approver_id'  => ($user->role === 'supervisor') ? $request->approver_id : $memo->approver_id,
        ]);

        if (!$isDraft && $memo->approvals()->count() == 0) {
            $memo->approvals()->attach(Auth::id(), [
                'note' => 'Memo Diterbitkan',
                'created_at' => now()
            ]);
        }

        $msg = $isDraft ? 'Draf diperbarui.' : 'Memo berhasil diterbitkan.';
        return redirect()->route('memos.index')->with('success', $msg);
    }

    /**
     * APPROVE
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['manager', 'gm', 'direksi'])) {
            abort(403, 'Akses ditolak.');
        }

        $memo = Memo::findOrFail($id);

        if ($memo->is_rejected) {
            return back()->with('error', 'Memo ini sudah ditolak.');
        }

        if (!$memo->approvals()->where('user_id', $user->id)->exists()) {
            $memo->approvals()->attach($user->id, [
                'note' => $request->note ?? 'Disetujui secara digital',
                'created_at' => now()
            ]);
        }

        return redirect()->route('memos.show', $id)->with('success', 'Persetujuan berhasil disimpan.');
    }

    /**
     * REJECT
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['manager','gm', 'direksi'])) {
            abort(403, 'Hanya GM atau Direksi yang dapat menolak memo.');
        }

        $memo = Memo::findOrFail($id);
        
        $memo->update([
            'is_rejected' => true, 
            'is_fully_approved' => false
        ]);
        
        return redirect()->route('memos.show', $id)->with('success', 'Memo telah ditolak.');
    }

    /**
     * DOWNLOAD PDF
     */
    public function download(Request $request, $id)
    {
        $memo = Memo::with(['approvals', 'user'])->findOrFail($id);
        $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
        
        if ($memo->is_rejected) {
            $status = 'DITOLAK';
        } elseif ($isExpired) {
            $status = 'KADALUARSA';
        } elseif ($memo->is_final) {
            $status = 'AKTIF';
        } else {
            $status = 'PENDING';
        }

        $pdf = Pdf::loadView('pdf.memo', compact('memo', 'status'))->setPaper('a4', 'portrait');
        $fileName = 'Memo-' . str_replace('/', '-', $memo->reference_no) . '.pdf';

        return $request->has('download') ? $pdf->download($fileName) : $pdf->stream($fileName);
    }

    /**
     * HELPER BULAN ROMAWI
     */
    private function getRomanMonth($month)
    {
        $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romans[$month - 1];
    }

    /**
     * LOGIKA THRESHOLD APPROVAL
     * Salin fungsi ini ke dalam Model Memo.php sebagai Accessor agar otomatis bekerja.
     */
    public function getIsFinalAttribute()
    {
        if ($this->is_draft || $this->is_rejected) return false;

        $jumlahTandaTangan = $this->approvals()->count();
        $rolePembuat = strtolower(optional($this->user)->role);

        if ($rolePembuat === 'supervisor') {
            return $jumlahTandaTangan >= 5; // Pembuat, Manager, GM, Direksi 1, Direksi 2
        } elseif ($rolePembuat === 'manager') {
            return $jumlahTandaTangan >= 4; // Pembuat, GM, Direksi 1, Direksi 2
        } elseif (in_array($rolePembuat, ['gm', 'direksi'])) {
            return $jumlahTandaTangan >= 2; // Pembuat + 1 Approval
        }

        return $jumlahTandaTangan >= 5;
    }
}