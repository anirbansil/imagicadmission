<table style="background: #d7e5ff;">
    <tr>
        <td>
            <table style="background :#D8E4BC;">
                <thead>
                
                <tr>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>Registration No</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>Application No</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>Student Name</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>Centre</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>EMI Date</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>EMI Amount</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>Extra Charge</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>Status</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 50%;"><b>Remarks</b></th>
                </tr>
                </thead>
                <tbody>
                    @php $sl_no=1; $flag = []; @endphp
                    @forelse($applications_emi as $emi)
                      <tr> 
                        <td style="height: 25px;margin: 0 10px;width: 50%;">{{@$emi->application->regDetails->roll_no}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 50%;">{{@$emi->application->application_id}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 50%;">{{@$emi->application->regDetails->first_name}} {{@$emi->application->regDetails->mid_name}} {{@$emi->application->regDetails->last_name}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 50%;">{{App\Helpers\Helper::getCenterName(@$emi->application->regDetails->center_name)}}</td>
                        <td style="height: 25px;margin: 0 10px;width: 50%;">{{ date('d M Y', strtotime(@$emi->emi_dates)) }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 50%;">{{$emi->emi_amounts}}</td>
                        @php
                          $current_date = date("Y-m-d");
                          $extra = 0;
                        @endphp
                        <td style="height: 25px;margin: 0 10px;width: 50%;">
                            @if($emi->extra_amount_add != 'off' )
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
                        </td>
                        <td style="height: 25px;margin: 0 10px;width: 50%;">Pending</td>
                        <td style="height: 25px;margin: 0 10px;width: 50%;">{{$emi->remarks}}</td>
                    @empty                  
                    @endforelse

                </tbody>
            </table>
            

        </td>
    </tr>
</table>
