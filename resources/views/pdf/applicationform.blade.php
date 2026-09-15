<!DOCTYPE html>
<html>
<head>
    <title>IMAGIC ADMISSION FORM</title>

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
        .dtls_pst_dt .all_table th{
            font-size: 12px;
            font-weight: lighter;
            padding: 6px 8px;
            text-align: center;
        }
        .dtls_pst_dt .all_table td{
            font-size: 12px;
            padding: 6px ;
            text-align: center;
        }
        .signature_wrap table tr td{
            border: none;
            padding-right: 120px;
            padding-bottom: 20px;
            font-size: 12px;
        }
        .signature_wrap table tr td p{
            margin: 0;
            border-top: 1px dotted #000;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    <div class="application_dtls" style="text-align: center; margin: 0px 0;">
        <div class="container">
            <div class="uper_logo_wrap" style="/*display: flex; justify-content: space-between;*/ margin-bottom: 0px;">
                
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="text-align: left; border: none; text-align: right; width: 60%;">
                                <img src="{{asset('public/images/imagic_logo.png')}}" alt="" style="width: 100px; ">
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



                <!-- <div class="logo" style="max-width: 140px; ">
                    <img src="{{asset('public/images/imagic_logo.png')}}" alt="" style="width: 100%; ">
                </div>
                @if($registrations->student_photo)
                <div class="student_img" style="width: 120px; height: 120px; border: 1px solid #000;"><img src="{{asset('public/images')}}/{{$registrations->student_photo}}" alt="student"></div>
                @endif -->
            </div>
            <div class="application_hd">
                <h3 style="margin: 0; font-size: 18px;">IMAGIC ADMISSION FORM</h3>
            </div>
        </div>
    </div>

    <div class="application_dtls" style="text-align: center; margin: 10px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 16px;"><u>REGISTRATION DETAILS</u></h3>
        </div>
    </div>
    <div class="application_table basic_details" style="margin-bottom: 10px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <!-- <thead>
                        <tr style="text-align: center;">
                            <th colspan="2"><u>Registration Details</u></th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td width="50%">Student Roll No </td>
                            <td width="50%">{{@$registrations->roll_no}}</td>
                        </tr>
                        <tr>
                            <td>Centre Name </td>
                            <td>{{App\Helpers\Helper::getCenterName(@$registrations->center_name)}}</td>
                        </tr>
                        <tr>
                            <td>Date of Admission </td>
                            <td>{{date_format($registrations->created_at,'d-m-Y')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="application_dtls" style="text-align: center; margin: 10px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 16px;"><u>PERSONAL DETAILS</u></h3>
        </div>
    </div>
    <div class="application_table student_details" style="margin-bottom: 10px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <!-- <thead>
                        <tr style="text-align: center;">
                            <th colspan="2"><u>Personal Details</u></th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td width="50%">Name </td>
                            <td width="50%">{{@$registrations->mr_mrs}} {{@$registrations->first_name}} {{@$registrations->mid_name}} {{@$registrations->last_name}}</td>
                        </tr>
                        <tr>
                            <td width="50%">Mobile </td>
                            <td width="50%">{{ 'XXXXXX' . substr($registrations->mobile_no, -4) }}</td>
                        </tr>
                        <tr>
                            <td>E-Mail Id </td>
                            <td>{{ substr($registrations->email, 0, 3) . '**********@' . explode('@', $registrations->email)[1] }}</td>
                        </tr>
                        <tr>
                            <td>Student Address </td>
                            <td>{{@$registrations->address}}</td>
                        </tr>
                        <tr>
                            <td>Pincode </td>
                            <td>{{@$registrations->pin_code}}</td>
                        </tr>
                        <tr>
                            <td>City </td>
                            <td>{{@$registrations->city}}</td>
                        </tr>
                        <tr>
                            <td>District </td>
                            <td>{{@$registrations->district}}</td>
                        </tr>
                        <tr>
                            <td>State </td>
                            <td>{{@$registrations->state}}</td>
                        </tr>                       
                        
                        <tr>
                            <td>Qualification </td>
                            <td>{{@$registrations->qualification}}</td>
                        </tr>
                        <tr>
                            <td>Guardian's Name </td>
                            <td>{{@$registrations->guardian_name}}</td>
                        </tr>
                        <tr>
                            <td>Guardian's Mobile No</td>
                            <td>{{@$registrations->guardian_mob_no}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="application_dtls" style="text-align: center; margin: 10px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 16px;"><u>COURSE DETAILS</u></h3>
        </div>
    </div>
    <div class="application_table course_details" style="margin-bottom: 30px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <!-- <thead>
                        <tr style="text-align: center;">
                            <th colspan="2"><u>Course Details</u></th>
                        </tr>
                    </thead> -->
                    <tbody>
                        @php
                            $current_year = date('Y');
                            $current_date = date("d-m-Y");
                            $finance_year = "01-03-2023";
                            $year = '';
                            if($current_date >= $finance_year){
                                $year = date('y').''.date('y')+1;
                            }else{
                                $pre = date('y')-1;
                                $year = $pre."".date('y');
                            }

                        @endphp
                        <tr>
                            <td width="50%">Invoice No.</td>
                            <td width="50%">IM{{@$apllication->id}}{{@$year}}</td>
                        </tr>
                        @if(@$apllication->regDetails->centerDetails->gst_no)
                        <tr>
                            <td width="50%">GSTIN</td>
                            <td width="50%">{{@$apllication->regDetails->centerDetails->gst_no}} </td>
                        </tr>
                        @endif
                        <tr>
                            <td width="50%">Course Name </td>
                            <td width="50%">{{App\Helpers\Helper::getCourseName(@$apllication->course)}}</td>
                        </tr>
                        <tr>
                            <td width="50%">Course Start Date </td>
                            <td width="50%">{{date_format(date_create(@$apllication->batch_start_date),'d-m-Y')}}</td>
                        </tr>
                        <tr>
                            <td>Course End Date </td>
                            <td>{{date_format(date_create(@$apllication->batch_end_date),'d-m-Y')}}</td>
                        </tr>
                        <tr>
                            <td>Batch Time </td>
                            <td>{{@$apllication->batch_time}} {{@$apllication->timeunit}}</td>
                        </tr>
                        <tr>
                            <td>Course Fee </td>
                            <td>{{@$apllication->course_fee}}</td>
                        </tr>
                        <tr>
                            <td>Scholarship </td>
                            <td>{{@$apllication->scholarship}}</td>
                        </tr>
                        <tr>
                            <td>Gross Total </td>
                            <td>{{$apllication->course_fee-$apllication->scholarship}}</td>
                        </tr>
                        <tr>
                            <td>CGST (9%)</td>
                            <td>{{@$apllication->gst_amount/2}}</td>
                        </tr>
                        <tr>
                            <td>SGST (9%)</td>
                            <td>{{@$apllication->gst_amount/2}}</td>
                        </tr>
                        <tr>
                            <td>Net Total </td>
                            <td>{{@$apllication->net_total}}</td>
                        </tr>
                        <tr>
                            <td>Net Amount By Word </td>
                            <td>{{@$apllication->course_by_word}} Only</td>
                        </tr>                                                
                        <tr>
                            <td>Job Required </td>
                            <td>{{@$apllication->job_required == 'Yes' ? 'Yes' : 'No'}}</td>
                        </tr>
                        <tr>
                            <td>Faculty Name</td>
                            <td>{{@App\Helpers\Helper::getFaculty(@$apllication->faculty_id)->surname}} {{@App\Helpers\Helper::getFaculty(@$apllication->faculty_id)->name}}</td>
                        </tr>
                        <tr>
                            <td>Remarks </td>
                            <td>{{@$apllication->remarks}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="application_dtls" style="text-align: center; margin: 10px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 16px;"><u>EMI PAYMENT DETAILS</u></h3>
        </div>
    </div>
    <div class="dtls_pst_dt">
        <div class="container">
            <!-- <div class="dtls_pst_hd">
                <h4 style="font-size: 16px; margin: 0 0 15px 0; text-align: center;">EMI Payment Details</h4>
            </div> -->
            <div class="all_table">
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Date</th>
                            <th>Payment type</th>
                            <th>Payment Details</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1 </td>
                            <td>{{date_format(date_create(@$apllication->created_at),'d-m-Y')}}</td>
                            <td>Admission</td>
                            <td></td>
                            <td>{{@$apllication->pay_on_admission}}</td>
                        </tr>
                        @php $con = 2; @endphp
                        @foreach($apllication->applicationEmi as $emi)
                        <tr>
                            <td>{{$con}}</td>
                            <td>{{date_format(date_create(@$emi->emi_dates),'d-m-Y')}}</td>
                            <td></td>
                            <td></td>
                            <td>{{@$emi->emi_amounts}}</td>
                        </tr>
                        @php $con++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="acknowledge_txt" style="margin-bottom: 35px;">
                <p style="font-size: 14px;">Yes, I am interested in joining Imagic Learning Solutions Pvt. Ltd. & prefer above mentioned course. I have understood the rules & regulations, terms & conditions of this institute. I do hereby agree and accept the same and confirm to follow it. All the information provided by me is true to my knowledge.</p>
            </div>

            <div class="signature_sec">
                <div class="signature_wrap">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td width="30%" style="padding-right: 50px;">
                                    <p style=" font-size: 12px; border-top: 1px dotted #000;">Counsellor´s Signature</p>
                                </td>
                                <td width="35%" style="padding-right: 50px;">
                                    <p style=" font-size: 12px; border-top: 1px dotted #000;">Centre Owner´s Signature</p>
                                </td>
                                <td width="35%" style="padding-right: 50px;">
                                    <p style="text-align: right; font-size: 12px; border-top: 1px dotted #000;">Student´s Signature</p>
                                </td>
                            </tr>
                            <tr>
                                <td width="30%" style="font-weight: bold;">
                                    ID Card issued:
                                </td>
                                <td width="35%">
                                    <table width="100%">
                                        <tr>
                                            <td style="vertical-align: bottom; font-size: 12px; width: 15%; padding: 0;">Date</td>
                                            <td style="vertical-align: bottom; border-bottom: 1px dotted #000; width: 85%; padding: 0;">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="35%">
                                    <table>
                                        <tr>
                                            <td style="border-bottom: 1px dotted #000;  "></td>
                                        </tr>
                                        <tr>
                                            <td style=" font-size: 12px; text-align: right;  padding: 10px 0 0 0;">(Signature)</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td width="30%" style="font-weight: bold;">
                                    Kit issued:
                                </td>
                                <td width="30%">
                                    <table width="100%">
                                        <tr>
                                            <td style="vertical-align: bottom; font-size: 12px; width: 15%; padding: 0;">Date</td>
                                            <td style="vertical-align: bottom; border-bottom: 1px dotted #000; width: 85%; padding: 0;">
                                            </td>
                                        </tr>
                                    </table>
                                    
                                </td>
                                <td width="30%">
                                    <table>
                                        <tr>
                                            <td style="border-bottom: 1px dotted #000;  "></td>
                                        </tr>
                                        <tr>
                                            <td style=" font-size: 12px; text-align: right;  padding: 10px 0 0 0;">(Signature)</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    Certificate issued:
                                </td>
                                <td>
                                    <table width="100%">
                                        <tr>
                                            <td style="vertical-align: bottom; font-size: 12px; width: 15%; padding: 0;">Date</td>
                                            <td style="vertical-align: bottom; border-bottom: 1px dotted #000; width: 85%; padding: 0;">
                                            </td>
                                        </tr>
                                    </table>
                                     
                                </td>
                                <td>
                                    <table>
                                        <tr>
                                            <td style="border-bottom: 1px dotted #000;  "></td>
                                        </tr>
                                        <tr>
                                            <td style=" font-size: 12px; text-align: right;  padding: 10px 0 0 0;">(Signature)</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="term_cond">
                <div class="container">
                    <h3 style="font-size: 18px; margin: 0 0 10px 0;"><u>Rules & Regulations</u></h3>
                        <ol style="padding-left: 16px; margin-bottom: 0;">
                            <li style="margin-bottom: 5px; font-size: 12px;">Admission and registration to the course of student is not transferable.</li>
                            <li style="margin-bottom: 5px; font-size: 12px;">Change of course after commencement of class is not permissible.</li>
                            <li style="margin-bottom: 5px; font-size: 12px;">Fees paid is neither refundable nor transferable.</li>
                            <li style="margin-bottom: 5px; font-size: 12px;">Cheque/ Online payments subject to realisation.</li>
                            <li style="margin-bottom: 5px; font-size: 12px;">&nbsp;
                                <ul style="padding-left: 3px; margin-bottom: 0; list-style: none; width: 80%; position: relative; margin-top: -16px;">
                                    <li style="margin-bottom: 5px; font-size: 12px;">A) Student must pay on / before EMI payment date.</li>
                                    <li style="margin-bottom: 5px; font-size: 12px;">B) Failing which <strong>Rs.50/- per day fine</strong> will be charged extra with monthly fees which will be allowed for maximum 7 days.</li>
                                    <li style="margin-bottom: 5px; font-size: 12px;">C) After 7 days from payment date class will be suspended if payment is not received with fine.</li>
                                </ul>
                            </li>
                            <li style="margin-bottom: 5px; font-size: 12px;">Service Limitation - COURSE END DATE – A specific period of time allotted to a student, during which the enrolment of a student for a course at the Centre is valid. On the expiry of the COURSE END DATE the student will no longer be entitled to continue course at the Centre. (For extension of course end date student must apply with valid documents before course end date subject to approval)</li>
                            <li style="margin-bottom: 5px; font-size: 12px;">Changes - These rules are subject to change from time to time. Each student will be deemed to read, study and agree to the new rules as and when either put up on notice board of centre or on web site of Imagic.(www.imagic.net.in)</li>
                            <li style="margin-bottom: 5px; font-size: 12px;">A final Note - Rules and regulations are meant to be followed because they bring order from chaos and academics is a serious business in the most literal sense of the word. Nevertheless, we at the Centre try to ensure, that learning is fun & you get a Job.</li>
                        </ol>
                    </div>
            </div>
        </div>
    </div>




</body>
</html>