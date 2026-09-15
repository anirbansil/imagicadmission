@extends('layout')



@section('title', $title)



@section('content')

    <div id="main" class="admin_main">



        <style type="text/css">

            .dashboard-user-rprts{                

                flex-wrap: wrap;

                margin-right: -15px;

                margin-left: -15px;

                align-items: stretch;

                align-content: flex-start;

                justify-content: center;

                flex-direction: row;

            }

            .mb_30{

                margin-bottom: 30px;

            }

            .user-boxes{                   

                height: 100%;

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

                justify-content: center;

                align-items: center;

                margin-bottom: 10px;

            }

            .flxitm .profress-dash-hldr{

                width: 120px;

                max-width: 60%;

                display: block;

                transition: .5s;

                transition-delay: .2s;

            }

            .user-boxes:hover .profress-dash-hldr{

                transform: scale(1.15);

            }

            .flxitm .profress-dash-hldr span.img{

                max-width: 100%;

                transition: .3s;

            }

            .user-boxes:hover span.img{

                padding: 2px;

            }

            .flxitm .profress-dash-hldr span.img img{

                width: 100%;

                overflow: hidden;

                object-fit: contain;

                object-position: left;

                border-radius: 50%;

            }

            .flxitm .titleh3{

                width: 100%;

                max-width: 100%;

                margin: 15px 0 0;

                font-size: 24px;

                text-align: center;

            }

            .user-boxes p{

                font-size: 13px;

                line-height: 1.4;

                color: gray;

                margin: 0;

            }

        </style>

        <div class="admin-inn-contents">



            <div class="container-fluid">



                <h1 class="main-title">Placements</h1>



                <div class="dashboard-materials">



                    <div class="row dashboard-user-rprts">                        

                        @foreach($data as $value)

                        <div class="mb_30 col-lg-4 col-md-4 col-sm-4 col-12">

                            <div class="user-boxes">

                            	<a href="#">

                                <div class="flxitm">

                                    @php $urljson = (array) $value->_embedded; @endphp 

                                    <div class="profress-dash-hldr">

                                        <span class="img"><img src="{{$urljson['wp:featuredmedia']['0']->source_url}}"></span>

                                    </div>

                                    <h3 class="titleh3">{{$value->title->rendered}}</h3>

                                </div>

                                {!!$value->content->rendered!!}

                            	</a>

                            </div>

                        </div>

                        

                        @endforeach



                        <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center">

                            <a href="https://imagic.net.in/placements/" target="blank">View All</a>

                        </div>

                        



                        </div>



                    </div>



                </div>



            </div>



        </div>







    </div>



    





@endsection

