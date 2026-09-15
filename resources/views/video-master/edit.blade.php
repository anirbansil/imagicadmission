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
                  <a href="{{route('video-master.index')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Video List</a></li>
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
                 <form role="form" action="{{ route('video-master.update',$category->id) }}" method="POST" enctype="multipart/form-data">
                 @csrf
                  <div class="form-inline">
                    <div class="form-group">
                      <label>Title
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="title" class="form-control required_field" value="{{old('title')? old('title') : $category->title}}">
                      @if($errors->has('title'))
                      <span class="error">{{$errors->first('title')}}</span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label>Iframe
                        <span class="required_field">*</span>
                      </label>
                      <textarea class="form-control required_field" name="iframe">{{old('iframe')? old('iframe') : $category->iframe}}</textarea>
                      @if($errors->has('iframe'))
                      <span class="error">{{$errors->first('iframe')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Status 
                        <span class="required_field">*</span>
                      </label>
                      <select name="status">
                        <option value="1" @if($category->status == 2) {{'selected'}} @endif>Active</option>
                        <option value="2" @if($category->status == 2) {{'selected'}} @endif>Inactive</option>
                      </select>
                      @if($errors->has('status'))
                      <span class="error">{{$errors->first('status')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>For 
                        <span class="required_field">*</span>
                      </label>
                      <select name="for_which">
                        <option value="1" @if($category->for_which == 2) {{'selected'}} @endif>Students</option>
                        <option value="2" @if($category->for_which == 2) {{'selected'}} @endif>Admin Users</option>
                        <option value="3" @if($category->for_which == 3) {{'selected'}} @endif>Enquiry Students</option>
                      </select>
                      @if($errors->has('for_which'))
                      <span class="error">{{$errors->first('for_which')}}</span>
                      @endif
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
