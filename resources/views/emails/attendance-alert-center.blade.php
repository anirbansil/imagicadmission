@extends('emails.layout')
@section('kicker', 'Attendance alert')
@section('body')
  <p style="margin:0 0 12px;"><strong>{{$student['name']}}</strong>{{$student['roll_no'] ? ' ('.$student['roll_no'].')' : ''}} has been <strong>{{$summary}}</strong> for <strong>{{$count}} classes in a row</strong>. The student has also been sent a warning email.</p>
  @include('emails._details', ['rows' => [
    'Course' => $student['course'],
    'Centre' => $student['center'],
    'Mobile' => $student['mobile'],
    'Email' => $student['email'],
  ]])
  @include('emails._streak', ['days' => $days])
  <p style="margin:0;font-size:14px;">Please get in touch with the student. If any of these days were on approved leave or need a correction, update them from the Student Attendance Requests page.</p>
  @include('emails._button', ['url' => $url, 'label' => 'Open the Attendance List'])
@endsection
