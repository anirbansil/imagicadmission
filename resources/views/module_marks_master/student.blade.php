@extends('layout')
@section('title', $title)
@section('content')
@php
  $pct = function ($p) { return App\Services\StudentResults::formatPercentage($p); };
  $fmt = function ($d) { return $d ? $d->format('d M Y') : '—'; };
  $r = $student->registration;
  $states = ['running' => 'Current', 'upcoming' => 'Upcoming', 'completed' => 'Completed'];
@endphp
<div id="main" class="admin_main">
@include('module_marks_master._styles')
<style>
  .mk-courses { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
  .mk-course { display: flex; flex-direction: column; margin-bottom: 0; overflow: hidden; }
  .mk-course.running, .mk-course.upcoming { border-top: 3px solid var(--mk-accent); }
  .mk-course.completed { border-top: 3px solid #2f8a4c; }
  .mk-course.running .mk-card-head, .mk-course.upcoming .mk-card-head { background: #fff6dc; }
  .mk-course.completed .mk-card-head { background: #e8f5ec; }
  .mk-course .mk-card-head { align-items: flex-start; }
  .mk-course .mk-table td { padding: 7px 12px; }
  .mk-course .mk-footer { margin-top: auto; }
  .mk-state { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; border-radius: 12px; padding: 2px 9px; white-space: nowrap; }
  .mk-state.running, .mk-state.upcoming { background: #fbe7b0; color: #7a5200; }
  .mk-state.completed { background: #cdebd6; color: #1e6b34; }
  .mk-course-stats { display: grid; grid-template-columns: repeat(3, 1fr); border-bottom: 1px solid var(--mk-line); }
  .mk-course-stats div { padding: 10px 18px; border-right: 1px solid var(--mk-line); }
  .mk-course-stats div:last-child { border-right: 0; }
  .mk-course-stats span { display: block; font-size: 11px; color: var(--mk-muted); text-transform: uppercase; letter-spacing: .4px; font-weight: 600; }
  .mk-course-stats strong { font-size: 16px; font-variant-numeric: tabular-nums; }
  @media (max-width: 991px) { .mk-courses { grid-template-columns: 1fr; } }
</style>
  <div class="admin-inn-contents">
    <div class="container-fluid">
      <h1 class="main-title">{{$title}}
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('student.marks.index')}}"><i class="fa fa-home" aria-hidden="true"></i>Module Marks</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $student->name }}</li>
          </ul>
        </nav>
      </h1>

      <div class="dashboard-materials mk-page">
        <div class="mk-card">
          <div class="mk-card-head">
            <div>
              <h3>{{ $student->name }}</h3>
              <p>{{ $r->roll_no }}@if($student->center) · {{ $student->center }}@endif @if($r->mobile_no) · {{ $r->mobile_no }}@endif @if($r->email) · {{ $r->email }}@endif</p>
            </div>
            <a class="mk-btn" href="{{ route('student.marks.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> All students</a>
          </div>
          <div class="mk-summary">
            <div><span>Courses</span><strong>{{ $enrolments->count() }}</strong></div>
            <div><span>Current</span><strong>{{ $enrolments->where('state', '!=', 'completed')->count() }}</strong></div>
            <div><span>Completed</span><strong>{{ $enrolments->where('state', 'completed')->count() }}</strong></div>
            <div><span>Final results</span><strong>{{ $enrolments->filter(function ($e) { return $e->sheet->final; })->count() }}</strong></div>
          </div>
        </div>

        @if($enrolments->isEmpty())
          <div class="mk-card"><div class="mk-empty">This student is not enrolled in any course yet.</div></div>
        @else
        <div class="mk-courses">
          @foreach($enrolments as $e)
          @php $s = $e->sheet; @endphp
          <div class="mk-card mk-course {{ $e->state }}">
            <div class="mk-card-head">
              <div>
                <h3>{{ $e->course->name }}</h3>
                <p>{{ $e->application->application_id }} · {{ $fmt($e->start) }} – {{ $fmt($e->end) }}</p>
              </div>
              <span class="mk-state {{ $e->state }}">{{ $states[$e->state] }}</span>
            </div>
            <div class="mk-course-stats">
              <div><span>Evaluated</span><strong>{{ $s->evaluated }} / {{ $s->total }}</strong></div>
              <div><span>Overall</span><strong>{{ $pct($s->percentage) }}</strong></div>
              <div><span>Grade</span><strong>@include('module_marks_master._grade', ['grade' => $s->grade])</strong></div>
            </div>
            @if($s->modules->isEmpty())
              <div class="mk-empty">No modules added for this course.</div>
            @else
            <div class="table-responsive">
              <table class="mk-table">
                <tbody>
                  @foreach($s->modules as $m)
                  <tr>
                    <td>{{ $m->module->module_name }}</td>
                    @if($m->mark)
                    <td class="num">{{ $m->theory }} + {{ $m->practical }} = <strong>{{ $m->obtained }}</strong>@if($m->full !== null) <span class="mk-of">/ {{ $m->full }}</span>@endif</td>
                    <td class="center" style="width: 60px">@include('module_marks_master._grade', ['grade' => $m->grade])</td>
                    @else
                    <td class="num mk-muted" colspan="2">Not entered</td>
                    @endif
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
            <div class="mk-footer">
              <p class="mk-note">
                @if($s->final) <span class="mk-pill final">Final result</span>
                @elseif($s->evaluated) <span class="mk-pill partial">{{ $s->total - $s->evaluated }} {{ $s->total - $s->evaluated === 1 ? 'module' : 'modules' }} pending</span>
                @else <span class="mk-pill none">No marks yet</span>
                @endif
              </p>
              <a class="mk-btn primary" href="{{ route('student.marks.sheet', $e->application->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> {{ $s->entered ? 'Edit marks' : 'Add marks' }}</a>
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
