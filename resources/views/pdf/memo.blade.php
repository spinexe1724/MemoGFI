<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header-title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 18px; margin-bottom: 8px; }
        .ref-no { text-align: center; font-weight: bold; font-size: 13px; margin-bottom: 30px; }
        .meta-container { margin-bottom: 20px; margin-top: 20px; }
        .meta-item { margin-bottom: 8px; font-size: 13px; }
        .meta-label { display: inline-block; width: 80px; font-weight: bold; }
        .line { border-top: 1px solid #000; margin: 15px 0; }
        .content { line-height: 1.6; text-align: justify; margin-bottom: 40px; font-size: 12px; }
        
        .table-sig { width: 100%; border-collapse: collapse; border: 1px solid black; margin-top: 30px; }
        .table-sig td { vertical-align: bottom; text-align: center; border: 1px solid black; padding: 10px; height: 100px; }
        .sig-space { height: 60px; position: relative; } 
        .sig-name { font-weight: bold; text-decoration: underline; }
        .mark-approved { color: green; font-weight: bold; font-size: 12px; }
        .approver-cell { font-weight: bold; background-color: #f2f2f2; padding: 5px; text-align: center; border: 1px solid black; }
    </style>
</head>
<body>
    <div class="header-title">MEMO INTERNAL</div>
    <div class="ref-no">{{ $memo->reference_no }}</div>

    <div class="meta-container">
        <div class="meta-item"><span class="meta-label">Kepada</span>: {{ $memo->recipient }}</div>
        <div class="meta-item"><span class="meta-label">Dari</span>: {{ $memo->sender }}</div>
        <div class="meta-item"><span class="meta-label">Cc.</span>: {{ $memo->cc_list }}</div>
        <div class="meta-item"><span class="meta-label">Perihal</span>: <strong>{{ $memo->subject }}</strong></div>
        <div class="meta-item"><span class="meta-label">Tanggal</span>: {{ $memo->created_at->format('d F Y') }}</div>
    </div>

    <div class="line"></div>

    <div class="content">
        {!! $memo->body_text !!}
    </div>

    <table class="table-sig">
        <tr>
            <td colspan="5" class="approver-cell" style="background-color: #f2f2f2; font-size: 14px;">Menyetujui:</td>
        </tr>
        <tr>
            @foreach($allGms as $gm)
            <td width="20%">
                <div class="sig-space">
                    @if($memo->approvals->contains('id', $gm->id))
                        <div class="mark-approved">
                            <span style="font-size: 24px;">✔</span><br>
                            APPROVED
                        </div>
                    @endif
                </div>
                <span class="sig-name">{{ $gm->name }}</span><br>
                Direktur
            </td>
            @endforeach
        </tr>
    </table>

    @if(!$memo->is_fully_approved)
        <div style="text-align:center; color:red; margin-top:10px; font-weight: bold;">
            *** DRAFT - PENDING FULL APPROVAL ({{ $memo->approvals->count() }}/5) ***
        </div>
    @endif
</body>
</html>
