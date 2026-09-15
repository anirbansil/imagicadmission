<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>

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
                                @if($registrations->student_photo)
                                <p >
                                    <img src="{{asset('public/images')}}/{{$registrations->student_photo}}" alt="student" style="width: 100px; height: 120px; border: 1px solid #000;">
                                </p>
                                @endif
                            </td>
                            
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="application_hd">
                <h3 style="margin: 0; font-size: 18px;"><u>RECEIPTs</u></h3>
            </div>
        </div>
    </div>

    <div class="application_table basic_details" style="margin-bottom: 30px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td width="50%">Registration No :</td>
                            <td width="50%">{{@$registrations->roll_no}}</td>
                        </tr>
                        <tr>
                            <td>Centre Name :</td>
                            <td>{{App\Helpers\Helper::getCenterName(@$registrations->center_name)}}</td>
                        </tr>
                        <tr>
                            <td>Date of Admission :</td>
                            <td>{{date_format($registrations->created_at,'d-m-Y')}}</td>
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
                            <th colspan="2"><u>PAYMENT DETAILS</u></th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td width="50%">Student Name</td>
                            <td width="50%">{{@$registrations->mr_mrs}} {{@$registrations->first_name}} {{@$registrations->mid_name}} {{@$registrations->last_name}}</td>
                        </tr>
                        <tr>
                            <td width="50%">Course Details</td>
                            <td width="50%">{{App\Helpers\Helper::getCourseName(@$apllication->course)}}</td>
                        </tr>
                        @php
                            if($apllication->gst_amount){
                                $course_fee = number_format((float)$apllication->pay_on_admission / 118*100, 2, '.', '');
                                $gst = number_format((float)$apllication->pay_on_admission - $course_fee, 2, '.', '');
                            }else{
                                $gst = 0;
                                $course_fee = $apllication->pay_on_admission;
                            } 

                            function AmountInWords(float $amount)
                            {
                               $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
                               // Check if there is any number after decimal
                               $amt_hundred = null;
                               $count_length = strlen($num);
                               $x = 0;
                               $string = array();
                               $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
                                 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
                                 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                                 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
                                 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
                                 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
                                 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
                                 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
                                 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
                                $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
                                while( $x < $count_length ) {
                                  $get_divider = ($x == 2) ? 10 : 100;
                                  $amount = floor($num % $get_divider);
                                  $num = floor($num / $get_divider);
                                  $x += $get_divider == 10 ? 1 : 2;
                                  if ($amount) {
                                   $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
                                   $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
                                   $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
                                   '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
                                   '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
                                    }
                               else $string[] = null;
                               }
                               $implode_to_Rupees = implode('', array_reverse($string));
                               $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
                               " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
                               return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
                            }  
                             
                        @endphp
                        <tr>
                            <td>Course Fee :</td>
                            <td>{{@$course_fee}}</td>
                        </tr>
                        <tr>
                            <td>GST (18%):</td>
                            <td>{{@$gst}}</td>
                        </tr>
                        <tr>
                            <td>Net Total :</td>
                            <td>{{@$apllication->pay_on_admission}}</td>
                        </tr>
                        <tr>
                            <td>Net Amount By Word :</td>
                            <td>{{AmountInWords(@$apllication->pay_on_admission)}} Only</td>
                        </tr>                        
                        <tr>
                            <td>Payment Mode :</td>
                            <td>
                                @if(@$apllication->paid_by == 'cash')
                                Cash
                                @elseif(@$apllication->paid_by == 'cheque')
                                Cheque
                                @elseif(@$apllication->paid_by == 'online')
                                Online
                                @else

                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="term_cond">
        <div class="container">
            <h3 style="font-size: 18px; margin: 0 0 15px 0;">Terms & Conditions</h3>
                <ol style="padding-left: 16px; margin-bottom: 0;">
                    <li style="margin-bottom: 5px; font-size: 12px;">Cheque/ Online payments subject to realisation.</li>
                    <li style="margin-bottom: 5px; font-size: 12px;">Fees paid is neither refundable nor transferable.</li>
                    
                </ol>
            </div>
    </div>




</body>
</html>