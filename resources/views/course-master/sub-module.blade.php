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

                  <a href="{{route('course-master.index')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Course List</a></li>

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

              <div class="mainform cmnpadding ">

                 <form role="form" action="{{ route('course-master.submodule.store',['id'=>$category->id]) }}" method="POST" enctype="multipart/form-data">

                 @csrf

                    <div class="form-inlinex" style="display: flex; flex-wrap: wrap; flex-direction: row; align-content: flex-start; justify-content: flex-start; align-items: stretch;">
                        <div class="col-xs-12 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label>Course Name<span class="required_field">*</span></label>
                                <input type="text" name="name" class="form-control required_field" value="{{$category->name}}" disabled>
                                @if($errors->has('name'))
                                <span class="error">{{$errors->first('name')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <table class="table table-bordered" id="item_table">
                             <tr>
                              <th>Module Name</th>
                              <th>Sub Module Name</th>
                              <th>Action</th>
                              </tr>
                              @php $a=1 @endphp
                              @if(!empty($sub_modules))
                              
                                  @foreach($sub_modules as $subval)
                                  <tr class="append-dive" data-id="{{$a}}">
                                    <td>
                                      <select name="module_name[]" class="form-control module_name"  required>
                                        <option value="">Select Module</option>
                                        @foreach($modules as $module)
                                        <option value="{{$module->id}}" {{$module->id == $subval->module_id ? 'selected':'' }}>{{$module->module_name}}</option>
                                        @endforeach
                                      </select>
                                    </td>
                                    <td>
                                      <input type="text" name="sub_module_name[]" class="form-control sub_module_name" value="{{$subval->sub_module_name}}"  required>
                                    </td>
                                    <td><button type="button" name="add" class="btn btn-success btn-sm add" id="add_new" style="display:{{$a == 1 ? 'block':'none'}};"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                    <button type="button" name="remove" class="btn btn-danger btn-sm remove-div remove_elm" style="display:{{$a == 1 ? 'none':'block'}};" ><i class="fa fa-minus-circle"></i></button>
                                    </td>
                                    </td>
                                  </tr>
                                  @php $a++ @endphp
                                  @endforeach
                              @endif
                              @if($a== 1)
                              <tr class="append-dive" data-id="{{$a}}">
                                 <td>
                                  <select name="module_name[]" class="form-control module_name"  required>
                                    <option value="">Select Module</option>
                                    @foreach($modules as $module)
                                    <option value="{{$module->id}}">{{$module->module_name}}</option>
                                    @endforeach
                                  </select>
                                 </td>
                                 <td>
                                  <input type="text" name="sub_module_name[]" class="form-control sub_module_name"  required>
                                 </td>
                                 <td><button type="button" name="add" class="btn btn-success btn-sm add" id="add_new" style="display:{{$a == 1 ? 'block':'none'}};"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                 <button type="button" name="remove" class="btn btn-danger btn-sm remove-div remove_elm" style="display:{{$a == 1 ? 'none':'block'}};" ><i class="fa fa-minus-circle"></i></button>
                                 </td>
                                 </td>
                              </tr>
                              @endif
                           </table>
                        </div>
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

