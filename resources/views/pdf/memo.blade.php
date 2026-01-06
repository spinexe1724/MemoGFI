<style>
    body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
    .header-title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 18px; margin-bottom: 8px; }
    .ref-no { text-align: center; font-weight: bold; font-size: 13px; margin-bottom: 30px; }
    
    /* Meta Data Header tanpa Tabel - Diperbesar & Spasi ditambah */
    .meta-container { margin-bottom: 20px; margin-top: 20px; }
    .meta-item { margin-bottom: 8px; font-size: 13px; }
    .meta-label { display: inline-block; width: 80px; font-weight: bold; }
    
    .line { border-top: 1px solid #000; margin: 15px 0; }
    .content { line-height: 1.6; text-align: justify; margin-bottom: 40px; font-size: 12px; }
    
    /* Layout Tanda Tangan */
    .table-sig { width: 100%; border-collapse: collapse; border: 1px solid black; margin-top: 30px; }
    .table-sig td { vertical-align: bottom; text-align: center; border: 1px solid black; padding: 10px; }
    .sig-space { height: 60px; } 
    .sig-name { font-weight: bold; text-decoration: underline; }
    .approver-cell { text-align: center; font-weight: bold; font-size: 14px; padding: 10px; background-color: #f2f2f2; }
</style>

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
        <td colspan="5" class="approver-cell">Menyetujui:</td>
    </tr>
    <tr>
         <td width="20%">
            <div class="sig-space"></div>
            <span class="sig-name">Tohir Sutanto</span><br>Direktur
        </td>
        <td width="20%">
            <div class="sig-space"></div>
            <span class="sig-name">Irwan Susanto</span><br>Direktur
        </td>
        <td width="20%">
            <div class="sig-space"></div>
            <span class="sig-name">Kwi Hui Fen</span><br>Direktur
        </td>
        <td width="20%">
            <div class="sig-space"></div>
            <span class="sig-name">Riko Aryanto</span><br>Direktur
        </td>
        <td width="20%">
            <div class="sig-space"></div>
            <span class="sig-name">Sastra Hamidjaja</span><br>Direktur Utama
        </td>
    </tr>
</table>
