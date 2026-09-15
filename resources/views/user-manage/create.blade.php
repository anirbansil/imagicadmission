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
                  <a href="#"><i class="fa fa-home" aria-hidden="true"></i>Home</a></li>
                <li class="breadcrumb-item">
                  <a href="#"><i class="fa fa-tachometer" aria-hidden="true"></i>User Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
              </ul>
          </nav>
          </h1>
          <div class="dashboard-materials">
            <div class="user-addform">  
              <div class="mainform cmnpadding ">   
                <form role="form" action="{{ route('user-manage.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                  <div class="form-inline">
                    <div class="form-group">
                      <label>First Name
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="first_name" class="required_field" value="{{old('first_name')}}">
                      @if($errors->has('first_name'))
                      <span class="error">{{$errors->first('first_name')}}</span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label>Last Name
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="last_name" class="required_field" value="{{old('last_name')}}">
                      @if($errors->has('last_name'))
                      <span class="error">{{$errors->first('last_name')}}</span>
                      @endif
                    </div>
                  </div>
                  <div class="form-inline">
                    <div class="form-group">
                      <label>Email
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="email" class="required_field" value="{{old('email')}}" placeholder="This will be your user-id for login">
                      @if($errors->has('email'))
                      <span class="error">{{$errors->first('email')}}</span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label>Mobile
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="phone_no" class="required_field" value="{{old('phone_no')}}">
                      @if($errors->has('phone_no'))
                      <span class="error">{{$errors->first('phone_no')}}</span>
                      @endif
                    </div>
                  </div>
                  <div class="form-inline form-inside-role">
                    <div class="form-group">
                      <label>Role
                        <span class="required_field">*</span>
                      </label>
                      @foreach($allRole as $role)
                      <input type="checkbox" class="minimal required_field" name="roles[]" value="{{$role->id}}" id="rol_{{$role->id}}" @if(old('roles')) {{in_array($role->id, old('roles')) ? 'checked' : ''}} @endif>&nbsp;
                      <label class="chk_box" for="rol_{{$role->id}}"><span>{{$role->name}}</span></label>
                      @endforeach
                      @if($errors->has('roles'))
                      <span class="error">{{$errors->first('roles')}}</span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label>Center
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <select name="branch_id">
                        <option value="">Select</option>
                        @foreach($centers as $center)
                        <option value="{{$center->id}}" {{old("branch_id")== @$center->id ? 'selected' : ''}}>{{$center->name}}</option>  
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Profile Image
                      </label>
                      <input type="file" name="profile_image" class="form-control">
                      @if($errors->has('profile_image'))
                      <span class="error">{{$errors->first('profile_image')}}</span>
                      @endif
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Address
                    </label>
                    <textarea name="address" class="required_field">{{old('address')}}</textarea>
                    @if($errors->has('address'))
                    <span class="error">{{$errors->first('address')}}</span>
                    @endif
                  </div>
                  
                  <div class="form-inline">

                    <div class="form-group">
                      <label>Password
                        <span class="required_field">*</span>
                      </label>
                      <input type="password" name="password" class="required_field">
                      @if($errors->has('password'))
                      <span class="error">{{$errors->first('password')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Confirm Password
                        <span class="required_field">*</span>
                      </label>
                      <input type="password" name="password_confirmation" class="required_field">
                      @if($errors->has('password_confirmation'))
                      <span class="error">{{$errors->first('password_confirmation')}}</span>
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
