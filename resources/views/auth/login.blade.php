<!DOCTYPE html>

<html lang="en" id="mmenu-open-fade">

    <head>

        <meta charset="utf-8"> 

        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">

        <meta name="keywords" content=" ">

        <meta name="description" content=" "> 

        <title>Imagic Admission</title>

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/font-awesome.min.css') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/material-design-iconic-font.min.css') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/animate.css') }}">  

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/slick.css') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/bootstrap.min.css') }}"> 

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/jquery.mmenu.all.css') }}"> 

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/owl.theme.default.min.css') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/owl.carousel.min.css') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('public/css/style.css') }}"> 

        <!-- favicon -->

        <link rel="shortcut icon" type="image/x-icon" href="images/fav.png">

        

        <style type="text/css">

            .login-bg{              

              display: flex;

              align-content: center;

              justify-content: center;

              align-items: center;

              justify-items: center;

              flex-direction: row;

              flex-wrap: wrap;

              background-image: url(/public/images/lgbg.jpg);

              animation: bganimation 10s infinite;

              background-size: 110%;

            }

            .login-bg::after{

                background: rgb(64 64 64 / 40%);

            }

            .lgbox{

              display: flex;

              flex-direction: row;

              flex-wrap: wrap;

              align-content: center;

              justify-content: center;

              align-items: stretch;

              max-width: 90%;

              max-height: 85vh;

              width: 860px;

              background: #fff;

              border-radius: 15px;

              box-shadow: 0 5px 20px rgb(0 0 0 / 12%);

              z-index: 1;

              overflow: hidden;

            }

            .lgbox .dgn_right{                

                width: 50%;

                position: relative;

                left: 0;

                top: 0;

                transform: none;

                z-index: 2;

            }

            .lgbox .login-right-hldr{

                width: 100%;

                height: 100%;

                min-height: 75vh;

                display: grid;

                align-content: center;

            }

            .lgbox .dgn_left{

                width: 50%;

                position: relative;

                background: #ffc712;

            }

            .lgbox .dgn_left img{

                width: 100%;

                height: 100%;

                object-fit: contain;

            }

            .lgbox .dgn_left h3{

                position: absolute;

                z-index: 2;

                left: 50%;

                top: 20px;

                transform: translateX(-50%);

                width: 100%;

                text-align: center;

                font-size: 22px;

                font-weight: 700;

                color: #571c1e;

                padding: 15px 20px;

                font-family: 'Poppins';

            }

            .lgbox .dgn_left h3 small{

                

            }

            .lgbox .form-group{

                position: relative;

            }

            .lgbox .form-group label{

                position: absolute;

                top: 50%;

                left: 5%;

                transform: translateY(-50%);

                color: #797979;

                font-weight: 500;

                cursor: initial;

                pointer-events: none;

                margin: 0;

                padding: 5px;

                background: #fff;

                display: inline-block;

                width: auto;

                transition: .3s;

            }

            .lgbox .form-group:hover label, 

            .lgbox .form-group.active label,

            .lgbox .form-group:focus-within label{

                top: 0px;

                font-size: 12px;

                line-height: 1;

            }

            .lgbox .form-group input[type="password"]{

                padding-right: 50px;

            }

            .lgbox #password .show{

                position: absolute;

                top: 50%;

                right: 10px;

                transform: translateY(-50%);

                font-size: 20px;

                padding: 5px;

                cursor: pointer;

                z-index: 3;

                color: #b6b6b6;

            }

            .lgbox #password .show:after{

                content: "";

                position: absolute;

                left: 50%;

                top: 50%;

                transform: translate(-50%, -50%) rotate(-45deg);

                width: 22px;

                height: 5px;

                background: #b6b6b6;

                border: 2px solid #fff;

                z-index: 1;

                border-radius: 100px;

                transition: .3s;

            }

            .lgbox #password .show.active:after{

                visibility: hidden;

                opacity: 0;

                height: 1px;

            }

            .registration-form{

              padding-top: 65px;

            }

            .registration-form form{

              padding: 28px 15px 0;

            }

            .registration-form input[type="submit"]{

              width: 100%;

            }

            .registration-form input{

              padding: 12px 22px;

              height: unset;

            }

            @media (max-width: 767px){

              .lgbox .dgn_right, .lgbox .dgn_left{

                width: 100%;

                max-width: 100%;

              }

              .lgbox .dgn_left{

                display: none;

              }

            }

            @keyframes bganimation {

              0%   {

                background-position: center;

              }

              20%  {

                background-position: top;

              }

              40%  {

                background-position: right;

              }

              60%  {

                background-position: bottom;

              }

              80%  {

                background-position: left;

              }

              100% {

                background-position: center;

              }

            }

        </style>

    </head>

    <body>

        <div class="wrapper">

            <main class="site-main">

                <div class="login-bg">     

                    <div class="lgbox">            

                        <div class="dgn_right login-panel-hldr">

                            <!-- <div class="login-left-hldr">

                                <h1>Imagic Admission System</h1>\

                                <div class="rail-log-lft-img">

                                    <img src="{{asset('public/images/lef_image.jpg')}}">

                                </div>

                            </div> -->

                            <div class="login-right-hldr">

                                <div class="login-form">

                                    <a class="logo" href="#">

                                        <img src="{{asset('public/images/logo.png')}}" title="Imagic Admission">

                                    </a>

                                    <h2 class="Login-hd">Login</h2>

                                    <form method="post" action="{{url('login')}}">

                                        @csrf

                                        <div id="email" class="form-group">

                                            <label>Email</label>

                                            <input type="email" name="email" class="user" value="{{ old('email') }}">

                                        </div>

                                        <div id="password" class="form-group">

                                            <label>Password</label>

                                            <input type="password" name="password" class="pass" value="">

                                            <div class="show">&#x1F441;</div>

                                        </div>

                                        <div class="form-inline">

                                            <div class="form-group">

                                                <!-- <input type="checkbox" id="remembr-me" checked>

                                                <label for="remembr-me">Remember Me</label> -->

                                            </div>

                                        </div>



                                        <div class="form-group">

                                            <input type="submit" value="Login">



                                        </div>

                                        <div class="form-group">

                                            <p>Not registered yet? <a class="create-acc" href="{{url('/')}}/registration">Create an Account</a></p>

                                        </div> 

                                    </form>

                                    @if(Session::has('error'))

                                        <div class="alert alert-error alert-dismissible">

                                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                                          {{session('error')}}

                                        </div>

                                    @endif

                                    @if(Session::has('msg'))

                                        <div class="alert alert-success alert-dismissible">

                                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                                          {{session('msg')}}

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="dgn_left">

                            <img src="/public/images/loginimg.png">

                            <h3><small>Welcome to</small><br>Imagic Admission System</h3>

                        </div>   

                    </div>

                </div>

            </main>

        </div>

    </body> 



    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  



    <script type="text/javascript">

        //alert();

        $(document).ready(function(){

            $('.lgbox input').each(function(){

                var getid = $(this).attr("name");

                if($(this).val()){

                    $("#"+getid).addClass("active");

                }else{

                    $("#"+getid).removeClass("active");

                }

            });

            $("input").keydown(function(){

                var getid = $(this).attr("name");

                if($(this).val()){

                    $("#"+getid).addClass("active");

                }else{

                    $("#"+getid).removeClass("active");

                }

            });

            $("input").keyup(function(){

                var getid = $(this).attr("name");

                if($(this).val()){

                    $("#"+getid).addClass("active");

                }else{

                    $("#"+getid).removeClass("active");

                }

            });

            $("#password .show").click(function(){

                $('.show').toggleClass("active");

                if($('#password input[type="password"]').attr('type') == 'password'){

                    $('#password input[name="password"]').attr('type', 'text');

                }else{                        

                    $('#password input[name="password"]').attr('type', 'password');

                }

                

            });

        });

    </script>

    <script type="text/javascript" src="{{asset('public/js/jquery.mmenu.all.js')}}"></script> 

    <script type="text/javascript" src="{{asset('public/js/owl.carousel.min.js')}}"></script>

    <script type="text/javascript" src="{{asset('public/js/bootstrap.min.js')}}"></script>

    <script type="text/javascript" src="{{asset('public/js/custom.js')}}"></script>

</html>

