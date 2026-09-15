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
                
                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                  <div class="form-group">
                    <label>Search by Reg No</label>
                    <input type="text" name="search" placeholder="Search by Reg No" value="{{$request->search}}">
                  </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <input type="submit" value="Submit" class="add-new-strc-btn">
                    <a href="{{route('reports.mcr')}}" class="reset">Reset</a>
                  </div>
                </div>
              </div>
              
             </form>
          </div>
          @if(!$applications->isEmpty() || !$applications_emi->isEmpty() )
          <div class="add-new-strc">
            <a class="add-new-strc-btn" href="{{route('mcr-export',['from_date'=>$request->from_date, 'to_date'=>$request->to_date, 'search'=>$request->search])}}">Export to Excel</a>
          </div>
          @endif

          <div class="relg-list">
            <div class="table-responsive">
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
                </tr>
                @php $app_total = 0; $sl_no=1; $cash = 0; $cheque = 0; @endphp
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
                    <td>{{@$application->created_at}}</td>
                    <td></td>
                    <td>{{@$application->course_fee}}</td>
                    <td>{{@$application->scholarship}}</td>
                    <td>{{@$application->net_total}}</td>
                    <td>{{@$application->paid_by}}</td>
                    <td>{{@$application->cheque_no}}</td><
                    <td></td>
                    <td style="text-align: right;">{{@$application->pay_on_admission}}</td>

                  </tr>
                  @php $app_total = $app_total + $application->pay_on_admission; $sl_no++; @endphp
                  @empty                  
                  @endforelse
                 @php $emi_total = 0; $extra = 0; @endphp
                 @forelse($applications_emi as $emi)

                  @if($emi->payment_type == 'Cash')
                    @php $cash = $cash + $emi->emi_amounts; @endphp  
                  @elseif($emi->payment_type == 'Cheque')
                    @php $cheque = $cheque + $emi->emi_amounts; @endphp
                  @else
                  @endif
                  <tr> 
                    <td width="20%">{{@$emi->application->regDetails->roll_no}}</td>
                    <td width="20%">{{@$emi->application->application_id}}</td>
                    <td>{{@$emi->application->regDetails->first_name}} {{@$emi->application->regDetails->mid_name}} {{@$emi->application->regDetails->last_name}}</td>
                    <td>{{App\Helpers\Helper::getCenterName(@$emi->application->regDetails->center_name)}}</td>
                    <td>{{App\Helpers\Helper::getCourseName(@$emi->application->course)}}</td>
                    <td>{{@$application->created_at}}</td>
                    <td>{{@$emi->emi_dates}}</td>
                    <td>{{@$application->course_fee}}</td>
                    <td>{{@$application->scholarship}}</td>
                    <td>{{@$application->net_total}}</td>
                    <td>{{@$emi->payment_type}}</td>
                    <td>{{@$emi->cheque_no}}</td>
                    <td style="text-align: right;">{{@$emi->extra_amount}}</td>
                    <td style="text-align: right;">{{@$emi->emi_amounts}}</td>                    
                  </tr>
                  @php 
                    $emi_total = @$emi_total + @$emi->emi_amounts + @$emi->extra_amount; 
                    $sl_no++;
                    $extra = $extra + @$emi->extra_amount; 
                  @endphp
                  @empty                  
                 @endforelse
                 @if($applications_emi->isEmpty() && $applications_emi->isEmpty() )
                  <tr>
                    <td colspan="12">No data found</td>
                  </tr>
                 @endif
                
                @php $total = $app_total + $emi_total;   @endphp 
              </table>
              <br>
              <table class="listingtable">
                <tr>
                  <th>TOTAL COLLECTION</th>
                  <th>CASH COLLECTION</th>
                  <th>CHEQUE COLLECTION</th>
                  <th>FRESH SALES COLLECTION </th>
                  <th>OLD OUTSTANDING COLLECTION</th>
                  <th>EXTRA AMOUNT</th>                  
                </tr>
                <tr> 
                  <td>{{@$total}}</td>
                  <td>{{@$cash}}</td>
                  <td>{{@$cheque}}</td>
                  <td>{{@$app_total}}</td>
                  <td>{{@$emi_total}}</td>
                  <td>{{@$extra}}</td>
                </tr>                  
              </table>
            </div>
          </div>
          

        </div>
  </div>
    
</div>
@endsection  
