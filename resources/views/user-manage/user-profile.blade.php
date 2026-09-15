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
              </ul>
          </nav>
          </h1>
          <div class="dashboard-materials">
            <div class="user-addform">  
               <div class="mainform cmnpadding ">  

                  <form role="form" action="{{route('user-manage.user-profile-update')}}" method="POST" enctype="multipart/form-data">
                     @csrf
                     @method('PUT')
                     <div class="box-body">
                        <div class="form-inline">
                           <div class="form-group row @if($errors->has('first_name'))has-error @endif">
                              <label for="first_name">First Name<span class="required_field">*</span></label>
                              <input type="text" name="first_name" class="form-control" value="{{old('first_name') ?old('first_name'):@$user->detailsUser->first_name }}">
                              @if($errors->has('first_name'))
                              <span class="error">{{$errors->first('first_name')}}</span>
                              @endif
                           </div>
                           <div class="form-group row @if($errors->has('last_name'))has-error @endif">
                              <label for="last_name">Last Name<span class="required_field">*</span></label>
                              <input type="text" name="last_name" class="form-control" value="{{old('last_name') ? old('last_name'): @$user->detailsUser->last_name}}">
                              @if($errors->has('last_name'))
                              <span class="error">{{$errors->first('l_name')}}</span>
                              @endif
                           </div>
                        </div>
                        <div class="form-inline">
                           <div class="form-group row @if($errors->has('email'))has-error @endif">
                              <label for="email">Email<span class="required_field">*</span></label>
                              <input type="email" name="email" class="form-control" value="{{old('email') ? old('email'): @$user->email}}" placeholder="will be your userid to login">
                              @if($errors->has('email'))
                              <span class="error">{{$errors->first('email')}}</span>
                              @endif
                           </div>
                           <div class="form-group row @if($errors->has('phone_no'))has-error @endif">
                              <label for="mobile">Mobile<span class="required_field">*</span></label>
                              <input type="number" name="phone_no" class="form-control" value="{{old('phone_no') ? old('phone_no'): @$user->phone_no}}">
                              @if($errors->has('mobile'))
                              <span class="error">{{$errors->first('mobile')}}</span>
                              @endif
                           </div>
                        </div>
                        <div class="form-inline">
                           <div class="form-group row @if($errors->has('address'))has-error @endif">
                              <label for="address">Address</label>
                              <textarea name="address" class="form-control">{{old('address') ? old('address'): @$user->detailsUser->address}}</textarea>
                              @if($errors->has('address'))
                              <span class="error">{{$errors->first('address')}}</span>
                              @endif
                           </div>

                           <div class="form-group row">
                              <label for="exampleInputFile">Profile Image</label>
                              @if(@$user->detailsUser->profile_image)
                              <img src="{{ url('public/images') }}/{!! @$user->detailsUser->profile_image !!}" height="100" width="100">
                              @endif
                              <input type="file" id="exampleInputFile" name="profile_image">
                           </div>
                        </div>

                       {{-- @if(Auth::guard()->user()->is_admin == 1) --}}
                        <div class="form-group want_to_chng_password">
                           <label>Do you want to change password?</label>
                           <input type="checkbox" class="minimal" name="chng_pass" id="chng_pass" value="1">
                           <label class="chk_box" for="chng_pass"></label>
                           @if($errors->has('password'))
                            <span class="error">You checked change password, please change your password or submit without checked the checkbox.</span>
                           @endif
                        </div>

                        <div class="chng_pswrd">
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
                        </div> 

                      {{--  @endif  --}}

                        <div class="form-group">
                          <input type="submit" value="Update" class="finalsubmit">
                        </div>

                     </div>
                     
                  </form>   


               </div> 
             
            </div> 
          </div>
        </div>
  </div>
    
</div>
@endsection
