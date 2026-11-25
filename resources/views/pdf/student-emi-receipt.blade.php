@php
  $application = $account->application;
  $emi = $row->emi;
  $online = $row->online;
  $money = function ($n) { return 'Rs. '.number_format((float) $n, 2); };
  // EMI amounts include 18% GST when the course is charged with GST (same split as the center's receipt).
  $base = $gst ? round($row->amount / 1.18, 2) : $row->amount;
  $tax = $gst ? round($row->amount - $base, 2) : 0;
  $name = $registration ? trim(preg_replace('/\s+/', ' ', $registration->mr_mrs.' '.$registration->first_name.' '.$registration->mid_name.' '.$registration->last_name)) : '';
  $logo = public_path('images/imagic_logo.png');
@endphp
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payment Receipt {{ $row->receipt_no }}</title>
  <style>
    body { font-family: sans-serif; color: #2B2022; font-size: 12px; }
    .head td { border: 0; vertical-align: middle; }
    .title { font-size: 18px; font-weight: bold; color: #571d1f; letter-spacing: 1px; }
    .paid { color: #1e6b34; font-weight: bold; font-size: 13px; border: 1.5px solid #1e6b34; padding: 3px 10px; }
    table { width: 100%; border-collapse: collapse; }
    .box td, .box th { border: 1px solid #cfc6c4; padding: 7px 9px; }
    .box th { background: #f4eeec; text-align: left; font-size: 11px; text-transform: uppercase; color: #6d6264; }
    .label { width: 38%; color: #6d6264; }
    .num { text-align: right; }
    .total td { font-weight: bold; font-size: 13px; background: #faf7f5; }
    h4 { margin: 18px 0 6px; font-size: 12px; text-transform: uppercase; color: #571d1f; letter-spacing: .5px; }
    .note { color: #6d6264; font-size: 10.5px; margin-top: 18px; }
  </style>
</head>
<body>
  <table class="head">
    <tr>
      <td style="width: 50%">@if(is_file($logo))<img src="{{ $logo }}" style="width: 120px">@endif</td>
      <td style="width: 50%; text-align: right">
        <div class="title">PAYMENT RECEIPT</div>
        <div style="margin-top: 6px"><span class="paid">PAID</span></div>
      </td>
    </tr>
  </table>

  <h4>Receipt</h4>
  <table class="box">
    <tr><td class="label">Receipt No.</td><td><b>{{ $row->receipt_no }}</b></td></tr>
    <tr><td class="label">Payment Date</td><td>{{ $online ? $online->paid_at->format('d M Y, h:i A') : optional($row->paid_on)->format('d M Y') }}</td></tr>
    <tr><td class="label">Paid Via</td><td>{{ $online ? 'Self payment (Student Portal)' : 'Center / Admin' }}</td></tr>
    <tr><td class="label">Payment Mode</td><td>{{ $row->mode }}@if($online && $online->method) ({{ strtoupper($online->method) }})@endif</td></tr>
    @if(!$online && $row->collected_by)
    <tr><td class="label">Collected By</td><td>{{ $row->collected_by }}</td></tr>
    @endif
    @if($row->reference)
    <tr><td class="label">{{ $row->mode === 'Cheque' ? 'Cheque No.' : 'Transaction ID' }}</td><td>{{ $row->reference }}</td></tr>
    @endif
    @if($online)
    <tr><td class="label">Order ID</td><td>{{ $online->order_id }}</td></tr>
    @endif
  </table>

  <h4>Student</h4>
  <table class="box">
    <tr><td class="label">Student Name</td><td>{{ $name }}</td></tr>
    <tr><td class="label">Registration No.</td><td>{{ optional($registration)->roll_no }}</td></tr>
    <tr><td class="label">Application No.</td><td>{{ $application->application_id }}</td></tr>
    <tr><td class="label">Centre</td><td>{{ $account->enrolment->center }}</td></tr>
    <tr><td class="label">Course</td><td>{{ $account->enrolment->course->name }}</td></tr>
  </table>

  <h4>Payment Details</h4>
  <table class="box">
    <tr><th>Description</th><th class="num">Amount</th></tr>
    <tr><td>EMI {{ $row->number }}@if($row->due) (due {{ $row->due->format('d M Y') }})@endif</td><td class="num">{{ $money($base) }}</td></tr>
    @if($gst)
    <tr><td>GST (18%)</td><td class="num">{{ $money($tax) }}</td></tr>
    @endif
    @if($row->late_fee)
    <tr><td>Late fee</td><td class="num">{{ $money($row->late_fee) }}</td></tr>
    @endif
    <tr class="total"><td>Total Paid</td><td class="num">{{ $money($row->paid_amount) }}</td></tr>
  </table>

  <div class="note">
    1. Online payments are subject to realisation. 2. Fees paid are neither refundable nor transferable.<br>
    This is a computer-generated receipt and does not need a signature.
  </div>
</body>
</html>
