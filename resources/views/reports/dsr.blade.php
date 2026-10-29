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

          @if(Auth::guard()->user()->is_admin == 1)
          <div class="common-frm-strct mainform">
            <form action="{{route('reports.dsr')}}" method="GET">
              <div class="row">
                

                <div class="col-xs-12 col-md-3 col-sm-6">
                    <div class="form-group">
                        <label>Centre</label>
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

                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                  <div class="form-group">
                    <label></label>
                    <input type="submit" value="Submit" class="add-new-strc-btn" style="color:black;">
                    <a href="{{route('reports.dsr')}}" class="reset">Reset</a>
                  </div>
                </div>
              </div>
              
             </form>
          </div>
          @endif

          @if(!in_array('counselor', $roleArray))
          @if(!$applications->isEmpty())
            <div class="add-new-strc">
              <a class="add-new-strc-btn" href="{{route('dsr-export',['search_by_center'=>$request->search_by_center])}}">Export to Excel</a>
            </div>
          @endif
		@endif

          <div class="relg-list">
            <div class="table-responsive">
              <p><strong>Total Result - {{@$applications->count()}}</strong></p>
              <table class="listingtable">
                <tr>
                  <th width="20%">Registration No</th>
                  <th width="20%">Application No</th>
                  <th>Student Name</th>
                  <th>Centre Name</th>
                  <th>Course Name</th>
                  <th>Gross Fee</th>
                  <th>Scholarship</th>
                  <th>Admission Fee</th>
                  <th>Nett Fees</th>
                  <th>GST</th>              
                </tr>
                @php $app_total = 0; $sl_no=1; @endphp
                  @forelse($applications as $application)
                  <tr> 
                    <td width="20%">{{@$application->regDetails->roll_no}}</td>
                    <td width="20%">{{@$application->application_id}}</td>
                    <td>{{@$application->regDetails->first_name}} {{@$application->regDetails->mid_name}} {{@$application->regDetails->last_name}}</td>
                    <td>{{App\Helpers\Helper::getCenterName(@$application->regDetails->center_name)}}</td>
                    <td>{{App\Helpers\Helper::getCourseName(@$application->course)}}</td>
                    <td>{{@$application->course_fee}}</td>
                    <td>{{@$application->scholarship}}</td>
                    <td>{{@$application->admission_fee}}</td>
                    <td>{{@$application->course_fee - @$application->scholarship}}</td>
                    <td>{{@$application->gst_amount}}</td>
                  </tr>
                  @empty  
                  <tr>
                    <td colspan="8" style="text-align: center;">No data found</td>
                  </tr>                
                  @endforelse
                 
                
              </table>
            </div>
          </div>
          

        </div>
  </div>
    
</div>
@endsection  
