@extends('layout')
@section('title', $title)
@section('content')

<div id="main" class="admin_main">
  <div class="admin-inn-contents">
        <div class="container-fluid">
          <h1 class="main-title">{{$title}}
            <nav aria-label="breadcrumb">
              <ul class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i>Dashboard</a></li>
                <li class="breadcrumb-item">
                  <a href="{{route('student.marks.index')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Marks List</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
              </ul>
          </nav>
          </h1>
          @if(Session::has('msg'))
           <div class="alert alert-success alert-dismissible">
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
             {{session('msg')}}
           </div>
           @elseif(Session::has('error'))
           <div class="alert alert-danger alert-dismissible">
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
             {{session('error')}}
           </div>
          @endif
          <div class="dashboard-materials">
            <div class="newform">
              <div class="mainform cmnpadding ModuleMarkstable">
                 <form role="form" action="{{ route('student.marks.save') }}" method="POST" enctype="multipart/form-data">
                 <input type="hidden" name="baseurl" id="baseurl" value="{{  url('')}}">
                 @csrf
                 <div class="table-responsive ">
                 <table class="table table-bordered" id="item_table">
                              <tr>
                              <th>Course Name</th>
                              <th>Module Name</th>
                              <!--<th>Sub Module</th>-->
                              <th>Student Name</th>
                              <th>Theory Marks</th>
                              <th>Practical Marks</th>
                              <th>Total</th>
                              <th>Action</th>
                              </tr>
                              <!-- @for($a=1; $a < 2; $a++ ) -->
                              <tr class="append-dive" data-id="1">
                                 <td>
                                  <select name="course_name[]" class="form-control course_name" data-target="1"  id="course_name1" required><option value="">Select Course</option>
                                    @if(Auth::user()->is_admin == 1)
                                    @foreach($courses as $course)
                                    <option value="{{$course->id}}">{{$course->name}}</option>
                                    @endforeach
                                    @else
                                    @php $subjets = @$logged_in_faculty->facultySubjects->pluck('subject')->toArray();  @endphp
                                    @forelse($subjets as $subjet)
                                    <option value="{{$subjet}}">{{@\App\Helpers\Helper::getCourseName(@$subjet)}}</option>
                                    @empty
                                    <option value="">Subjects Not found</option>
                                    @endforelse
                                    @endif
                                  </select>
                                </td>
                                 <td>
                                  <select name="module_name[]" class="form-control module_name" data-target="1" id="module_name1" required>
                                    <option value="">Select Module</option>
                                  </select>
                                 </td>
                                 <!--<td>-->
                                 <!-- <select name="sub_module_name[]" class="form-control" id="sub_module_name1"  data-target="1" >-->
                                 <!--   <option value="">Select Sub Module</option>-->
                                 <!-- </select>-->
                                 <!--</td>-->
                                 <td>
                                  <select name="student[]" class="form-control student_name" id="student_name1" required>
                                    <option value="">Select Student</option>
                                  </select>
                                 </td>
                                 <td>
                                    <input type="text" name="student_marks[]" class="form-control" max="100" required>
                                 </td>
                                 <td>
                                    <input type="text" name="student_marksp[]" class="form-control" max="100" required>
                                 </td>
                                 <td>
                                      <input type="text" name="student_markstotal[]" max="100"  class="form-control disabled"  readonly>
                                 </td>
                                 <td><button type="button" name="add" class="btn btn-success btn-sm add" id="add_new"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                 <button type="button" name="remove" class="btn btn-danger btn-sm remove-div remove_elm" style="display:none;" ><i class="fa fa-minus-circle"></i></button>
                                 </td>
                              </tr>
                              <!-- @endfor -->
                           </table>
                          </div>
                  <div class="form-group">
                    <input type="submit" value="Save" class="finalsubmit">
                  </div>
                 </form>
              </div>
            </div>
          </div>
        </div>
  </div>
</div>
@endsection
