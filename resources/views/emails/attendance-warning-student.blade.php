@extends('emails.layout')
@section('kicker', 'Attendance warning')
@section('body')
  <p style="margin:0 0 12px;">Dear {{$student['salutation']}},</p>
  <p style="margin:0 0 12px;">Our records show that you have been <strong>{{$summary}}</strong> for your last <strong>{{$count}} classes in a row</strong>{{$student['course'] ? ' of '.$student['course'] : ''}}:</p>
  @include('emails._streak', ['days' => $days])
  <p style="margin:0 0 12px;">Regular, on-time attendance is important for completing your course. Please attend your next classes on time. Your centre has also been informed.</p>
  <p style="margin:0;font-size:14px;color:#6f6466;">If you were unwell or this is a mistake, send a leave or "Fix a day" request from My Attendance, or contact your centre.</p>
  @include('emails._button', ['url' => $url, 'label' => 'Open My Attendance'])
  <p style="margin:18px 0 0;">Regards,<br>{{$student['center'] ? 'IMAGIC '.$student['center'] : 'IMAGIC Institute'}}</p>
@endsection
