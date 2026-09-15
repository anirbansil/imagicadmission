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
                  <a href="{{route('registration-form.index')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Registration List</a></li>
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
                 <form role="form" action="{{ route('registration-form.update',$registration->id) }}" method="POST" enctype="multipart/form-data">
                 @csrf
                  <div class="form-inline">
                    <div class="form-group">
                      <label>Title 
                        <span class="required_field">*</span>
                      </label>
                      <select name="mr_mrs" class="form-control required_field">
                        <option value="">Select</option>
                        <option value="Mr" {{old('mr_mrs') == 'Mr' ? 'selected':''}} {{@$registration->mr_mrs == 'Mr' ? 'selected':''}}>Mr</option>
                        <option value="Mrs" {{old('mr_mrs') == 'Mrs' ? 'selected':''}} {{@$registration->mr_mrs == 'Mrs' ? 'selected':''}}>Mrs</option>
                        <option value="Miss" {{old('mr_mrs') == 'Miss' ? 'selected':''}} {{@$registration->mr_mrs == 'Miss' ? 'selected':''}}>Miss</option>
                      </select>
                      @if($errors->has('mr_mrs'))
                      <span class="error">{{$errors->first('mr_mrs')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>First Name
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="first_name" class="form-control required_field" value="{{@$registration->first_name ? $registration->first_name:old('first_name')}}">
                      @if($errors->has('first_name'))
                      <span class="error">{{$errors->first('first_name')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Middle Name
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="mid_name" class="form-control required_field" value="{{@$registration->mid_name ? $registration->mid_name:old('mid_name')}}">
                      @if($errors->has('mid_name'))
                      <span class="error">{{$errors->first('mid_name')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Last Name
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="last_name" class="form-control required_field" value="{{@$registration->last_name ? $registration->last_name:old('last_name')}}">
                      @if($errors->has('last_name'))
                      <span class="error">{{$errors->first('last_name')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Permanent Address
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="address" class="form-control required_field" value="{{@$registration->address ? $registration->address:old('address')}}">
                      @if($errors->has('address'))
                      <span class="error">{{$errors->first('address')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Present Address
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="res_address" class="form-control required_field" value="{{@$registration->res_address ? $registration->res_address:old('res_address')}}">
                      @if($errors->has('res_address'))
                      <span class="error">{{$errors->first('res_address')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>State
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="state" class="form-control required_field" value="{{@$registration->state ? $registration->state:old('state')}}">
                      @if($errors->has('state'))
                      <span class="error">{{$errors->first('state')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>City
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="city" class="form-control required_field" value="{{@$registration->city ? $registration->city:old('city')}}">
                      @if($errors->has('city'))
                      <span class="error">{{$errors->first('city')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>District
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="district" class="form-control required_field" value="{{@$registration->district ? $registration->district:old('district')}}">
                      @if($errors->has('district'))
                      <span class="error">{{$errors->first('district')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Pin Code
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="pin_code" class="form-control required_field" value="{{@$registration->pin_code ? $registration->pin_code:old('pin_code')}}">
                      @if($errors->has('pin_code'))
                      <span class="error">{{$errors->first('pin_code')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Date of Birth
                        <span class="required_field">*</span>
                      </label>
                      <input type="date" name="date_of_birth" class="form-control required_field" value="{{@$registration->date_of_birth ? $registration->date_of_birth:old('date_of_birth')}}">
                      @if($errors->has('date_of_birth'))
                      <span class="error">{{$errors->first('date_of_birth')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Mobile No.
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="mobile_no" class="form-control required_field" value="{{@$registration->mobile_no ? $registration->mobile_no:old('mobile_no')}}">
                      @if($errors->has('mobile_no'))
                      <span class="error">{{$errors->first('mobile_no')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>E-mail Id
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="email" class="form-control required_field" value="{{@$registration->email ? $registration->email:old('email')}}">
                      @if($errors->has('email'))
                      <span class="error">{{$errors->first('email')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>E-Qualification
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="qualification" class="form-control required_field" value="{{@$registration->qualification ? $registration->qualification:old('qualification')}}">
                      @if($errors->has('qualification'))
                      <span class="error">{{$errors->first('qualification')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Centre
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <select name="center_name" class="form-control required_field">
                        @if(Auth::guard()->user()->is_admin == 1)
                          <option value="">select centre</option>
                          @endif  
                        @foreach($centers as $center)
                          <option value="{{$center->id}}" {{@$registration->center_name == $center->id ? 'selected':''}} {{old('center_name')==$center->id ?'selected':''}}>{{$center->name}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('center_name'))
                      <span class="error">{{$errors->first('center_name')}}</span>
                      @endif
                    </div>
                    
                    <div class="form-group">
                      <label>Student Photo
                        <!-- <span class="required_field">*</span> -->
                      </label> 
                      @if($registration->student_photo)
                      <img src="{{asset('public/images')}}/{{$registration->student_photo}}" width="100px;" height="25px;">
                      @endif
                      <input type="file" name="student_photo" class="form-control required_field" value="{{old('student_photo')}}">
                      @if($errors->has('student_photo'))
                      <span class="error">{{$errors->first('student_photo')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>ID Proof
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      @if($registration->id_proff)    
                      <img src="{{asset('public/images/')}}/{{$registration->id_proff}}" width="100px;" height="25px;">
                      @endif
                      <input type="file" name="id_proff" class="form-control required_field" value="{{old('id_proff')}}">
                      @if($errors->has('id_proff'))
                      <span class="error">{{$errors->first('id_proff')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Qualification Certificate
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      @if($registration->qualification_certificate)
                      <img src="{{asset('public/images/')}}/{{$registration->qualification_certificate}}" width="100px;" height="25px;">
                      @endif
                      <input type="file" name="qualification_certificate" class="form-control required_field" value="{{old('qualification_certificate')}}">
                      @if($errors->has('qualification_certificate'))
                      <span class="error">{{$errors->first('qualification_certificate')}}</span>
                      @endif
                    </div>
                    
                    <div class="form-group">
                      <label>Guardian's Name
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="guardian_name" class="form-control required_field" value="{{@$registration->guardian_name ? $registration->guardian_name:old('guardian_name')}}">
                      @if($errors->has('guardian_name'))
                      <span class="error">{{$errors->first('guardian_name')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Guardian's Mobile No 
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="guardian_mob_no" class="form-control required_field" value="{{@$registration->guardian_mob_no ? $registration->guardian_mob_no:old('guardian_mob_no')}}">
                      @if($errors->has('guardian_mob_no'))
                      <span class="error">{{$errors->first('guardian_mob_no')}}</span>
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
