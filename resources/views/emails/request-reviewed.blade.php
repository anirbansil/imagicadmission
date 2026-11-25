@extends('emails.layout')
@section('kicker', $what['title'].' request '.($approved ? 'approved' : 'not approved'))
@section('body')
  <p style="margin:0 0 12px;">Dear {{$student['salutation']}},</p>
  @if($approved)
    <p style="margin:0 0 12px;">Your <strong>{{strtolower($what['title'])}}</strong> request has been <strong style="color:#1d6b35;">approved</strong>.</p>
  @else
    <p style="margin:0 0 12px;">Your <strong>{{strtolower($what['title'])}}</strong> request has <strong style="color:#b3261e;">not been approved</strong>.</p>
  @endif
  @include('emails._details', ['rows' => [
    'Request' => $what['title'],
    'Date' => $what['when'],
    $req->type == 'fix' ? 'Times' : 'Class' => $what['detail'],
    'Your reason' => $req->reason,
    'Note from your centre' => $req->admin_note,
    'Reviewed by' => optional($req->reviewer)->name,
  ]])
  @if($approved)
    <p style="margin:0;padding:10px 12px;background:#dff3e4;border-radius:6px;font-size:14px;color:#1d4f2b;">{{$what['effect']}}</p>
  @elseif($req->type == 'online')
    <p style="margin:0;font-size:14px;">Please attend the class at the institute and check in there.</p>
  @else
    <p style="margin:0;font-size:14px;">If you have questions, please contact your centre.</p>
  @endif
  @include('emails._button', ['url' => $url, 'label' => 'Open My Attendance'])
  <p style="margin:18px 0 0;">Regards,<br>{{$student['center'] ? 'IMAGIC '.$student['center'] : 'IMAGIC Institute'}}</p>
@endsection
