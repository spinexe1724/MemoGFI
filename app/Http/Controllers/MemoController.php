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
     * Menampilkan daftar memo berdasarkan tingkatan (level) pengguna.
     */
    public function index()
    {
      $user = Auth::user();

        // 1. Redirect Superadmin langsung ke Manajemen User saat akses index
        if ($user->role === 'superadmin') {
            return redirect()->route('users.index');
        }
        // Superadmin diarahkan ke halaman Logs
       

        if ($user->level == 3) {

            $memos = Memo::with(['approvals', 'user'])->latest()->paginate(5);
        } elseif ($user->level == 2) {


            
            $memos = Memo::where(function($query) use ($user) {
                // Cek divisi pembuat/pengirim
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('division', $user->division);
                })
                // ATAU Cek apakah nama divisi user ada di dalam teks cc_list
                ->orWhere('cc_list', 'like', '%' . $user->division . '%');
            })->with(['approvals', 'user'])->latest()->paginate(5);
        } else {
            // Level 1: Hanya melihat memo miliknya sendiri
            $memos = $user->memos()->with('approvals')->latest()->paginate(5);
        }

        return view('memos.index', compact('memos', 'user'));
    }

    /**
     * Log Memo untuk Superadmin
     */
      public function allLogs()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses khusus Superadmin.');
        }

        $memos = Memo::with('user')->latest()->paginate(20);
        return view('memos.logs', compact('memos'));
    }

    private function getRomanMonth($month)
    {
        $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romans[$month - 1];
    }

    public function create()
    {
        $user = Auth::user();
        $divisions = Division::all();
        $allowedRoles = ['supervisor', 'manager', 'gm', 'direksi'];
        
        if (!in_array($user->role, $allowedRoles)) {
            return redirect()->route('dashboard')->with('error', 'Hanya Supervisor, Manager, GM atau Direksi yang boleh membuat memo.');
        }
        
        $year = date('Y');
        $month = date('n');

        $division = Division::where('name', $user->division)->first();
        $divCode = $division ? $division->initial : $user->division;

        $count = Memo::whereYear('created_at', $year)
                     ->where('sender', $user->division)
                     ->count() + 1;

        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);
        $monthRoman = $this->getRomanMonth($month);
        $autoRef = "{$sequence}/MI/{$divCode}/{$monthRoman}/{$year}";
        
        $memo = new Memo();
        return view('memos.create', compact('autoRef', 'memo','divisions'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'reference_no' => 'required|string|unique:memos,reference_no',
            'recipient'    => 'required|string',
            'subject'      => 'required|string',
            'body_text'    => 'required|string',
            'valid_until'  => 'required|date|after_or_equal:today',
            'cc_list'      => 'nullable|array',       
 ]);

        $memo = Memo::create([
            'user_id'      => $user->id,
            'reference_no' => $request->reference_no,
            'recipient'    => $request->recipient,
            'sender'       => $user->division, 
            'subject'      => $request->subject,
            'body_text'    => $request->body_text,
            'valid_until'  => $request->valid_until,
            'cc_list'      => $request->cc_list,
        ]);

        $memo->approvals()->attach($user->id, [
            'note' => 'Otomatis disetujui oleh pembuat (' . ucfirst($user->role) . ')',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('memos.index')->with('success', 'Memo berhasil dibuat.');
    }

    /**
     * Menampilkan detail memo dengan proteksi Level & Divisi.
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

        
        // 1. Superadmin selalu punya akses
        if ($user->role === 'superadmin') {
            $canView = true;
        } 
        // 2. Level 3 (Global)
        elseif ($user->level == 3) {
            $canView = true;
        }
        // 3. Level 2 (Per Divisi)
           elseif ($user->level == 2 && ($memo->user->division == $user->division || $isCCed)) {
            $canView = true;
        }
        // 4. Pemilik Memo
        elseif ($memo->user_id == $user->id) {
            $canView = true;
        }

        if (!$canView) {
            abort(403, 'Akses ditolak. Memo ini berada di luar wewenang level/divisi Anda.');
        }

        return view('memos.show', compact('memo', 'user'));
    }

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

        if (!$memo->approvals()->where('user_id', $user->id)->exists()) {
            $memo->approvals()->attach($user->id, [
                'note' => $request->note ?? 'Disetujui secara digital',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Contoh: Jika sudah ada minimal 3 approval (termasuk pembuat), set Final
       

        return redirect()->route('memos.show', $id)->with('success', 'Persetujuan berhasil disimpan.');
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['gm', 'direksi'])) {
            abort(403, 'Hanya GM atau Direksi yang dapat menolak memo.');
        }

        $memo = Memo::findOrFail($id);

        if ($memo->approvals()->where('user_id', $user->id)->exists()) {
            // Opsional: Pejabat yang sudah approve tidak boleh reject? 
            // Tergantung kebijakan Anda.
        }
        
        $memo->update([
            'is_rejected' => true, 
            'is_fully_approved' => false
        ]);
        
        return redirect()->route('memos.show', $id)->with('success', 'Memo telah ditolak.');
    }

    public function download(Request $request, $id)
    {
        $memo = Memo::with(['approvals', 'user'])->findOrFail($id);
        $isExpired = $memo->valid_until ? Carbon::now()->startOfDay()->gt(Carbon::parse($memo->valid_until)) : false;
        if ($memo->is_rejected) {
            $status = 'DITOLAK';
        } elseif ($isExpired) {
            $status = 'KADALUARSA';
        } else {
            // Menghapus kondisi 'FINAL', semua yang valid langsung 'AKTIF'
            $status = 'AKTIF';
        }
        $pdf = Pdf::loadView('pdf.memo', compact('memo', 'status'))->setPaper('a4', 'portrait');
        $fileName = 'Memo-' . str_replace('/', '-', $memo->reference_no) . '.pdf';

        return $request->has('download') ? $pdf->download($fileName) : $pdf->stream($fileName);
    }

    public function edit($id)
    {
        $memo = Memo::with('approvals')->findOrFail($id);

        if ($memo->approvals()->count() > 1 || $memo->is_rejected) {
            return redirect()->route('memos.index')->with('error', 'Memo tidak dapat diubah karena sudah masuk tahap persetujuan GM/Direksi.');
        }

        if ($memo->user_id !== Auth::id()) {
            abort(403);
        }

        return view('memos.edit', compact('memo'));
    }

    public function update(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);

        if ($memo->approvals()->count() > 1 || $memo->is_rejected) {
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