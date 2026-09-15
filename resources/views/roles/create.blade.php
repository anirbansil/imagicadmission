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
                  <a href="{{route('role.index')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Roles and Permissions</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New Role</li>
              </ul>
          
            </nav>
          </h1>
          <div class="dashboard-materials">
            <div class="user-addform">  
              <div class="mainform cmnpadding ">   
                
                <div class="box-header with-border">
                  <!-- <h3 class="box-title">Role Form</h3> -->
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
                </div>

                <form role="form" action="{{ route('role.store') }}" method="POST" class="role_form" enctype="multipart/form-data">
                  {{csrf_field()}}
                  <div class="box-body">
                     <div class="row">
                        <div class="col-md-6 col-sm-6 col-12">
                           <div class="form-group  @if($errors->has('name'))has-error @endif">
                              <label for="f_name">Role Name</label>
                              <input type="text" name="name" class="form-control required_field">
                              @if($errors->has('name'))
                              <span class="error">{{$errors->first('name')}}</span>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-12">
                           <div class="form-group @if($errors->has('slug'))has-error @endif">
                              <label for="f_name">Role Slug</label>
                              <input type="text" name="slug" class="form-control required_field">
                              @if($errors->has('slug'))
                              <span class="error">{{$errors->first('slug')}}</span>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-12">
                           <div class="prmsn-form-wrp">
                              <div class="pWrap">
                                 <label>Permissions<span class="required_field">*</span></label>
                              </div>
                              <table border="1" class="table ">
                                 <tr>
                                    <thead>
                                       <th>Module / Action</th>
                                       <th>Permission</th>
                                    </thead>

                                    <tbody>
                                       @php $x = 1 @endphp
                                       @foreach($permissions as $permission)
                                       <tr>
                                          <td><h4>{{ $permission[0]->main_label }}</h4></td>
                                          <td>
                                             <label class="pCom">
                                                <input type="checkbox" class="minimal checkAll" data-chk="min_{{$x}}">&nbsp; All
                                             </label>
                                             @foreach($permission as $perm)
                                                <label class="pCom">
                                                   <input type="checkbox" class="minimal min_{{$x}}" data-p="min_{{$x}}" name="permissions[]" value="{{$perm->id}}">&nbsp; {{$perm->label}}
                                                </label>
                                             @endforeach
                                          </td>
                                       </tr>
                                       @php $x++; @endphp
                                       @endforeach
                                    </tbody>
                                    
                                 </tr>
                              </table>
                              @if($errors->has('permissions'))
                                 <span class="error">{{$errors->first('permissions')}}</span>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="box-footer">
                     <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>

              </div> 
             
            </div> 
          </div>
        </div>
  </div>
    
</div>
@endsection