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
                <form action="{{route('application-receipt.all')}}" method="get">
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
                              <select class="form-control" name="students_app_id">
                                  <option value="">Select Student</option>
                                  @foreach ($applicant as $students_app_id)
                                  <option value="{{$students_app_id->application_id}}" @if(!empty($request->students_app_id) && $request->students_app_id == $students_app_id->application_id) selected @endif>{{@$students_app_id->regDetails->first_name}} {{@$students_app_id->regDetails->mid_name}} {{@$students_app_id->regDetails->last_name}}</option>
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
                          <a href="{{route('application-receipt.index')}}" class="reset">Reset</a>

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
                  <th>EMI Amount</th>
                  <th>Extra Charge</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                @php $sl_no=1; $flag = []; @endphp
                  @forelse($applications_emi as $emi)
                  <tr class="">
                    <td width="15%">{{@$emi->application->regDetails->roll_no}}</td>
                    <td width="15%">{{@$emi->application->application_id}}</td>
                    <td>{{@$emi->application->regDetails->first_name}} {{@$emi->application->regDetails->mid_name}} {{@$emi->application->regDetails->last_name}}</td>
                    <td>{{App\Helpers\Helper::getCenterName(@$emi->application->regDetails->center_name)}}</td>
                    <td>{{ date('d M Y', strtotime(@$emi->emi_dates)) }}</td>
                    <td>{{$emi->emi_amounts}}</td>
                    @php
                      $current_date = date("Y-m-d");
                      $extra = 0;
                    @endphp
                    <td>
                    @if(@$emi->payment_type == 'Cash' || @$emi->payment_type == 'Cheque' || @$emi->payment_type == 'Online')
                        {{@$emi->extra_amount}}
                    @else
                      @if($emi->extra_amount_add == 'on')
                        @if($emi->emi_dates < $current_date)
                        @php
                          $now = time(); 
                          $your_date = strtotime("+1 day", strtotime($emi->emi_dates));
                          $datediff = $now - $your_date;
                          $extra = round($datediff / (60 * 60 * 24)) * 50;
                        @endphp
                        {{round($datediff / (60 * 60 * 24)) * 50}}
                        @endif
                      @endif
                    @endif
                    </td>
                    <td>
                      @if(@$emi->payment_type == 'Cash' || @$emi->payment_type == 'Cheque' || @$emi->payment_type == 'Online')
                      Paid
                      @else
                      Pending
                      @endif
                    </td>
                    <td style="{{(empty($emi->payment_type) && !empty($user_app_id) && $emi->application->regDetails->center_name == 4) ? 'background: red;' : ''}}">
                        @if(!empty($emi->payment_type) && in_array($emi->payment_type, ['Cash', 'Cheque', 'Online']))
                            <a href="{{ route('pdf.applicationemireceipt', ['id' => $emi->id]) }}" target="_blank" class="edit">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                            </a>
                        @else
                            @if(App\Helpers\Helper::sidenavAccess('receipt_edit'))
                                <a href="{{ route('application-receipt.edit', ['id' => $emi->application_id, 'emi_id' => $emi->id]) }}" class="edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endif
                        @endif
                        
                      <!--@if(empty($user_app_id))-->
                      <!--  @if(App\Helpers\Helper::sidenavAccess('receipt_edit'))-->
                      <!--  <a href="{{route('application-receipt.edit', ['id'=>$emi->application_id,'emi_id'=>$emi->id])}}" class="edit">-->
                      <!--    <i class="fa fa-edit"></i>-->
                      <!--  </a>-->
                      <!--  @endif-->
                      <!--@endif-->

                      @if(!empty($user_app_id) && $emi->application->regDetails->center_name == 4)
                        @if(empty($emi->payment_type))
                          
                          @php 
                            //$ammount = ($emi->emi_amounts + $extra) * 100;
                            $ammount = 1 * 100;
                          @endphp
                            <form role="form" action="{{ route('application-receipt.update',$emi->application_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="ammount" value="{{$ammount}}">
                            <input type="hidden" name="ids[]" value="{{$emi->id}}">
                            <input type="hidden" name="extra_amount" value="{{$extra}}">
                              <script src="https://checkout.razorpay.com/v1/checkout.js"
                                    data-key="rzp_live_G4haDmHJPHcZJE"
                                    data-amount="{{$ammount}}"
                                    data-name="Imagic"
                                    data-description="Razorpay payment"
                                    data-image="https://admission.imagic.net.in/public/images/logo.png" 
                                    data-theme.color="#4B0082">
                              </script>
                            </form>
                          
                        @endif
                      @endif

                      <!--@if(@$emi->payment_type == 'Cash' || @$emi->payment_type == 'Cheque' || @$emi->payment_type == 'Online')-->
                      <!--  <a href="{{route('pdf.applicationemireceipt', ['id'=>$emi->id])}}" target="blank" class="edit">-->
                      <!--    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>-->
                      <!--  </a> -->
                      <!--@endif-->

                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="9" style="text-align: center;">No data available</td>
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
