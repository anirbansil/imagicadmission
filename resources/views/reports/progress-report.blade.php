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
                <div class="filter_div row ">
                    <div class="col-lg-12 col-md-5 col-sm-5">
                        <div id="chart_div"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-materials">
       
          <div class="relg-new-struct-add">
            <div class="row">
            <div class="col">
                <a class="add-new-strc-btn" href="{{route('student.marks.export')}}">Export</a>
            </div>
            </div>
            <div class="relg-list">
              <div class="table-responsive">
                <table class="listingtable">
                    <thead>
                  <tr>
                    <th>Sl No</th>
                    <th>Student Name</th>
                    <th>Course Name</th>
                    <th>Progress Report</th>
                  </tr>
                  </thead>
                  <tbody>
                  @php $a=($studentsMarks->currentpage()-1)* $studentsMarks->perpage() + 1; @endphp
                    @forelse($studentsMarks as $stdm)
                    <tr>
                        <td>{{$a}}</td>
                        <td>{{optional(optional($stdm->student)->regDetails)->first_name}} &nbsp; {{optional(optional($stdm->student)->regDetails)->mid_name}} &nbsp; {{optional(optional($stdm->student)->regDetails)->last_name}}</td>
                        <td>{{optional($stdm->courseName)->name}}</td>
                        <td>
                        <table>
                            @php
                            $modules = explode(",",optional($stdm->courseName)->module)
                            @endphp
                                <tr>
                                    @foreach($modules as $module)
                                    <th>{{$module}}</th>
                                    @endforeach
                                </tr>
                            <tr>
                                @foreach($modules as $mdl)
                                <td>{{@App\Helpers\Helper::marksBymodule($stdm->stdnt_id,$mdl)->marks}}</td>
                                @endforeach
                            </tr>
                            </table>
                            Total Marks : {{$stdm->total_marks}}
                        </td>
                    </tr>
                    @php $a++; @endphp
                    @empty
                    <tr><td colspan="6">No Data </td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            
          </div>
        </div>
      </div>
  </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Student Name');
        data.addColumn('number', 'Student Total Marks');
        // Populate data from the variable passed by the controller

        @foreach($studentsMarks as $item)
                data.addRow(['{{ $item->student->regDetails->first_name }}',{{ $item->total_marks }}]);            
        @endforeach

        var options = {
            title: 'Student Progress CHart ',
        };
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>
@endsection

