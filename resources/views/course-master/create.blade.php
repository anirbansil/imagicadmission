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
                 <form role="form" action="{{ route('course-master.store') }}" method="POST" enctype="multipart/form-data">
                 @csrf
                  <div class="form-inlinex" style="display: flex; flex-wrap: wrap; flex-direction: row; align-content: flex-start; justify-content: flex-start; align-items: stretch;">
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Course Name
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="name" class="form-control required_field" value="{{old('name')}}">
                        @if($errors->has('name'))
                        <span class="error">{{$errors->first('name')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Duration (In Days)
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="duration" class="form-control required_field" value="{{old('duration')}}">
                        @if($errors->has('duration'))
                        <span class="error">{{$errors->first('duration')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Fees
                          <span class="required_field">*</span>
                        </label>
                        <input type="number" name="fees" class="form-control required_field" value="{{old('fees')}}">
                        @if($errors->has('fees'))
                        <span class="error">{{$errors->first('fees')}}</span>
                        @endif
                      </div>
                    </div>

                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Number of EMI </label>
                        <input type="number" name="number_of_emi" class="form-control required_field" value="{{old('number_of_emi')}}">
                        @if($errors->has('number_of_emi'))
                        <span class="error">{{$errors->first('number_of_emi')}}</span>
                        @endif
                      </div>
                    </div>

                    {{--<div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Add Module [use comma(,) separated]
                        </label>
                        <textarea name="module"></textarea>
                        @if($errors->has('module'))
                        <span class="error">{{$errors->first('module')}}</span>
                        @endif
                      </div> 
                    </div>--}}
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Include GST
                          <span class="required_field">*</span>
                        </label>
                        <select name="gst">
                          <option value="1" {{old('gst') == 1 ? 'selected' : ''}}>Yes</option>
                          <option value="2" {{old('gst') == 2 ? 'selected' : ''}}>No</option>
                        </select>
                        @if($errors->has('gst'))
                        <span class="error">{{$errors->first('gst')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Status
                          <span class="required_field">*</span>
                        </label>
                        <select name="status">
                          <option value="1" {{old('status') == 1 ? 'selected' : ''}}>Active</option>
                          <option value="2" {{old('status') == 2 ? 'selected' : ''}}>Inactive</option>
                        </select>
                        @if($errors->has('status'))
                        <span class="error">{{$errors->first('status')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class=" col-12">
                        <table class="table table-bordered" id="item_table">
                          <tr>
                          <th>Module Name</th>
                          <th>Date</th>
                          <th>Action</th>
                          </tr>
                          @for($a=1; $a < 2; $a++ )
                          <tr class="append-dive">
                              <td><input class="form-control" type="text" name="module[]" id="module" required></td>
                              <td><input class="form-control" type="date" name="module_date[]" id="module_date[]"></td>
                              <td><button type="button" name="add" class="btn btn-success btn-sm add" id="add_new"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                              <button type="button" name="remove" class="btn btn-danger btn-sm remove-div remove_elm" style="display:none;" ><i class="fa fa-minus-circle"></i></button>
                              </td>
                          </tr>
                          @endfor
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
