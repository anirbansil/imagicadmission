<!DOCTYPE html>
<html>
<head>
    <title>IMAGIC STUDENT DETAILS (FACULTY COPY)</title>

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
            padding: 6px 8px;
            text-align: center;
            font-weight: lighter;
        }
        .dtls_pst_dt .all_table td{
            font-size: 12px;
            padding: 6px ;
            text-align: center;
            font-weight: lighter;
        }
        .signature_sec{
            display: flex;
            flex-wrap: wrap;
        }
        .signature_wrap{
            width: 33.3%;
            margin-bottom: 30px;
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
                <h3 style="margin: 0; font-size: 18px;">IMAGIC STUDENT DETAILS (FACULTY COPY)</h3>
            </div>
        </div>
    </div>

    <div class="application_dtls" style="text-align: center; margin: 20px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 18px;"><u>REGISTRATION DETAILS</u></h3>
        </div>
    </div>
    <div class="application_table basic_details" style="margin-bottom: 30px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <!-- <thead>
                        <tr style="text-align: center;">
                            <th colspan="2"><u>REGISTRATION DETAILS</u></th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td width="50%">Registration No</td>
                            <td width="50%">{{@$registrations->roll_no}}</td>
                        </tr>
                        <tr>
                            <td>Centre Name</td>
                            <td>{{App\Helpers\Helper::getCenterName(@$registrations->center_name)}}</td>
                        </tr>
                        <tr>
                            <td>Date of Admission</td>
                            <td>{{date_format($registrations->created_at,'d-m-Y')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="application_dtls" style="text-align: center; margin: 20px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 18px;"><u>PERSONAL DETAILS</u></h3>
        </div>
    </div>
    <div class="application_table student_details" style="margin-bottom: 30px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <!-- <thead>
                        <tr style="text-align: center;">
                            <th colspan="2"><u>PERSONAL DETAILS</u></th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td width="50%">Student Name</td>
                            <td width="50%">{{@$registrations->mr_mrs}} {{@$registrations->first_name}} {{@$registrations->mid_name}} {{@$registrations->last_name}}</td>
                        </tr>
                        <tr>
                            <td width="50%">Mobile</td>
                            <td width="50%">{{ 'XXXXXX' . substr($registrations->mobile_no, -4) }}</td>
                        </tr>
                        <tr>
                            <td>E-Mail Id</td>
                            <td>{{ substr($registrations->email, 0, 3) . '**********@' . explode('@', $registrations->email)[1] }}</td>
                        </tr>                        
                        <tr>
                            <td>Qualification</td>
                            <td>{{@$registrations->qualification}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="application_dtls" style="text-align: center; margin: 20px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 18px;"><u>COURSE DETAILS</u></h3>
        </div>
    </div>
    <div class="application_table course_details" style="margin-bottom: 30px;">
        <div class="container">
            <div class="all_table">
                <table style="width: 100%;">
                    <!-- <thead>
                        <tr style="text-align: center;">
                            <th colspan="2"><u>COURSE DETAILS</u></th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>
                            <td width="50%">Course Name</td>
                            <td width="50%">{{App\Helpers\Helper::getCourseName(@$apllication->course)}}</td>
                        </tr>
                        <tr>
                            <td width="50%">Course Start Date</td>
                            <td width="50%">{{date_format(date_create(@$apllication->batch_start_date),'d-m-Y')}}</td>
                        </tr>
                        <tr>
                            <td>Course End Date</td>
                            <td>{{date_format(date_create(@$apllication->batch_end_date),'d-m-Y')}}</td>
                        </tr>
                        <tr>
                            <td>Batch Time</td>
                            <td>{{@$apllication->batch_time}} {{@$apllication->timeunit}}</td>
                        </tr> 
                        <tr>
                            <td>Faculty Name</td>
                            <td>{{@App\Helpers\Helper::getFaculty(@$apllication->faculty_id)->surname}} {{@App\Helpers\Helper::getFaculty(@$apllication->faculty_id)->name}}</td>
                        </tr>                       
                        <tr>
                            <td>Remarks</td>
                            <td>{{@$apllication->remarks}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="application_dtls" style="text-align: center; margin: 20px 0;">
        <div class="application_hd">
            <h3 style="margin: 0; font-size: 18px;"><u>MODULE/SOFTWARE DETAILS</u></h3>
        </div>
    </div>
    <div class="dtls_pst_dt">
        <div class="container">
            <!-- <div class="dtls_pst_hd">
                <h4 style="font-size: 20px; margin: 0 0 15px 0;"><u>MODULE/SOFTWARE DETAILS</u></h4>
            </div> -->
            <div class="all_table">
                <table style="width: 100%; font-weight: lighter;">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Module/Software</th>
                            <th>Faculty Sign</th>
                            <th>Exam Date</th>
                            <th style="padding: 0; border-collapse: collapse;">
                                <table width="100%" >
                                    <tr>
                                        <td colspan="2" style="border-left: none; border-top: none; border-right: none; border-bottom: none;">Exam Marks</td>
                                    </tr>
                                    <tr>
                                        <td style="border-left: none; border-bottom: none;">Th.</td>
                                        <td style="border-right: none; border-bottom: none;">Pr.</td>
                                    </tr>
                                </table>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sl_no = 1; 
                        //$modules = $category->courseModule; 
                        @endphp
                    	@forelse($course_details->courseModule as $module)
                        <tr>
                            <td>{{$sl_no}}</td>
                            <td>{{$module->module_name}}</td>
                            <td></td>
                            <td></td>
                            <td style="padding: 0">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="border-left: none; border-bottom: none; border-top: none;"></td>
                                        <td style="border-right: none; border-bottom: none; border-top: none;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        @php $sl_no++; @endphp
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>




</body>
</html>