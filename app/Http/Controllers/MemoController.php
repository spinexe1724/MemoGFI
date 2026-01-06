<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MemoController extends Controller
{
    public function index() {
        return view('memos.index', ['memos' => Memo::latest()->get()]);
    }

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

        Memo::create($data);
        return redirect()->route('memos.index');
    }

    public function download($id) {
        $memo = Memo::findOrFail($id);
        $pdf = Pdf::loadView('pdf.memo', compact('memo'));
        return $pdf->stream("Memo-{$memo->id}.pdf");
    }
}

