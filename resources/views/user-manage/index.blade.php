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
                    <div class="relg-new-struct-add">
                        <form action="{{route('user-manage.index')}}" method="get">
                            <div class="filter_row">
                              <div class="filter_div row">
                                <div class="col-xs-12 col-md-4 col-sm-6">
                                <div class="form-group">
                                <select class="form-control" name="search_by_role">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}" {{$request->has('search_by_role') && $request->search_by_role == $role->id ? 'selected': '' }}>{{$role->name}}</option>
                                      @endforeach
                                    </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4 col-sm-6">
                                    <div class="form-group">
                                      <input type="text" class="form-control" name="input_search" placeholder="Search by Name, phone number, Email" value="{!!($request->has('input_search')) && !empty($request->input_search) ?$request->input_search:''!!}">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4 col-sm-6">
                                  <button type="submit" class="btn btn-info">Search</button>
                                  <a href="{{route('user-manage.index')}}" class="reset">Reset</a>
                                </div>
                              </div>
                            </div>
                          </form>
                    </div>
                </div>
        <div class="dashboard-materials">
         <div class="relg-new-struct-add">
          @if(App\Helpers\Helper::sidenavAccess('user-create'))
          <div class="add-new-strc">
            <a class="add-new-strc-btn" href="{{route('user-manage.create')}}">Add New User</a>
          </div>
          @endif
          <div class="relg-list">
            <div class="table-responsive">
              <table>
                <tr>
                  <th>SL</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone Number</th>
                  <th>Center</th>
                  <th>User Role</th>
                  <th>Actions</th>
                </tr>
                @php $sl_no=1; @endphp
                @forelse($users as $user)
                <tr>
                  <td><span>{{$sl_no}}</span></td>
                  <td>{{$user->detailsUser->first_name}}  {{$user->detailsUser->last_name}}</td>
                  <td>{{$user->email}}</td>
                  <td>{{$user->phone_no}}</td>
                  <td>{{@App\Helpers\Helper::getCenterName(@$user->branch_id)}}</td>
                  <td>{{implode(',',$user->roles()->pluck('name')->toArray())}}</td>
                  <td>
                    @php
                      $deleteroute = route('user-manage.delete', ['id'=>$user->id]);
                    @endphp
                    @if(App\Helpers\Helper::sidenavAccess('user-edit'))
                    <a class="edit" href="{{route('user-manage.edit', $user->id)}}"><img src="{{asset('public/images/draw.png')}}"></a>
                    @endif
                    @if(App\Helpers\Helper::sidenavAccess('user-delete'))
                    <a href="javascript:void(0)" onclick="deleteData('{{$deleteroute}}','{{csrf_token()}}')" class="delete">
                      <img src="{{asset('public/images/trash.png')}}">
                    </a>
                    @endif
                  </td>
                </tr>
                @php $sl_no++; @endphp
                @empty
                <tr>
                  <td colspan="7" style="text-align: center;">No data available</td>
                </tr>
               @endforelse
              </table>
            </div>
          </div>
          <div class="pagination-sec">
            {{$users->links()}}
          </div>
         </div>
        </div>
      </div>
  </div>
  
</div>

@endsection