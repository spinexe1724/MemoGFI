<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Memo - {{ $memo->reference_no }}</title>
    <style>
        /* Pengaturan Halaman */
        @page { 
            margin: 1.5cm 1cm 1.5cm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 0;
            padding: 0;
            position: relative;
        }

        /* Penomoran Halaman (Fixed di setiap halaman) */
        .page-number {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .pagenum:before { 
            content: counter(page); 
        }

        /* Header */
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
            margin-bottom: 25px; 
        }

        /* Metadata menggunakan Tabel agar lurus */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 12px;
        }
        .meta-label {
            width: 130px;
            font-weight: bold;
        }
        .meta-separator {
            width: 10px;
        }

        .line { 
            border-top: 1px solid #000; 
            margin: 10px 0 20px 0; 
        }
        
        /* Konten Utama */
        .content { 
            line-height: 1.6; 
            text-align: justify; 
            font-size: 12px;
            /* Memberikan ruang kosong setinggi footer agar teks tidak tertutup sign box */
            padding-bottom: 220px;
        }
        
        /* Sign Box Terkunci di Bagian Bawah Halaman */
        .footer-container {
            position: absolute;
            bottom: 0px;
            left: 0px;
            width: 100%;
            /* Menjaga agar blok tanda tangan tidak terpotong antar halaman */
            page-break-inside: avoid;
        }

        .approver-title { 
            text-align: center; 
            font-weight: bold; 
            font-size: 13px; 
            margin-bottom: 10px; 
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
            padding: 8px; 
            height: 80px; 
            width: 20%;
        }
        .sig-space { 
            height: 45px; 
            text-align: center;
        } 
        .sig-name { 
            font-weight: bold; 
            text-decoration: underline; 
            display: block;
            font-size: 10px;
        }
        .sig-role {
            font-size: 9px;
            display: block;
        }
        .mark-approved { 
            color: green; 
            font-weight: bold; 
            font-size: 11px; 
        }

        .status-badge {
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            border: 1px solid;
        }
    </style>
</head>
<body>

    <!-- Nomor Halaman -->
    <div class="page-number">
        Halaman <span class="pagenum"></span>
    </div>

    <!-- Judul -->
    <div class="header-title">MEMO INTERNAL</div>
    <div class="ref-no">{{ $memo->reference_no }}</div>

    <!-- Informasi Metadata -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Kepada</td>
            <td class="meta-separator">:</td>
            <td>{{ $memo->recipient }}</td>
        </tr>
        <tr>
            <td class="meta-label">Dari</td>
            <td class="meta-separator">:</td>
            <td>{{ $memo->sender }}</td>
        </tr>
        @if($memo->cc_list)
        <tr>
            <td class="meta-label">Tembusan</td>
            <td class="meta-separator">:</td>
            <td>{{ $memo->cc_list }}</td>
        </tr>
        @endif
        <tr>
            <td class="meta-label">Perihal</td>
            <td class="meta-separator">:</td>
            <td><strong>{{ $memo->subject }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Terbit</td>
            <td class="meta-separator">:</td>
            <td>{{ $memo->created_at->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Akhir Berlaku Memo</td>
            <td class="meta-separator">:</td>
            <td>{{ $memo->valid_until ? \Carbon\Carbon::parse($memo->valid_until)->format('d F Y') : 'Tanpa Batas' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Status Memo</td>
            <td class="meta-separator">:</td>
            <td>
                @if($memo->is_rejected)
                    <span class="status-badge" style="color:red;">DITOLAK / DIBATALKAN</span>
                @else
                    @php
                        $isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt($memo->valid_until) : false;
                        $displayStatus = $isExpired ? 'TIDAK AKTIF' : 'AKTIF';
                    @endphp
                    <span class="status-badge" style="color:{{ $displayStatus == 'AKTIF' ? 'green' : 'red' }};">
                        {{ $displayStatus }}
                    </span>
                @endif
            </td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- Isi Pesan -->
    <div class="content">
        {!! $memo->body_text !!}
    </div>

    <!-- Kotak Tanda Tangan -->
    <div class="footer-container">
        @if($memo->is_rejected)
            <div style="text-align:center; color:red; border:2px solid red; padding:15px; font-size:14px; font-weight:bold; border-radius: 8px;">
                DITOLAK: MEMO INI TELAH DIBATALKAN DAN TIDAK BERLAKU
            </div>
        @else
            <div class="approver-title">Menyetujui:</div>
            
            <table class="table-sig">
                <tr>
                    @foreach($allGms as $gm)
                    <td>
                        <div class="sig-space">
                            @if($memo->approvals->contains('id', $gm->id))
                                <div class="mark-approved">
                                    <span style="font-size: 16px;">✔</span><br>
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