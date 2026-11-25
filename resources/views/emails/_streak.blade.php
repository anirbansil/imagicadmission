{{-- The classes that make up an attendance warning. $days: day rows from StudentAttendance::day(). --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eee5e4;border-radius:8px;border-collapse:separate;margin:14px 0;font-size:14px;">
  <tr>
    <td style="padding:8px 12px;color:#6f6466;font-size:12px;text-transform:uppercase;letter-spacing:.4px;">Class</td>
    <td style="padding:8px 12px;color:#6f6466;font-size:12px;text-transform:uppercase;letter-spacing:.4px;">What happened</td>
  </tr>
  @foreach($days as $day)
  <tr>
    <td style="padding:8px 12px;border-top:1px solid #f1ebea;vertical-align:top;">
      <strong>{{\Carbon\Carbon::parse($day['date'])->format('D, d M Y')}}</strong><br>
      <span style="color:#6f6466;font-size:13px;">{{$day['class']['start'] ?? ''}}{{isset($day['class']['end']) ? ' – '.$day['class']['end'] : ''}}</span>
    </td>
    <td style="padding:8px 12px;border-top:1px solid #f1ebea;vertical-align:top;">
      @if($day['status'] == 'absent')
        <strong style="color:#b3261e;">Absent</strong>
      @else
        <strong style="color:#8a5a00;">Late by {{$day['late']}} min</strong><br><span style="color:#6f6466;font-size:13px;">Checked in at {{$day['in']}}</span>
      @endif
    </td>
  </tr>
  @endforeach
</table>
