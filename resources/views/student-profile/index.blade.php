@extends('layout')
@section('title', $title)
@section('content')
@php
  $photo = (!empty($registrations->student_photo) && file_exists(public_path('images/'.$registrations->student_photo)))
    ? asset('public/images/'.$registrations->student_photo) : asset('public/images/user.png');
  $fullName = !empty($registrations->id)
    ? trim(preg_replace('/\s+/', ' ', $registrations->mr_mrs.' '.$registrations->first_name.' '.$registrations->mid_name.' '.$registrations->last_name))
    : $user->name;
@endphp
<style>
  .student-profile-head { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; flex-wrap: wrap; }
  .student-profile-head img { width: 110px; height: 110px; object-fit: cover; border-radius: 50%; border: 3px solid #eca107; }
  .student-profile-head h3 { margin: 0 0 6px; }
  .student-profile-head p { margin: 0; color: #666; }
  .student-profile .listingtable th { width: 30%; }
  .student-profile .profile-section { margin-bottom: 25px; }
</style>
<div id="main" class="admin_main">
  <div class="admin-inn-contents">
    <div class="container-fluid">
      <h1 class="main-title">{{$title}}
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="{{route('student.profile')}}"><i class="fa fa-home" aria-hidden="true"></i>Home</a></li>
          </ul>
        </nav>
      </h1>
      <div class="dashboard-materials student-profile">

        @if(Session::has('msg') && !is_array(Session::get('msg')))
          <div class="alert alert-success">{{ Session::get('msg') }}</div>
        @endif

        <div class="student-profile-head">
          <img src="{{$photo}}" alt="{{$fullName}}">
          <div>
            <h3 class="titleh3">{{$fullName}}</h3>
            @if(!empty($registrations->roll_no))
              <p>Registration Number: <strong>{{$registrations->roll_no}}</strong></p>
            @endif
            @if(!empty($registrations->center_name))
              <p>Center: {{App\Helpers\Helper::getCenterName($registrations->center_name)}}</p>
            @endif
          </div>
        </div>

        @if(empty($registrations->id))
          <div class="alert alert-warning">
            No registration details were found for {{$user->email}}. Please contact your center.
          </div>
        @else

        <div class="profile-section relg-list">
          <h3 class="titleh3">Personal Details</h3>
          <div class="table-responsive">
            <table class="listingtable">
              <tr><th>Name</th><td>{{$fullName}}</td></tr>
              <tr><th>Date of Birth</th><td>{{!empty($registrations->date_of_birth) ? date('d-m-Y', strtotime($registrations->date_of_birth)) : ''}}</td></tr>
              <tr><th>Qualification</th><td>{{$registrations->qualification}}</td></tr>
              <tr><th>Date of Registration</th><td>{{!empty($registrations->created_at) ? date_format($registrations->created_at, 'd-m-Y') : ''}}</td></tr>
            </table>
          </div>
        </div>

        <div class="profile-section relg-list">
          <h3 class="titleh3">Contact Details</h3>
          <div class="table-responsive">
            <table class="listingtable">
              <tr><th>Email</th><td>{{$registrations->email}}</td></tr>
              <tr><th>Mobile Number</th><td>{{$registrations->mobile_no}}</td></tr>
              <tr><th>Guardian Name</th><td>{{$registrations->guardian_name}}</td></tr>
              <tr><th>Guardian Mobile Number</th><td>{{$registrations->guardian_mob_no}}</td></tr>
            </table>
          </div>
        </div>

        <div class="profile-section relg-list">
          <h3 class="titleh3">Address</h3>
          <div class="table-responsive">
            <table class="listingtable">
              <tr><th>Address</th><td>{{$registrations->address}}</td></tr>
              @if(!empty($registrations->res_address))
              <tr><th>Residential Address</th><td>{{$registrations->res_address}}</td></tr>
              @endif
              <tr><th>City</th><td>{{$registrations->city}}</td></tr>
              <tr><th>District</th><td>{{$registrations->district}}</td></tr>
              <tr><th>State</th><td>{{$registrations->state}}</td></tr>
              <tr><th>PIN Code</th><td>{{$registrations->pin_code}}</td></tr>
            </table>
          </div>
        </div>

        @if($applications->isNotEmpty())
        <div class="profile-section relg-list">
          <h3 class="titleh3">Course Details</h3>
          <div class="table-responsive">
            <table class="listingtable">
              <tr>
                <th>Application Number</th>
                <th>Course</th>
                <th>Batch Start Date</th>
                <th>Batch End Date</th>
                <th>Batch Time</th>
              </tr>
              @foreach($applications as $application)
              <tr>
                <td>{{$application->application_id}}</td>
                <td>{{App\Helpers\Helper::getCourseName($application->course)}}</td>
                <td>{{!empty($application->batch_start_date) ? date('d-m-Y', strtotime($application->batch_start_date)) : ''}}</td>
                <td>{{!empty($application->batch_end_date) ? date('d-m-Y', strtotime($application->batch_end_date)) : ''}}</td>
                <td>{{$application->batch_time}} {{$application->timeunit}}</td>
              </tr>
              @endforeach
            </table>
          </div>
        </div>
        @endif

        <p>If any of your details are incorrect, please contact your center.</p>
        @endif

      </div>
    </div>
  </div>
</div>
@endsection
