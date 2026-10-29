@extends('layout')
@section('title', $title)
@section('content')

    <div id="main" class="admin_main">
        <div class="admin-inn-contents">
            <div class="container-fluid">
                <h1 class="main-title">{{ $title }}
                    <nav aria-label="breadcrumb">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="fa fa-home"
                                        aria-hidden="true"></i>Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ul>

                    </nav>
                </h1>
                @if (Session::has('msg'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('msg') }}
                    </div>
                @elseif(Session::has('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"
                            aria-hidden="true">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif
                <div class="dashboard-materials">
                    <div class="relg-new-struct-add">
                        <form action="{{route('inquiry-form.index')}}" method="get">
                            <div class="filter_row">
                              <div class="filter_div row">
                                <div class="col-xs-12 col-md-2 col-sm-6">
                                  <div class="form-group">
                                    <div class="input-group date">
                                      <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                      </div>
                                      <input type="date" name="from_date" class="form-control pull-right datepicker" placeholder="From Date" value="{{!empty($request->from_date)? $request->from_date: '' }}">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 col-md-2 col-sm-6">
                                  <div class="form-group">
                                    <div class="input-group date">
                                      <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                      </div>
                                      <input type="date" name="to_date" class="form-control pull-right datepicker" placeholder="To Date" value="{{!empty($request->to_date)?$request->to_date: '' }}">
                                    </div>
                                  </div>
                                </div>


                                <div class="col-xs-12 col-md-3 col-sm-6">
                                    <div class="form-group">
                                <select class="form-control" name="search_by_center">
                                    <option value="">select centre</option>
                                    @foreach ($centerDetails as $center)
                                    <option value="{{$center->id}}" {{Auth::guard()->user()->branch_id==$center->id ?'selected':''}} @if(!empty($request->search_by_center) && $request->search_by_center == $center->id) selected @endif>{{$center->name}}</option>
                                    @endforeach
                                </select>
                                    </div>
                                </div>
                                 @php 
                                           $today = today();
                                           $year = \Carbon\Carbon::parse($today)->format('Y');
                                       @endphp
                                <div class="col-xs-12 col-md-3 col-sm-6">
                                    <div class="form-group">
                                    <select class="form-control" name="search_by_year">
                                            <option value="">select Year</option>
                                             @for ($x = $year; $x >= 2021; $x--)
                                             <option value="{{$x}}" @if(!empty($request->search_by_year) && $request->search_by_year == $x) selected @endif>{{$x}}</option>
                                             @endfor
                                          </select>
                                    </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <select class="form-control" name="search_by_course">
                                            <option value="">All courses</option>
                                            @foreach ($courseDetails as $course)
                                                <option value="{{ $course->id }}"
                                                    @if (!empty($request->search_by_course) && $request->search_by_course == $course->id) selected @endif>
                                                    {{ $course->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <select class="form-control" name="status">
                                            <option value="">Select Status</option>
                                            <option value="1" @if(!empty($request->status) && $request->status == 1) selected @endif>Hot</option>
                                            <option value="2" @if(!empty($request->status) && $request->status == 2) selected @endif>Warm</option>
                                            <option value="3" @if(!empty($request->status) && $request->status == 3) selected @endif>Cold</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4 col-sm-6">
                                    <div class="form-group">
                                      <input type="text" class="form-control" name="serial_no" placeholder="Search by serial no." value="{{!empty($request->serial_no)?$request->serial_no: '' }}">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-6">
                                    <div class="form-group">
                                      <input type="text" class="form-control" name="input_search" placeholder="Search by student name, phone number, qualification" value="{{!empty($request->input_search)?$request->input_search: '' }}">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3 col-sm-6">
                                  <button type="submit" class="btn btn-info">Search</button>
                                  <a href="{{route('inquiry-form.index')}}" class="reset">Reset</a>

                                </div>
                              </div>
                            </div>
                          </form>
                    </div>
                </div>
                
                <div class="dashboard-materials">
                    <div class="relg-new-struct-add">
                      @if(!in_array('counselor', $roleArray))
                        @if(!$inquires->isEmpty())
                            <div class="add-new-strc">
                              <a class="add-new-strc-btn" href="{{route('inquiry-export',['from_date'=>$request->from_date,'to_date'=>$request->to_date,'dashboard'=>$request->dashboard,'search_by_year'=>$request->search_by_year,'search_by_center'=>$request->search_by_center,'search_by_course'=>$request->search_by_course,'status'=>$request->status])}}">Export to Excel</a>
                            </div>
                        @endif
						@endif
                        <br>
                        <div class="add-new-strc">
                            <a class="add-new-strc-btn" href="{{ route('inquiry-form.create') }}">Add Inquiry</a>
                        </div>

                        <div class="relg-list">
                            <div class="table-responsive">
                                <table class="listingtable">
                                    <tr>
                                        <th width="15%">Serial No</th>
                                        <th>Student Name</th>
                                        <th>Phone Number</th>
                                        <th>Centre Name</th>
                                        <th>Course Name</th>                                        
                                        <th>Date of Inquiry </th>
                                        <th>Action</th>
                                    </tr>
                                    @php $sl_no=1; @endphp
                                    @forelse($inquires as $inquire)
                                        <tr>
                                            <td width="15%">{{ @$inquire->serial_no }}</td>
                                            <td>{{ @$inquire->first_name }} {{ @$inquire->mid_name }}
                                                {{ @$inquire->last_name }}</td>
                                            <td>{{ @$inquire->mobile_no }}</td>
                                            <td>{{ @$inquire->center_name ? App\Helpers\Helper::getCenterName(@$inquire->center_name) : '' }}</td>
                                            <td>{{ @$inquire->course ? App\Helpers\Helper::getCourseName(@$inquire->course) : '' }}</td>
                                            <td>{{ date('d M Y', strtotime(@$inquire->created_at)) }}</td>
                                            <td>
                                                <a href="{{ route('inquiry-form.edit', ['id' => $inquire->id]) }}"
                                                    class="edit">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                                @php
                                                    $deleteroute = route('inquiry-form.delete', ['id' => $inquire->id]);
                                                @endphp
                                                <a href="javascript:void(0)"
                                                    onclick="deleteData('{{ $deleteroute }}','{{ csrf_token() }}')"
                                                    class="delete">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
                                                @if(!$inquire->regDetails)
                                                <a href="{{ route('registration-form.create', ['search' => $inquire->serial_no]) }}"
                                                    class="edit">
                                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $sl_no++; @endphp
                                    @empty
                                        <tr>
                                            <td colspan="9" style="text-align: center;">No inquiry available</td>
                                        </tr>
                                    @endforelse

                                </table>
                            </div>
                        </div>
                        <div class="pagination-sec">
                            {{ $inquires->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
