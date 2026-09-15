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
          
          <form action="{{ route('students-list.index') }}" method="get">
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
                                <option value="">select centre</option>
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
                          <select class="form-control" name="search_by_faculty">
                            <option value="">select Faculty</option>
                            @foreach ($faculties as $faculti)
                            <option value="{{ $faculti->id }}" @if (!empty($request->search_by_faculty) && $request->search_by_faculty == $faculti->id) selected @endif>{{ $faculti->name }}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-3 col-sm-6">
                        <button type="submit" class="btn btn-info">Search</button>
                        <a href="{{ route('students-list.index') }}" class="reset">Reset</a>

                    </div>
                </div>
            </div>
          </form>

          <div class="relg-list">
            <div class="table-responsive">
              <table class="listingtable">
                <tr>
                  <th>Sl No</th>
                  <th width="20%">Registration No</th>
                  <th>Student Name</th>
                  <th>Centre Name</th>
                  <th>Course Name</th>
                  <th>Qualification</th>
                  <th>Batch Start Date</th>
                  <th>Batch End Date</th>
                  <th>Faculty</th>
                </tr>
                @php $sl_no=1; @endphp
                  @forelse($students as $student)
                  <tr> 
                    <td>{{$sl_no}}</td>
                    <td width="20%">{{@$student->regDetails->roll_no}}</td>
                    <td>{{@$student->regDetails->first_name}} {{@$student->regDetails->mid_name}} {{@$student->regDetails->last_name}}</td>
                    <td>{{App\Helpers\Helper::getCenterName(@$student->regDetails->center_name)}}</td>
                    <td>{{App\Helpers\Helper::getCourseName(@$student->course)}}</td>

                    <td>{{@$student->regDetails->qualification}}</td>
                    <td width="15%">{{ date('d M Y', strtotime(@$student->batch_start_date)) }}</td>
                    <td width="15%">{{ date('d M Y', strtotime(@$student->batch_end_date)) }}</td>
                    <td>{{@App\Helpers\Helper::getFaculty(@$student->faculty_id)->surname}} {{@App\Helpers\Helper::getFaculty(@$student->faculty_id)->name}}</td>
                  </tr>
                  @php $sl_no++; @endphp
                  @empty
                  <tr>
                    <td colspan="9" style="text-align: center;">No data found</td>
                  </tr>
                 @endforelse
                
              </table>
            </div>
          </div>
          <div class="pagination-sec">
            {{$students->withQueryString()->links()}}
          </div>
         </div>
        </div>
      </div>
  </div>
  
</div>

@endsection
