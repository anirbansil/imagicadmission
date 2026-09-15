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

          <div class="relg-list">
            <div class="table-responsive">
              <table class="listingtable">
                <tr>
                  <th width="15%">Registration No</th>
                  <th width="15%">Application No</th>
                  <th>Student Name</th>
                  <th>PDC Details</th>
                </tr>
                @php $sl_no=1; $flag = []; @endphp
                  @forelse($applications as $application)
                  @if($application->applicationEmi->count() > 0)
                    @foreach($application->applicationEmi as $emi)
                      @php $flag[] = $emi->payment_type; @endphp
                    @endforeach
                  @if(in_array('NULL', $flag) || in_array('', $flag))  
                  <tr> 
                    <td width="15%">{{@$application->regDetails->roll_no}}</td>
                    <td width="15%">{{@$application->application_id}}</td>
                    <td>{{@$application->regDetails->first_name}} {{@$application->regDetails->mid_name}} {{@$application->regDetails->last_name}}</td>
                    <td>
                      <table class="">
                        <tr>
                          <th>SL. No.</th>
                          <th>EMI Date</th>
                          <th>Payment Type</th>
                          <th>Cheque No.</th>
                          <th>EMI Amount</th>
                          <th>Extra Charge</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                        @php $con = 1; @endphp
                        @foreach($application->applicationEmi as $emi) 

                        @if(is_null($emi->payment_type) || $emi->payment_type == '')
                        <tr>
                          <td>{{$con}}</td>
                          <td>{{$emi->emi_dates}}</td>
                          <td>
                            {{@$emi->payment_type}}
                          </td>
                          <td>{{$emi->cheque_no}}</td>
                          <td>{{$emi->emi_amounts}}</td>
                          @php
                            $current_date = date("Y-m-d");
                          @endphp
                          <td>
                            @if($emi->emi_dates < $current_date)
                            @php
                              $now = time(); 
                              $your_date = strtotime($emi->emi_dates);
                              $datediff = $now - $your_date;
                            @endphp
                            {{round($datediff / (60 * 60 * 24)) * 50}}
                            @endif
                          </td>
                          <td>
                            @if(@$emi->payment_type == 'Cash' || @$emi->payment_type == 'Cheque' || @$emi->payment_type == 'Online')
                            Paid
                            @else
                            Pending
                            @endif
                          </td>
                          <td>
                            <a href="{{route('application-receipt.adminupdate', ['id'=>$application->id,'emi_id'=>$emi->id])}}" class="edit">
                              <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                          </td>
                        </tr>
                        @php $con++; @endphp
                        @endif
                        
                        @endforeach
                      </table>
                    </td>
                    <!-- <td>
                      
                      
                    </td> -->
                  </tr>
                  @endif
                  @php $flag = []; @endphp
                  @else
                  <!-- <tr>
                    <td colspan="9" style="text-align: center;">No data available</td>
                  </tr> -->
                  @endif
                  @php $sl_no++; @endphp
                  @empty
                  <tr>
                    <td colspan="9" style="text-align: center;">No data available</td>
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
