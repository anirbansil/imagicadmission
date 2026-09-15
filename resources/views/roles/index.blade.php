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
              <li class="breadcrumb-item active" aria-current="page">Roles and Permissions</li>
            </ul>
          </nav>
        </h1>
        <div class="dashboard-materials">
         <div class="relg-new-struct-add">
          @if(App\Helpers\Helper::sidenavAccess('role-create'))
          <div class="add-new-strc">
            <a class="add-new-strc-btn" href="{{route('role.create')}}">Add New Role</a>
          </div>
          @endif
          <div class="relg-list">
            <div class="table-responsive">
              <table>
                <tr>
                  <th>Sl No.</th>
                  <th>Name</th>
                  <th>Slug</th>
                  <th>Permissions</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                @php $count=1; @endphp
                @forelse($roles as $role)
               
                    <tr>
                      <td>{{$count}}</td>
                      <td>{{$role->name}}</td>
                      <td>{{$role->slug}}</td>
                      <td>{{$role->getPermissions($role->permission_array) }}</td>
                      <td>Active</td>
                      <td>
                        @if(App\Helpers\Helper::sidenavAccess('role-edit'))
                        <a href="{{route('role.edit', ['id'=>encrypt($role->id)])}}" class=""><img src="{{asset('public/images/draw.png')}}"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                        @endif
                        @php
                          $deleteroute = route('role.delete', ['id'=>$role->id]);
                        @endphp
                        @if(App\Helpers\Helper::sidenavAccess('role-delete'))
                           <a href="javascript:void(0)" onclick="deleteData('{{$deleteroute}}','{{csrf_token()}}')" class=""><img src="{{asset('public/images/trash.png')}}"></a>
                        @endif
                        
                      </td>
                    </tr>
                    @php $count++; @endphp
                @empty
                  <tr>
                    <td colspan="6" style="text-align: center;">No data available</td>
                  </tr>
                @endforelse
              </table>
            </div>
          </div>
          <div class="pagination-sec">
            <!-- <p>Showing 1 - 8 results </p> -->
            {{ $roles->links() }}
          </div>
         </div>
        </div>
      </div>
  </div>
  
</div>
 
@endsection
