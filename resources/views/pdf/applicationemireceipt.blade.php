<!DOCTYPE html>
<html>
<head>
    <title>EMI RECEIPT</title>

    <style>
        body{
            margin: 0 ;
        }
        .container{
            max-width: 1170px;
            margin: 0 auto;
        }
        table{
            border-collapse: collapse;
        }
        th{
            padding: 6px 0;
            font-size: 12px;
        }
        td{
            font-size: 12px;
            padding: 6px;
        }
        th, td{
            border: 1px solid #000;
        }
    </style>
</head>
<body>

    <div class="application_dtls" style="text-align: center; margin: 20px 0;">
        <div class="container">
            <div class="uper_logo_wrap" style="display: flex; justify-content: space-between; margin-bottom: 14px;">
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="text-align: left; border: none; text-align: right; width: 60%;">
                                <img src="{{asset('public/images/imagic_logo.png')}}" alt="" style="width: 120px; ">
                            </td>
                            
                            <td style="text-align: right; border: none; width: 40%;">
                               
                            </td>
                            
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="application_hd">
                <h3 style="margin: 0; font-size: 18px;"><u>EMI RECEIPT</u></h3>
            </div>
        </div>
    </div>

    <div class="application_table basic_details" style="margin-bottom: 30px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td width="50%">Registration No</td>
                            <td width="50%">{{@$registrations->roll_no}}</td>
                        </tr>
                        <tr>
                            <td width="50%">Student Application No :</td>
                            <td width="50%">{{@$apllication->application_id}}</td>
                        </tr>
                        <tr>
                            <td>Centre Name</td>
                            <td>{{App\Helpers\Helper::getCenterName(@$registrations->center_name)}}</td>
                        </tr>
                        <tr>
                            <td>EMI Payment Date:</td>
                            <td>{{date_format(date_create($emi_details->update_date),"d-m-Y")}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="application_dtls" style="text-align: center; margin: 20px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 18px;"><u>PAYMENT DETAILS</u></h3>
        </div>
    </div>
    <div class="application_table course_details" style="margin-bottom: 30px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <!-- <thead>
                        <tr style="text-align: center;">
                            <th colspan="2"><u>PAYMENT DETAILS</u> </th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td width="50%">Student Name</td>
                            <td width="50%">{{@$registrations->mr_mrs}} {{@$registrations->first_name}} {{@$registrations->mid_name}} {{@$registrations->last_name}}</td>
                        </tr>
                        <tr>
                            <td width="50%">Course Name</td>
                            <td width="50%">{{App\Helpers\Helper::getCourseName(@$apllication->course)}}</td>
                        </tr>
                        @php
                            if($apllication->gst_amount){
                                $emi_amounts = number_format((float)$emi_details->emi_amounts / 118*100, 2, '.', '');
                                $gst = number_format((float)$emi_details->emi_amounts - $emi_amounts, 2, '.', '');
                            }else{
                                $gst = 0;
                                $emi_amounts = $emi_details->emi_amounts;
                            }

                        @endphp
                        <tr>
                            <td>EMI Amount</td>
                            <td>{{@$emi_amounts}}</td>
                        </tr>
                        <tr>
                            <td>GST</td>
                            <td>{{@$gst}}</td>
                        </tr>
                        <tr>
                            <td>Installment No</td>
                            <td>{{@$emi_details->emi_no}}</td>
                        </tr>
                        
                        @if($emi_details->payment_type != 'Cash')
                        <tr>
                            <td>Payment Details</td>
                            <td>{{@$emi_details->cheque_no}}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Payment Mode</td>
                            <td>{{@$emi_details->payment_type}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="term_cond">
        <div class="container">
            <h3 style="font-size: 16px; margin: 0 0 15px 0;"><u>Terms & Conditions</u></h3>
                <ol style="padding-left: 16px; margin-bottom: 0;">
                    <li style="margin-bottom: 5px; font-size: 12px;">Cheque/ Online payments subject to realisation.</li>
                    <li style="margin-bottom: 5px; font-size: 12px;">Fees paid is neither refundable nor transferable.</li>
                    
                </ol>
            </div>
    </div>




</body>
</html>