@extends('emails.layout')
@section('kicker', 'Registration confirmed')
@section('body')
  <p style="margin:0 0 12px;">Dear {{$student['salutation']}},</p>
  <p style="margin:0 0 12px;"><strong>Congratulations! Welcome to IMAGIC Institute.</strong> We confirm your registration. Wishing you a colourful career.</p>
  @include('emails._details', ['rows' => [
    'Registration no.' => $student['roll_no'],
    'Name' => $student['name'],
    'Centre' => $student['center'],
    'Mobile' => $student['mobile'],
  ]])
  @if($password)
    <p style="margin:16px 0 6px;font-weight:bold;">Your Student Portal login</p>
    @include('emails._details', ['rows' => [
      'Email' => $student['email'],
      'Password' => $password,
    ]])
    <p style="margin:0;font-size:13px;color:#6f6466;">In the Student Portal you can see your course, attendance, marks and fees, and pay your EMIs online. Please keep your password private.</p>
    @include('emails._button', ['url' => $loginUrl, 'label' => 'Log in to the Student Portal'])
  @endif
  <p style="margin:18px 0 0;">Regards,<br>IMAGIC Institute</p>
@endsection
@section('footer')
  Questions? Please contact your centre{{$student['center'] ? ' ('.$student['center'].')' : ''}}.
@endsection
