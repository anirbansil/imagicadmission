@extends('layout')
@section('title', $title)
@section('content')
@php
  $pct = function ($p) { return App\Services\StudentResults::formatPercentage($p); };
  $fmt = function ($date) { return $date ? $date->format('d M Y') : '—'; };
  $sections = [
    ['key' => 'current', 'title' => 'Current Courses', 'icon' => 'fa-hourglass-half', 'items' => $current],
    ['key' => 'completed', 'title' => 'Completed Courses', 'icon' => 'fa-check-circle', 'items' => $completed],
  ];
@endphp
<div id="main" class="admin_main">
@include('module_marks_master._styles')
<style>
  .mks-page { --mks-ink: #2B2022; --mks-muted: #8a7f80; --mks-line: #ece6e3; --mks-soft: #faf7f5; --mks-primary: #571d1f;
    --mks-yellow-bg: #fff6dc; --mks-yellow-line: #f3dfa3; --mks-accent: #eca107; --mks-green: #2f8a4c; --mks-green-bg: #e8f5ec; --mks-green-line: #c9e6d2; color: var(--mks-ink); }
  .mks-page.dashboard-materials { margin-top: 10px; }
  .mks-page * { box-sizing: border-box; }
  .mks-intro { color: var(--mks-muted); margin: 0 0 14px; }
  .mks-page .mks-section { margin: 0 0 22px; padding: 0; } /* site CSS gives every <section> 70px padding */
  .mks-section-head { display: flex; align-items: center; gap: 10px; margin: 0 0 10px; }
  .mks-section-head h2 { font-size: 19px; font-weight: 700; margin: 0; }
  .mks-section-icon { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; }
  .mks-section-icon.current { background: var(--mks-yellow-bg); color: #a87100; }
  .mks-section-icon.completed { background: var(--mks-green-bg); color: var(--mks-green); }

  .mks-card { background: #fff; border: 1px solid var(--mks-line); border-radius: 10px; box-shadow: 0 1px 3px rgba(43, 32, 34, .05); overflow: hidden; margin-bottom: 16px; }
  .mks-card.current { border-top: 3px solid var(--mks-accent); }
  .mks-card.completed { border-top: 3px solid var(--mks-green); }
  .mks-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; padding: 14px 18px; border-bottom: 1px solid var(--mks-line); }
  .mks-card.current .mks-head { background: var(--mks-yellow-bg); border-bottom-color: var(--mks-yellow-line); }
  .mks-card.completed .mks-head { background: var(--mks-green-bg); border-bottom-color: var(--mks-green-line); }
  .mks-head h3 { font-size: 17px; font-weight: 600; margin: 0 0 3px; }
  .mks-head p { margin: 0; color: var(--mks-muted); font-size: 13px; }

  .mks-score { display: flex; align-items: center; gap: 14px; }
  .mks-ring { --p: 0; width: 64px; height: 64px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    background: conic-gradient(var(--mks-primary) calc(var(--p) * 1%), rgba(87, 29, 31, .12) 0); }
  .mks-ring span { width: 50px; height: 50px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13.5px; font-variant-numeric: tabular-nums; }
  .mks-score-text strong { display: block; font-size: 13px; }
  .mks-score-text small { display: block; color: var(--mks-muted); font-size: 12px; margin-top: 3px; }

  .mks-stats { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 1px solid var(--mks-line); }
  .mks-stats div { padding: 10px 18px; border-right: 1px solid var(--mks-line); }
  .mks-stats div:last-child { border-right: 0; }
  .mks-stats span { display: block; font-size: 11px; letter-spacing: .4px; text-transform: uppercase; color: var(--mks-muted); font-weight: 600; margin-bottom: 2px; }
  .mks-stats strong { font-size: 15px; font-variant-numeric: tabular-nums; }
  .mks-pass { color: var(--mks-green); }
  .mks-fail { color: #a12620; }

  .mks-page .mk-table td { padding: 9px 12px; }
  .mks-pending td { color: var(--mks-muted); }
  .mks-pending em { font-style: normal; font-size: 12.5px; background: var(--mks-soft); border: 1px dashed var(--mks-line); border-radius: 12px; padding: 2px 10px; }
  .mks-empty { background: var(--mks-soft); border: 1px dashed var(--mks-line); border-radius: 10px; padding: 18px; color: var(--mks-muted); }
  .mks-legend { font-size: 12.5px; color: var(--mks-muted); display: flex; gap: 6px 12px; flex-wrap: wrap; align-items: center; }

  @media (max-width: 767px) {
    .mks-stats { grid-template-columns: 1fr 1fr; }
    .mks-stats div:nth-child(2) { border-right: 0; }
    .mks-stats div:nth-child(-n+2) { border-bottom: 1px solid var(--mks-line); }
  }
  @media (max-width: 575px) {
    .mks-head, .mks-stats div { padding-left: 14px; padding-right: 14px; }
  }
</style>
  <div class="admin-inn-contents">
    <div class="container-fluid">
      <h1 class="main-title">{{$title}}
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('student.profile')}}"><i class="fa fa-home" aria-hidden="true"></i>Home</a></li>
            <li class="breadcrumb-item active">{{$title}}</li>
          </ul>
        </nav>
      </h1>
      <div class="dashboard-materials mks-page">
        <p class="mks-intro">Your module-wise exam results. Each module has a Theory and a Practical exam; your percentage and grade are worked out from both.</p>

        @if($registrations->isEmpty())
          <div class="alert alert-warning">No registration details were found for {{$user->email}}. Please contact your center.</div>
        @elseif($current->isEmpty() && $completed->isEmpty())
          <div class="mks-empty">You are not enrolled in any course yet, so there are no marks to show.</div>
        @endif

        @foreach($sections as $section)
        @if($section['items']->isNotEmpty())
        <section class="mks-section">
          <div class="mks-section-head">
            <span class="mks-section-icon {{ $section['key'] }}"><i class="fa {{ $section['icon'] }}" aria-hidden="true"></i></span>
            <h2>{{ $section['title'] }}</h2>
          </div>

          @foreach($section['items'] as $e)
          @php $s = $sheets[$e->application->id]; @endphp
          <div class="mks-card {{ $section['key'] }}">
            <div class="mks-head">
              <div>
                <h3>{{ $e->course->name }}</h3>
                <p>Application {{ $e->application_id }} · {{ $fmt($e->start) }} – {{ $fmt($e->end) }}</p>
              </div>
              <div class="mks-score">
                <div class="mks-ring" style="--p: {{ $s->percentage ?? 0 }}"><span>{{ $s->percentage === null ? '—' : $pct($s->percentage) }}</span></div>
                <div class="mks-score-text">
                  <strong>Overall @include('module_marks_master._grade', ['grade' => $s->grade])</strong>
                  <small>
                    @if($s->final) Final result
                    @elseif($s->evaluated) Based on {{ $s->evaluated }} of {{ $s->total }} modules
                    @else No results yet
                    @endif
                  </small>
                </div>
              </div>
            </div>
            <div class="mks-stats">
              <div><span>Modules Evaluated</span><strong>{{ $s->evaluated }} / {{ $s->total }}</strong></div>
              <div><span>Marks Obtained</span><strong>{{ $s->evaluated ? $s->obtained.' / '.$s->full : '—' }}</strong></div>
              <div><span>Percentage</span><strong>{{ $pct($s->percentage) }}</strong></div>
              <div><span>Result</span><strong>
                @if(!$s->grade) —
                @elseif(!$s->final) <span class="mk-muted" style="font-weight: 600">In progress</span>
                @elseif($s->grade['pass']) <span class="mks-pass">Pass · {{ $s->grade['remark'] }}</span>
                @else <span class="mks-fail">Not passed</span>
                @endif
              </strong></div>
            </div>

            @if($s->modules->isEmpty())
              <div class="mk-empty">Modules for this course have not been added yet.</div>
            @else
            <div class="table-responsive">
              <table class="mk-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Module</th>
                    <th>Evaluated By</th>
                    <th>Exam Date</th>
                    <th class="num">Theory</th>
                    <th class="num">Practical</th>
                    <th class="num">Total</th>
                    <th class="num">%</th>
                    <th class="center">Grade</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($s->modules as $i => $m)
                  @if(!$m->mark)
                  <tr class="mks-pending">
                    <td>{{ $i + 1 }}</td>
                    <td><strong style="color: var(--mks-ink)">{{ $m->module->module_name }}</strong></td>
                    <td colspan="7"><em><i class="fa fa-clock-o" aria-hidden="true"></i> Result awaited</em></td>
                  </tr>
                  @else
                  <tr>
                    <td class="mk-muted">{{ $i + 1 }}</td>
                    <td><strong>{{ $m->module->module_name }}</strong></td>
                    <td>{{ $m->faculty ?: '—' }}</td>
                    <td style="white-space: nowrap">{{ $fmt($m->exam_date) }}</td>
                    {{-- Older results may not have full marks yet: show the marks without "/ full". --}}
                    <td class="num">{{ $m->theory }}@if($m->theory_full !== null) <span class="mk-of">/ {{ $m->theory_full }}</span>@endif</td>
                    <td class="num">{{ $m->practical }}@if($m->practical_full !== null) <span class="mk-of">/ {{ $m->practical_full }}</span>@endif</td>
                    <td class="num"><strong>{{ $m->obtained }}</strong>@if($m->full !== null) <span class="mk-of">/ {{ $m->full }}</span>@endif</td>
                    <td class="num">{{ $pct($m->percentage) }}</td>
                    <td class="center">@include('module_marks_master._grade', ['grade' => $m->grade])</td>
                  </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
          @endforeach
        </section>
        @endif
        @endforeach

        @if($current->isNotEmpty() || $completed->isNotEmpty())
        <p class="mks-legend"><strong>Grades:</strong>
          @foreach(config('marks.grades') as $g)
            <span>@include('module_marks_master._grade', ['grade' => $g]) {{ $g['min'] }}%+</span>
          @endforeach
          <span>· Pass mark {{ config('marks.pass_percentage') }}%</span>
        </p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
