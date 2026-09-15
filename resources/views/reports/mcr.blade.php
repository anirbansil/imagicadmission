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

          <div class="common-frm-strct mainform">
            <form action="{{route('reports.mcr')}}" method="GET">
              <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                  <div class="form-group">
                    <label>Form Date</label>
                    <input type="date" name="from_date" placeholder="Form Date" value="{{$request->from_date}}">
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                  <div class="form-group">
                    <label>To Date</label>
                    <input type="date" name="to_date" placeholder="To Date" value="{{$request->to_date}}">
                  </div>
                </div>

                <div class="col-xs-12 col-md-3 col-sm-6">
                    <div class="form-group">
                        <label>Centre</label>
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
                
                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                  <div class="form-group">
                    <label>Search by Reg No</label>
                    <input type="text" name="search" placeholder="Search by Reg No" value="{{$request->search}}">
                  </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                  <div class="form-group">
                    <label></label>
                    <input type="submit" value="Submit" class="add-new-strc-btn"  style="color:black;">
                    <a href="{{route('reports.mcr')}}" class="reset">Reset</a>
                  </div>
                </div>
              </div>
              
             </form>
          </div>
          @if(!$applications->isEmpty() || !$applications_emi->isEmpty() )
          <div class="add-new-strc">
            <a class="add-new-strc-btn" href="{{route('mcr-export',['from_date'=>$request->from_date, 'to_date'=>$request->to_date, 'search'=>$request->search, 'search_by_center'=>$request->search_by_center])}}">Export to Excel</a>
          </div>
          @endif

          <div class="relg-list">
            <div class="table-responsive">
              @php $total_result = @$applications->count() + @$applications_emi->count(); @endphp
              <p><strong>Total Result - {{@$total_result}}</strong></p>
              <table class="listingtable">
                <tr>
                  <th width="20%">Registration No</th>
                  <th width="20%">Application No</th>
                  <th>Student Name</th>
                  <th>Centre Name</th>
                  <th>Course Name</th>
                  <th>Admission Date</th>
                  <th>EMI Date</th>
                  <th>Gross Fee</th>
                  <th>Scholarship</th>
                  <th>Nett Fees</th>
                  <th>GST</th>
                  <th>Payment Type</th>
                  <th>Cheque No.</th>
                  <th>Extra Amount</th>
                  <th>Amount</th> 
                  <th>GST</th>                 
                </tr>
                @php $app_total = 0; $app_gst_total = 0; $sl_no=1; $cash = 0; $cheque = 0; @endphp
                  @forelse($applications as $application)
                    
                  @if($application->paid_by == 'Cash')
                    @php $cash = $cash + $application->pay_on_admission; @endphp  
                  @elseif($application->paid_by == 'Cheque')
                    @php $cheque = $cheque + $application->pay_on_admission; @endphp
                  @else
                  @endif
                  <tr> 
                    <td width="20%">{{@$application->regDetails->roll_no}}</td>
                    <td width="20%">{{@$application->application_id}}</td>
                    <td>{{@$application->regDetails->first_name}} {{@$application->regDetails->mid_name}} {{@$application->regDetails->last_name}}</td>
                    <td>{{App\Helpers\Helper::getCenterName(@$application->regDetails->center_name)}}</td>
                    <td>{{App\Helpers\Helper::getCourseName(@$application->course)}}</td>
                    <td>{{ date('d M Y', strtotime(@$application->created_at)) }}</td>
                    <td></td>
                    <td>{{@$application->course_fee}}</td>
                    <td>{{@$application->scholarship}}</td>
                    <td>{{@$application->course_fee - @$application->scholarship}}</td>
                    <td>{{@$application->gst_amount}}</td>
                    <td>{{@$application->paid_by}}</td>
                    <td>{{@$application->cheque_no}}</td>
                    <td></td>
                    @php
                      if($application->gst_amount){
                        $pay_on_admission = number_format((float)$application->pay_on_admission / 118*100, 2, '.', '');
                        $pay_on_admission_gst = number_format((float)$application->pay_on_admission - $pay_on_admission, 2, '.', '');
                      }else{
                        $pay_on_admission = $application->pay_on_admission;
                        $pay_on_admission_gst = 0;
                      }
                    @endphp
                    <td style="text-align: right;">{{@$pay_on_admission}}</td>
                    <td style="text-align: right;">{{@$pay_on_admission_gst}}</td>

                  </tr>
                  @php 
                    $app_total = $app_total + $pay_on_admission; 
                    $app_gst_total = $app_gst_total + $pay_on_admission_gst;
                    $sl_no++; 
                  @endphp
                  @empty                  
                  @endforelse
                 @php $emi_total = 0; $emi_total_gst = 0; $extra = 0; @endphp
                 @forelse($applications_emi as $emi)

                  @if($emi->application)
                    @if($emi->payment_type == 'Cash')
                      @php $cash = $cash + $emi->emi_amounts; @endphp  
                    @elseif($emi->payment_type == 'Cheque')
                      @php $cheque = $cheque + $emi->emi_amounts; @endphp
                    @else
                    @endif
                    <tr> 
                      <td width="20%">{{@$emi->application->regDetails->roll_no}} {{$emi->application->deleted_at}}</td>
                      <td width="20%">{{@$emi->application->application_id}}</td>
                      <td>{{@$emi->application->regDetails->first_name}} {{@$emi->application->regDetails->mid_name}} {{@$emi->application->regDetails->last_name}}</td>
                      <td>{{App\Helpers\Helper::getCenterName(@$emi->application->regDetails->center_name)}}</td>
                      <td>{{App\Helpers\Helper::getCourseName(@$emi->application->course)}}</td>
                      <td>{{ date('d M Y', strtotime(@$emi->application->created_at)) }}</td>
                      <td>{{ date('d M Y', strtotime(@$emi->emi_dates)) }}</td>
                      <td>{{@$emi->application->course_fee}}</td>
                      <td>{{@$emi->application->scholarship}}</td>
                      <td>{{@$emi->application->course_fee - @$emi->application->scholarship}}</td>
                      <td>{{@$emi->application->gst_amount}}</td>
                      <td>{{@$emi->payment_type}}</td>
                      <td>{{@$emi->cheque_no}}</td>
                      <td style="text-align: right;">{{@$emi->extra_amount}}</td>
                      @php
                        if($emi->application->gst_amount){
                          $emi_amounts = number_format((float)$emi->emi_amounts / 118*100, 2, '.', '');
                          $gst = number_format((float)$emi->emi_amounts - $emi_amounts, 2, '.', '');
                        }else{
                          $emi_amounts = $emi->emi_amounts;
                          $gst = 0;
                        }
                      @endphp
                      <td style="text-align: right;">{{@$emi_amounts}}</td>  
                      <td style="text-align: right;">{{@$gst}}</td>                    
                    </tr>
                    @php 
                      $emi_total = @$emi_total + @$emi_amounts + @$emi->extra_amount;
                      $emi_total_gst = @$emi_total_gst + @$gst; 
                      $sl_no++;
                      $extra = $extra + @$emi->extra_amount; 
                    @endphp
                  @endif
                  @empty                  
                 @endforelse
                 @if($applications_emi->isEmpty() && $applications_emi->isEmpty() )
                  <tr>
                    <td colspan="12">No data found</td>
                  </tr>
                 @endif
                
                @php  
                  $total_gst = $app_gst_total + $emi_total_gst;
                  //$total = $app_total + $emi_total + $extra - $total_gst;
                  $total = $app_total + $emi_total + $extra; 
                @endphp 
              </table>
              <br>
              <table class="listingtable">
                <tr>
                  <th>TOTAL COLLECTION</th>
                  <th>CASH COLLECTION</th>
                  <th>CHEQUE/ONLINE COLLECTION</th>
                  <th>FRESH SALES COLLECTION </th>
                  <th>OLD OUTSTANDING COLLECTION</th>
                  <th>EXTRA AMOUNT</th>
                  <th>GST</th>                  
                </tr>
                <tr> 
                  <td>{{number_format((float)@$total, 2, '.', '')}}</td>
                  <td>{{number_format((float)@$cash, 2, '.', '')}}</td>
                  <td>{{number_format((float)@$cheque, 2, '.', '')}}</td>
                  <td>{{number_format((float)@$app_total, 2, '.', '')}}</td>
                  <td>{{number_format((float)@$emi_total, 2, '.', '')}}</td>
                  <td>{{number_format((float)@$extra, 2, '.', '')}}</td>
                  <td>{{number_format((float)@$total_gst, 2, '.', '')}}</td>
                </tr>                  
              </table>
            </div>
          </div>
          

        </div>
  </div>
    
</div>
@endsection  
