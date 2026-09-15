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
          </div>
          <div class="relg-list">
            <div class="container">
              <div class="row">
              
              @forelse($categories as $category)
                <div class="col-md-6">
                  <h3>{{@$category->title}}</h3><br>
                  <div>{!!@$category->iframe!!}</div>
                </div>
              @empty
                <p style="text-align: center;">No video available</p>
              @endforelse
                
              </div>
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
