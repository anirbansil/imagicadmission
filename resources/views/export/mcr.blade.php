<table style="background: #d7e5ff;">
    <tr>
        <td>
            <table style="background :#D8E4BC;">
                <thead>
                
                <tr>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Registration No</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Application No</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Student Name</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Centre Name</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Course Name</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Admission Date</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>EMI Date</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Gross Fee</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Scholarship</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Nett Fees</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>GST</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Payment Type</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Cheque No.</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Extra Amount</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Amount</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>GST</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Date</b></th>
                </tr>
                </thead>
                <tbody>
                    @php $app_total = 0; $app_gst_total = 0; $sl_no=1; $cash = 0; $cheque = 0; @endphp
                    @forelse($applications as $application)

                        @if($application->paid_by == 'Cash')
                            @php $cash = $cash + $application->pay_on_admission; @endphp  
                        @elseif($application->paid_by == 'Cheque')
                            @php $cheque = $cheque + $application->pay_on_admission; @endphp
                        @else
                        @endif
                      <tr> 
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->regDetails->roll_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->application_id}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->regDetails->first_name}} {{@$application->regDetails->mid_name}} {{@$application->regDetails->last_name}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCenterName(@$application->regDetails->center_name)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCourseName(@$application->course)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ date('d M Y', strtotime(@$application->created_at)) }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;"></td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->course_fee}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->net_total}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->course_fee - @$application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->paid_by}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->cheque_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;"></td>
                        @php
                          if($application->gst_amount){
                            $pay_on_admission = number_format((float)$application->pay_on_admission / 118*100, 2, '.', '');
                            $pay_on_admission_gst = number_format((float)$application->pay_on_admission - $pay_on_admission, 2, '.', '');
                          }else{
                            $pay_on_admission = $application->pay_on_admission;
                            $pay_on_admission_gst = 0;
                          }
                        @endphp
                        <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$pay_on_admission}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$pay_on_admission_gst}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$application->created_at}}</td>
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
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->regDetails->roll_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->application_id}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->regDetails->first_name}} {{@$emi->application->regDetails->mid_name}} {{@$emi->application->regDetails->last_name}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCenterName(@$emi->application->regDetails->center_name)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCourseName(@$emi->application->course)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ date('d M Y', strtotime(@$emi->application->created_at)) }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ date('d M Y', strtotime(@$emi->emi_dates)) }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->course_fee}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->course_fee - @$emi->application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->gst_amount}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->payment_type}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->cheque_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$emi->extra_amount}}</td>
                        @php
                            if($emi->application->gst_amount){
                              $emi_amounts = number_format((float)$emi->emi_amounts / 118*100, 2, '.', '');
                              $gst = number_format((float)$emi->emi_amounts - $emi_amounts, 2, '.', '');
                            }else{
                              $emi_amounts = $emi->emi_amounts;
                              $gst = 0;
                            }
                        @endphp
                        <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$emi_amounts}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$gst}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$emi->updated_at}}</td>
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

                    <tr>
                        @php 
                          //$total = $app_total + $emi_total + $app_gst_total + $emi_total_gst;  
                          //$total_gst = $app_gst_total + $emi_total_gst;

                          $total_gst = $app_gst_total + $emi_total_gst;
                          //$total = $app_total + $emi_total + $extra - $total_gst;
                          $total = $app_total + $emi_total + $extra; 
                        @endphp 
                        <td colspan="13" style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">Total - {{@$total}}</td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td>
            <table style="background :#D8E4BC;">
                <tr>
                  <th><b>TOTAL COLLECTION</b></th>
                  <th><b>CASH COLLECTION</b></th>
                  <th><b>CHEQUE/ONLINE COLLECTION</b></th>
                  <th><b>FRESH SALES COLLECTION</b> </th>
                  <th><b>OLD OUTSTANDING COLLECTION</b></th>
                  <th><b>EXTRA AMOUNT</b></th> 
                  <th><b>GST</b></th>                  
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
        </td>
    </tr>
</table>
