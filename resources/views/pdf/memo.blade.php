<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Memo - {{ $memo->reference_no }}</title>
    <style>
        /* Pengaturan Margin Halaman */
        @page { 
            margin: 1.5cm 1.5cm 1.5cm 1.5cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 0; 
            padding: 0;
            line-height: 1.4;
            position: relative;
            min-height: 100%;
        }

        /* Penomoran Halaman */
        .page-number { 
            position: fixed; 
            bottom: -35px; 
            left: 0; 
            right: 0; 
            text-align: center; 
            font-size: 9px; 
            color: #999; 
        }
        .pagenum:before { content: counter(page); }
        
        .header-title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 18px; margin-bottom: 5px; }
        .ref-no { text-align: center; font-weight: bold; font-size: 13px; margin-bottom: 25px; }
        
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .meta-table td { padding: 4px 0; vertical-align: top; font-size: 12px; }
        .meta-label { width: 120px; font-weight: bold; }
        .meta-separator { width: 15px; text-align: center; }
        
        .line { border-top: 1.5px solid #000; margin: 10px 0 20px 0; }
        
        /* --- CSS KHUSUS KONTEN CKEDITOR --- */
        .content { 
            line-height: 1.6; 
            text-align: justify; 
            font-size: 12px; 
            padding-bottom: 280px; 
        }

        /* Styling Tabel dari CKEditor agar muncul di PDF */
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            table-layout: fixed; /* Menghindari tabel melebar keluar kertas */
        }
        
        .content table, .content th, .content td {
            border: 1px solid black;
        }
        
        .content th, .content td {
            padding: 8px;
            text-align: left;
            word-wrap: break-word; /* Memaksa teks panjang pecah baris */
        }

        .content th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Styling Gambar dari CKEditor */
        .content img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
        }
        /* ---------------------------------- */
        
        .footer-container { 
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%; 
            page-break-inside: avoid; 
        }
        
        /* CSS Signature Box (Tidak Dirubah) */
        .table-sig { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            margin-bottom: 10px; 
        }
        .table-sig td { 
            border: 1px solid #000; 
            padding: 8px; 
            vertical-align: top; 
            text-align: center; 
            height: 110px; 
        }
        
        .sig-header { font-weight: bold; font-size: 8px; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 8px; display: block; height: 18px; overflow: hidden; }
        .sig-space { height: 55px; position: relative; display: table; width: 100%; }
        .mark-approved { display: table-cell; vertical-align: middle; color: green; font-weight: bold; font-size: 9px; line-height: 1.2; }
        .sig-name { font-weight: bold; text-decoration: underline; display: block; font-size: 9px; margin-top: 5px; }
        .sig-role { font-size: 7px; display: block; color: #555; text-transform: uppercase; margin-top: 2px; }
        .status-badge { font-weight: bold; padding: 2px 8px; font-size: 10px; border: 1.5px solid; border-radius: 3px; }
        .empty-cell { border: none !important; }
    </style>
</head>
<body>
    
    <div class="page-number">Halaman <span class="pagenum"></span>  -  <span>{{ $memo->reference_no }}</span></div>
    
    <div class="header-title">MEMO INTERNAL</div>
    <div class="ref-no">{{ $memo->reference_no }}</div>

    <table class="meta-table">
        <tr><td class="meta-label">Kepada</td><td class="meta-separator">:</td><td>{{ $memo->recipient }}</td></tr>
        <tr><td class="meta-label">Dari</td><td class="meta-separator">:</td><td>{{ $memo->user->division }}</td></tr>
        @if($memo->cc_list)
        <tr><td class="meta-label">Tembusan</td><td class="meta-separator">:</td><td>{{ is_array($memo->cc_list) ? implode(', ', $memo->cc_list) : $memo->cc_list }}</td></tr>
        @endif
        <tr><td class="meta-label">Perihal</td><td class="meta-separator">:</td><td><strong>{{ $memo->subject }}</strong></td></tr>
        <tr><td class="meta-label">Tanggal Terbit</td><td class="meta-separator">:</td><td>{{ $memo->created_at->format('d F Y') }}</td></tr>
        <tr><td class="meta-label">Status Memo</td><td class="meta-separator">:</td>
            <td>
            @php
                $isExpired = $memo->valid_until ? \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($memo->valid_until)) : false;
            @endphp

            @if($memo->is_rejected)
                <span class="status-badge" style="color:red; border-color: red;">DITOLAK / DIBATALKAN</span>
            @elseif($isExpired)
                <span class="status-badge" style="color:gray; border-color: gray;">KADALUARSA</span>
            @elseif($memo->is_final)
                <span class="status-badge" style="color:green; border-color: green;">AKTIF / VALID</span>
            @else
                <span class="status-badge" style="color:blue; border-color: blue;">PENDING ({{ $memo->approvals->count() }} Tanda Tangan)</span>
            @endif
        </td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="content">
        @php
            $body = $memo->body_text;

            /**
             * LOGIKA PERBAIKAN GAMBAR: KONVERSI KE BASE64
             * Hal ini wajib agar Dompdf dapat merender gambar tanpa kendala jaringan atau GD extension.
             */
            $body = preg_replace_callback('/<img[^>]+src="([^">]+)"/i', function($matches) {
                $src = $matches[1];
                $fullPath = null;
                
                if (strpos($src, 'storage/') !== false) {
                    $parts = explode('storage/', $src);
                    $relativePath = end($parts);
                    
                    $paths = [
                        public_path('storage/' . $relativePath),
                        storage_path('app/public/' . $relativePath),
                    ];

                    foreach ($paths as $path) {
                        if (file_exists($path)) {
                            $fullPath = $path;
                            break;
                        }
                    }
                }

                if ($fullPath && file_exists($fullPath)) {
                    try {
                        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($fullPath);
                        $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
                        return str_replace($src, $base64, $matches[0]);
                    } catch (\Exception $e) {
                        return $matches[0];
                    }
                }

                return $matches[0];
            }, $body);
        @endphp

        {!! $body !!}
    </div>

    <div class="footer-container">
        @if($memo->is_rejected)
            <div style="text-align:center; color:red; border:2px solid red; padding:20px; font-size:13px; font-weight:bold;">
                DITOLAK: MEMO INI TELAH DIBATALKAN DAN TIDAK BERLAKU
            </div>
        @elseif($memo->approvals->count() > 0)
            @php
                $columnCount = $memo->approvals->count() > 5 ? 3 : $memo->approvals->count();
                if($columnCount < 2) $columnCount = 2; 
            @endphp

            @foreach($memo->approvals->chunk($columnCount) as $chunk)
                <table class="table-sig">
                    <tr>
                        @foreach($chunk as $approver)
                            <td>
                                <span class="sig-header">{{ strtoupper($approver->role) }} - {{ $approver->division }}</span>
                                <div class="sig-space">
                                    <div class="mark-approved">
                                        <span style="font-size: 16px;">✔</span><br>
                                        DIGITAL SIGNATURE APPROVED<br>
                                        <small style="font-size: 7px; color: #777;">{{ $approver->pivot->created_at->format('d/m/y H:i') }}</small>
                                    </div>
                                </div>
                                <span class="sig-name">{{ strtoupper($approver->name) }}</span>
                                <span class="sig-role">{{ $approver->role }}</span>
                            </td>
                        @endforeach
                        @for ($i = 0; $i < ($columnCount - count($chunk)); $i++)
                            <td class="empty-cell"></td>
                        @endfor
                    </tr>
                </table>
            @endforeach
        @endif
    </div>
</body>
</html>