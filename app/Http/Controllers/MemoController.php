<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class MemoController extends Controller
{
    public function index() {
        $memos = (Auth::user()->role === 'gm') 
            ? Memo::with('approvals')->latest()->get() 
            : Auth::user()->memos()->with('approvals')->latest()->get();
            
        return view('memos.index', compact('memos'));
    }

    /**
     * Show the form for creating a new memo.
     */
    public function create() {
        return view('memos.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'reference_no' => 'required',
            'recipient' => 'required',
            'sender' => 'required',
            'subject' => 'required',
            'body_text' => 'required',
            'cc_list' => 'nullable',
        ]);

        $data['user_id'] = Auth::id();
        Memo::create($data);
        
        return redirect()->route('memos.index')->with('success', 'Memo berhasil dibuat.');
    }

    public function approve($id) {
        if (Auth::user()->role !== 'gm') abort(403);

        $memo = Memo::findOrFail($id);
        
        if (!$memo->approvals()->where('user_id', Auth::id())->exists()) {
            $memo->approvals()->attach(Auth::id());
        }

        if ($memo->approvals()->count() >= 5) {
            $memo->update(['is_fully_approved' => true]);
        }

        return back()->with('success', 'Persetujuan berhasil dicatat.');
    }

    public function download($id) {
        // This line requires the 'approvals' relationship defined in Memo.php
        $memo = Memo::with('approvals')->findOrFail($id);
        $allGms = User::where('role', 'gm')->orderBy('id', 'asc')->get();
        
        $pdf = Pdf::loadView('pdf.memo', compact('memo', 'allGms'));
        return $pdf->stream("Memo-{$memo->id}.pdf");
    }
}
