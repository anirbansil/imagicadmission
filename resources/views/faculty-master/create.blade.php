@extends('layout')
@section('title', $title)
@section('content')
<div id="main" class="admin_main">
          
  <div class="admin-inn-contents">
        <div class="container-fluid">
          <h1 class="main-title">{{$title}}
            <nav aria-label="breadcrumb">
              <ul class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i>Dashboard</a></li>
                <li class="breadcrumb-item">
                  <a href="{{route('course-master.index')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Course List</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
              </ul>
          </nav>
          </h1>
          @if(Session::has('msg'))
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
              <div class="mainform cmnpadding ">   
                 <form role="form" action="{{ route('faculty-master.store') }}" method="POST" enctype="multipart/form-data">
                 @csrf
                  <div class="form-inline">
                    <div class="four-inline">
                      <div class="form-group">
                        <label>Title
                          <span class="required_field">*</span>
                        </label>
                        <select name="surname" class="form-control required_field">
                          <option value="">Select</option>
                          <option value="Mr" {{old("surname") == "Mr" ? "selected" : ""}}>Mr</option>
                          <option value="Mrs" {{old("surname") == "Mrs" ? "selected" : ""}}>Mrs</option>
                          <option value="Miss" {{old("surname") == "Miss" ? "selected" : ""}}>Miss</option>
                        </select>
                        @if($errors->has('surname'))
                        <span class="error">{{$errors->first('surname')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Name
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="name" class="form-control required_field" value="{{old('name')}}">
                        @if($errors->has('name'))
                        <span class="error">{{$errors->first('name')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Email
                          <span class="required_field">*</span>
                        </label>
                        <input type="eamil" name="email" class="form-control required_field" value="{{old('email')}}">
                        @if($errors->has('email'))
                        <span class="error">{{$errors->first('email')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Phone Number
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="phone_no" class="form-control required_field" value="{{old('phone_no')}}">
                        @if($errors->has('phone_no'))
                        <span class="error">{{$errors->first('phone_no')}}</span>
                        @endif
                      </div>
                    </div>
                    
                    <div class="form-group full-width-group">
                      <label>Courses
                      </label>
                      @foreach($courses as $course)
                        <input type="checkbox" class="form-control required_field" name="subject[]" value="{{@$course->id}}" >{{@$course->name}}&nbsp;
                      @endforeach
                      @if($errors->has('subject'))
                      <span class="error">{{$errors->first('subject')}}</span>
                      @endif
                    </div>
                  </div>

                  <div class="form-group">
                    <input type="submit" value="Save" class="finalsubmit">
                  </div>
                 </form>
              </div> 
            </div> 
          </div>
        </div>
  </div>
    
</div>
@endsection  
