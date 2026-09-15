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
                  <a href="{{route('inquiry-form.index')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Inquiry List</a></li>
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
                 <form role="form" action="{{ route('inquiry-form.store') }}" method="POST" enctype="multipart/form-data">
                 @csrf
                  <div class="form-inline">
                    <div class="four-inline">
                      <div class="form-group">
                        <label>Title
                          <span class="required_field">*</span>
                        </label>
                        <select name="mr_mrs" class="form-control required_field">
                          <option value="">Select</option>
                          <option value="Mr" {{old('mr_mrs') == 'Mr' ? 'selected':''}}>Mr</option>
                          <option value="Mrs" {{old('mr_mrs') == 'Mrs' ? 'selected':''}}>Mrs</option>
                          <option value="Miss" {{old('mr_mrs') == 'Miss' ? 'selected':''}}>Miss</option>
                        </select>
                        @if($errors->has('mr_mrs'))
                        <span class="error">{{$errors->first('mr_mrs')}}</span>
                        @endif
                      </div>

                      <div class="form-group">
                        <label>First Name
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="first_name" class="form-control required_field" value="{{old('first_name')}}">
                        @if($errors->has('first_name'))
                        <span class="error">{{$errors->first('first_name')}}</span>
                        @endif
                      </div>

                      <div class="form-group">
                        <label>Middle Name
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <input type="text" name="mid_name" class="form-control required_field" value="{{old('mid_name')}}">
                        @if($errors->has('mid_name'))
                        <span class="error">{{$errors->first('mid_name')}}</span>
                        @endif
                      </div>

                      <div class="form-group">
                        <label>Last Name
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <input type="text" name="last_name" class="form-control required_field" value="{{old('last_name')}}">
                        @if($errors->has('last_name'))
                        <span class="error">{{$errors->first('last_name')}}</span>
                        @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label>Date of Birth
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="date" name="date_of_birth" class="form-control required_field" value="{{old('date_of_birth')}}">
                      @if($errors->has('date_of_birth'))
                      <span class="error">{{$errors->first('date_of_birth')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Qualification
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="qualification" class="form-control required_field" value="{{old('qualification')}}">
                      @if($errors->has('qualification'))
                      <span class="error">{{$errors->first('qualification')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Occupation
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="occupation" class="form-control required_field" value="{{old('occupation')}}">
                      @if($errors->has('occupation'))
                      <span class="error">{{$errors->first('occupation')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Address
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <textarea name="address" class="form-control required_field">{{old('address')}}</textarea>
                      @if($errors->has('address'))
                      <span class="error">{{$errors->first('address')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>City
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="city" class="form-control required_field" value="{{old('city')}}">
                      @if($errors->has('city'))
                      <span class="error">{{$errors->first('city')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Pin Code
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="pin_code" class="form-control required_field" value="{{old('pin_code')}}">
                      @if($errors->has('pin_code'))
                      <span class="error">{{$errors->first('pin_code')}}</span>
                      @endif
                    </div>

                    
                    <div class="four-inline">
                      <div class="form-group">
                        <label>Mobile No.
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="mobile_no" class="form-control required_field" value="{{old('mobile_no')}}">
                        @if($errors->has('mobile_no'))
                        <span class="error">{{$errors->first('mobile_no')}}</span>
                        @endif
                      </div>

                      <div class="form-group">
                        <label>E-mail Id
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <input type="text" name="email" class="form-control required_field" value="{{old('email')}}">
                        @if($errors->has('email'))
                        <span class="error">{{$errors->first('email')}}</span>
                        @endif
                      </div>

                      <div class="form-group">
                        <label>Course
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <select name="course" class="form-control required_field">
                          <option value="">Select</option>
                          @foreach($corses as $corse)
                            <option value="{{$corse->id}}" {{old('course')==$corse->id ?'selected':''}}>{{$corse->name}}</option>
                          @endforeach
                        </select>
                        @if($errors->has('course'))
                        <span class="error">{{$errors->first('course')}}</span>
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
                            <option value="{{$center->id}}" {{old('center_name')==$center->id ?'selected':''}} {{Auth::guard()->user()->branch_id==$center->id ?'selected':''}}>{{$center->name}}</option>
                          @endforeach
                        </select>
                        @if($errors->has('center_name'))
                        <span class="error">{{$errors->first('center_name')}}</span>
                        @endif
                      </div>
                    </div>
                    

                    <div class="form-group full-width-group">
                      <label>How did you come to know about IMAGIC?
                        <span class="required_field">*</span>
                      </label>
                      <input type="radio" name="know_about_imagic" class="form-control required_field" value="Website" {{old('know_about_imagic')=='Website' ?'checked':''}}>Website
                      <input type="radio" name="know_about_imagic" class="form-control required_field" value="Facebook" {{old('know_about_imagic')=='Facebook' ?'checked':''}}>Facebook
                      
                      <input type="radio" name="know_about_imagic" class="form-control required_field" value="Reference" {{old('know_about_imagic')=='Reference' ?'checked':''}}>Reference
                      <input type="radio" name="know_about_imagic" class="form-control required_field" value="Seminar/Exhibition" {{old('know_about_imagic')=='Seminar/Exhibition' ?'checked':''}}>Seminar/Exhibition
                      <input type="radio" name="know_about_imagic" class="form-control required_field" value="Others" {{old('know_about_imagic')=='Others' ?'checked':''}}>Others
                      @if($errors->has('know_about_imagic'))
                      <span class="error">{{$errors->first('know_about_imagic')}}</span>
                      @endif
                    </div>

                    

                    <div class="form-group">
                      <h2>FOR OFFICE USE ONLY</h2>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                    </div>
                    
                    <div class="form-group">
                      <label>General Remarks
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <textarea name="general_remarks" class="form-control required_field">{{old('general_remarks')}}</textarea>
                      @if($errors->has('general_remarks'))
                      <span class="error">{{$errors->first('')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Was the DEMO Class Shown ?
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="was_show_demo_class" class="form-control required_field" value="{{old('was_show_demo_class')}}">
                      @if($errors->has('was_show_demo_class'))
                      <span class="error">{{$errors->first('was_show_demo_class')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>If Yes, then by whom ?
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="whom_show_demo_class" class="form-control required_field" value="{{old('whom_show_demo_class')}}">
                      @if($errors->has('whom_show_demo_class'))
                      <span class="error">{{$errors->first('whom_show_demo_class')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>If no, then when expected to see the DEMO Class?
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="text" name="when_show_demo_class" class="form-control required_field" value="{{old('when_show_demo_class')}}">
                      @if($errors->has('when_show_demo_class'))
                      <span class="error">{{$errors->first('when_show_demo_class')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Date of Registration
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="date" name="date_of_reg" class="form-control required_field" value="{{old('date_of_reg')}}">
                      @if($errors->has('Date of Registration'))
                      <span class="error">{{$errors->first('Date of Registration')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                        <label>Status
                          <!-- <span class="required_field">*</span> -->
                        </label>
                    <select class="form-control" name="status">
                        <option value="">select</option>
                        <option value="1">Hot</option>
                        <option value="2">Warm</option>
                        <option value="3">Cold</option>
                    </select>
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
