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
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Gross Fee</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Scholarship</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Admission Fee</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Nett Fees</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>GST</b></th>
                </tr>
                </thead>
                <tbody>
                    @php $app_total = 0; $total_gst = 0; $sl_no=0; @endphp
                    @forelse($applications as $application)
                      <tr> 
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->regDetails->roll_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->application_id}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->regDetails->first_name}} {{@$application->regDetails->mid_name}} {{@$application->regDetails->last_name}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCenterName(@$application->regDetails->center_name)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCourseName(@$application->course)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ date('d M Y', strtotime(@$application->created_at)) }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->course_fee}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->admission_fee}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->course_fee - @$application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->gst_amount}}</td>
                      </tr>
                    @php 
                        $app_total = $app_total + @$application->course_fee - @$application->scholarship;
                        $total_gst =  $total_gst + @$application->gst_amount;
                        $sl_no++; 
                    @endphp
                    @empty                  
                    @endforelse

                    <tr> 
                      <td colspan="10" style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">Total - {{@$app_total}}</td>
                      <td style="height: 25px;margin: 0 10px;width: 25%;text-align: right;">{{@$total_gst}}</td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td>
            <table class="listingtable" style="background :#D8E4BC;">
                <tr>
                  <th style="text-align: center;"><b>TOTAL ADMISSION</b></th>
                  <th style="text-align: center;"><b>NETT SALES</b></th>
                  <th style="text-align: center;"><b>GST</b></th>                  
                </tr>
                <tr> 
                  <td style="text-align: center;">{{@$sl_no}}</td>
                  <td style="text-align: center;">{{@$app_total}}</td>
                  <td style="text-align: center;">{{@$total_gst}}</td>
                </tr>                  
            </table>
        </td>
        
    </tr>
</table>
