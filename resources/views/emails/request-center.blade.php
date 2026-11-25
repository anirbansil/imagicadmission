@extends('emails.layout')
@section('kicker', $what['title'].' request')
@section('body')
  <p style="margin:0 0 12px;"><strong>{{$student['name']}}</strong> has sent a{{$req->type == 'online' ? 'n' : ''}} <strong>{{strtolower($what['title'])}}</strong> request. Please approve or reject it on the Student Attendance Requests page.</p>
  @include('emails._details', ['rows' => [
    'Student' => $student['name'].($student['roll_no'] ? ' ('.$student['roll_no'].')' : ''),
    'Course' => $student['course'],
    'Centre' => $student['center'],
    'Mobile' => $student['mobile'],
    'Request' => $what['title'],
    'Date' => $what['when'],
    $req->type == 'fix' ? 'Correct times' : 'Class' => $what['detail'],
    'Reason' => $req->reason,
    'Sent on' => $req->created_at ? $req->created_at->timezone(config('attendance.timezone'))->format('d M Y, h:i A') : null,
  ]])
  @if($req->type == 'online')
    <p style="margin:0;padding:10px 12px;background:#fdf0d3;border-radius:6px;font-size:13px;color:#6b4a00;">This request is for <strong>today's class</strong>. Please decide before the class starts. Approving it lets the student check in from anywhere for this class.</p>
  @elseif($req->type == 'fix')
    <p style="margin:0;font-size:13px;color:#6f6466;">Approving changes that day's attendance record to these times.</p>
  @endif
  @include('emails._button', ['url' => $url, 'label' => 'Review the request'])
@endsection
