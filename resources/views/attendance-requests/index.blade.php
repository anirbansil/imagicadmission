@extends('layout')
@section('title', $title)
@section('content')
<div id="main" class="admin_main">
<style>
  .attreq-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 15px; }
  .attreq-tabs a { padding: 6px 14px; border: 1px solid #ddd; border-radius: 20px; color: #2B2022; font-size: 14px; text-decoration: none; background: #fff; }
  .attreq-tabs a.active { background: #571d1f; border-color: #571d1f; color: #fff; }
  .attreq-tabs a span { opacity: .75; margin-left: 4px; }
  .attreq-type { display: inline-block; padding: 1px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
  .attreq-type.leave { background: #eef1f5; color: #46566d; }
  .attreq-type.fix { background: #fdf0d3; color: #8a5a00; }
  .attreq-type.online { background: #e3f0fb; color: #1f4f8a; }
  .attreq-today { display: inline-block; margin-top: 3px; padding: 0 8px; border-radius: 10px; font-size: 11px; font-weight: 700; background: #571d1f; color: #fff; }
  .attreq-help { font-size: 13px; color: #6f6466; margin: -4px 0 12px; }
  .attreq-old { color: #999; text-decoration: line-through; margin-right: 6px; }
  .attreq-actions form { display: flex; gap: 6px; align-items: center; margin: 0; }
  .attreq-actions input { max-width: 170px; height: 32px; font-size: 13px; }
  .attreq-actions .btn { padding: 4px 10px; font-size: 13px; }
  .attreq-reason { white-space: normal; min-width: 180px; max-width: 280px; }
</style>
  <div class="admin-inn-contents">
    <div class="container-fluid">
      <h1 class="main-title">
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('checkin-checkout.list')}}">Attendance List</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
          </ul>
        </nav>
      </h1>
      @if(Session::has('msg') && !is_array(Session::get('msg')))
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

      <div class="relg-new-struct-add">
        <div class="attreq-tabs">
          @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
            <a href="{{route('attendance-requests.index', ['status' => $key, 'type' => $request->type])}}" class="{{$status == $key ? 'active' : ''}}">{{$label}}<span>({{$counts[$key] ?? 0}})</span></a>
          @endforeach
          <span style="flex:1"></span>
          @foreach(['' => 'All types', 'leave' => 'Leave', 'fix' => 'Fix', 'online' => 'Online class'] as $key => $label)
            <a href="{{route('attendance-requests.index', ['status' => $status, 'type' => $key])}}" class="{{(string) $request->type == $key ? 'active' : ''}}">{{$label}}</a>
          @endforeach
        </div>
        @if($status == 'pending' && $requests->contains('type', 'online'))
          <p class="attreq-help"><strong>Online class:</strong> approving lets the student check in from anywhere for that day's class instead of at the institute. Please review these before the class starts.</p>
        @endif

        <div class="relg-list">
          <div class="table-responsive">
            <table class="listingtable">
              <tr>
                <th>Sl No</th>
                <th>Student</th>
                <th>Type</th>
                <th>Date</th>
                <th>Requested Time</th>
                <th>Reason</th>
                <th>Submitted</th>
                @if($status == 'pending')<th>Action</th>@else<th>Reviewed</th>@endif
              </tr>
              @forelse($requests as $i => $req)
              <tr>
                <td>{{$requests->firstItem() + $i}}</td>
                <td>{{optional($req->user)->name}}<br><small>{{optional($req->user)->email}}</small></td>
                <td><span class="attreq-type {{$req->type}}">{{['fix' => 'Fix', 'leave' => 'Leave', 'online' => 'Online class'][$req->type] ?? ucfirst($req->type)}}</span></td>
                <td>
                  {{\Carbon\Carbon::parse($req->from_date)->format('d-M-Y')}}
                  @if($req->to_date != $req->from_date)<br>to {{\Carbon\Carbon::parse($req->to_date)->format('d-M-Y')}}@endif
                  @if($req->type == 'online' && $req->from_date == \App\Services\StudentAttendance::now()->toDateString())<br><span class="attreq-today">Today</span>@endif
                </td>
                <td>
                  @if($req->type == 'fix')
                    @php $cur = $current[$req->id] ?? null; @endphp
                    @if($status == 'pending')
                      <span class="attreq-old" title="Current record">{{$cur ? substr($cur->check_in_time, 0, 5).' – '.($cur->checkout_time ? substr($cur->checkout_time, 0, 5) : '…') : 'No record'}}</span><br>
                    @endif
                    <strong>{{substr($req->check_in_time, 0, 5)}} &ndash; {{$req->checkout_time ? substr($req->checkout_time, 0, 5) : '…'}}</strong>
                  @elseif($req->type == 'online')
                    Class {{substr($req->check_in_time, 0, 5)}} &ndash; {{substr($req->checkout_time, 0, 5)}}<br><small>Check in from anywhere</small>
                  @else
                    {{\Carbon\Carbon::parse($req->from_date)->diffInDays(\Carbon\Carbon::parse($req->to_date)) + 1}} day(s)
                  @endif
                </td>
                <td class="attreq-reason">{{$req->reason}}</td>
                <td>{{$req->created_at ? $req->created_at->timezone(config('attendance.timezone'))->format('d-M-Y h:i A') : ''}}</td>
                @if($status == 'pending')
                <td class="attreq-actions">
                  <form method="post">
                    @csrf
                    <input type="text" name="admin_note" class="form-control" placeholder="Note (optional)" maxlength="500">
                    <button type="submit" class="btn btn-success" formaction="{{route('attendance-requests.approve', ['id' => $req->id])}}">Approve</button>
                    <button type="submit" class="btn btn-danger" formaction="{{route('attendance-requests.reject', ['id' => $req->id])}}">Reject</button>
                  </form>
                </td>
                @else
                <td>
                  {{optional($req->reviewer)->name}}
                  @if($req->reviewed_at)<br><small>{{\Carbon\Carbon::parse($req->reviewed_at)->timezone(config('attendance.timezone'))->format('d-M-Y h:i A')}}</small>@endif
                  @if($req->admin_note)<br><small><em>{{$req->admin_note}}</em></small>@endif
                </td>
                @endif
              </tr>
              @empty
              <tr><td colspan="8">No {{$status}} requests.</td></tr>
              @endforelse
            </table>
          </div>
        </div>
        <div class="pagination-sec">
          {{$requests->links()}}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
