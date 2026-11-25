@extends('layout')
@section('title', $title)
@section('content')
<div id="main" class="admin_main">
  <div class="admin-inn-contents">
    <div class="container-fluid">
      <h1 class="main-title">{{$title}}
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i>Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
          </ul>
        </nav>
      </h1>
      @if(Session::has('msg') && !is_array(Session::get('msg')))
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{session('msg')}}
        </div>
      @elseif(Session::has('error'))
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{session('error')}}
        </div>
      @endif
      <div class="dashboard-materials">
        <div class="newform">
          <div class="mainform cmnpadding">
            <h2 style="font-size:20px;margin:0 0 4px;color:#2B2022">{{$center->name}}</h2>
            <p style="font-size:13px;color:#6f6466;margin:0">Keep your centre's address and check-in location correct: your students' check-in depends on it.</p>
            <form action="{{route('center-master.mine.update')}}" method="POST">
              @csrf
              @include('center-master._location', ['center' => $center])
              <input type="submit" value="Save" class="finalsubmit">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
