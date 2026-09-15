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
        <div class="dashboard-materials">
          <div class="relg-new-struct-add">
            @if(Auth::guard()->user()->is_admin == 1)
              <input type="hidden" name="baseurl" id="baseurl" value="{{  url('/')}}">
                  <form action="{{route('student.marks.index')}}" method="get">
                    <div class="filter_row">
                      <div class="filter_div row">
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
                                  <select name="module_name" class="form-control module_name" id="module_name1" >
                                    <option value="">Select Module</option>
                                    @if(!empty($module))
                                    @foreach($module as $key=>$value)
                                    <option value="{{$value->module_name}}" @if(!empty($request->module_name) && $request->module_name == $value->module_name) selected @endif>{{$value->module_name}}</option>
                                    @endforeach
                                    @endif
                                  </select>
                            </div>
                          </div>
                          <div class="col-xs-12 col-md-3 col-sm-6">
                            <div class="form-group">
                                  <select name="student" class="form-control student_name" id="student_name1" >
                                    <option value="">Select Student</option>
                                   
                                    @if(!empty($students))
                                    @foreach($students as $student)
                                   
                                    <option value="{{$student->id}}"  @if(!empty($request->student) && $request->student == $student->id) selected @endif>{{$student->regDetails->first_name}}&nbsp;{{$student->regDetails->last_name}}</option>
                                    @endforeach
                                    @endif
                                  </select>
                            </div>
                          </div>
                          <div class="col-xs-12 col-md-3 col-sm-6">
                            <button type="submit" class="btn btn-info">Search</button>
                            <a href="{{ route('student.marks.index') }}" class="reset">Reset</a>
                          </div>
                      </div>
                    </div>
                  </form>
                  @endif
                  @if(Auth::guard()->user()->roles[0]->slug != 'student') 
                  <div class="row">
                    <div class="add-new-strc col">
                      <a class="add-new-strc-btn" href="{{route('student.marks.create')}}">Add Marks</a>
                    </div>
                    {{-- <div class="add-new-strc col">
                      <a class="add-new-strc-btn" href="{{route('student.marks.export')}}">Export</a>
                    </div>
                   <div class="add-new-strc col">
                      <a class="add-new-strc-btn" href="{{route('student.progress.report')}}">ProgressReport</a>
                    </div> --}}
                  </div>
           @endif
            <div class="relg-list">
              <div class="table-responsive">
                <table class="listingtable">
                  <tr>
                    <th>Sl No</th>
                    <th>Student Name</th>
                    <th>Course Name</th>
                    <th>Module Name</th>
                    {{--  <th>Sub Module Name</th> --}}
                    <th>Theory Marks</th>
                     <th>Practical Marks</th>
                     <th>Total</th>
                    <th>Created_by</th>
                    <th>Action</th>
                  </tr>
                  @php $a=($marks_list->currentpage()-1)* $marks_list->perpage() + 1; @endphp
                    @forelse($marks_list as $marks)
                <tr>
                    <td>{{$a}}</td>
                    <td>{{$marks->student->regDetails->first_name}} &nbsp; {{$marks->student->regDetails->mid_name}} &nbsp; {{$marks->student->regDetails->last_name}}</td>
                    <td>{{$marks->courseName->name}}</td>
                    <td>{{$marks->module_name}}</td>
                    {{-- <td>{{$marks->sub_module_name}}</td> --}}
                    <td>{{$marks->marks}}</td>
                    <td>{{$marks->practical_marks}}</td>
                    <td>{{$marks->total_marks}}</td>
                    <td>{{$marks->user->name}}</td>
                    <td> @if(Auth::guard()->user()->is_admin == 1) 
                      <a class="edit" href="{{route('student.mark.edit', $marks->id)}}"><img src="{{asset('public/images/draw.png')}}"></a>
                      
                      @php
                        $deleteroute = route('student.progress.delete', ['id'=>$marks->id]);
                      @endphp
                      <a href="javascript:void(0)" onclick="deleteData('{{$deleteroute}}','{{csrf_token()}}')" class="delete">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                      </a>
                      @endif
                     
                    </td>
                </tr>
                @php $a++; @endphp
                @empty
                <tr><td colspan="12">No Data </td></tr>
                @endforelse
                </table>
              </div>
            </div>
            <div class="pagination-sec">
              {{$marks_list->links()}}
            </div>
          </div>
        </div>
        @if(!empty($request->student) && $request->student)
          <div class="dashboard-materials">
            <div class="relg-new-struct-add">
                <div class="filter_div row ">
                    <div class="col-lg-12 col-md-5 col-sm-5">
                        <div id="chart_div"></div>
                    </div>
                </div>
            </div>
          </div>
        @endif
      </div>
  </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Module Name');
        data.addColumn('number', 'Student Total Marks');
        // Populate data from the variable passed by the controller

        @foreach($marks_list as $item)
                data.addRow(['{{ $item->module_name }}',{{ $item->marks }}]);            
        @endforeach

        var options = {
            title: 'Student Progress CHart ',
        };
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>
@endsection
