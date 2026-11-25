<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{$subject ?? 'IMAGIC Institute'}}</title>
</head>
<body style="margin:0;padding:0;background:#f4efee;font-family:Arial,Helvetica,sans-serif;color:#2b2022;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4efee;padding:24px 12px;">
  <tr>
    <td align="center">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e6dcdc;">
        <tr>
          <td style="background:#571d1f;padding:18px 24px;color:#ffffff;">
            <div style="font-size:20px;font-weight:bold;letter-spacing:.5px;">IMAGIC Institute</div>
            @hasSection('kicker')<div style="font-size:13px;opacity:.85;margin-top:2px;">@yield('kicker')</div>@endif
          </td>
        </tr>
        <tr>
          <td style="padding:24px;font-size:15px;line-height:1.55;">
            @yield('body')
          </td>
        </tr>
        <tr>
          <td style="padding:14px 24px;background:#faf7f6;border-top:1px solid #eee5e4;font-size:12px;color:#8a7f81;line-height:1.5;">
            This is an automatic email from the IMAGIC Institute portal. @yield('footer')
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
