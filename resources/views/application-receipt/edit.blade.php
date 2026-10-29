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
            <div class="newform">  
              <div class="mainform cmnpadding ">  
                 
                 <form role="form" action="{{ route('application-receipt.adminupdate',$apllication->id) }}" method="POST" enctype="multipart/form-data">
                 @csrf
                
                  <div class="form-inline details_of_payment">
                    
                    <div class="five_column_append">
                      @php $con = 1; @endphp
                      @foreach($apllication->applicationEmi as $emi)
                      @if($request->emi_id == $emi->id) 
                      <input type="hidden" name="ids[]" id="emiid" value="{{$emi->id}}">                   
                      <div class="five_column six_column">
                        <input type="hidden" name="ids[]" value="{{$emi->id}}">
                        <div class="form-group">
                          <label>No.</label>
                          <input type="text" name="sl_no[]" id="sl_no" class="form-control required_field sl_no" value="{{$con}}" readonly>
                        </div>
                        <div class="form-group">
                          <label>Date</label>
                          <input type="date" name="emi_dates[]" id="emi_dates" class="form-control required_field emi_dates" value="{{$emi->emi_dates}}" readonly>
                        </div>
                        <div class="form-group">
                          <label>Payment Type</label>
                          <select name="emi_payment_type[]" class="form-control required_field">
                            <option value=" ">Select</option>
                            <option value="Cash" {{$emi->payment_type == 'Cash' ? 'Selected' : ''}}>Cash</option>
                            <option value="Cheque" {{$emi->payment_type == 'Cheque' ? 'Selected' : ''}}>Cheque</option>
                            <option value="Online" {{$emi->payment_type == 'Online' ? 'Selected' : ''}}>Online</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label>Payment Details</label>
                          <input type="text" name="cheque_no[]" id="cheque_no" class="form-control required_field cheque_no" value="{{$emi->cheque_no}}">
                        </div>
                        <div class="form-group">
                          <label>Extra Amount</label>
                          <select name="extra_amount_add" id="extra_amount_add" class="form-control required_field">
                            <option value="">Select</option>
                            <option value="on" {{$emi->extra_amount_add == 'on' ? 'Selected' : ''}}>On</option>
                            <option value="off" {{$emi->extra_amount_add == 'off' ? 'Selected' : ''}}>Off</option>
                          </select>
                        </div>
                          @php
                            $current_date = date("Y-m-d");
                            $ammount = $emi->emi_amounts * 100;
                          @endphp
                          @if($emi->emi_dates < $current_date && $emi->extra_amount_add != 'off' )
                            @php
                              $now = time(); 
                              $your_date = strtotime("+1 day", strtotime($emi->emi_dates));
                              $datediff = $now - $your_date;
                              $total = $emi->emi_amounts + (round($datediff / (60 * 60 * 24)) * 50);
                              $ammount = $total * 100;
                            @endphp 

                          <div class="form-group">
                            <input type="hidden" name="extra_amount" value="{{round($datediff / (60 * 60 * 24)) * 50}}"> 
                            <label>Total Amount</label>                          
                            <input type="text" name="emi_amounts[]" id="emi_amounts" class="form-control required_field emi_amounts" value="{{$total}}" readonly>
                          </div>
                          @else
                          <div class="form-group">
                            <input type="hidden" name="extra_amount" value="0">
                            <label>Total Amount</label>
                            <input type="text" name="emi_amounts[]" id="emi_amounts" class="form-control required_field emi_amounts" value="{{$emi->emi_amounts}}" readonly>
                          </div>
                          @endif
                          <input type="hidden" name="ammount" value="{{$ammount}}">
                          
                      </div>
                      @endif
                      @php $con++; @endphp
                      @endforeach
                    </div>
                  </div>
                  @if(Auth::guard()->user()->is_admin == 1)
                  <!-- <script src="https://checkout.razorpay.com/v1/checkout.js"
                        data-key="rzp_test_LdMBBcASc883Xj"
                        data-amount="{{$ammount}}"
                        data-name="Imagic"
                        data-description="Razorpay payment"
                        data-image="https://pinealmagic.in/imagik-version2/public/images/logo.png" 
                        data-theme.color="#ff7529">
                  </script> -->
                  @endif
                  <div class="form-group">
                    <label>Remarks</label>
                    <textarea  name="remarks">{{$emi->remarks}}</textarea>
                  </div>
                  <div class="form-group">
                    <input type="submit" value="Update" class="finalsubmit">
                  </div>
                 </form>
              </div> 
            </div> 
          </div>
          
         

        </div>
  </div>
    
</div>
@endsection  
