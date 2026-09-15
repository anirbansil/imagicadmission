<div id="mySidenav" class="sidenav closebtn">

  <!--  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a> -->

   <a href="javascript:void(0)" class="openbtn"></a>

   <a class="logo" href="{{URL('/')}}">

     <img src="{{asset('public/images/logo.png')}}" class="main-logo">

     <img src="{{asset('public/images/logo.png')}}" class="small-logo">

     <!-- <span class="logo-txt">Criminal Records - Delhi Police</span> -->

   </a>

   <ul class="side-list">

    @if(App\Helpers\Helper::sidenavAccess('dashboard'))

     <li class="{{ Request::is('dashboard') ? 'active' : '' }}"><a href="{{route('dashboard')}}" class="home" title="Dashboard">Dashboard</a></li>

     @endif

     <li class="{{ Request::is('quick-inquiry-form') ? 'active' : '' }}"><a href="{{route('quick-inquiry-form.index')}}" class="attendance" title="Attendance List">Quick Inquiry</a></li>

     @if(App\Helpers\Helper::sidenavAccess('attendance'))

     <li class="{{ Request::is('checkin-checkout-list') ? 'active' : '' }}"><a href="{{route('checkin-checkout.list')}}" class="attendance" title="Attendance List">Attendance List</a></li>

     @endif

     @if(App\Helpers\Helper::sidenavAccess('user-list'))

     <li class="{{ Request::is('user-manage') || Request::is('user-manage/*') ? 'active' : '' }}"><a href="{{route('user-manage.index')}}" class="user" title="User Management">User Management</a></li>

     @endif

     @if(App\Helpers\Helper::sidenavAccess('role-list'))

     <li class="{{ Request::is('role') || Request::is('role/*') ? 'active' : '' }}"><a href="{{route('role.index')}}" class="permissions" title="Roles and Permissions">Roles and Permissions</a></li>

     @endif

     @if(App\Helpers\Helper::sidenavAccess('placements'))

     <li class="{{ Request::is('placements') || Request::is('placements/*') ? 'active' : '' }}"><a href="{{route('placement')}}" class="user" title="Placements">Placements</a></li>

      @endif

      @if(App\Helpers\Helper::sidenavAccess('jobs'))

     <li class="{{ Request::is('jobs') || Request::is('jobs/*') ? 'active' : '' }}"><a href="{{route('jobs')}}" class="jobs" title="Jobs">Jobs</a></li>

      @endif

     @if(App\Helpers\Helper::sidenavAccess('faculty-list'))

     <li class="{{ Request::is('faculty-master') || Request::is('faculty-master/*') ? 'active' : '' }}"><a href="{{route('faculty-master.index')}}" class="faculty" title="Course Master">Faculty</a></li>

     @endif

     

     @if(App\Helpers\Helper::sidenavAccess('course'))

     <li class="{{ Request::is('course-master') || Request::is('course-master/*') ? 'active' : '' }}"><a href="{{route('course-master.index')}}" class="course" title="Course Master">Course Master</a></li>

     @endif

     @if(App\Helpers\Helper::sidenavAccess('marks_module'))

      <li><a href="{{route('student.marks.index')}}" class="{{ Request::is('student_marks_distribution') || Request::is('student_marks_distribution/*') ? 'active' : '' }}">Module Marks</a></li>

      @endif



     @if(App\Helpers\Helper::sidenavAccess('center'))

     <li class="{{ Request::is('center-master') || Request::is('center-master/*') ? 'active' : '' }}"><a href="{{route('center-master.index')}}" class="centre" title="Centre Master">Centre Master</a></li>

     @endif



     @if(App\Helpers\Helper::sidenavAccess('center'))

     <li class="{{ Request::is('video-master') || Request::is('video-master/*') ? 'active' : '' }}"><a href="{{route('video-master.index')}}" class="video" title="Centre Master">Video Master</a></li>

     @endif



     @if(App\Helpers\Helper::sidenavAccess('inquery'))

     <li class="{{ Request::is('inquiry-form') || Request::is('inquiry-form/*') ? 'active' : '' }}"><a href="{{route('inquiry-form.index')}}" class="inquery" title="Inquery Form">Inquery Form</a></li>

     @endif

     @if(App\Helpers\Helper::sidenavAccess('registration'))

     <li class="{{ Request::is('registration-form') || Request::is('registration-form/*') ? 'active' : '' }}"><a href="{{route('registration-form.index')}}" class="registration" title="Registration Form">Registration Form</a></li>

     @endif

     @if(App\Helpers\Helper::sidenavAccess('application'))

     <li class="{{ Request::is('application-form') || Request::is('application-form/*') ? 'active' : '' }}"><a href="{{route('application-form.index')}}" class="application" title="Application Form">Application Form</a></li>

     @endif

     @if(App\Helpers\Helper::sidenavAccess('receipt'))

     @php $user_app_id = Helper::getApplicationIdByEmiId(Auth::guard()->user()->email); @endphp

     <li class="{{ Request::is('pending-receipt') || Request::is('cleared-receipt') || Request::is('edit-receipt/*') || Request::is('update-receipt/*') ? 'active' : '' }} hashchild"><a href="#" class="receipt" title="Receipt">Receipt</a>

      <ul class="submenu">

        <li><a href="{{route('application-receipt.all')}}" class="receipt" title="All Receipt">All Receipt</a></li>

        @if(empty($user_app_id))

        <li><a href="{{route('application-receipt.index')}}" class="receipt" title="Pending Receipt">Pending Receipt</a></li>

        <li><a href="{{route('application-receipt.cleared')}}" class="receipt" title="Clear Receipt">Cleared Receipt</a></li>

        @endif

      </ul>

     </li>

     @endif

     @if(App\Helpers\Helper::sidenavAccess('report'))

     <li class="{{ Request::is('dcr') || Request::is('mcr') || Request::is('dsr') || Request::is('msr') ? 'active' : '' }} hashchild "><a href="#" class="reprt" title="Reports">Reports</a>

      <ul class="submenu">

        <li><a href="{{route('reports.dcr')}}" class="reprt" title="DCR">DCR</a></li>

        <li><a href="{{route('reports.mcr')}}" class="reprt" title="MCR">MCR</a></li>

        <li><a href="{{route('reports.dsr')}}" class="reprt" title="DSR">DSR</a></li>

        <li><a href="{{route('reports.msr')}}" class="reprt" title="MSR">MSR</a></li>

       {{-- <li><a href="{{route('student.progress.report')}}" class="reprt" title="MSR">Progress Report</a></li> --}}

      </ul>

     </li>

     @endif

     <li class="{{ Request::is('invoice') || Request::is('invoice/*') ? 'active' : '' }}"><a href="{{route('invoice')}}" class="tutorial" title="Invoice">Invoice</a></li>

     @if(App\Helpers\Helper::sidenavAccess('students-list'))

     <li class="{{ Request::is('students') || Request::is('students/*') ? 'active' : '' }} hashchild"><a href="#" class="student" title="Student Master">Student Master</a>

     <ul class="submenu">

          <li><a href="{{route('students-list.index')}}" class="student" title="Students List">Student's List</a></li>

          <li><a href="{{route('outsider_students')}}" class="student" title="Outsider Student">Outsider Student</a></li>

      </ul>

    </li>

     @endif



     <li class="{{ Request::is('video') || Request::is('video/*') ? 'active' : '' }}"><a href="{{route('video-master.show')}}" class="tutorial" title="Students List">Tutorial Video's</a></li>



     <li><a href="{{route('logout')}}" class="logout" title="Logout">Logout</a></li>



   </ul>

 

</div>