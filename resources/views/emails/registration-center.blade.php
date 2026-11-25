@extends('emails.layout')
@section('kicker', 'New student registration')
@section('body')
  <p style="margin:0 0 12px;">A new student has been registered{{$student['center'] ? ' at '.$student['center'] : ''}}.</p>
  @include('emails._details', ['rows' => [
    'Registration no.' => $student['roll_no'],
    'Name' => trim($reg->mr_mrs.' '.$student['name']),
    'Mobile' => $reg->mobile_no,
    'Email' => $reg->email,
    'Date of birth' => $reg->date_of_birth ? \Carbon\Carbon::parse($reg->date_of_birth)->format('d M Y') : null,
    'Qualification' => $reg->qualification,
    'Guardian' => trim($reg->guardian_name.($reg->guardian_mob_no ? ' ('.$reg->guardian_mob_no.')' : '')),
    'Address' => trim(implode(', ', array_filter([$reg->address, $reg->city, $reg->district, $reg->state, $reg->pin_code]))),
    'Registered on' => \Carbon\Carbon::parse($reg->created_at)->timezone(config('attendance.timezone'))->format('d M Y, h:i A'),
  ]])
  <p style="margin:0;font-size:13px;color:#6f6466;">The student has been sent a welcome SMS and email with their Student Portal login.</p>
  @include('emails._button', ['url' => $url, 'label' => 'Open the registration'])
@endsection
