{{-- A two-column details table. $rows: [label => value]; empty values are left out. --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eee5e4;border-radius:8px;border-collapse:separate;margin:14px 0;font-size:14px;">
  @foreach(array_filter($rows, function ($v) { return $v !== null && $v !== ''; }) as $label => $value)
  <tr>
    <td style="padding:8px 12px;color:#6f6466;width:38%;vertical-align:top;{{$loop->first ? '' : 'border-top:1px solid #f1ebea;'}}">{{$label}}</td>
    <td style="padding:8px 12px;font-weight:bold;vertical-align:top;{{$loop->first ? '' : 'border-top:1px solid #f1ebea;'}}">{{$value}}</td>
  </tr>
  @endforeach
</table>
