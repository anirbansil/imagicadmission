@extends('layout')



@section('title', $title)



@section('content')





    <div id="main" class="admin_main">



        <style type="text/css">

            .user-boxes{    

                margin-bottom: 35px;

                box-shadow: 0 2px 40px -2px rgb(0 0 0 / 10%);

            }

            .user-boxes:hover{

                box-shadow: 0 12px 20px -2px rgb(0 0 0 / 18%);

            }

            .flxitm{

                display: flex;

                flex-direction: row;

                flex-wrap: wrap;

                align-content: center;

                justify-content: flex-start;

                align-items: center;

                margin-bottom: 10px;

            }

            .flxitm .profress-dash-hldr{

                width: 140px;

                max-width: 30%;

                display: block;

            }

            .flxitm .profress-dash-hldr span.img{

                background: rgb(0 0 0 / 0%);

                width: 100%;

                max-width: 100%;

                padding: 0;

            }

            .flxitm .profress-dash-hldr span.img img{

                width: 100%;

                border-radius: 5px;

                overflow: hidden;

                object-fit: contain;

                object-position: left;

            }

            .flxitm .titleh3{

                width: calc(100% - 155px);

                max-width: calc(70% - 15px);

                margin: 0 0 0 15px;

            }

            .user-boxes p{

                font-size: 13px;

                line-height: 1.4;

                color: gray;

                margin: 0;

            }

            .user-boxes .btn{

                background: #ffc712;

                color: #571d1f;

                min-width: 120px;

                float: right;

                border-radius: 50px;

                margin-top: 5px;

            }

            .user-boxes .btn:hover{

                background: #571d1f;

                color: #ffc712;

            }

        </style>

        <div class="admin-inn-contents">



            <div class="container-fluid">



                <h1 class="main-title">Jobs</h1>



                <div class="dashboard-materials">



                    <div class="row dashboard-user-rprts">                        

                        @foreach($data as $value)

                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">

                            <div class="user-boxes">

                                <div class="flxitm">                            	

                                    @php $urljson = (array) $value->_embedded; @endphp 

                                    <div class="profress-dash-hldr">

                                        <span class="img"><img src="{{@$urljson['wp:featuredmedia']['0']->source_url}}"></span>

                                    </div>

                                    <h3 class="titleh3">{{$value->title->rendered}}</h3>

                                </div>

                                {!!$value->excerpt->rendered!!}

                                <a href="https://imagic.net.in/{{$value->slug}}" target="blank"><button class="btn">Details</button></a>

                            </div>

                        </div>                        

                        @endforeach

                        <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center">

                            <a href="https://imagic.net.in/job-vacancies/" target="blank">View All</a>

                        </div>

                        <br>

                    </div>



                    <div class="pagination_div">

                        @php $noOfPage = $total_data / 10; @endphp



                        @for($i=1; $i<=$noOfPage;$i++)

                        <a href="?page={{$i}}" class="@if($page==$i)selected @endif">{{$i}}</a>

                        @endfor



                    </div>



                </div>



            </div>



        </div>



    </div>



</div>



@endsection

