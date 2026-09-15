<!DOCTYPE html>

<html lang="en">

  <head>

    <!-- Required meta tags -->

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">

    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="{{ asset('public/css/bootstrap.min.css') }}">

    <!-- favicon css -->

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('public/images/favicon.ico') }}">

    <!-- animate css -->

    <link rel="stylesheet" href="{{ asset('public/css/animate.css') }}">

    <!-- font awesome -->

     <link rel="stylesheet" href="{{ asset('public/css/material-design-iconic-font.min.css') }}">

     <link rel="stylesheet" href="{{ asset('public/css/loginnew-style.css') }}">

    <title>Pathfinder E-Learning Classroom</title>

   </head>

  <body class="signup-body admin-sign-body">
 
    <div class="wrapper">

      <div class="container">
        <div class="signup-holder login-holder admin-login-hlder">
         

          <div class="signup-right login-right">
            <div class="logo-hldr">
              <a href="#" title="PATHFINDER"><img src="{{ asset('public/images/login-logo.png') }}" alt="logo" > </a>
             
            </div>

            @if(Session::has('msg'))
                <div class="alert alert-error alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{session('msg')}}
                </div>
            @endif
            @if(Session::has('resmsg'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{session('resmsg')}}
                </div>
            @endif
            

            <form method="post" action="{{url('login')}}">
              @csrf
              
              <input type="hidden" id="admin" name="user_type" value="admin">

              <div class="zadmin_loginz">
                <div class="form-group">
                  <input type="email" name="email" value="{{ old('email') }}"  placeholder="Email">
                </div>
              </div>

              <div class="form-group"> 
                  <input type="password" type="password" name="password" value="" placeholder="Password">
              </div>


              <div class="form-group">
                  <img src="{{asset('public/images/recapcha.jpg') }}" alt="recapcha">
                </div>
              <div class="form-group less-mrgin">
                  <input type="submit" name="" value="LOGIN">
              </div>
              
            </form>
            

          </div>
        </div>
      </div>


      <!-- site-main end -->

      <footer  class="site-footer">

        <div class="footer-bottom">

          <div class="container ">
            <p>Copyright © 2020 Pathfinder | Privacy Policy and T&C | <span>Developed by <a href="https://www.pixelsolutionz.com/" target="_blank">Pixel Solutionz</a></span></p>
           </div>

        </div>

      </footer>

    </div>

<script src="{{asset('public/bower_components/jquery/dist/jquery.min.js')}}"></script>
    
<script src="{{asset('public/js/login.js')}}"></script>
  </body>
</html>