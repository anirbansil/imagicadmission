@extends('layout')
@section('title', $title)
@section('content')

<div id="main" class="admin_main">
  <div class="admin-inn-contents">
      <div class="container-fluid">
        <h1 class="main-title">
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
          
        @php 
            $admin = Auth::guard()->user();
            $roleArray = $admin->roles()->pluck('slug')->toArray();
        @endphp
        @if($roleArray[0] == "student")
        <div class="dashboard-materials">
          <div class="relg-new-struct-add mb-4">
            @if(!empty($checkin))
                <form action="{{ route('checkout' ,['id'=>$checkin->id]) }}" method="get">
                <input type="hidden" name="baseurl" id="baseurl" value="{{  url('') }}/">
                    <div class="filter_row">
                        <div class="filter_div row ">
                            <div class="col-xs-12 col-md-4 col-sm-6">
                                <div class="form-group">
                                <select name="course" class="form-control" data-target="1"  id="chkIn_course_name" required disabled><option value="">Select Course</option>
                                    @foreach($courses as $course)
                                    <option value="{{$course->id}}" {{@$checkin->checkout_time == null &&  $checkin->course == $course->id ?'selected':''}}>{{$course->name}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-6">
                                <div class="form-group">
                                <select name="module" class="form-control " id="chkIn_module_name" required  disabled>
                                    <option value="{{@$checkin->checkout_time == null ?$checkin->module:''}}">{{@$checkin->checkout_time == null ?$checkin->module:'Select Module'}}</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-6">
                                <button type="submit" class="btn btn-danger"> Check-out</button>
                                @php
                                $startTime = \Carbon\Carbon::parse($checkin->check_in_time)->format('h:i:s A');
                                $current =\Carbon\Carbon::now('Asia/Kolkata');
                                $endTime = \Carbon\Carbon::parse($current)->format('h:i:s A');
                                @endphp
                                <span id="clock-wrapper" data-value="{{(new \Carbon\Carbon($endTime))->diff(new \Carbon\Carbon($startTime))->format('%h:%I:%s') }}" >{{(new \Carbon\Carbon($endTime))->diff(new \Carbon\Carbon($startTime))->format('%h:%I:%s') }}</span>
                            </div>
                        </div>
                    </div>
                </form>
            @else
            <form action="{{route('checkin' ,['userid'=>Auth::user()->id])}}" method="get">
            <input type="hidden" name="baseurl" id="baseurl" value="{{  url('') }}/">
                <div class="filter_row">
                    <div class="filter_div row ">
                        <div class="col-xs-12 col-md-4 col-sm-6">
                            <div class="form-group">
                            <select name="course" class="form-control" data-target="1"  id="chkIn_course_name" required ><option value="">Select Course</option>
                                @foreach($courses as $course)
                                <option value="{{$course->id}}">{{$course->name}}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-sm-6">
                            <div class="form-group">
                            <select name="module" class="form-control " id="chkIn_module_name" required  >
                                <option value="">Select Module</option>
                            </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-sm-6">
                            <button type="submit" class="btn btn-success"> Check-in </button>
                        </div>
                    </div>
                </div>
            </form>
            @endif
            </div>
         </div> 
        @endif
         <div class="relg-new-struct-add">
            @if(Auth::guard()->user()->is_admin == 1  || $roleArray[0]== "center")
              <input type="hidden" name="baseurl" id="baseurl" value="{{  url('/')}}">
              <form action="{{route('checkin-checkout.list')}}" method="get">
                <div class="filter_row">
                  <div class="filter_div row">
                        <div class="col-xs-12 col-md-4 col-sm-6">
                            <div class="form-group">
                                <select class="form-control" name="search_by_role" id="search_by_role">
                                <option value="">Select User Type</option>
                                    @foreach($users_role as $role)
                                    <option value="{{$role->id}}" {{$request->has('search_by_role') && $request->search_by_role == $role->id ? 'selected': '' }}>{{$role->name}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                             <div class="col-xs-12 col-md-4 col-sm-6">
                            <div class="form-group">
                                <select class="form-control" name="search_by_users" id="search_by_users">
                                <option value="">Select User Type first</option>
                                </select>
                                </div>
                            </div>
                      <div class="col-xs-12 col-md-3 col-sm-6">
                        <div class="form-group">
                            <select name="course_name" class="form-control course_name" data-target="1"  id="course_name1" required><option value="">Select Course</option>
                                @foreach($courses as $course)
                                <option value="{{$course->id}}"  @if (!empty($request->course_name) && $request->course_name == $course->id) selected @endif>{{$course->name}}</option>
                                @endforeach
                              </select>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-3 col-sm-6">
                        <div class="form-group">
                              <select name="module_name" class="form-control module_name" id="module_name1" required="">
                                <option value="">Select Module</option>
                                @if(!empty($module))
                                @foreach($module as $key=>$value)
                                <option value="{{$value->module_name}}"  @if (!empty($request->module_name) && $request->module_name == $value->module_name) selected @endif>{{$value->module_name}}</option>
                                @endforeach
                                @endif
                              </select>
                        </div>
                      </div>
                      <!-- <div class="col-xs-12 col-md-3 col-sm-6">
                        <div class="form-group">
                              <select name="faculaty" class="form-control student_name" id="student_name1" required="">
                                <option value="">Select Student</option>
                              </select>
                        </div>
                      </div> -->
                    <div class="col-xs-12 col-md-3 col-sm-6">
                      <button type="submit" class="btn btn-info">Search</button>
                      <a href="{{route('checkin-checkout.list')}}" class="reset">Reset</a>
                    </div>
                  </div>
                </div>
              </form>
              <div class="row">
                <div class="add-new-strc col">
                  <a class="add-new-strc-btn" href="{{route('checkin-checkout.list.export',['course_name'=>!empty($request->course_name) ? $request->course_name :'' ,'module_name'=>!empty($request->module_name) ? $request->module_name:''])}}">Export</a>
                </div>
                {{-- <div class="add-new-strc col">
                  <a class="add-new-strc-btn" href="javascript:Void(0);">Graph View</a>
                </div>--}}
              </div>
            @endif
          <div class="relg-list">
            <div class="table-responsive">
              <table class="listingtable">
                <tr>
                  <th>Sl No</th>
                  <th>teacher Name </th>
                  <th>Course Name</th>
                  <th>Module Name</th>
                  <th>Date</th>
                  <th>Checkin Time</th>
                  <th>Checkout Time</th>
                  <th>Total Time</th>
                </tr>
                @php $a=($checkin_list->currentpage()-1)* $checkin_list->perpage() + 1; @endphp
                  @forelse($checkin_list as $list)
               <tr>
                <td>{{$a}}</td>
                <td>{{@$list->user->name}}</td>
                <td>{{@$list->courseName->name}}</td>
                <td>{{@$list->module}}</td>
                <td>{{\Carbon\Carbon::parse(@$list->date)->format('d-M-Y')}}</td>
                <td>{{\Carbon\Carbon::parse(@$list->check_in_time)->format('h:i A')}}</td>
                <td>{{@$list->checkout_time != null ? \Carbon\Carbon::parse(@$list->checkout_time)->format('h:i A') : "Didn't Checkout"}}</td>
                <td>{{@$list->checkout_time != null ? (new \Carbon\Carbon(@$list->check_in_time))->diff(new \Carbon\Carbon(@$list->checkout_time))->format('%h:%I:%s') : '00:00'}}</td>
               </tr>
               @php $a++; @endphp
               @empty
               <tr><td colspan="6">No Data </td></tr>
               @endforelse
              </table>
            </div>
          </div>
          <div class="pagination-sec">
            {{$checkin_list->links()}}
          </div>
         </div>
        </div>
      </div>
  </div>
</div>
@endsection
