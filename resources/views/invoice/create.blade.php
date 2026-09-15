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
                 <form role="form" action="{{ route('invoice.download') }}" method="POST" enctype="multipart/form-data">
                 @csrf
                  <div class="form-inlinex" style="display: flex; flex-wrap: wrap; flex-direction: row; align-content: flex-start; justify-content: flex-start; align-items: stretch;">
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Company Name
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="company_name" class="form-control required_field" value="{{old('company_name')}}" required>
                        @if($errors->has('company_name'))
                        <span class="error">{{$errors->first('company_name')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>GSTIN/UIN
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="gst_in" class="form-control required_field" value="{{old('gst_in')}}" required>
                        @if($errors->has('gst_in'))
                        <span class="error">{{$errors->first('gst_in')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>State Name
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="state_name" class="form-control required_field" value="{{old('state_name')}}" required>
                        @if($errors->has('state_name'))
                        <span class="error">{{$errors->first('state_name')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-sm-12">
                      <div class="form-group">
                        <label>Address
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="address" class="form-control required_field" value="{{old('address')}}" required>
                        @if($errors->has('address'))
                        <span class="error">{{$errors->first('address')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Invoice No
                          <span class="required_field">*</span>
                        </label>
                        <input type="text" name="invoice_no" class="form-control required_field" value="{{old('invoice_no')}}" required>
                        @if($errors->has('invoice_no'))
                        <span class="error">{{$errors->first('invoice_no')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Date
                          <span class="required_field">*</span>
                        </label>
                        <input type="date" name="invoice_date" class="form-control required_field" value="{{old('invoice_date')}}" required>
                        @if($errors->has('invoice_date'))
                        <span class="error">{{$errors->first('invoice_date')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-6">
                      <div class="form-group">
                        <label>Include GST 
                          <span class="required_field">*</span>
                        </label>
                        <select name="include_gst">
                          <option value="">Select</option>
                          <option value="1">Yes</option>
                          <option value="2">No</option>
                        </select>
                        @if($errors->has('invoice_date'))
                        <span class="error">{{$errors->first('invoice_date')}}</span>
                        @endif
                      </div>
                    </div>

                    <div class=" col-12">
                        <table class="table table-bordered" id="item_table">
                          <tr>
                          <th>Description</th>
                          <th>HSN/SAC</th>
                          <th>Amount</th>
                          <th>Action</th>
                          </tr>
                          @for($a=1; $a < 2; $a++ )
                          <tr class="append-dive">
                              <td><input class="form-control" type="text" name="description[]" id="description" required></td>
                              <td><input class="form-control" type="text" name="hsn[]" id="hsn[]"></td>
                              <td><input class="form-control" type="number" name="amount[]" id="amount[]"></td>
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
