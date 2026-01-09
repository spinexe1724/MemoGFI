<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Memo - {{ $memo->reference_no }}</title>
    <style>
        @page { margin: 1.5cm 1cm 1.5cm 1cm; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            position: relative;
            min-height: 100%;
        }
        .page-number { position: fixed; bottom: -40px; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; }
        .pagenum:before { content: counter(page); }
        
        .header-title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 18px; margin-bottom: 5px; }
        .ref-no { text-align: center; font-weight: bold; font-size: 13px; margin-bottom: 25px; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .meta-table td { padding: 3px 0; vertical-align: top; font-size: 12px; }
        .meta-label { width: 130px; font-weight: bold; }
        .meta-separator { width: 10px; }
        .line { border-top: 1px solid #000; margin: 10px 0 20px 0; }
        
        .content { 
            line-height: 1.6; 
            text-align: justify; 
            font-size: 12px; 
            padding-bottom: 180px; /* Ruang untuk footer signature agar tidak tertimpa */
        }
        
        .footer-container { 
            position: absolute; 
            bottom: 0px; 
            left: 0px; 
            width: 100%; 
            page-break-inside: avoid; 
        }
        .approver-title { text-align: left; font-weight: bold; font-size: 11px; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        .table-sig { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-sig td { border: 1px solid black; padding: 5px; vertical-align: top; text-align: center; height: 105px; }
        .sig-header { font-weight: bold; font-size: 8px; border-bottom: 1px solid black; padding-bottom: 3px; margin-bottom: 5px; display: block; height: 20px; overflow: hidden; }
        .sig-space { height: 45px; margin-top: 2px; position: relative; }
        .mark-approved { color: green; font-weight: bold; font-size: 9px; line-height: 1.1; }
        .sig-name { font-weight: bold; text-decoration: underline; display: block; font-size: 9px; margin-top: 3px; }
        .sig-role { font-size: 7px; display: block; color: #555; text-transform: uppercase; }
        .status-badge { font-weight: bold; padding: 2px 6px; font-size: 10px; border: 1px solid; }
    </style>
</head>
<body>
    
    <div class="page-number">Halaman <span class="pagenum"></span></div>
    <div class="header-title">MEMO INTERNAL</div>
    <div class="ref-no">{{ $memo->reference_no }}</div>

    <table class="meta-table">
        <tr><td class="meta-label">Kepada</td><td class="meta-separator">:</td><td>{{ $memo->recipient }}</td></tr>
        <tr><td class="meta-label">Dari</td><td class="meta-separator">:</td><td>{{ $memo->user->name }} ({{ $memo->sender }})</td></tr>
        @if($memo->cc_list)
        <tr><td class="meta-label">Tembusan</td><td class="meta-separator">:</td><td>{{ $memo->cc_list }}</td></tr>
        @endif
        <tr><td class="meta-label">Perihal</td><td class="meta-separator">:</td><td><strong>{{ $memo->subject }}</strong></td></tr>
        <tr><td class="meta-label">Tanggal Terbit</td><td class="meta-separator">:</td><td>{{ $memo->created_at->format('d F Y') }}</td></tr>
        <tr><td class="meta-label">Status Memo</td><td class="meta-separator">:</td>
            <td>
                @if($memo->is_rejected)
                    <span class="status-badge" style="color:red; border-color: red;">DITOLAK / DIBATALKAN</span>
                @else
                    <span class="status-badge" style="color:{{ $status == 'AKTIF' ? 'green' : 'red' }}; border-color: {{ $status == 'AKTIF' ? 'green' : 'red' }};">{{ $status }}</span>
                @endif
            </td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="content">
        {!! nl2br($memo->body_text) !!}
    </div>

    <div class="footer-container">
        @if($memo->is_rejected)
            <div style="text-align:center; color:red; border:2px solid red; padding:15px; font-size:12px; font-weight:bold;">
                DITOLAK: MEMO INI TELAH DIBATALKAN DAN TIDAK BERLAKU
            </div>
        @elseif($memo->approvals->count() > 0)
            <div class="approver-title">Daftar Persetujuan Digital:</div>
            <table class="table-sig">
                <tr>
                    @foreach($memo->approvals as $index => $approver)
                        <td>
                            <span class="sig-header">{{ strtoupper($approver->role) }} - {{ $approver->division }}</span>
                            <div class="sig-space">
                                <div class="mark-approved">
                                    <span style="font-size: 14px;">✔</span><br>
                                    DIGITAL SIGNATURE<br>
                                    APPROVED<br>
                                    <small style="font-size: 7px; color: #777; font-weight: normal;">
                                        {{ $approver->pivot->created_at->format('d/m/y H:i') }}
                                    </small>
                                </div>
                            </div>
                            <span class="sig-name">{{ strtoupper($approver->name) }}</span>
                            <span class="sig-role">
                                {{ $approver->role == 'gm' ? 'General Manager' : ($approver->role == 'direksi' ? 'Direksi' : ($approver->role == 'supervisor' ? 'Supervisor' : ($approver->role == 'manager' ? 'Manager' : ucfirst($approver->role)))) }}
                            </span>
                        </td>
                        {{-- Pecah baris jika lebih dari 5 tanda tangan --}}
                        @if(($index + 1) % 5 == 0 && !$loop->last) </tr><tr> @endif
                    @endforeach
                </tr>
            </table>
        @endif
    </div>
</body>
</html>