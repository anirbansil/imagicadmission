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

  <body class="signup-body">
 
    <div class="wrapper">

      <div class="container">
        <div class="signup-holder login-holder">
          <div class="signup-left">
            <div class="logo-hldr left-logo-hldr">
              <a href="#" title="PATHFINDER"><img src="{{ asset('public/images/login-logo.png') }}" alt="logo" > </a>
            </div>
            <div class="left-hdr">
              <h1>Welcome to Pathfinder’s</h1>
              <h2>Learning Management System</h2>
            </div>
            <div class="lft-pera-hlder">
              <p>Pathfinder’s Learning Management System (LMS) helps students to understand the need and scope of change in learning patterns from current level to meet the expectations of the next level. Pathfinder’s LMS support face-to-face learning, and online and blended learning solutions which facilitate and improve upon traditional educational methods and bridge the gap to make students equipped enough to face any academic or competitive examinations with high success rate.</p>

              <p>Pathfinder’s LMS also gives an opportunity to identify the strategies that each student need to develop to achieve desired academic excellence in competitive examinations like KVPY, NTSE, NEET, JEE as well as Boards.</p>

              <p class="bold-pera">
                Student, Your Reservation No is your User ID and Mobile number will be pf@phonenumber eg. pf@9830098300 as your password.
              </p>
            </div>
            <div class="left-footr-hldr">
              <span>Helpline:</span>
              <div class="anchr-hldr">
                <a href="tel:9748931430">9748931430</a> <em>/</em>
                <a href="tel:9830344334">9830344334</a>
              </div>
              <p>LMS by Pixel Solutionz</p>
            </div>
          </div>

          <div class="signup-right">
            <div class="logo-hldr">
              <a href="#" title="PATHFINDER"><img src="{{ asset('public/images/login-logo.png') }}" alt="logo" > </a>
              <h5>Registration Form for Online Courses</h5>
              <!-- <h3 style="color: red; font-size: 15px;">You need to fill all the Fields</h3> -->
            </div>

            <div class="box-header with-border">
              @if(Session::has('error'))
              <div class="alert alert-danger">
                 {{ Session('error')}}
              </div>
              @endif
            </div> 
            

            <form method="post" action="{{route('signup.store')}}" >
              @csrf
              <div class="form-group @if($errors->has('name'))has-error @endif">
                <input class="form-control required_field" type="text" name="name" value="{{ old('name') }}"  placeholder="Full Name" />
                @if($errors->has('name'))
                  <span class="error">{{$errors->first('name')}}</span>
                @endif
              </div>

              <div class="form-group @if($errors->has('phone_no'))has-error @endif">
                <input class="form-control required_field" type="text" name="phone_no" value="{{ old('phone_no') }}"  placeholder="Phone Number eg. 9800012345" />
                @if($errors->has('phone_no'))
                  <span class="error">{{$errors->first('phone_no')}}</span>
                @endif
              </div>

              <div class="form-group @if($errors->has('class'))has-error @endif">
                <select name="class" class="required_field">
                  <option value="">Select Class</option>
                  <option value="vii">VII</option>
                  <option value="viii">VIII</option>
                  <option value="ix">IX</option>
                  <option value="x">X</option>        
                  <option value="xi">XI</option>
                  <option value="xii">XII</option>
                  <option value="xii_passed">XII Passed</option>
                </select>
                @if($errors->has('class'))
                  <span class="error">{{$errors->first('class')}}</span>
                @endif
              </div>

              <div class="form-group @if($errors->has('email'))has-error @endif">
                <input class="form-control required_field" type="email" name="email" value="{{ old('email') }}"  placeholder="Email" /> 
                @if($errors->has('email'))
                  <span class="error">{{$errors->first('email')}}</span>
                @endif
              </div>
              <div class="form-group @if($errors->has('center'))has-error @endif">
                <select name="center" class="required_field">
                  <option value="">Select Branch</option>  
                  @foreach($all_branch as $branch)
                  <option value="{{$branch->name}}">{{$branch->name}}</option>
                  @endforeach
                </select>
                @if($errors->has('center'))
                  <span class="error">{{$errors->first('center')}}</span>
                @endif
              </div>
              <div class="form-group @if($errors->has('course_category'))has-error @endif">
                <select name="course_category" class="required_field">
                  <option value="">Select Course</option>
                  @foreach($all_course as $course)
                  @if($course->id != 25)
                  <option value="{{$course->name}}">{{$course->name}}</option>
                  @endif
                  @endforeach
                </select>
                @if($errors->has('course_category'))
                  <span class="error">{{$errors->first('course_category')}}</span>
                @endif
              </div>
              <div class="form-inline">
                <div class="form-group">
                  <img src="{{asset('public/images/recapcha.jpg') }}" alt="recapcha">
                </div>
                <div class="form-group">
                  <input type="submit" name="" value="Sign up">
                </div>
              </div>
              <div class="form-group">
                <a href="{{route('login')}}"  class="loginancchr">Login</a>
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