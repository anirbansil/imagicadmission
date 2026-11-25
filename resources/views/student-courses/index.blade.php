@extends('layout')
@section('title', $title)
@section('content')
@php
  $fmt = function ($date) { return $date ? $date->format('d M Y') : '—'; };
  $duration = function ($course) { return App\Http\Controllers\StudentCourseController::duration($course->duration); };
  $moduleCount = function ($course) { $n = $course->courseModule->count(); return $n.' '.($n === 1 ? 'module' : 'modules'); };
@endphp
<div id="main" class="admin_main">
<style>
  .crs-page.dashboard-materials { margin-top: 10px; }
  .crs-page { --crs-primary: #571d1f; --crs-primary-dark: #3a1214; --crs-accent: #eca107; --crs-ink: #2B2022; --crs-muted: #8a7f80; --crs-line: #ece6e3; --crs-soft: #faf7f5;
    --crs-yellow-bg: #fff6dc; --crs-yellow-line: #f3dfa3; --crs-green: #2f8a4c; --crs-green-bg: #e8f5ec; --crs-green-line: #c9e6d2; color: var(--crs-ink); }
  .crs-page * { box-sizing: border-box; }

  /* Intro row with a shortcut to the explore section */
  .crs-intro-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin: 0 0 14px; }
  .crs-intro { color: var(--crs-muted); margin: 0; }
  .crs-jump { display: inline-flex; align-items: center; gap: 8px; background: var(--crs-accent); color: var(--crs-ink); font-weight: 600; font-size: 13.5px; border-radius: 20px; padding: 7px 16px; text-decoration: none; box-shadow: 0 4px 12px rgba(236, 161, 7, .35); transition: transform .15s, box-shadow .15s; }
  .crs-jump:hover { color: var(--crs-ink); text-decoration: none; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(236, 161, 7, .45); }
  .crs-jump i.fa-arrow-down { animation: crs-bob 1.6s ease-in-out infinite; }
  @keyframes crs-bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(3px); } }

  /* Sections */
  .crs-page .crs-section { margin: 0 0 22px; padding: 0; } /* site CSS gives every <section> 70px padding */
  .crs-section-head { display: flex; align-items: center; gap: 10px; margin: 0 0 10px; }
  .crs-section-head h2 { font-size: 19px; font-weight: 700; margin: 0; color: var(--crs-ink); }
  .crs-section-icon { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
  .crs-section-icon.current { background: var(--crs-yellow-bg); color: #a87100; }
  .crs-section-icon.completed { background: var(--crs-green-bg); color: var(--crs-green); }
  .crs-count { font-size: 12px; font-weight: 600; background: var(--crs-soft); border: 1px solid var(--crs-line); color: var(--crs-muted); border-radius: 10px; padding: 1px 9px; }

  /* Current / completed course cards, two per row */
  .crs-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; align-items: start; }
  .crs-card { background: #fff; border: 1px solid var(--crs-line); border-radius: 10px; box-shadow: 0 1px 3px rgba(43, 32, 34, .05); overflow: hidden; }
  .crs-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; padding: 14px 18px 12px; border-bottom: 1px solid var(--crs-line); }
  .crs-card-head h3 { font-size: 17px; font-weight: 600; margin: 0 0 3px; color: var(--crs-ink); }
  .crs-card-head p { margin: 0; color: var(--crs-muted); font-size: 13px; }
  .crs-card.current { border-top: 3px solid var(--crs-accent); }
  .crs-card.current .crs-card-head { background: var(--crs-yellow-bg); border-bottom-color: var(--crs-yellow-line); }
  .crs-card.current .crs-card-head p { color: #8a6d3b; }
  .crs-card.completed { border-top: 3px solid var(--crs-green); }
  .crs-card.completed .crs-card-head { background: var(--crs-green-bg); border-bottom-color: var(--crs-green-line); }
  .crs-card.completed .crs-card-head p { color: #4f7a5d; }
  .crs-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; border-radius: 12px; padding: 3px 11px; white-space: nowrap; flex-shrink: 0; }
  .crs-badge.running { background: #fff; color: #1e6b34; border: 1px solid #bfe3c9; }
  .crs-badge.upcoming { background: #fff; color: #7a5200; border: 1px solid var(--crs-yellow-line); }
  .crs-badge.completed { background: #fff; color: var(--crs-green); border: 1px solid var(--crs-green-line); }

  .crs-meta { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px 16px; padding: 12px 18px; }
  .crs-meta span { display: block; font-size: 11px; letter-spacing: .4px; text-transform: uppercase; color: var(--crs-muted); font-weight: 600; margin-bottom: 2px; }
  .crs-meta strong { font-size: 14px; font-weight: 600; overflow-wrap: anywhere; }

  .crs-progress-wrap { padding: 0 18px 12px; }
  .crs-progress-label { display: flex; justify-content: space-between; font-size: 12.5px; color: var(--crs-muted); }
  .crs-progress { height: 7px; background: #f3ead2; border-radius: 4px; overflow: hidden; margin-top: 6px; }
  .crs-progress i { display: block; height: 100%; background: linear-gradient(90deg, var(--crs-accent), #d98a00); border-radius: 4px; }

  /* Syllabus (shared) */
  .crs-syllabus { padding: 0 18px 2px; border-top: 1px solid var(--crs-line); }
  .crs-syllabus summary { cursor: pointer; padding: 9px 0; font-weight: 600; font-size: 14px; color: var(--crs-primary); list-style: none; display: flex; align-items: center; gap: 8px; }
  .crs-syllabus summary::-webkit-details-marker { display: none; }
  .crs-syllabus summary::before { content: '\25B8'; transition: transform .15s; display: inline-block; }
  .crs-syllabus[open] summary::before { transform: rotate(90deg); }
  .crs-syllabus summary small { color: var(--crs-muted); font-weight: 400; }
  .crs-modules { list-style: none; margin: 0 0 10px; padding: 0; }
  .crs-modules > li { display: flex; gap: 12px; padding: 8px 0; border-top: 1px dashed var(--crs-line); }
  .crs-modules > li:first-child { border-top: 0; }
  .crs-num { flex-shrink: 0; width: 30px; height: 30px; border-radius: 6px; background: var(--crs-soft); color: var(--crs-primary); font-weight: 700; font-size: 12.5px; display: flex; align-items: center; justify-content: center; font-variant-numeric: tabular-nums; }
  .crs-modules strong { display: block; font-size: 14px; font-weight: 600; line-height: 30px; }
  .crs-topics { margin: 0 0 2px; padding-left: 18px; color: #5d5254; font-size: 13px; }
  .crs-empty-syllabus { color: var(--crs-muted); font-size: 13px; margin: 0 0 10px; }

  /* Explore: a highlighted panel so students notice it */
  .crs-explore-panel { scroll-margin-top: 80px; }
  /* Only the title banner is maroon; the course grid sits on the page background. */
  .crs-explore-head { position: relative; overflow: hidden; text-align: center; border-radius: 14px; padding: 22px 20px; margin-bottom: 18px; background: linear-gradient(135deg, var(--crs-primary) 0%, var(--crs-primary-dark) 100%); box-shadow: 0 10px 28px rgba(87, 29, 31, .25); }
  .crs-explore-head::after { content: '\f19d'; font-family: FontAwesome; position: absolute; right: -10px; top: -28px; font-size: 150px; color: rgba(255, 255, 255, .06); pointer-events: none; }
  .crs-explore-head > div { position: relative; z-index: 1; }
  .crs-explore-head .crs-kicker { display: inline-block; font-size: 11.5px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--crs-accent); margin-bottom: 4px; }
  .crs-explore-head h2 { font-size: 22px; font-weight: 700; color: #fff; margin: 0 0 4px; text-align: center; }
  .crs-explore-head p { color: rgba(255, 255, 255, .78); margin: 0; font-size: 14px; }
  .crs-explore-head .crs-count { background: rgba(255, 255, 255, .12); border-color: rgba(255, 255, 255, .2); color: #fff; font-size: 13px; padding: 4px 12px; }

  .crs-explore { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(auto-fill, minmax(330px, 1fr)); gap: 20px; align-items: start; }
  .crs-x-card { background: #fff; border: 1px solid var(--crs-line); border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(43, 32, 34, .08); transition: transform .15s, box-shadow .15s; }
  .crs-x-card:hover { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(43, 32, 34, .16); }
  .crs-x-card::before { content: ''; display: block; height: 4px; background: linear-gradient(90deg, var(--crs-accent), #f6c75a); }
  .crs-x-top { display: flex; gap: 12px; align-items: flex-start; padding: 14px 16px 8px; }
  .crs-x-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--crs-yellow-bg); color: var(--crs-primary); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
  .crs-x-top h3 { font-size: 15.5px; font-weight: 600; margin: 0; line-height: 1.35; color: var(--crs-ink); }
  .crs-chips { display: flex; gap: 6px; flex-wrap: wrap; padding: 0 16px 12px 68px; }
  .crs-chips span { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; background: var(--crs-soft); border: 1px solid var(--crs-line); border-radius: 12px; padding: 2px 10px; color: #5d5254; }
  .crs-x-card .crs-syllabus { padding: 0 16px 4px; }
  .crs-x-card .crs-syllabus summary { justify-content: center; margin: 10px 0 8px; padding: 7px 0; border-radius: 6px; background: var(--crs-primary); color: #fff; font-size: 13.5px; transition: background .15s; }
  .crs-x-card .crs-syllabus summary:hover { background: var(--crs-primary-dark); }
  .crs-x-card .crs-syllabus summary::before { content: '\f02d'; font-family: FontAwesome; transform: none; }
  .crs-x-card .crs-syllabus .crs-hide { display: none; }
  .crs-x-card .crs-syllabus[open] .crs-show { display: none; }
  .crs-x-card .crs-syllabus[open] .crs-hide { display: inline; }
  .crs-explore-foot { margin: 16px 0 0; color: var(--crs-muted); font-size: 13.5px; display: flex; align-items: center; justify-content: center; gap: 8px; }
  .crs-explore-foot i { color: var(--crs-accent); }

  @media (max-width: 991px) {
    .crs-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 575px) {
    .crs-card-head, .crs-meta, .crs-syllabus, .crs-progress-wrap { padding-left: 14px; padding-right: 14px; }
    .crs-explore-head { padding: 16px 14px; }
    .crs-explore { grid-template-columns: 1fr; }
  }
  @media (prefers-reduced-motion: reduce) {
    .crs-jump i.fa-arrow-down { animation: none; }
    .crs-x-card, .crs-x-card:hover, .crs-jump:hover { transition: none; transform: none; }
  }
</style>
  <div class="admin-inn-contents">
    <div class="container-fluid">
      <h1 class="main-title">{{$title}}
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="{{route('student.profile')}}"><i class="fa fa-home" aria-hidden="true"></i>Home</a></li>
            <li class="breadcrumb-item active">{{$title}}</li>
          </ul>
        </nav>
      </h1>
      <div class="dashboard-materials crs-page">
        <div class="crs-intro-row">
          <p class="crs-intro">Your courses with the institute and what each one covers, module by module.</p>
          @if($otherCourses->isNotEmpty())
          <a href="#explore-courses" class="crs-jump"><i class="fa fa-compass" aria-hidden="true"></i> {{ $otherCourses->count() }} more courses to explore <i class="fa fa-arrow-down" aria-hidden="true"></i></a>
          @endif
        </div>

        @if($registrations->isEmpty())
          <div class="alert alert-warning">No registration details were found for {{$user->email}}. Please contact your center.</div>
        @endif

        {{-- Current courses --}}
        @if($current->isNotEmpty())
        <section class="crs-section">
          <div class="crs-section-head">
            <span class="crs-section-icon current"><i class="fa fa-hourglass-half" aria-hidden="true"></i></span>
            <h2>Current Courses</h2>
            <span class="crs-count">{{ $current->count() }}</span>
          </div>

          <div class="crs-grid">
            @foreach($current as $e)
            <div class="crs-card current">
              <div class="crs-card-head">
                <div>
                  <h3>{{ $e->course->name }}</h3>
                  <p>Application {{ $e->application_id }}@if($e->center) · {{ $e->center }}@endif</p>
                </div>
                <span class="crs-badge {{ $e->state }}">
                  @if($e->state === 'upcoming')<i class="fa fa-clock-o" aria-hidden="true"></i> Starts {{ $fmt($e->start) }}
                  @else<i class="fa fa-circle" aria-hidden="true" style="font-size: 8px"></i> In progress
                  @endif
                </span>
              </div>
              <div class="crs-meta">
                <div><span>Batch Start</span><strong>{{ $fmt($e->start) }}</strong></div>
                <div><span>Batch End</span><strong>{{ $fmt($e->end) }}</strong></div>
                <div><span>Duration</span><strong>{{ $duration($e->course) ?: '—' }}</strong></div>
                <div><span>Batch Time</span><strong>{{ $e->batch_time ?: '—' }}</strong></div>
                <div><span>Faculty</span><strong>{{ $e->faculty ?: '—' }}</strong></div>
              </div>
              @if($e->progress !== null)
              <div class="crs-progress-wrap">
                <div class="crs-progress-label">
                  <span>Course progress</span>
                  <span>{{ $e->progress }}%</span>
                </div>
                <div class="crs-progress"><i style="width: {{ $e->progress }}%"></i></div>
              </div>
              @endif
              <details class="crs-syllabus">
                <summary>Syllabus <small>{{ $moduleCount($e->course) }}</small></summary>
                @include('student-courses._syllabus', ['course' => $e->course])
              </details>
            </div>
            @endforeach
          </div>
        </section>
        @endif

        {{-- Completed courses --}}
        @if($completed->isNotEmpty())
        <section class="crs-section">
          <div class="crs-section-head">
            <span class="crs-section-icon completed"><i class="fa fa-check-circle" aria-hidden="true"></i></span>
            <h2>Completed Courses</h2>
            <span class="crs-count">{{ $completed->count() }}</span>
          </div>

          <div class="crs-grid">
            @foreach($completed as $e)
            <div class="crs-card completed">
              <div class="crs-card-head">
                <div>
                  <h3>{{ $e->course->name }}</h3>
                  <p>Application {{ $e->application_id }}@if($e->center) · {{ $e->center }}@endif</p>
                </div>
                <span class="crs-badge completed"><i class="fa fa-check" aria-hidden="true"></i> Completed</span>
              </div>
              <div class="crs-meta">
                <div><span>Batch Start</span><strong>{{ $fmt($e->start) }}</strong></div>
                <div><span>Completed On</span><strong>{{ $fmt($e->end) }}</strong></div>
                <div><span>Duration</span><strong>{{ $duration($e->course) ?: '—' }}</strong></div>
                <div><span>Faculty</span><strong>{{ $e->faculty ?: '—' }}</strong></div>
              </div>
              <details class="crs-syllabus">
                <summary>Syllabus <small>{{ $moduleCount($e->course) }}</small></summary>
                @include('student-courses._syllabus', ['course' => $e->course])
              </details>
            </div>
            @endforeach
          </div>
        </section>
        @endif

        {{-- Every other active course --}}
        @if($otherCourses->isNotEmpty())
        <section class="crs-section crs-explore-panel" id="explore-courses">
          <div class="crs-explore-head">
            <div>
              <span class="crs-kicker"><i class="fa fa-star" aria-hidden="true"></i> Grow your skills</span>
              <h2>Explore All Courses</h2>
              <p>Other courses offered by the institute. Open any course to see what you will learn.</p>
            </div>
          </div>

          <div class="crs-explore">
            @foreach($otherCourses as $course)
            <div class="crs-x-card">
              <div class="crs-x-top">
                <span class="crs-x-icon"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span>
                <h3>{{ $course->name }}</h3>
              </div>
              <div class="crs-chips">
                @if($duration($course))<span><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $duration($course) }}</span>@endif
                <span><i class="fa fa-book" aria-hidden="true"></i> {{ $moduleCount($course) }}</span>
              </div>
              <details class="crs-syllabus">
                <summary><span class="crs-show">View syllabus</span><span class="crs-hide">Hide syllabus</span></summary>
                @include('student-courses._syllabus', ['course' => $course])
              </details>
            </div>
            @endforeach
          </div>

          <p class="crs-explore-foot"><i class="fa fa-info-circle" aria-hidden="true"></i> Interested in a course? Talk to your center to enroll.</p>
        </section>
        @endif

      </div>
    </div>
  </div>
</div>
@endsection
