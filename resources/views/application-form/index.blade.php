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
          <div class="dashboard-materials">
            <div class="relg-new-struct-add">
                <form action="{{ route('application-form.index') }}" method="get">
                    <div class="filter_row">
                        <div class="filter_div row">
                            <div class="col-xs-12 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="date" name="from_date"
                                            class="form-control pull-right datepicker" placeholder="From Date"
                                            value="{{ !empty($request->from_date) ? $request->from_date : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="date" name="to_date" class="form-control pull-right datepicker"
                                            placeholder="To Date"
                                            value="{{ !empty($request->to_date) ? $request->to_date : '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <select class="form-control" name="search_by_center">
                                        @if(Auth::guard()->user()->is_admin == 1)
                                        <option value="">select centre</option>
                                        @endif  
                                        @foreach ($centerDetails as $center)
                                            <option value="{{ $center->id }}"
                                                @if (!empty($request->search_by_center) && $request->search_by_center == $center->id) selected @endif>
                                                {{ $center->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3 col-sm-6">
                                <div class="form-group">
                            <select class="form-control" name="search_by_course">
                                <option value="">select course</option>
                                @foreach ($courseDetails as $course)
                                    <option value="{{ $course->id }}"
                                        @if (!empty($request->search_by_course) && $request->search_by_course == $course->id) selected @endif>
                                        {{ $course->name }}</option>
                                @endforeach
                            </select>
                                </div>
                            </div>


                            <div class="col-xs-12 col-md-12 col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="input_search"
                                        placeholder="Search by Reg No., App No., student name, qualification"
                                        value="{{ !empty($request->input_search) ? $request->input_search : '' }}">
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-3 col-sm-6">
                                <button type="submit" class="btn btn-info">Search</button>
                                <a href="{{ route('application-form.index') }}" class="reset">Reset</a>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
          </div>

        <div class="relg-new-struct-add">
          <div class="add-new-strc">
              <a class="add-new-strc-btn" href="{{ route('application-form.create') }}">Add Application</a>
          </div>
        </div>
          <div class="relg-list">
            <div class="table-responsive">
              <table class="listingtable">
                <tr>
                  <th width="20%">Registration No</th>
                  <th width="20%">Application No</th>
                  <th>Student Name</th>
                  <th>Centre Name</th>
                  <th>Course Name</th>
                  <th>Qualification</th>
                  <th>Date of Registration </th>
                  <th>Receipt</th>
                  <th>Print</th>
                  <th>Faculty PDF</th>
                  @if(Auth::guard()->user()->is_admin == 1)
                  <th>Action</th>
                  @endif
                </tr>
                @php $sl_no=1; @endphp
                  @forelse($applications as $application)
                  <tr>
                    <td width="20%">{{@$application->regDetails->roll_no}}</td>
                    <td width="20%">{{@$application->application_id}}</td>
                    <td>{{@$application->regDetails->first_name}} {{@$application->regDetails->mid_name}} {{@$application->regDetails->last_name}}</td>
                    <td>{{App\Helpers\Helper::getCenterName(@$application->regDetails->center_name)}}</td>
                    <td>{{App\Helpers\Helper::getCourseName(@$application->course)}}</td>

                    <td>{{@$application->regDetails->qualification}}</td>
                    <td>{{ date('d M Y', strtotime(@$application->regDetails->created_at)) }}</td>
                    <td>
                      <a href="{{route('pdf.applicationreceipt', ['id'=>$application->id])}}" target="blank" class="edit">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                      </a>
                    </td>
                    <td>
                      <a href="{{route('pdf.applicationform', ['id'=>$application->id])}}" target="blank" class="edit">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                      </a>
                    </td>
                    <td>
                      <a href="{{route('pdf.applicationfaculty', ['id'=>$application->id])}}" target="blank" class="edit">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                      </a>
                    </td>
                    <td>
                      
                    @if(Auth::guard()->user()->is_admin == 1) 

                    <a class="edit" href="{{route('application-form.edit', $application->id)}}"><img src="{{asset('public/images/draw.png')}}"></a>
                     
                    @php
                      $deleteroute = route('application-form.delete', ['id'=>$application->id]);
                    @endphp
                    <a href="javascript:void(0)" onclick="deleteData('{{$deleteroute}}','{{csrf_token()}}')" class="delete">
                      <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </a>
                    @endif
                      
                    </td>
                  </tr>
                  @php $sl_no++; @endphp
                  @empty
                  <tr>
                    <td colspan="9" style="text-align: center;">No application available</td>
                  </tr>
                 @endforelse

              </table>
            </div>
          </div>
          <div class="pagination-sec">
            {{$applications->links()}}
          </div>

        </div>
  </div>

</div>
@endsection
