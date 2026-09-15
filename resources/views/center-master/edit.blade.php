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
                  <a href="{{route('center-master.index')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Centre List</a></li>
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
                 <form role="form" action="{{ route('center-master.update',$category->id) }}" method="POST" enctype="multipart/form-data">
                 @csrf
                  <div class="form-inline">
                    <div class="form-group">
                      <label>Course Name
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="name" class="form-control required_field" value="{{old('name')? old('name') : $category->name}}">
                      @if($errors->has('name'))
                      <span class="error">{{$errors->first('name')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Include GST 
                        <span class="required_field">*</span>
                      </label>
                      <select name="gst">
                        <option value="1" @if($category->gst == 1) {{'selected'}} @endif>Yes</option>
                        <option value="2" @if($category->gst == 2) {{'selected'}} @endif>No</option>
                      </select>
                      @if($errors->has('gst'))
                      <span class="error">{{$errors->first('gst')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>GST No.
                        <span class="required_field">*</span>
                      </label>
                      <input type="text" name="gst_no" class="form-control required_field" value="{{old('gst_no')? old('gst_no') : $category->gst_no}}">
                      @if($errors->has('gst_no'))
                      <span class="error">{{$errors->first('gst_no')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Scholership Percentage
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="number" name="sc_per" class="form-control required_field" value="{{old('sc_per')? old('sc_per') : $category->sc_per}}">
                      @if($errors->has('sc_per'))
                      <span class="error">{{$errors->first('sc_per')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Center Credit
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="hidden" name="center_amount_old" class="form-control required_field" value="{{old('center_amount')? old('center_amount') : $category->center_amount}}">

                      <input type="number" name="center_amount" class="form-control required_field" value="{{old('center_amount')? old('center_amount') : $category->center_amount}}">
                      @if($errors->has('center_amount'))
                      <span class="error">{{$errors->first('center_amount')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Credit Start Date
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="date" name="center_amount_updated" class="form-control required_field" value="{{old('center_amount_updated')? old('center_amount_updated') : $category->center_amount_updated}}">
                      @if($errors->has('center_amount_updated'))
                      <span class="error">{{$errors->first('center_amount_updated')}}</span>
                      @endif
                    </div>

                    <div class="form-group">
                      <label>Used Credit
                        <!-- <span class="required_field">*</span> -->
                      </label>
                      <input type="number" name="use_amount" class="form-control required_field" value="{{old('use_amount')? old('use_amount') : $category->use_amount}}" readonly>
                      @if($errors->has('use_amount'))
                      <span class="error">{{$errors->first('use_amount')}}</span>
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
                      <label>&nbsp;</label>
                      <input type="submit" value="Save" class="finalsubmit">
                    </div>
                    
                  </div>
                  
                 </form>
              </div> 
            </div> 
          </div>
        </div>
  </div>
    
</div>
@endsection  
