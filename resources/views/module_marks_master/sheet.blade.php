@extends('layout')
@section('title', $title)
@section('content')
@php
  $pct = function ($p) { return App\Services\StudentResults::formatPercentage($p); };
  $defaultTheory = config('marks.default_full_marks.theory');
  $defaultPractical = config('marks.default_full_marks.practical');
@endphp
<div id="main" class="admin_main">
@include('module_marks_master._styles')
  <div class="admin-inn-contents">
    <div class="container-fluid">
      <h1 class="main-title">{{$title}}
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('student.marks.index')}}"><i class="fa fa-home" aria-hidden="true"></i>Module Marks</a></li>
            @if($application->regDetails)
            <li class="breadcrumb-item"><a href="{{ route('student.marks.student', $application->reg_id) }}">{{ $enrolment->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $enrolment->course }}</li>
          </ul>
        </nav>
      </h1>

      <div class="dashboard-materials mk-page">
        @if(Session::has('msg'))
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>{{ session('msg') }}
          </div>
        @endif
        @if($errors->any())
          <div class="mk-errors"><strong>Nothing was saved. Please fix these:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <div class="mk-card">
          <div class="mk-card-head">
            <div>
              <h3>{{ $enrolment->name }}</h3>
              <p>{{ $enrolment->roll_no }}@if($enrolment->center) · {{ $enrolment->center }}@endif</p>
            </div>
            @if($application->regDetails)
            <a class="mk-btn" href="{{ route('student.marks.student', $application->reg_id) }}"><i class="fa fa-th-large" aria-hidden="true"></i> All courses of this student</a>
            @endif
          </div>
          @if($siblings->count() > 1)
          {{-- One tab per enrolled course: marks are kept separately for each. --}}
          <nav class="mk-tabs" aria-label="Courses of this student">
            @foreach($siblings as $e)
            <a href="{{ route('student.marks.sheet', $e->application->id) }}" class="mk-tab {{ $e->application->id == $application->id ? 'active' : '' }}" @if($e->application->id == $application->id) aria-current="page" @endif>
              <span class="mk-tab-dot {{ $e->state }}" title="{{ $e->state === 'completed' ? 'Completed' : ($e->state === 'upcoming' ? 'Upcoming' : 'Current') }}"></span>
              <span class="mk-tab-name">{{ $e->course->name }}</span>
              <small>{{ $e->sheet->evaluated }}/{{ $e->sheet->total }}</small>
            </a>
            @endforeach
          </nav>
          @endif
          <div class="mk-summary">
            <div><span>Course</span><strong style="font-size: 15px">{{ $enrolment->course }}</strong></div>
            <div><span>Application</span><strong style="font-size: 15px">{{ $application->application_id }}</strong></div>
            <div><span>Batch</span><strong style="font-size: 15px">{{ $enrolment->start ? $enrolment->start->format('d M Y') : '—' }} – {{ $enrolment->end ? $enrolment->end->format('d M Y') : '—' }}</strong></div>
            <div><span>Overall</span><strong><span id="mk-overall-pct">{{ $pct($sheet->percentage) }}</span> <span id="mk-overall-grade">@include('module_marks_master._grade', ['grade' => $sheet->grade])</span></strong></div>
            <div><span>Result</span><strong style="font-size: 15px" id="mk-overall-state">
              @if($sheet->final) <span class="mk-pill final">Final</span>
              @elseif($sheet->evaluated) <span class="mk-pill partial">{{ $sheet->evaluated }} of {{ $sheet->total }} modules</span>
              @else <span class="mk-pill none">No results yet</span>
              @endif
            </strong></div>
          </div>
        </div>

        <form method="post" action="{{ route('student.marks.sheet.save', $application->id) }}" class="mk-card" id="mk-sheet">
          @csrf
          <div class="mk-card-head">
            <div>
              <h3>Module-wise Marks</h3>
              <p>Enter Theory and Practical marks out of each exam's full marks. Clear both marks of a module to remove its result.</p>
            </div>
          </div>
          @if($sheet->modules->isEmpty())
            <div class="mk-empty">This course has no modules yet. Add modules in Course Master first.</div>
          @else
          <div class="table-responsive">
            <table class="mk-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Module</th>
                  <th>Faculty</th>
                  <th>Exam Date</th>
                  <th>Theory <span style="text-transform: none">(obtained / full)</span></th>
                  <th>Practical <span style="text-transform: none">(obtained / full)</span></th>
                  <th class="num">Total</th>
                  <th class="num">%</th>
                  <th class="center">Grade</th>
                </tr>
              </thead>
              <tbody>
                @foreach($sheet->modules as $i => $m)
                @php
                  $id = $m->module->id;
                  $key = "rows.$id";
                  $facultyValue = old("$key.faculty_id", $m->faculty_id);
                @endphp
                <tr class="mk-row @if($errors->has($key)) mk-row-error @endif">
                  <td class="mk-muted">{{ $i + 1 }}</td>
                  <td><strong>{{ $m->module->module_name }}</strong></td>
                  <td>
                    <select name="rows[{{ $id }}][faculty_id]" class="mk-select" aria-label="Faculty for {{ $m->module->module_name }}">
                      <option value="">—</option>
                      @foreach($faculties as $f)
                      <option value="{{ $f->id }}" @if($facultyValue == $f->id) selected @endif>{{ App\Services\StudentEnrolments::facultyName($f) }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><input type="date" name="rows[{{ $id }}][exam_date]" class="mk-date" value="{{ old("$key.exam_date", $m->exam_date ? $m->exam_date->toDateString() : '') }}" aria-label="Exam date for {{ $m->module->module_name }}"></td>
                  <td style="white-space: nowrap">
                    <input type="text" inputmode="numeric" name="rows[{{ $id }}][theory]" class="mk-input js-ob" value="{{ old("$key.theory", $m->mark ? $m->theory : '') }}" aria-label="Theory marks">
                    <span class="mk-of">/</span>
                    <input type="text" inputmode="numeric" name="rows[{{ $id }}][theory_full]" class="mk-input js-full" style="width: 58px" value="{{ old("$key.theory_full", $m->theory_full ?? $defaultTheory) }}" aria-label="Theory full marks">
                  </td>
                  <td style="white-space: nowrap">
                    <input type="text" inputmode="numeric" name="rows[{{ $id }}][practical]" class="mk-input js-ob" value="{{ old("$key.practical", $m->mark ? $m->practical : '') }}" aria-label="Practical marks">
                    <span class="mk-of">/</span>
                    <input type="text" inputmode="numeric" name="rows[{{ $id }}][practical_full]" class="mk-input js-full" style="width: 58px" value="{{ old("$key.practical_full", $m->practical_full ?? $defaultPractical) }}" aria-label="Practical full marks">
                  </td>
                  <td class="num js-total">{{ $m->mark ? $m->obtained.' / '.($m->full ?? '?') : '—' }}</td>
                  <td class="num js-pct">{{ $pct($m->percentage) }}</td>
                  <td class="center js-grade">@include('module_marks_master._grade', ['grade' => $m->grade])</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mk-footer">
            <p class="mk-note">Grades: @foreach(config('marks.grades') as $g){{ $g['grade'] }} {{ $g['min'] }}%+@if(!$loop->last), @endif @endforeach · Pass mark {{ config('marks.pass_percentage') }}%</p>
            <button type="submit" class="mk-btn primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save Result Sheet</button>
          </div>
          @endif
        </form>
      </div>
    </div>
  </div>
</div>
@include('module_marks_master._live-script', ['rowSelector' => '#mk-sheet .mk-row', 'overall' => true])
@endsection
