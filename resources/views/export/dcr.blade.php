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
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Payment Type</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Cheque No.</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Extra Amount</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Amount</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>GST</b></th>
                </tr>
                </thead>
                <tbody>
                    @php $app_total = 0; $total_pay_on_admission_gst = 0; $sl_no=1; @endphp
                    @forelse($applications as $application)
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
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->paid_by}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->cheque_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;"></td>
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
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->regDetails->roll_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->application_id}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->regDetails->first_name}} {{@$emi->application->regDetails->mid_name}} {{@$emi->application->regDetails->last_name}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCenterName(@$emi->application->regDetails->center_name)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCourseName(@$emi->application->course)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ date('d M Y', strtotime(@$emi->application->created_at)) }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ date('d M Y', strtotime(@$emi->emi_dates)) }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->course_fee}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$emi->application->net_total}}</td>
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
                      <td colspan="14" style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">Total - {{@$total}}</td>
                      <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$total_gst}}</td>
                    </tr>
                </tbody>
            </table>
            

        </td>
    </tr>
</table>
