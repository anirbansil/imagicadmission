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
    </head>
    <body>
        <div class="wrapper">
            <main class="site-main">
                <div class="login-bg">
                    
                    <div class="login-panel-hldr">
                        <div class="login-left-hldr">
                            <h1>Imagic Admission System</h1>
                            <div class="rail-log-lft-img">
                                <img src="{{asset('public/images/lef_image.jpg')}}">
                            </div>
                        </div>
                        <div class="login-right-hldr">
                            <div class="login-form">
                                <a class="logo" href="#">
                                    <img src="{{asset('public/images/logo.png')}}" title="Imagic Admission">
                                </a>
                                <h2 class="Login-hd">Forgot Password</h2>
                                <form method="post" action="{{ route('forgot-password.store') }}" >
                                  @csrf
                                  <div class="form-group">
                                    <input class="form-control user" type="email" name="email" value="{{ old('email') }}"  placeholder="Email" required />                    

                                    @if(session()->has('email_error'))
                                      <span class="error">{{session('email_error')}}</span>
                                    @endif
                                  </div>

                                  <div class="form-group">
                                    <input class="form-control" type="submit" value="Send" />
                                  </div>
                                  <a href="{{route('login')}}" class="forgotpassword">Login</a>
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
                </div>
            </main>
        </div>

        
    </body> 

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  
    <script type="text/javascript" src="{{asset('public/js/jquery.mmenu.all.js')}}"></script> 
    <script type="text/javascript" src="{{asset('public/js/owl.carousel.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('public/js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('public/js/custom.js')}}"></script>
</html>
