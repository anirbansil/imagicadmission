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
            <form action="{{route('reports.dcr')}}" method="GET">
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
                    <a href="{{route('reports.dcr')}}" class="reset">Reset</a>
                  </div>
                </div>
              </div>
              
             </form>
          </div>
          @endif

          @if(!$applications->isEmpty() || !$applications_emi->isEmpty())
            <div class="add-new-strc">
              <a class="add-new-strc-btn" href="{{route('dcr-export',['search_by_center'=>$request->search_by_center])}}">Export to Excel</a>
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
                  <th>Payment Type</th>
                  <th>Cheque No.</th>
                  <th>Extra Amount</th>
                  <th>Amount</th>
                  <th>GST</th>                  
                </tr>
                
                @php $app_total = 0; $total_pay_on_admission_gst = 0; $sl_no=1; @endphp
                  @forelse($applications as $application)
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
                    <td>{{@$application->net_total}}</td>
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
                      $total_pay_on_admission_gst = $total_pay_on_admission_gst + $pay_on_admission_gst;
                      $sl_no++; 
                  @endphp
                  @empty                  
                  @endforelse
                 @php $emi_total = 0; $emi_total=0; $gst_total=0; @endphp
                 @forelse($applications_emi as $emi)
                  <tr> 
                    <td width="20%">{{@$emi->application->regDetails->roll_no}}</td>
                    <td width="20%">{{@$emi->application->application_id}}</td>
                    <td>{{@$emi->application->regDetails->first_name}} {{@$emi->application->regDetails->mid_name}} {{@$emi->application->regDetails->last_name}}</td>
                    <td>{{App\Helpers\Helper::getCenterName(@$emi->application->regDetails->center_name)}}</td>
                    <td>{{App\Helpers\Helper::getCourseName(@$emi->application->course)}}</td>
                    <td>{{ date('d M Y', strtotime(@$emi->application->created_at)) }}</td>
                    <td>{{ date('d M Y', strtotime(@$emi->emi_dates)) }}</td>
                    <td>{{@$emi->application->course_fee}}</td>
                    <td>{{@$emi->application->scholarship}}</td>
                    <td>{{@$emi->application->net_total}}</td>
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
                    $emi_total = $emi_total + $emi_amounts; 
                    $gst_total = $gst_total + $gst;
                    $sl_no++; 
                  @endphp
                  @empty                  
                 @endforelse
                <tr>
                  @php 
                  $total = @$app_total + @$emi_total + @$emi->extra_amount;   
                  $total_gst = $total_pay_on_admission_gst + $gst_total;
                  @endphp
                  <td colspan="14" style="text-align: right;">Total - {{@$total}}</td>
                  <td style="text-align: right;"><strong>{{@$total_gst}}</strong></td>
                </tr>
              </table>
            </div>
          </div>
          

        </div>
  </div>
    
</div>
@endsection  
