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
                 <form role="form" action="{{ route('student.marks.update',['id'=>$data->id]) }}" method="POST" enctype="multipart/form-data">
                 <input type="hidden" name="baseurl" id="baseurl" value="{{  url('')}}">
                 @csrf
                 @method('put')
                 <div class="table-responsive ">
                 <table class="table table-bordered" id="item_table">
                              <tr>
                              <th>Course Name</th>
                              <th>Module Name</th>
                            {{-- <th>Sub Module Name</th> --}}
                              <th>Student Name</th>
                              <th>Theory Marks</th>
                              <th>Practical Marks</th>
                              <th>Total</th>
                              </tr>
                              <tr class="append-dive" data-id="1">
                                 <td>
                                  <select name="course_name[]" class="form-control course_name" data-target="1"  id="course_name1" required><option value="">Select Course</option>
                                    @foreach($courses as $course)
                                    <option value="{{$course->id}}" @if ($data->course_name == $course->id) selected @endif>{{$course->name}}</option>
                                    @endforeach
                                  </select>
                                </td>
                                 <td>
                                  <select name="module_name[]" class="form-control module_name" id="module_name1" data-target="1" required>
                                    @foreach($module as $key=>$value)
                                    <option value="{{$value->module_name}}" @if( $data->module_name == $value->module_name) selected @endif data-id="{{$value->id}}">{{$value->module_name}}</option>
                                    @endforeach
                                  </select>
                                 </td>
                                 {{-- <td>
                                  <select name="sub_module_name[]" class="form-control" id="sub_module_name1"  data-target="1" >
                                     @foreach($sub_module as $key2=>$val)
                                        <option value="{{$val->sub_module_name}}" @if( $data->sub_module_name == $val->sub_module_name) selected @endif >{{$val->sub_module_name}}</option>
                                     @endforeach
                                  </select>
                                 </td> --}}
                                 <td>
                                  <select name="student[]" class="form-control student_name" max="100" id="student_name1" required>
                                  @foreach($students as $student)
                                   <option value="{{$student->id}}"   @if($data->stdnt_id == $student->id) selected @endif>{{$student->regDetails->first_name}}&nbsp;{{$student->regDetails->last_name}}</option>
                                   @endforeach
                                  </select>
                                 </td>
                                 <td>
                                    <input type="text" name="student_marks[]" id="student_marks" max="100" class="form-control" value="{{$data->marks}}" required>
                                 </td>
                                 <td>
                                   <input type="text" name="student_marksp[]" id="student_marks" max="100" class="form-control" value="{{$data->practical_marks}}" required>
                                 </td>
                                 <td>
                                      <input type="text" name="student_markstotal[]"  class="form-control disabled" value="{{$data->total_marks}}"   readonly>
                                 </td>
                              </tr>
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
