@extends('layout')

@section('title', $title)

@section('content')
    <div id="main" class="admin_main">
        <style>
            .user-boxes .btn {
                background: #ffc712;
                color: #571d1f;
                min-width: 120px;
                border-radius: 50px;
                margin: 0px auto 0;
                transform: translateX(-50%);
                left: 50%;
                position: relative;
            }
            .profress-dash-hldr .img img {
                width: 100%;
                max-height: 200px;
                object-fit: contain;
            }

            [style*="--aspect-ratio"] > :first-child {
              width: 100%;
            }
            [style*="--aspect-ratio"] > img {  
              height: auto;
            } 
            @supports (--custom:property) {
              [style*="--aspect-ratio"] {
                position: relative;
              }
              [style*="--aspect-ratio"]::before {
                content: "";
                display: block;
                padding-bottom: calc(100% / (var(--aspect-ratio)));
              }  
              [style*="--aspect-ratio"] > :first-child {
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
              }  
            }

        </style>

        <div class="admin-inn-contents">

            <div class="container-fluid">

                <h1 class="main-title">Dashboard</h1>

                <div class="dashboard-materials">

                    <div class="row dashboard-user-rprts">

                        <div class="mb_30 col-lg-12 col-md-12 col-sm-12 col-12">
                            <h3 class="titleh3" style="color: green;">Welcome {{Auth::guard()->user()->name}}</h3>
                        </div>
                        <br>        

                        @if($user_role == 4)
                        <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                            <div class="user-boxes">
                                <a href="#">
                                <h3 class="titleh3">
                                    Your Credit
                                </h3>

                                <div class="profress-dash-hldr">

                                    <h4>{{$center_detials->center_amount}}</h4>

                                </div>
                                </a>
                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                            <div class="user-boxes">
                                <a href="#">
                                <h3 class="titleh3">
                                    You have used
                                </h3>

                                <div class="profress-dash-hldr">

                                    <h4>{{$center_detials->use_amount}}</h4>

                                </div>
                                </a>
                            </div>

                        </div>
                        @endif
                        <br>
                        @if(Auth::guard()->user()->is_admin == 1)
                        {{--<div class="col-lg-3 col-md-6 col-sm-6 col-6">

                            <div class="user-boxes">

                                <h3 class="titleh3">

                                    Users

                                </h3>

                                <div class="profress-dash-hldr">

                                    <span class="img"><img
                                            src="{{ asset('public/images/statistics.png') }}"></span>

                                    <h4>{{$user_counts}}</h4>

                                </div>

                            </div>

                        </div>--}}
                        @endif
                        
                        @php 
                            $admin = Auth::guard()->user();
                            $roleArray = $admin->roles()->pluck('slug')->toArray();
                            
                        @endphp
                        
                        @if(Auth::guard()->user()->is_admin == 1 || $roleArray[0]== "center")
                        <br>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">

                            <div class="user-boxes">
                            	<a href="{{route('inquiry-form.index',['dashboard'=>1])}}">
                                <h3 class="titleh3">

                                    Today's Inquery

                                </h3>

                                <div class="profress-dash-hldr">

                                    <span class="img"><img
                                            src="{{ asset('public/images/enquiry.png') }}"></span>

                                    <h4>{{$today_inquiry}}</h4>

                                </div>
                            	</a>
                            </div>

                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">

                            <div class="user-boxes">
                            	<a href="{{route('inquiry-form.index',['dashboard'=>2])}}">
                                <h3 class="titleh3">

                                    Today's Hot Leads

                                </h3>

                                <div class="profress-dash-hldr">

                                    <span class="img"><img
                                            src="{{ asset('public/images/enquiry.png') }}"></span>

                                    <h4>{{$today_hot_inquiry}}</h4>

                                </div>
                            	</a>
                            </div>

                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">

                            <div class="user-boxes">
                            	<a href="{{route('inquiry-form.index',['dashboard'=>3])}}">

                                <h3 class="titleh3">

                                    Total Inquery

                                </h3>

                                <div class="profress-dash-hldr">

                                    <span class="img"><img
                                            src="{{ asset('public/images/enquiry.png') }}"></span>

                                    <h4>{{$total_inquiry}}</h4>

                                </div>
                            	</a>

                            </div>

                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">

                            <div class="user-boxes">

                                <h3 class="titleh3">

                                    Total Courses

                                </h3>

                                <div class="profress-dash-hldr">

                                    <span class="img"><img
                                            src="{{ asset('public/images/course.png') }}"></span>

                                    <h4>{{$total_courses}}</h4>

                                </div>

                            </div>

                        </div>

                        <br>
                        <div class="dashboard-materials">
                            <div class="relg-new-struct-add">
                                <form action="{{ route('dashboard') }}" method="get">
                                    <div class="filter_row">
                                        <div class="filter_div row ">
                                            <div class="col-xs-12 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="date" name="from_date"
                                                            class="form-control pull-right datepicker" placeholder="From Date"
                                                            value="{{ !empty($request->from_date) ? $request->from_date : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="date" name="to_date" class="form-control pull-right datepicker"
                                                            placeholder="To Date"
                                                            value="{{ !empty($request->to_date) ? $request->to_date : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-md-4 col-sm-6">
                                                <button type="submit" class="btn btn-info">Search</button>
                                                <a href="{{ route('dashboard') }}" class="reset">Reset</a>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <br>&nbsp;
                        <div class="dashboard-materials">
                            <div class="relg-new-struct-add">
                                <div class="filter_div row ">
                                    <div class="col-xs-12 col-md-5 col-sm-5">
                                        <div id="piechart" style="height: 400px;"></div>
                                    </div>
                                    <!-- <div class="col-xs-12 col-md-1 col-sm-1">
                                    </div> -->
                                    <div class="col-xs-12 col-md-7 col-sm-7">
                                        <div id="columnchart_material" style="height: 400px;"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endif

                        

                        @if($roleArray[0]== "student" && !empty($registrations->created_by) )

                            <div class="application_dtls" style="text-align: center; margin: 20px 0;">
                                <div class="container">
                                    <div class="uper_logo_wrap" style="display: flex; justify-content: space-between; margin-bottom: 14px;">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: left; border: none; text-align: center; width: 60%;">
                                                        <img src="{{asset('public/images/imagic_logo.png')}}" alt="" style="width: 120px; ">
                                                    </td>
                                                    
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="relg-list">
                                        <div class="table-responsive">
                                          <table class="listingtable">
                                            <tr>
                                              <th>Name</th>
                                              <th>Course</th>
                                              <th>Center</th>
                                              <th>Registration Number</th>
                                              <th>Date of Registration</th>
                                            </tr>
                                            <tr> 
                                                <td>{{@$registrations->mr_mrs}} {{@$registrations->first_name}} {{@$registrations->mid_name}} {{@$registrations->last_name}}</td>
                                                <td>{{App\Helpers\Helper::getCourseName(@$application->course)}}</td>
                                                <td>{{App\Helpers\Helper::getCenterName(@$registrations->center_name)}}</td>
                                                <td>{{@$registrations->roll_no}}</td>
                                                <td>{{!empty($registrations->created_at) ? date_format(@$registrations->created_at,'d-m-Y') : ''}}</td>
                                            </tr>
                                          </table>
                                        </div>
                                      </div>
                                      <br>
                                      @if(!empty($registrations->created_by) )
                                      <span>Helpline Number - {{App\Helpers\Helper::getUser(@$registrations->created_by)->phone_no}}</strong></span>
                                      @endif
                                </div>
                            </div>
                            <br>
                            

                        @endif
                        @if($roleArray[0]== "outsider_student" )


                            <div class="mb_30 col-lg-12 col-md-12 col-sm-12 col-12">
                            <h3 class="titleh3">For any query please contact us - <a href="tel:7044393332" style="font-variant-numeric: lining-nums;">7044393332</a></h3>
                            </div>
                            <br>
                            <br>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>

                            <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                <h3  class="titleh3">Latest Placement</h3>
                                @php $pl = 1; @endphp
                                @foreach($placements as $placement)
                                @if($pl == 1)
                                @php $urljson = (array) $placement->_embedded; @endphp
                                <div class="user-boxes">
                                    <!-- <a href="#"> -->
                                        <div class="flxitm">
                                            <div class="profress-dash-hldr" style="justify-content: center;">
                                                <span class="img" style="max-width:100%;  background:#ffff;"><img src="{{$urljson['wp:featuredmedia']['0']->source_url}}"></span>
                                            </div>
                                            <h3 class="titleh3">{{$placement->title->rendered}}</h3>
                                        </div>
                                        <p>{!!$placement->content->rendered!!}</p>
                                        <a href="https://imagic.net.in/placements/" target="blank"><button class="btn">Know More</button></a>
                                </div>
                                @endif
                                @php $pl++; @endphp                                
                                @endforeach
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <h3 class="titleh3">Latest Job</h3>
                                @php $jo = 1; @endphp
                                @foreach($jobs as $job)
                                @if($jo == 1)
                                @php $jurljson = (array) $job->_embedded; @endphp
                                <div class="user-boxes">
                                    <div class="flxitm"> 
                                        <div class="profress-dash-hldr"  style="justify-content: center;">
                                            <span class="img" style="max-width:100%;  background:#ffff;"><img src="{{$jurljson['wp:featuredmedia']['0']->source_url}}"></span>
                                        </div>
                                        <h3 class="titleh3">{{$job->title->rendered}}</h3>
                                    </div>
                                    <p>{!!$job->excerpt->rendered!!}</p>
                                    <a href="https://imagic.net.in/job-vacancies" target="blank"><button class="btn">Know More</button></a>
                                </div>
                                @endif
                                @php $jo++; @endphp                                
                                @endforeach
                            </div>
                            <br>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <div class="mb_30 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="user-boxes">
                                    <div class="flxitm" style="--aspect-ratio: 16/9;">
                                        <iframe class="youtube" width="1600" height="500" src="https://www.youtube.com/embed/XJ-zTiOmFUo?si=rVSYZbvV1ihPE8Vl" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <div class="mb_30 col-lg-12 col-md-12 col-sm-12 col-12">
                                <span style="text-align: center;"><a href="https://wa.me/7044393332" target="blank"><i class="fa fa-whatsapp" aria-hidden="true" style="color: green;font-size: 50px;margin-left: 20px;"></i></a></span>
                            </div>
                            
                        @endif
                        </div>

                    </div>

                </div>

            </div>

        </div>



    </div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day', { role: 'style' }],
                <?php echo $regInqChartData;  ?>
                // ['Eat', 2],
                // ['Commute', 2],
                // ['Watch TV', 2],
                // ['Sleep', 7]
            ]);

            var options = {
                title: 'Monthly Inquery & Registration Activities',
                is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
        }

        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawBarChart);

        function drawBarChart() {
            var data = google.visualization.arrayToDataTable([
                ['', '', { role: 'style' }],
                <?php echo $regInqBarChartData; ?>
            ]);

            var options = {
                chart: {
                    title: 'Imagic Admission',
                    subtitle: 'Monthly Collection & Sales Report',
                }
            };

            var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>


@endsection
