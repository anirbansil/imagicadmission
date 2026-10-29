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

          <form role="form" action="" method="GET" enctype="multipart/form-data">
            <div class="mainform cmnpadding "> 
              <div class="form-inline">
                <div class="form-group">
                  <label>Search by Roll / Mobile No
                    <span class="required_field">*</span>
                  </label>
                  <input type="text" name="search" value="{{@$_GET['search']}}">
                  @if($errors->has('search'))
                  <span class="error">{{$errors->first('search')}}</span>
                  @endif

                  @if(@$_GET['search'])
                    @if(empty($registrations))
                    <span class="error">No data found</span>
                    @endif
                  @endif
                </div>
                <div class="form-group">
                  <label>&nbsp;</label>
                  <input type="submit" value="Search">
                </div>
                <div class="form-group"></div>
              </div>
            </div>
          </form>

          @if(@$_GET['search'])
            @if(!empty($registrations))

            <h2>Fill Up Application Form for Candidate</h2>

            <div class="table_format">
              <table width="100%" border="0" align="center" cellpadding="5" cellspacing="1" class="enquiry_table2">
                <tbody>
                  <tr>
                    <td width="50%" align="left" valign="middle"><span class="lebel">Center Name :</span>
                    {{App\Helpers\Helper::getCenterName(@$registrations->center_name)}}</td>
                    <td width="50%" align="left" valign="middle"><span class="lebel">Center Address :</span>
                    {{App\Helpers\Helper::getCenterName(@$registrations->center_name)}}</td>
                  </tr>
                  <tr>
                    <td width="50%" align="left" valign="middle"><span class="lebel">Roll No :</span>
                      {{@$registrations->roll_no}}</td>
                    <td colspan="2" width="100%" align="left" valign="middle"><span class="lebel">Name :</span> 
                    {{@$registrations->mr_mrs}} {{@$registrations->first_name}} {{@$registrations->mid_name}} {{@$registrations->last_name}}</td>
                  </tr>
                  <tr>
                    <td width="50%" align="left" valign="middle"><span class="lebel">Residential Address :</span>{{@$registrations->res_address}}</td>
                    <td width="50%" align="left" valign="middle"><span class="lebel">State  :</span>
                      {{@$registrations->state}}</td>
                  </tr>
                  <tr>
                    <td width="50%" align="left" valign="middle"><span class="lebel">City :</span>
                      {{@$registrations->city}}</td>
                    <td width="50%" align="left" valign="middle"><span class="lebel">District :</span>
                      {{@$registrations->district}}</td>
                  </tr>
                  <tr>
                    <td width="50%" align="left" valign="middle"><span class="lebel">Pin Code :</span>
                      {{@$registrations->pin_code}}</td>
                    <td width="50%" align="left" valign="middle"><span class="lebel">Birth Date:</span>
                      {{@$registrations->date_of_birth}}</td>
                  </tr>
                  <tr>
                    <td align="left" valign="middle"><span class="lebel">Mobile No:</span>
                      {{@$registrations->mobile_no}}</td>
                    <td align="left" valign="middle"><span class="lebel">Email ID:</span>
                      {{@$registrations->email}}</td>
                  </tr>
                  <tr>
                    <td width="50%" align="left" valign="middle"><span class="lebel">E-Qualification:</span>
                      {{@$registrations->qualification}}</td>
                      <td width="50%" align="left" valign="middle"><span class="lebel">Student Photo:</span>
                      @if($registrations->student_photo)
                      <img src="{{asset('public/images')}}/{{$registrations->student_photo}}" width="100px;" height="25px;">
                      @endif
                      </td>
                  </tr>
                  <tr>
                    <td width="50%" align="left" valign="middle"><span class="lebel">Guardian's Name:</span>
                      {{@$registrations->guardian_name}}</td>
                      <td width="50%" align="left" valign="middle"><span class="lebel">Guardian's Mobile No:</span>
                      {{@$registrations->guardian_mob_no}}</td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center" valign="middle"><span class=""><strong>Yes, I am interested in joining IMAGIC and prefer below mentioned course. I have understood the rules &amp; regulations, terms &amp; conditions of this institute. I hereby agree and accept the same and confirm to follow it. All information provided by me is true to my knowledge.</strong> </span></td>
                  </tr>
                  <div class="clear"></div>
                  </tbody>
                </table>
                
            </div>

            <div class="dashboard-materials">
              <div class="newform">  
                <div class="mainform cmnpadding ">   
                   <form role="form" action="{{ route('application-form.store') }}" method="POST" enctype="multipart/form-data">
                   @csrf
                   <input type="hidden" name="reg_id" value="{{$registrations->id}}">
                    <div class="form-inline">                      
                      <div class="form-group">
                        <label>Course
                          <span class="required_field">*</span>
                        </label>
                        <select name="course" class="form-control required_field" id="application_course">
                          <option value="">Select</option>
                          @foreach($corses as $corse)
                            <option value="{{$corse->id}}" {{old('course')==$corse->id ?'selected':''}}>{{$corse->name}}</option>
                          @endforeach
                        </select>
                        @if($errors->has('course'))
                        <span class="error">{{$errors->first('course')}}</span>
                        @endif
                      </div>
                      <!-- <div class="form-group"></div>
                      <div class="form-group"></div>  -->
                      
                      <div class="form-group">
                        <label>Batch Start Date
                          <span class="required_field">*</span>
                        </label>
                        <input type="date" name="batch_start_date" id="batch_start_date" class="form-control required_field" value="{{old('batch_start_date')}}">
                        @if($errors->has('batch_start_date'))
                        <span class="error">{{$errors->first('batch_start_date')}}</span>
                        @endif
                        <span id="batch_start_date_error"></span>
                      </div>
                      <div class="form-group">
                        <label>Batch End Date
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="batch_end_date" id="batch_end_date" class="form-control required_field" placeholder="mm/dd/yyyy" value="{{old('batch_end_date')}}" readonly>
                        @if($errors->has('batch_end_date'))
                        <span class="error">{{$errors->first('batch_end_date')}}</span>
                        @endif
                      </div>
                      <div class="form-group batch_time">
                        <label>Batch Time
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="batch_time" class="form-control required_field batch_time_clck" value="{{old('batch_time')}}">
                        <select name="timeunit" class="batch_time_am">
                          <option value="AM">AM</option>
                          <option value="PM">PM</option>
                        </select>
                        @if($errors->has('batch_time'))
                        <span class="error">{{$errors->first('batch_time')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Course Fee
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="course_fee" id="course_fee" class="form-control required_field" value="{{old('course_fee')}}" readonly>
                        @if($errors->has('course_fee'))
                        <span class="error">{{$errors->first('course_fee')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Scholarship
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="scholarship" id="scholarship" class="form-control required_field" value="{{old('scholarship') ? old('scholarship') : 0}}">
                        @if($errors->has('scholarship'))
                        <span class="error">{{$errors->first('scholarship')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Admission Fee
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="admission_fee" id="admission_fee" class="form-control required_field" value="{{old('admission_fee') ? old('admission_fee') : 0}}" readonly>
                        @if($errors->has('admission_fee'))
                        <span class="error">{{$errors->first('admission_fee')}}</span>
                        @endif

                      </div>
                      <input type="hidden" name="min_admission_fee" id="min_admission_fee" value="">
                      <div class="form-group">
                        <label>Pay on Admission
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="pay_on_admission" id="pay_on_admission" class="form-control required_field" value="{{old('pay_on_admission') ? old('pay_on_admission') : ''}}" placeholder="0">
                        @if($errors->has('pay_on_admission'))
                        <span class="error">{{$errors->first('pay_on_admission')}}</span>
                        @endif
                        <span class="error admission_fee_error"></span>
                      </div>
                      <div class="form-group">
                        <label>Balance Rs
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="balance" id="balance" class="form-control required_field" value="{{old('balance') ? old('balance') : 0}}" readonly>
                        @if($errors->has('balance'))
                        <span class="error">{{$errors->first('balance')}}</span>
                        @endif
                      </div>
                      
                      
                      <div class="form-group">
                        <label>Number of EMI
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="number_of_emi" id="number_of_emi" class="form-control required_field" value="{{old('number_of_emi') ? old('number_of_emi') : 0}}">
                        @if($errors->has('number_of_emi'))
                        <span class="error">{{$errors->first('number_of_emi')}}</span>
                        @endif
                        <span class="error number_of_emi_error"></span>
                      </div>
                      
                      <div class="form-group">
                        <label>GST (18%)
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="gst_amount" id="gst_amount" class="form-control required_field" value="{{old('gst_amount') ? old('gst_amount') : 0}}" readonly>
                        @if($errors->has('gst_amount'))
                        <span class="error">{{$errors->first('gst_amount')}}</span>
                        @endif
                        @if(in_array($registrations->center_name, $gst_centers))
                        <input type="hidden" name="is_gst" id="is_gst" value="1">
                        @else
                        <input type="hidden" name="is_gst" id="is_gst" value="0">
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Net Total
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="net_total" id="net_total" class="form-control required_field" value="{{old('net_total') ? old('net_total') : 0}}" readonly>
                        @if($errors->has('net_total'))
                        <span class="error">{{$errors->first('net_total')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>EMI Rs
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="emi" id="emi" class="form-control required_field" value="{{old('emi') ? old('emi') : ''}}" placeholder="0">
                        @if($errors->has('emi'))
                        <span class="error">{{$errors->first('emi')}}</span>
                        @endif
                        <!-- <span class="error_msg" style="color: red;">{{$errors->first('emi')}}</span> -->
                      </div>
                      <div class="form-group">
                        <label>Job Required
                          <span class="required_field">*</span>
                        </label>
                        <select name="job_required">
                          <option value="">Select</option>
                          <option value="Yes" {{old('job_required')=='Yes' ? 'selected':''}}>Yes</option>
                          <option value="No" {{old('job_required')=='No' ? 'selected':''}}>No</option>
                        </select>
                        @if($errors->has('job_required'))
                        <span class="error">{{$errors->first('job_required')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Course Fee By Word
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="course_by_word" id="course_by_word" class="form-control required_field" value="{{old('course_by_word')}}" readonly>
                        @if($errors->has('course_by_word'))
                        <span class="error">{{$errors->first('course_by_word')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>EMI Date
                          <span class="required_field">*</span>
                        </label>
                        <input type="date" name="emi_date" id="emi_date" class="form-control required_field" value="{{old('emi_date')}}">
                        @if($errors->has('emi_date'))
                        <span class="error">{{$errors->first('emi_date')}}</span>
                        @endif
                      </div>                     

                    </div>

                    <h2>Details of Pending Payments.</h2>
                    <div class="form-inline details_of_payment">
                      <div class="five_column">
                        <div class="form-group">
                          <label>No.
                          </label>
                        </div>
                        <div class="form-group">
                          <label>Date
                          </label>
                        </div>
                        <div class="form-group">
                          <label>Payment Type
                          </label>
                        </div>
                        <div class="form-group">
                          <label>Payment Details
                          </label>
                        </div>
                        <div class="form-group">
                          <label>Amount
                          </label>
                        </div>
                      </div>
                      <div class="five_column_append"></div>
                    </div>
                    <!-- <h2>Details of Pending Payments</h2> -->
                    <div class="form-inline">
                      <div class="form-group">
                        <label>Faculty
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <select name="faculty_id" id="faculty_id">
                          <option value="">Select</option>
                          @foreach($faculties as $faculty)
                          <option value="{{@$faculty->id}}">{{@$faculty->surname}} {{@$faculty->name}}</option>
                          @endforeach
                        </select>
                        @if($errors->has('faculty_id'))
                        <span class="error">{{$errors->first('faculty_id')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="form-group">
                      <textarea class="form-control" name="remarks" placeholder="Any Special Instructions / Remarks"></textarea>
                    </div> 
                    <div class="form-group">
                      <label>Details of Payment Received at the time of Admission</label>
                      <input type="radio" name="paid_by" value="cash" checked>Cash
                      <input type="radio" name="paid_by" value="cheque">By Cheque
                      <input type="radio" name="paid_by" value="online">Online
                    </div>

                    <div class="three_field cheque_fields form-inline">
                      <div class="form-group">
                        <label>Payment Details
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <input type="text" name="cheque_no" id="cheque_no" class="form-control required_field" value="{{old('cheque_no') ? old('cheque_no') : ''}}" >
                        @if($errors->has('cheque_no'))
                        <span class="error">{{$errors->first('cheque_no')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Dated
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <input type="date" name="cheque_date" id="cheque_date" class="form-control required_field" value="{{old('cheque__date')}}" >
                        @if($errors->has('cheque__date'))
                        <span class="error">{{$errors->first('cheque__date')}}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Drawn on
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <input type="text" name="cheque_dwawn" id="cheque_dwawn" class="form-control required_field" value="{{old('cheque_dwawn') ? old('cheque_dwawn') : ''}}" >
                        @if($errors->has('cheque_dwawn'))
                        <span class="error">{{$errors->first('cheque_dwawn')}}</span>
                        @endif
                      </div>
                    </div>

                    <div class="online_field form-inline">
                      <div class="form-group">
                        <label>Payment Number
                          <!-- <span class="required_field">*</span> -->
                        </label>
                        <input type="text" name="online_pay_number" id="online_pay_number" class="form-control required_field" value="{{old('online_pay_number') ? old('online_pay_number') : ''}}" >
                        @if($errors->has('online_pay_number'))
                        <span class="error">{{$errors->first('online_pay_number')}}</span>
                        @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <input type="submit" value="Save" class="finalsubmit" id="application_submit">
                    </div>
                   </form>
                </div> 
              </div> 
            </div>
            @endif
          @endif

        </div>
  </div>
    
</div>
@endsection  
