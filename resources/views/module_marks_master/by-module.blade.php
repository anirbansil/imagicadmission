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
            <li class="breadcrumb-item"><a href="{{route('student.marks.index')}}"><i class="fa fa-home" aria-hidden="true"></i>Module Marks</a></li>
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
        @if($errors->any())
          <div class="mk-errors"><strong>Nothing was saved. Please fix these:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        {{-- Step 1: which exam --}}
        <div class="mk-card">
          <div class="mk-card-head">
            <div>
              <h3>Choose the module exam</h3>
              <p>Pick a course and module to list its students, then enter everyone's marks at once.</p>
            </div>
          </div>
          <form class="mk-filters" method="get" action="{{ route('student.marks.create') }}" id="mk-pick">
            <div class="mk-field wide">
              <label for="mk-course">Course</label>
              <select id="mk-course" name="course" class="form-control">
                <option value="">Select course</option>
                @foreach($courseOptions as $c)
                <option value="{{ $c->id }}" @if(optional($course)->id == $c->id) selected @endif>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mk-field wide">
              <label for="mk-module">Module</label>
              <select id="mk-module" name="module" class="form-control" @if(!$course) disabled @endif>
                <option value="">{{ $course ? 'Select module' : 'Select a course first' }}</option>
                @if($course)
                @foreach($course->courseModule as $m)
                <option value="{{ $m->id }}" @if(optional($module)->id == $m->id) selected @endif>{{ $m->module_name }}</option>
                @endforeach
                @endif
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
              <label for="mk-batch">Students</label>
              <select id="mk-batch" name="batch" class="form-control">
                <option value="current" @if($batch === 'current') selected @endif>Current batches</option>
                <option value="completed" @if($batch === 'completed') selected @endif>Completed batches</option>
                <option value="all" @if($batch === 'all') selected @endif>All batches</option>
              </select>
            </div>
            <div class="mk-actions">
              <button type="submit" class="mk-btn primary"><i class="fa fa-users" aria-hidden="true"></i> Show Students</button>
            </div>
          </form>
        </div>

        {{-- Step 2: marks --}}
        @if($module)
        <form method="post" action="{{ route('student.marks.save') }}" class="mk-card" id="mk-batch-form">
          @csrf
          <input type="hidden" name="course" value="{{ $course->id }}">
          <input type="hidden" name="module" value="{{ $module->id }}">
          <input type="hidden" name="batch" value="{{ $batch }}">
          @if($seesAll && $request->center)<input type="hidden" name="center" value="{{ $request->center }}">@endif

          <div class="mk-card-head">
            <div>
              <h3>{{ $module->module_name }} <span class="mk-muted" style="font-weight: 400">· {{ $course->name }}</span></h3>
              <p>Full marks, faculty and exam date apply to every student you enter marks for. Leave a student blank to skip them; clear both marks to remove a saved result.</p>
            </div>
          </div>
          <div class="mk-filters" style="border-bottom: 1px solid var(--mk-line)">
            <div class="mk-field">
              <label for="mk-theory-full">Theory Full Marks</label>
              <input id="mk-theory-full" type="text" inputmode="numeric" name="theory_full" class="form-control" value="{{ old('theory_full', $defaults->theory_full) }}">
            </div>
            <div class="mk-field">
              <label for="mk-practical-full">Practical Full Marks</label>
              <input id="mk-practical-full" type="text" inputmode="numeric" name="practical_full" class="form-control" value="{{ old('practical_full', $defaults->practical_full) }}">
            </div>
            <div class="mk-field wide">
              <label for="mk-faculty">Evaluated By (Faculty)</label>
              <select id="mk-faculty" name="faculty_id" class="form-control">
                <option value="">—</option>
                @foreach($faculties as $f)
                <option value="{{ $f->id }}" @if(old('faculty_id', $defaults->faculty_id) == $f->id) selected @endif>{{ App\Services\StudentEnrolments::facultyName($f) }}</option>
                @endforeach
              </select>
            </div>
            <div class="mk-field">
              <label for="mk-exam-date">Exam Date</label>
              <input id="mk-exam-date" type="date" name="exam_date" class="form-control" value="{{ old('exam_date', $defaults->exam_date) }}">
            </div>
          </div>

          @if($students->isEmpty())
            <div class="mk-empty">No {{ $batch === 'all' ? '' : $batch }} students found for this course. Try "All batches".</div>
          @else
          <div class="table-responsive">
            <table class="mk-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Student</th>
                  <th>Batch</th>
                  <th>Theory</th>
                  <th>Practical</th>
                  <th class="num">Total</th>
                  <th class="num">%</th>
                  <th class="center">Grade</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($students as $i => $s)
                @php
                  $a = $s->application;
                  $r = $a->regDetails;
                  $key = "marks.{$a->id}";
                  $name = $r ? trim(preg_replace('/\s+/', ' ', $r->first_name.' '.$r->mid_name.' '.$r->last_name)) : $a->application_id;
                @endphp
                <tr class="mk-row @if($errors->has($key)) mk-row-error @endif">
                  <td class="mk-muted">{{ $i + 1 }}</td>
                  <td><strong>{{ $name }}</strong><small>{{ optional($r)->roll_no }} · {{ $a->application_id }}@if($r && $seesAll && !$request->center) · {{ App\Helpers\Helper::getCenterName($r->center_name) }}@endif</small></td>
                  <td class="mk-muted" style="white-space: nowrap">{{ $fmt($a->batch_start_date) }}<small>to {{ $fmt($a->batch_end_date) }}</small></td>
                  <td><input type="text" inputmode="numeric" name="marks[{{ $a->id }}][theory]" class="mk-input js-ob" value="{{ old("$key.theory", $s->mark ? (int) $s->mark->marks : '') }}" aria-label="Theory marks for {{ $name }}"></td>
                  <td><input type="text" inputmode="numeric" name="marks[{{ $a->id }}][practical]" class="mk-input js-ob" value="{{ old("$key.practical", $s->mark ? (int) $s->mark->practical_marks : '') }}" aria-label="Practical marks for {{ $name }}"></td>
                  <td class="num js-total">—</td>
                  <td class="num js-pct">—</td>
                  <td class="center js-grade"><span class="mk-muted">—</span></td>
                  <td class="num"><a href="{{ route('student.marks.sheet', $a->id) }}" class="mk-link" title="Open full result sheet"><i class="fa fa-list-alt" aria-hidden="true"></i></a></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mk-footer">
            <p class="mk-note">{{ $students->count() }} {{ $students->count() === 1 ? 'student' : 'students' }} · {{ $students->filter(function ($s) { return $s->mark; })->count() }} already have a result for this module</p>
            <button type="submit" class="mk-btn primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save Marks</button>
          </div>
          @endif
        </form>
        @endif
      </div>
    </div>
  </div>
</div>
<script>
  // Changing the course reloads the page with that course's modules.
  document.getElementById('mk-course').addEventListener('change', function () {
    document.getElementById('mk-module').value = '';
    document.getElementById('mk-pick').submit();
  });
</script>
@include('module_marks_master._live-script', ['rowSelector' => '#mk-batch-form .mk-row'])
@endsection
