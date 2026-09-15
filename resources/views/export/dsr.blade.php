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
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Gross Fee</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Scholarship</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Admission Fee</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Nett Fees</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>GST</b></th>
                </tr>
                </thead>
                <tbody>
                    @php $app_total = 0; $sl_no=1; @endphp
                    @forelse($applications as $application)
                      <tr> 
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->regDetails->roll_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->application_id}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->regDetails->first_name}} {{@$application->regDetails->mid_name}} {{@$application->regDetails->last_name}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCenterName(@$application->regDetails->center_name)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{App\Helpers\Helper::getCourseName(@$application->course)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->course_fee}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->admission_fee}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->course_fee - @$application->scholarship}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{@$application->gst_amount}}</td>
                      </tr>
                    @php $sl_no++; @endphp
                    @empty                  
                    @endforelse

                </tbody>
            </table>
            

        </td>
    </tr>
</table>
