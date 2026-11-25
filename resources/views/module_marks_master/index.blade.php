@extends('layout')
@section('title', $title)
@section('content')
@php
  $pct = function ($p) { return App\Services\StudentResults::formatPercentage($p); };
  $fmt = function ($d) { $d = App\Services\StudentEnrolments::date($d); return $d ? $d->format('d M Y') : '—'; };
@endphp
<div id="main" class="admin_main">
@include('module_marks_master._styles')
  <div class="admin-inn-contents">
    <div class="container-fluid">
      <h1 class="main-title">{{$title}}
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i>Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
          </ul>
        </nav>
      </h1>

      <div class="dashboard-materials mk-page">
        @if(Session::has('msg'))
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>{{ session('msg') }}
          </div>
        @endif

        <div class="mk-card">
          <div class="mk-card-head">
            <div>
              <h3>Student Results</h3>
              <p>One row per course enrolment. Click a student's name to see all their courses, or open a result sheet to add or edit marks for one course.</p>
            </div>
            <a class="mk-btn accent" href="{{ route('student.marks.create') }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Enter Marks by Module</a>
          </div>
          <form class="mk-filters" method="get" action="{{ route('student.marks.index') }}">
            <div class="mk-field wide">
              <label for="mk-q">Student</label>
              <input id="mk-q" type="search" name="q" value="{{ $request->q }}" class="form-control" placeholder="Name, registration no., application no., phone">
            </div>
            <div class="mk-field wide">
              <label for="mk-course">Course</label>
              <select id="mk-course" name="course" class="form-control">
                <option value="">All courses</option>
                @foreach($courseOptions as $c)
                <option value="{{ $c->id }}" @if($request->course == $c->id) selected @endif>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            @if($seesAll)
            <div class="mk-field">
              <label for="mk-center">Center</label>
              <select id="mk-center" name="center" class="form-control">
                <option value="">All centers</option>
                @foreach($centers as $c)
                <option value="{{ $c->id }}" @if($request->center == $c->id) selected @endif>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="mk-field">
              <label for="mk-batch">Batch</label>
              <select id="mk-batch" name="batch" class="form-control">
                <option value="all" @if(($request->batch ?: 'all') === 'all') selected @endif>All batches</option>
                <option value="current" @if($request->batch === 'current') selected @endif>Current</option>
                <option value="completed" @if($request->batch === 'completed') selected @endif>Completed</option>
              </select>
            </div>
            <div class="mk-actions">
              <button type="submit" class="mk-btn primary"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
              <a href="{{ route('student.marks.index') }}" class="mk-link">Reset</a>
            </div>
          </form>
        </div>

        <div class="mk-card">
          <div class="table-responsive">
            <table class="mk-table">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Course</th>
                  <th>Batch</th>
                  <th>Modules Evaluated</th>
                  <th class="num">Marks</th>
                  <th class="num">Overall</th>
                  <th class="center">Grade</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse($applications as $a)
                @php
                  $s = $sheets[$a->id];
                  $r = $a->regDetails;
                  $name = $r ? trim(preg_replace('/\s+/', ' ', $r->first_name.' '.$r->mid_name.' '.$r->last_name)) : '—';
                @endphp
                <tr>
                  <td>
                    @if($r)
                    <a href="{{ route('student.marks.student', $r->id) }}" class="mk-student-link" title="All courses of {{ $name }}"><strong>{{ $name }}</strong></a>
                    @if(($courseCounts[$r->id] ?? 1) > 1)<span class="mk-count">{{ $courseCounts[$r->id] }} courses</span>@endif
                    @else
                    <strong>{{ $name }}</strong>
                    @endif
                    <small>{{ optional($r)->roll_no }}@if($r && $seesAll) · {{ App\Helpers\Helper::getCenterName($r->center_name) }}@endif</small>
                  </td>
                  <td>
                    {{ optional($courses->get($a->course))->name ?? '—' }}
                    <small>{{ $a->application_id }}</small>
                  </td>
                  <td class="mk-muted" style="white-space: nowrap">{{ $fmt($a->batch_start_date) }}<small>to {{ $fmt($a->batch_end_date) }}</small></td>
                  <td>
                    @if($s->total)
                    <div class="mk-bar"><span><i style="width: {{ round($s->evaluated * 100 / $s->total) }}%"></i></span><b>{{ $s->evaluated }}/{{ $s->total }}</b></div>
                    @else
                    <span class="mk-muted">No modules</span>
                    @endif
                  </td>
                  <td class="num">{{ $s->evaluated ? $s->obtained.' / '.$s->full : '—' }}</td>
                  <td class="num"><strong>{{ $pct($s->percentage) }}</strong></td>
                  <td class="center">@include('module_marks_master._grade', ['grade' => $s->grade])</td>
                  <td class="num"><a class="mk-btn small" href="{{ route('student.marks.sheet', $a->id) }}"><i class="fa fa-list-alt" aria-hidden="true"></i> Result sheet</a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="mk-empty">No students match these filters.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <div class="pagination-sec">{{ $applications->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
