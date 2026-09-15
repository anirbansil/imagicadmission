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
          <div class="add-new-strc">
            <a class="add-new-strc-btn" href="{{route('course-master.create')}}">Add Course</a>
          </div>
          <div class="relg-list">
            <div class="table-responsive">
              <table class="listingtable">
                <tr>
                  <th>Sl No</th>
                  <th>Course Name</th>
                  <th>Duration</th>
                  <th>Fees</th>
                  <th>Include GST</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                @php $sl_no=1; @endphp
                  @forelse($categories as $category)
                  <tr> 
                    <td>{{$sl_no}}</td>
                    <td>{{@$category->name}}</td>
                    <td>{{@$category->duration}} Days</td>
                    <td>{{@$category->fees}}</td>
                    <td>{{@$category->gst == 1 ? 'Yes' : 'No'}}</td>
                    <td>{{@$category->status == 1 ? 'Active' : 'Inactive'}}</td>
                    <td>
                      <a href="{{route('course-master.edit', ['id'=>$category->id])}}" title="Edit" class="edit">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                      </a>
                      @php
                        $deleteroute = route('course-master.delete', ['id'=>$category->id]);
                      @endphp
                      <a href="javascript:void(0)" title="Delete" onclick="deleteData('{{$deleteroute}}','{{csrf_token()}}')" class="delete">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                      </a>
                      <a href="{{route('course-master.submodule', ['id'=>$category->id])}}" title="Add sub Module"  class="add_module">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                      </a>
                    </td>
                  </tr>
                  @php $sl_no++; @endphp
                  @empty
                  <tr>
                    <td colspan="9" style="text-align: center;">No Category available</td>
                  </tr>
                 @endforelse
                
              </table>
            </div>
          </div>
          <div class="pagination-sec">
            {{$categories->links()}}
          </div>
         </div>
        </div>
      </div>
  </div>
  
</div>

@endsection
