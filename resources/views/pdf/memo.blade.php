<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Pengaturan Halaman */
        @page { 
            margin: 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 0; 
            padding: 0; 
        }

        /* Header & Metadata */
        .header-title { 
            text-align: center; 
            font-weight: bold; 
            text-decoration: underline; 
            font-size: 18px; 
            margin-bottom: 5px; 
        }
        .ref-no { 
            text-align: center; 
            font-weight: bold; 
            font-size: 13px; 
            margin-bottom: 30px; 
        }
        .meta-container { 
            margin-bottom: 20px; 
        }
        .meta-item { 
            margin-bottom: 6px; 
            font-size: 12px; 
        }
        .meta-label { 
            display: inline-block; 
            width: 110px; 
            font-weight: bold; 
        }
        .line { 
            border-top: 1px solid #000; 
            margin: 15px 0; 
        }
        
        /* Konten Utama */
        .content { 
            line-height: 1.6; 
            text-align: justify; 
            font-size: 12px; 
            /* Memberikan ruang kosong di bawah agar tidak menimpa footer */
            margin-bottom: 260px; 
        }
        
        /* Footer & Sign Box Positioning */
        .footer-container {
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .approver-title { 
            text-align: center; 
            font-weight: bold; 
            font-size: 14px; 
            margin-bottom: 12px; /* Jarak antara teks 'Menyetujui' dan tabel */
        }
        .table-sig { 
            width: 100%; 
            border-collapse: collapse; 
            border: 1px solid black; 
        }
        .table-sig td { 
            vertical-align: bottom; 
            text-align: center; 
            border: 1px solid black; 
            padding: 10px; 
            height: 100px; 
            width: 20%;
        }
        .sig-space { 
            height: 60px; 
            position: relative; 
            display: block;
        } 
        .sig-name { 
            font-weight: bold; 
            text-decoration: underline; 
            display: block;
        }
        .sig-role {
            font-size: 10px;
            display: block;
        }
        .mark-approved { 
            color: green; 
            font-weight: bold; 
            font-size: 12px; 
        }

        /* Badge Status & Warning */
        .status-badge {
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .status-aktif { color: green; border: 1px solid green; }
        .status-expired { color: red; border: 1px solid red; }
    </style>
</head>
<body>

    <!-- Judul & No Referensi -->
    <div class="header-title">MEMO INTERNAL</div>
    <div class="ref-no">{{ $memo->reference_no }}</div>

    <!-- Metadata Informasi -->
    <div class="meta-container">
        <div class="meta-item"><span class="meta-label">Kepada</span>: {{ $memo->recipient }}</div>
        <div class="meta-item"><span class="meta-label">Dari</span>: {{ $memo->sender }}</div>
        
        @if($memo->cc_list)
            <div class="meta-item"><span class="meta-label">Cc.</span>: {{ $memo->cc_list }}</div>
        @endif
        
        <div class="meta-item"><span class="meta-label">Perihal</span>: <strong>{{ $memo->subject }}</strong></div>
        <div class="meta-item"><span class="meta-label">Tanggal Terbit</span>: {{ $memo->created_at->format('d F Y') }}</div>
        <div class="meta-item"><span class="meta-label">Masa Berlaku</span>: {{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('d F Y') : 'Tanpa Batas' }}</div>
        
        <div class="meta-item">
            <span class="meta-label">Status Memo</span>: 
            @if($memo->is_rejected)
                <span class="status-badge status-expired">DITOLAK / DIBATALKAN</span>
            @else
                @php
                    $isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt($memo->valid_until) : false;
                    $displayStatus = $isExpired ? 'TIDAK AKTIF' : 'AKTIF';
                @endphp
                <span class="status-badge {{ $displayStatus == 'AKTIF' ? 'status-aktif' : 'status-expired' }}">
                    {{ $displayStatus }}
                </span>
            @endif
        </div>
    </div>

    <div class="line"></div>

    <!-- Isi Pesan Memo -->
    <div class="content">
        {!! $memo->body_text !!}
    </div>

    <!-- Footer Tanda Tangan (Tetap di bawah) -->
    <div class="footer-container">
        @if($memo->is_rejected)
            <div style="text-align:center; color:red; border:2px solid red; padding:15px; font-size:16px; font-weight:bold; border-radius: 8px;">
                DITOLAK: MEMO INI TELAH DIBATALKAN DAN TIDAK BERLAKU
            </div>
        @else
            <!-- Teks Menyetujui di luar tabel -->
            <div class="approver-title">Menyetujui:</div>
            
            <table class="table-sig">
                <tr>
                    @foreach($allGms as $gm)
                    <td>
                        <div class="sig-space">
                            @if($memo->approvals->contains('id', $gm->id))
                                <div class="mark-approved">
                                    <span style="font-size: 20px;">✔</span><br>
                                    APPROVED
                                </div>
                            @endif
                        </div>
                        <span class="sig-name">{{ $gm->name }}</span>
                        <span class="sig-role">Direktur</span>
                    </td>
                    @endforeach
                </tr>
            </table>

            @if(!$memo->is_fully_approved)
                <div style="text-align:center; color:#999; margin-top:8px; font-style: italic; font-size: 10px;">
                    *** Dokumen ini dalam proses persetujuan ({{ $memo->approvals->count() }}/5 GM) ***
                </div>
            @endif
        @endif
    </div>

</body>
</html>