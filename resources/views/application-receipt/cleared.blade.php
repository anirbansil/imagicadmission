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

          @if(empty($user_app_id))
          <div class="dashboard-materials">
            <div class="relg-new-struct-add">
                <form action="{{route('application-receipt.cleared')}}" method="get">
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

                        <div class="col-xs-12 col-md-3 col-sm-6">
                          <div class="form-group">
                            <div class="input-group date">
                              <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" name="search_by_app_id" class="form-control pull-right datepicker" placeholder="Search by Application ID" value="{{!empty($request->search_by_app_id)? $request->search_by_app_id: '' }}">
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-md-3 col-sm-6">
                          <button type="submit" class="btn btn-info">Search</button>
                          <a href="{{route('application-receipt.cleared')}}" class="reset">Reset</a>

                        </div>
                      </div>
                    </div>
                  </form>
            </div>
          </div>
          @endif
          
          <div class="relg-list">
            <div class="table-responsive">
              <table class="listingtable">
                <tr>
                  <th width="15%">Registration No</th>
                  <th width="15%">Application No</th>
                  <th>Student Name</th>
                  <th>Centre</th>
                  <th>EMI Date</th>
                  <th>EMI Paid Date</th>
                  <th>Payment Type</th>
                  <th>Cheque No.</th>
                  <th>EMI Amount</th>
                  <th>Status</th>
                  <th>Receipt</th>
                </tr>
                @php $sl_no=1; $flag = []; @endphp
                  @forelse($applications_emi as $emi)
                  <tr>
                    <td width="15%">{{@$emi->application->regDetails->roll_no}}</td>
                    <td width="15%">{{@$emi->application->application_id}}</td>
                    <td>{{@$emi->application->regDetails->first_name}} {{@$emi->application->regDetails->mid_name}} {{@$emi->application->regDetails->last_name}}</td>
                    <td>{{App\Helpers\Helper::getCenterName(@$emi->application->regDetails->center_name)}}</td>
                    <td>{{ date('d M Y', strtotime(@$emi->emi_dates)) }}</td>
                    <td>{{ date('d M Y', strtotime(@$emi->update_date)) }}</td>
                    <td>{{@$emi->payment_type}}</td>
                    <td>
                      @if($emi->payment_type != 'Cash')
                            {{$emi->cheque_no}}
                      @endif
                    </td>
                    <td>{{$emi->emi_amounts}}</td>
                    <td>
                      @if(@$emi->payment_type == 'Cash' || @$emi->payment_type == 'Cheque' || @$emi->payment_type == 'Online')
                      Paid
                      @else
                      Pending
                      @endif
                    </td>
                    <td>
                      <a href="{{route('pdf.applicationemireceipt', ['id'=>$emi->id])}}" target="blank" class="edit">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                      </a> 
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="11" style="text-align: center;">No data available</td>
                  </tr>
                 @endforelse
                
              </table>
            </div>
          </div>
          <div class="pagination-sec">
            {{$applications_emi->links()}}
          </div>

        </div>
  </div>
    
</div>
@endsection  
