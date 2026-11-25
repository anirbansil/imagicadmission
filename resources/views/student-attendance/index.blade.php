@extends('layout')
@section('title', $title)
@section('content')
@php
  $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(function ($w) { return mb_strtoupper(mb_substr($w, 0, 1)); })->implode('');
@endphp
<div id="main" class="admin_main">
<style>
  .att-page { --att-primary: #571d1f; --att-primary-dark: #3a1214; --att-accent: #eca107; --att-ink: #2B2022; --att-muted: #8a7f80; --att-line: #ece6e3; --att-soft: #faf7f5; color: var(--att-ink); }
  .att-page * { box-sizing: border-box; }
  .att-head h1 { font-size: 26px; font-weight: 700; margin: 0 0 6px; color: var(--att-ink); }
  .att-head p { color: var(--att-muted); margin: 0 0 22px; }
  .att-grid { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 20px; align-items: start; }
  .att-main, .att-side { display: flex; flex-direction: column; gap: 20px; min-width: 0; }
  .att-card { background: #fff; border: 1px solid var(--att-line); border-radius: 10px; box-shadow: 0 1px 3px rgba(43, 32, 34, .05); overflow: hidden; position: relative; }
  .att-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; padding: 16px 18px; border-bottom: 1px solid var(--att-line); }
  .att-card-head h3 { font-size: 17px; font-weight: 600; margin: 0 0 3px; color: var(--att-ink); }
  .att-card-head p { margin: 0; color: var(--att-muted); font-size: 13px; }
  .att-loading::after { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.55); }

  /* Today card */
  .att-today { background: linear-gradient(160deg, var(--att-primary) 0%, var(--att-primary-dark) 100%); color: #fff; border-radius: 10px; padding: 18px; box-shadow: 0 8px 24px rgba(87, 29, 31, .25); }
  .att-who { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
  .att-avatar { width: 40px; height: 40px; border-radius: 50%; background: #fbeed0; color: var(--att-primary); border: 2px solid rgba(255,255,255,.35); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0; }
  .att-who strong { display: block; font-size: 15px; }
  .att-who span { font-size: 12.5px; color: rgba(255,255,255,.7); }
  .att-clock { font-size: 34px; font-weight: 700; letter-spacing: 1px; line-height: 1.1; font-variant-numeric: tabular-nums; }
  .att-date { font-size: 13px; color: rgba(255,255,255,.75); margin-top: 4px; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,.14); }
  .att-window:not(:empty) { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
  .att-window span { font-size: 12px; background: rgba(255,255,255,.12); border-radius: 12px; padding: 3px 10px; color: rgba(255,255,255,.9); }
  .att-progress { height: 6px; background: #efe7e6; border-radius: 3px; overflow: hidden; margin: 8px 0 6px; }
  .att-progress i { display: block; height: 100%; background: var(--att-primary); border-radius: 3px; }
  .att-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; padding: 16px 0; }
  .att-stats span { display: block; font-size: 11px; letter-spacing: .5px; text-transform: uppercase; color: rgba(255,255,255,.6); margin-bottom: 4px; font-weight: 600; }
  .att-stats strong { font-size: 17px; font-variant-numeric: tabular-nums; }
  .att-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
  .att-btn-in, .att-btn-out { width: 100%; border: 1px solid transparent; border-radius: 6px; padding: 9px 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: filter .15s; }
  .att-btn-in { background: #fbeed0; color: var(--att-primary); }
  .att-btn-out { background: var(--att-accent); color: var(--att-ink); }
  .att-btn-in:hover:not(:disabled), .att-btn-out:hover:not(:disabled) { filter: brightness(1.05); }
  .att-btn-in:disabled, .att-btn-out:disabled { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.14); color: rgba(255,255,255,.42); cursor: not-allowed; }
  .att-today-note { margin-top: 12px; font-size: 12.5px; color: rgba(255,255,255,.8); text-align: center; min-height: 1em; }
  .att-today-next { margin-top: 6px; font-size: 12.5px; color: rgba(255,255,255,.6); text-align: center; }
  .att-geo:not(:empty) { margin: 0 0 12px; display: flex; flex-direction: column; gap: 6px; }
  .att-geo div { display: flex; gap: 8px; align-items: flex-start; font-size: 12.5px; line-height: 1.4; background: rgba(255,255,255,.1); border-radius: 6px; padding: 7px 10px; color: rgba(255,255,255,.92); }
  .att-geo div.ok { background: rgba(214, 240, 222, .18); }
  .att-geo div.wait { background: rgba(236, 161, 7, .2); }
  .att-geo div.no { background: rgba(255, 190, 185, .16); }
  .att-geo i { margin-top: 2px; width: 14px; text-align: center; flex-shrink: 0; }
  .att-geo small { display: block; color: rgba(255,255,255,.7); }
  .att-online { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,.14); text-align: center; }
  .att-btn-online { width: 100%; background: transparent; border: 1px dashed rgba(255,255,255,.55); color: #fff; border-radius: 6px; padding: 8px 10px; font-size: 13.5px; font-weight: 600; cursor: pointer; }
  .att-btn-online:hover:not(:disabled) { background: rgba(255,255,255,.08); border-style: solid; }
  .att-btn-online:disabled { opacity: .45; cursor: not-allowed; }
  .att-online-note { margin-top: 6px; font-size: 12px; color: rgba(255,255,255,.7); }

  /* Side info */
  .att-info { padding: 14px 18px; font-size: 13.5px; }
  .att-info h4 { font-size: 13px; font-weight: 600; margin: 0 0 8px; text-transform: uppercase; letter-spacing: .4px; color: var(--att-muted); }
  .att-days { display: flex; gap: 5px; margin-bottom: 10px; }
  .att-days span { flex: 1; text-align: center; padding: 5px 0; border-radius: 6px; font-size: 12px; background: var(--att-soft); color: #c2b8b8; }
  .att-days span.on { background: var(--att-primary); color: #fff; font-weight: 600; }
  .att-info p { margin: 0 0 4px; color: #5d5254; }
  .att-info + .att-info { border-top: 1px solid var(--att-line); }
  .att-decision { display: flex; justify-content: space-between; gap: 8px; padding: 6px 0; border-top: 1px dashed var(--att-line); font-size: 13px; }
  .att-decision:first-of-type { border-top: 0; }
  .att-decision small { display: block; color: var(--att-muted); }

  /* Buttons */
  .att-btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--att-line); background: #fff; color: var(--att-ink); border-radius: 6px; padding: 6px 12px; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: none; }
  .att-btn:hover { border-color: var(--att-primary); color: var(--att-primary); }
  .att-btn.primary { background: var(--att-primary); border-color: var(--att-primary); color: #fff; }
  .att-btn.primary:hover { background: var(--att-primary-dark); color: #fff; }
  .att-btn:disabled { opacity: .6; cursor: wait; }
  .att-btn-group { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
  .att-icon-btn { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--att-line); border-radius: 6px; color: var(--att-ink); background: #fff; cursor: pointer; }
  .att-icon-btn:hover:not(:disabled) { border-color: var(--att-primary); color: var(--att-primary); }
  .att-icon-btn:disabled { opacity: .35; cursor: default; }

  /* Month */
  .att-month-nav { display: flex; align-items: center; gap: 8px; }
  .att-month-nav strong { min-width: 120px; text-align: center; font-size: 14px; }
  .att-month-stats { display: grid; grid-template-columns: repeat(6, 1fr); border-bottom: 1px solid var(--att-line); }
  .att-month-stats div { padding: 12px 14px; border-right: 1px solid var(--att-line); }
  .att-month-stats div:last-child { border-right: 0; }
  .att-month-stats span { display: block; font-size: 12px; color: var(--att-muted); margin-bottom: 2px; white-space: nowrap; }
  .att-month-stats strong { font-size: 19px; color: var(--att-primary); font-variant-numeric: tabular-nums; }
  .att-cal { padding: 14px 18px 6px; }
  .att-cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
  .att-cal-dow { font-size: 11px; color: var(--att-muted); text-align: center; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; padding-bottom: 2px; }
  .att-cal-day { height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; background: transparent; color: #c9c0bf; position: relative; font-variant-numeric: tabular-nums; border: 1px solid transparent; }
  .att-cal-day.present { background: #f3e6e4; color: var(--att-primary); font-weight: 600; }
  .att-cal-day.late { background: #fdf0d3; color: #8a5a00; font-weight: 600; }
  .att-cal-day.absent { background: #fdeceb; color: #b3261e; font-weight: 600; }
  .att-cal-day.leave { background: #e9edf3; color: #46566d; font-weight: 600; }
  .att-cal-day.leave_pending { background: #fff; color: #46566d; border: 1px dashed #a9b4c4; }
  .att-cal-day.holiday { background: repeating-linear-gradient(135deg, #f4f1f0, #f4f1f0 4px, #fff 4px, #fff 8px); color: var(--att-muted); }
  .att-cal-day.upcoming, .att-cal-day.today_class { background: #fff; color: var(--att-ink); border: 1px solid #d9cfcd; }
  .att-cal-day.today { box-shadow: inset 0 0 0 2px var(--att-primary); }
  .att-legend { display: flex; gap: 12px; flex-wrap: wrap; padding: 10px 18px 14px; font-size: 12px; color: var(--att-muted); }
  .att-legend i { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 5px; vertical-align: -1px; }
  .att-pending { border-top: 1px solid var(--att-line); padding: 12px 18px; }
  .att-pending h4 { font-size: 13px; font-weight: 600; margin: 0 0 8px; }
  .att-pending-item { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 7px 0; font-size: 13px; border-top: 1px dashed var(--att-line); }
  .att-pending-item:first-of-type { border-top: 0; }
  .att-link { background: none; border: 0; padding: 0; color: var(--att-primary); font-size: 12.5px; cursor: pointer; text-decoration: underline; }

  /* History */
  .att-table { width: 100%; border-collapse: collapse; font-size: 14px; }
  .att-table th { font-size: 11.5px; font-weight: 600; color: var(--att-muted); text-align: left; padding: 11px 14px; border-bottom: 1px solid var(--att-line); background: var(--att-soft); white-space: nowrap; text-transform: uppercase; letter-spacing: .4px; }
  .att-table td { padding: 11px 14px; border-bottom: 1px solid var(--att-line); vertical-align: middle; white-space: nowrap; }
  .att-table tr:last-child td { border-bottom: 0; }
  .att-table tbody tr:hover td { background: #fdfbfa; }
  .att-table small { display: block; color: var(--att-muted); font-size: 12px; }
  .att-times { color: #6f6466; font-variant-numeric: tabular-nums; }
  .att-hours { font-weight: 500; font-variant-numeric: tabular-nums; }
  .att-badge { display: inline-flex; align-items: center; gap: 6px; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; border: 1px solid; }
  .att-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .8; }
  .att-badge.present { background: #f6ecea; border-color: #e6d0cc; color: var(--att-primary); }
  .att-badge.absent { background: #fdeceb; border-color: #f5c6c2; color: #b3261e; }
  .att-badge.leave { background: #eef1f5; border-color: #d3dbe6; color: #46566d; }
  .att-badge.leave_pending { background: #fff; border-color: #c4cdd9; border-style: dashed; color: #46566d; }
  .att-badge.holiday { background: #f4f1f0; border-color: #e2dcda; color: var(--att-muted); }
  .att-flag { color: #a15c00; }
  .att-flag.danger { color: #b3261e; }
  .att-dash { color: #b9afb0; }
  .att-fix { background: none; border: 0; color: var(--att-muted); font-size: 12.5px; cursor: pointer; padding: 2px 6px; border-radius: 4px; }
  .att-fix:hover { color: var(--att-primary); background: #f6ecea; }
  .att-fix-pending { font-size: 12px; color: #a15c00; }
  .att-foot { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 12px 14px; border-top: 1px solid var(--att-line); font-size: 13px; color: var(--att-muted); }
  .att-foot strong { color: var(--att-ink); }
  .att-pager { display: flex; gap: 6px; }
  .att-pager button { min-width: 32px; height: 32px; padding: 0 6px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--att-line); border-radius: 6px; color: var(--att-ink); font-size: 13px; background: #fff; cursor: pointer; }
  .att-pager button:hover:not(:disabled) { border-color: var(--att-primary); color: var(--att-primary); }
  .att-pager button.active { background: var(--att-primary); border-color: var(--att-primary); color: #fff; font-weight: 600; }
  .att-pager button:disabled:not(.active) { color: #cfc6c6; cursor: default; }
  .att-empty { padding: 30px 14px; text-align: center; color: var(--att-muted); white-space: normal !important; }

  /* Toast + modal */
  .att-toast { position: fixed; right: 20px; bottom: 20px; z-index: 2100; max-width: 360px; padding: 12px 16px; border-radius: 8px; font-size: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.18); opacity: 0; transform: translateY(10px); transition: opacity .2s, transform .2s; pointer-events: none; }
  .att-toast.show { opacity: 1; transform: none; }
  .att-toast.ok { background: var(--att-primary); color: #fff; }
  .att-toast.err { background: #8c1d18; color: #fff; }
  .att-notice { border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 14px; background: #fdf6e3; color: #7a5200; border: 1px solid #f3e0b0; }
  .att-modal { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(43, 32, 34, .55); align-items: center; justify-content: center; padding: 16px; }
  .att-modal.open { display: flex; }
  .att-modal-box { background: #fff; border-radius: 10px; width: 100%; max-width: 440px; max-height: 100%; overflow: auto; box-shadow: 0 20px 50px rgba(0,0,0,.25); margin: 0; }
  .att-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid var(--att-line); }
  .att-modal-head h4 { margin: 0; font-size: 16px; font-weight: 600; }
  .att-modal-close { background: none; border: 0; font-size: 22px; line-height: 1; color: var(--att-muted); cursor: pointer; }
  .att-modal-body { padding: 16px 18px; }
  .att-modal-body label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px; }
  .att-modal-body .form-control { margin-bottom: 12px; }
  .att-modal-body .att-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .att-modal-body .att-hint { font-size: 12px; color: var(--att-muted); margin: -6px 0 12px; }
  .att-modal-body .att-day-info { font-size: 12.5px; color: var(--att-primary); margin: -6px 0 12px; min-height: 1em; }
  .att-form-error { background: #fdecea; color: #8c1d18; border: 1px solid #f5c6c2; border-radius: 6px; padding: 8px 12px; font-size: 13px; margin-bottom: 12px; display: none; }
  .att-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid var(--att-line); }

  @media (max-width: 1199px) {
    .att-month-stats { grid-template-columns: repeat(3, 1fr); }
    .att-month-stats div:nth-child(3) { border-right: 0; }
    .att-month-stats div:nth-child(-n+3) { border-bottom: 1px solid var(--att-line); }
  }
  @media (max-width: 991px) {
    .att-grid { grid-template-columns: 1fr; }
    .att-side { order: -1; }
  }
  @media (max-width: 575px) {
    .att-cal-day { height: 30px; font-size: 12px; }
    .att-clock { font-size: 30px; }
    .att-toast { left: 16px; right: 16px; max-width: none; }
  }
</style>
  <div class="admin-inn-contents">
    <div class="container-fluid att-page">

      <div class="att-head">
        <h1>My Attendance</h1>
        <p>Check in on your class days, see your month, and fix a day that looks wrong.</p>
      </div>

      <div id="att-notice"></div>

      <div class="att-grid">
        <div class="att-main">

          {{-- My month --}}
          <div class="att-card" id="att-month-card">
            <div class="att-card-head">
              <div>
                <h3>My month</h3>
                <p id="att-month-sub"></p>
              </div>
              <div class="att-btn-group">
                <div class="att-month-nav">
                  <button type="button" class="att-icon-btn" id="att-prev" title="Previous month" aria-label="Previous month"><i class="fa fa-angle-left"></i></button>
                  <strong id="att-month-label"></strong>
                  <button type="button" class="att-icon-btn" id="att-next" title="Next month" aria-label="Next month"><i class="fa fa-angle-right"></i></button>
                </div>
                <button type="button" class="att-btn" id="att-open-leave"><i class="fa fa-calendar-minus-o"></i> Request leave</button>
                <button type="button" class="att-btn" id="att-open-fix"><i class="fa fa-pencil"></i> Fix a day</button>
              </div>
            </div>
            <div class="att-month-stats" id="att-month-stats"></div>
            <div class="att-cal"><div class="att-cal-grid" id="att-cal"></div></div>
            <div class="att-legend">
              <span><i style="background:#f3e6e4"></i>Present</span>
              <span><i style="background:#fdf0d3"></i>Late / early</span>
              <span><i style="background:#fdeceb"></i>Absent</span>
              <span><i style="background:#e9edf3"></i>On leave</span>
              <span><i style="background:#fff;border:1px dashed #a9b4c4"></i>Leave pending</span>
              <span><i style="background:repeating-linear-gradient(135deg,#e2dcda,#e2dcda 2px,#fff 2px,#fff 4px)"></i>Holiday</span>
              <span><i style="background:#fff;border:1px solid #d9cfcd"></i>Class day</span>
            </div>
            <div class="att-pending" id="att-pending" hidden></div>
          </div>

          {{-- My history --}}
          <div class="att-card" id="att-history-card">
            <div class="att-card-head">
              <div>
                <h3>My history</h3>
                <p id="att-history-sub"></p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="att-table">
                <thead>
                  <tr><th>Day</th><th>Class</th><th>In &ndash; Out</th><th>Hours</th><th>Status</th><th>Late / Early</th><th></th></tr>
                </thead>
                <tbody id="att-history"></tbody>
              </table>
            </div>
            <div class="att-foot" id="att-history-foot"></div>
          </div>

        </div>

        <div class="att-side">
          {{-- Today --}}
          <div class="att-today">
            <div class="att-who">
              <div class="att-avatar">{{$initials}}</div>
              <div>
                <strong>{{$user->name}}</strong>
                <span>Today's attendance</span>
              </div>
            </div>
            <div class="att-clock" id="att-clock">--:--:--</div>
            <div class="att-date" id="att-today-date"></div>
            <div class="att-window" id="att-today-window"></div>
            <div class="att-stats">
              <div><span>Check in</span><strong id="att-today-in">—</strong></div>
              <div><span>Check out</span><strong id="att-today-out">—</strong></div>
              <div><span>Hours</span><strong id="att-today-hours">—</strong></div>
            </div>
            <div class="att-geo" id="att-geo" aria-live="polite"></div>
            <div class="att-actions">
              <button type="button" class="att-btn-in" id="att-check-in" disabled><i class="fa fa-sign-in"></i> Check in</button>
              <button type="button" class="att-btn-out" id="att-check-out" disabled><i class="fa fa-sign-out"></i> Check out</button>
            </div>
            <div class="att-today-note" id="att-today-note"></div>
            <div class="att-today-next" id="att-today-next"></div>
            <div class="att-online" id="att-online" hidden>
              <button type="button" class="att-btn-online" id="att-open-online"><i class="fa fa-laptop"></i> Request online class</button>
              <div class="att-online-note" id="att-online-note"></div>
            </div>
          </div>

          {{-- Class days --}}
          <div class="att-card">
            <div class="att-info">
              <h4>My classes</h4>
              <div class="att-days" id="att-days"></div>
              <div id="att-schedule-text"></div>
            </div>
            <div class="att-info" id="att-course" hidden></div>
            <div class="att-info" id="att-overall"></div>
            <div class="att-info" id="att-reviewed" hidden></div>
          </div>
        </div>
      </div>

      {{-- Fix a day --}}
      <div class="att-modal" id="att-fix-modal" role="dialog" aria-modal="true" aria-labelledby="att-fix-title">
        <form class="att-modal-box" id="att-fix-form" novalidate>
          <div class="att-modal-head">
            <h4 id="att-fix-title">Fix a day</h4>
            <button type="button" class="att-modal-close" data-close aria-label="Close">&times;</button>
          </div>
          <div class="att-modal-body">
            <div class="att-form-error"></div>
            <label for="fix-date">Day</label>
            <input type="date" class="form-control" id="fix-date" name="date" required>
            <div class="att-day-info" id="fix-day-info"></div>
            <div class="att-row">
              <div>
                <label for="fix-in">Correct check-in</label>
                <input type="time" class="form-control" id="fix-in" name="check_in_time" required>
              </div>
              <div>
                <label for="fix-out">Correct check-out</label>
                <input type="time" class="form-control" id="fix-out" name="checkout_time">
              </div>
            </div>
            <label for="fix-reason">What went wrong?</label>
            <textarea class="form-control" id="fix-reason" name="reason" rows="3" maxlength="500" required placeholder="e.g. Attended class but forgot to check in"></textarea>
            <p class="att-hint">Your center will review this request. Days from the last {{$data['limits']['fix_days_back']}} days can be fixed.</p>
          </div>
          <div class="att-modal-foot">
            <button type="button" class="att-btn" data-close>Cancel</button>
            <button type="submit" class="att-btn primary">Send request</button>
          </div>
        </form>
      </div>

      {{-- Request leave --}}
      <div class="att-modal" id="att-leave-modal" role="dialog" aria-modal="true" aria-labelledby="att-leave-title">
        <form class="att-modal-box" id="att-leave-form" novalidate>
          <div class="att-modal-head">
            <h4 id="att-leave-title">Request leave</h4>
            <button type="button" class="att-modal-close" data-close aria-label="Close">&times;</button>
          </div>
          <div class="att-modal-body">
            <div class="att-form-error"></div>
            <div class="att-row">
              <div>
                <label for="leave-from">From</label>
                <input type="date" class="form-control" id="leave-from" name="from_date" required>
              </div>
              <div>
                <label for="leave-to">To</label>
                <input type="date" class="form-control" id="leave-to" name="to_date" required>
              </div>
            </div>
            <div class="att-day-info" id="leave-info"></div>
            <label for="leave-reason">Reason</label>
            <textarea class="form-control" id="leave-reason" name="reason" rows="3" maxlength="500" required></textarea>
            <p class="att-hint">Only your class days count. Up to {{$data['limits']['max_leave_days']}} days per request; your center will review it.</p>
          </div>
          <div class="att-modal-foot">
            <button type="button" class="att-btn" data-close>Cancel</button>
            <button type="submit" class="att-btn primary">Send request</button>
          </div>
        </form>
      </div>

      {{-- Online class request --}}
      <div class="att-modal" id="att-online-modal" role="dialog" aria-modal="true" aria-labelledby="att-online-title">
        <form class="att-modal-box" id="att-online-form" novalidate>
          <div class="att-modal-head">
            <h4 id="att-online-title">Attend today's class online</h4>
            <button type="button" class="att-modal-close" data-close aria-label="Close">&times;</button>
          </div>
          <div class="att-modal-body">
            <div class="att-form-error"></div>
            <div class="att-day-info" id="online-info"></div>
            <label for="online-reason">Why can't you come to the institute today?</label>
            <textarea class="form-control" id="online-reason" name="reason" rows="3" maxlength="500" required placeholder="e.g. Unwell, travelling, family emergency"></textarea>
            <p class="att-hint">Your center will review it. Once approved, you can check in from anywhere for today's class.</p>
          </div>
          <div class="att-modal-foot">
            <button type="button" class="att-btn" data-close>Cancel</button>
            <button type="submit" class="att-btn primary">Send request</button>
          </div>
        </form>
      </div>

      <div class="att-toast" id="att-toast" role="status" aria-live="polite"></div>
    </div>
  </div>
</div>
<script>
(function () {
  var TZ = @json(config('attendance.timezone'));
  var CSRF = @json(csrf_token());
  var URLS = {
    data: @json(route('student.attendance.data')),
    checkIn: @json(route('student.attendance.checkin')),
    checkOut: @json(route('student.attendance.checkout')),
    fix: @json(route('student.attendance.fix')),
    leave: @json(route('student.attendance.leave')),
    online: @json(route('student.attendance.online')),
    cancel: @json(route('student.attendance.cancel', ['id' => '__ID__']))
  };
  var TYPE_LABEL = { fix: 'Fix', leave: 'Leave', online: 'Online class' };
  var DAY_NAMES = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  var STATUS = { present: 'Present', absent: 'Absent', leave: 'On Leave', leave_pending: 'Leave Pending', holiday: 'Holiday' };

  var state = @json($data);
  var view = { month: state.month.key, page: state.history.page };
  var offset = 0;
  var busy = false;

  function $(id) { return document.getElementById(id); }
  function esc(v) { return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function pad(n) { return (n < 10 ? '0' : '') + n; }
  function plural(n, word) { return n + ' ' + word + (n == 1 ? '' : (/(s|x|ch|sh)$/.test(word) ? 'es' : 's')); }
  function mins(m) { return m < 60 ? m + ' min' : Math.floor(m / 60) + 'h ' + pad(m % 60) + 'm'; }
  function nowIst() { return new Date(Date.now() + offset); }
  function istParts() {
    var p = {};
    new Intl.DateTimeFormat('en-GB', { timeZone: TZ, hour12: false, year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' })
      .formatToParts(nowIst()).forEach(function (x) { p[x.type] = x.value; });
    if (p.hour === '24') { p.hour = '00'; }
    return p;
  }

  // ---------- AJAX ----------
  function request(method, url, body) {
    var params = new URLSearchParams({ month: view.month, page: view.page });
    var opts = { method: method, credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF } };
    if (body) {
      opts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
      body.append('month', view.month);
      body.append('page', view.page);
      opts.body = body.toString();
    } else {
      url += (url.indexOf('?') < 0 ? '?' : '&') + params.toString();
    }
    return fetch(url, opts).then(function (res) {
      if (res.status === 419 || res.status === 401) {
        throw { message: 'Your session has expired. Please refresh the page and log in again.' };
      }
      return res.json().catch(function () { return {}; }).then(function (json) {
        if (json.data) { apply(json.data); }
        if (!res.ok || json.ok === false) { throw { message: json.message || 'Something went wrong. Please try again.', errors: json.errors }; }
        return json;
      });
    }, function () { throw { message: 'Could not reach the server. Check your internet connection.' }; });
  }

  function load(cards) {
    cards.forEach(function (id) { $(id).classList.add('att-loading'); });
    return request('GET', URLS.data).then(apply).catch(function (e) { toast(e.message, true); })
      .then(function () { cards.forEach(function (id) { $(id).classList.remove('att-loading'); }); });
  }

  var toastTimer;
  function toast(message, error) {
    var t = $('att-toast');
    t.textContent = message;
    t.className = 'att-toast show ' + (error ? 'err' : 'ok');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { t.className = 'att-toast'; }, error ? 9000 : 4500);
  }

  // ---------- Rendering ----------
  function apply(data) {
    state = data;
    view.month = data.month.key;
    view.page = data.history.page;
    offset = data.server_time * 1000 - Date.now();
    renderNotice(); renderToday(); renderSchedule(); renderMonth(); renderHistory(); renderPending();
  }

  function renderNotice() {
    $('att-notice').innerHTML = state.today.has_schedule ? '' :
      '<div class="att-notice"><i class="fa fa-info-circle"></i> Your class days have not been set by your center yet. You can check in any day until then; absences are not counted.</div>';
  }

  function renderToday() {
    var t = state.today;
    $('att-today-date').textContent = t.label + ' · ' + (t.class ? 'Class ' + t.class.start + '–' + t.class.end : (t.holiday ? t.holiday : 'No class today'));
    $('att-today-window').innerHTML = t.window ? '<span>In ' + esc(t.window.in_opens) + '–' + esc(t.window.in_closes) + '</span><span>Out ' + esc(t.window.out_opens) + '–' + esc(t.window.out_closes) + '</span>' : '';
    phase = null;
    $('att-today-in').textContent = t.in || '—';
    $('att-today-out').textContent = t.out || '—';
    $('att-today-hours').textContent = t.out ? fmtSeconds(t.seconds) : (t.in ? '' : '—');
    $('att-today-next').textContent = t.next_class && !t.in ? 'Next class: ' + t.next_class.label + ', ' + t.next_class.start + '–' + t.next_class.end : '';
    renderGeo();
    tick();
  }

  // Location check and online class status for today.
  function renderGeo() {
    var t = state.today, o = t.online, lines = [];
    if (o && o.status === 'approved') {
      lines.push(['ok', 'fa-globe', 'Online class approved for today. You can check in from anywhere.', o.note]);
    } else {
      if (o && o.status === 'pending') {
        lines.push(['wait', 'fa-clock-o', 'Online class request sent. Waiting for your center to approve it.', null]);
      } else if (o && o.status === 'rejected') {
        lines.push(['no', 'fa-times-circle', 'Your online class request was not approved.', o.note]);
      }
      if (t.location.required && t.class && !t.in) {
        lines.push(['', 'fa-map-marker', 'Be inside the institute to check in. Your location is checked when you tap Check in.', t.location.center ? t.location.center : null]);
      } else if (t.location.required && t.in && !t.out) {
        lines.push(['', 'fa-map-marker', 'You can check out from anywhere.', null]);
      }
    }
    $('att-geo').innerHTML = lines.map(function (l) {
      return '<div class="' + l[0] + '"><i class="fa ' + l[1] + '" aria-hidden="true"></i><span>' + esc(l[2]) + (l[3] ? '<small>' + esc(l[3]) + '</small>' : '') + '</span></div>';
    }).join('');
    var r = t.online_request;
    $('att-online').hidden = !(r && r.visible);
  }

  // The browser's position, for checking in / out at the institute.
  function getPosition() {
    return new Promise(function (resolve, reject) {
      if (!window.isSecureContext || !navigator.geolocation) {
        reject({ message: 'This browser cannot share your location here. Open the site with https:// or use Chrome on your phone.' });
        return;
      }
      navigator.geolocation.getCurrentPosition(function (p) { resolve(p.coords); }, function (err) {
        reject({ message: err.code === 1
          ? 'Location access is blocked. Allow location for this site (tap the lock icon in the address bar → Location → Allow), then try again.'
          : (err.code === 3 ? 'Finding your location took too long. Make sure location / GPS is on and try again.'
            : 'Could not find your location. Turn on location / GPS and try again.') });
      }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
    });
  }

  function fmtSeconds(s) { return Math.floor(s / 3600) + 'h ' + pad(Math.floor(s % 3600 / 60)) + 'm'; }

  // Live clock; also opens / closes the check-in and check-out windows without a reload.
  // Windows are inclusive to the minute (13:45–14:15 allows 14:15). The server checks the same rules.
  var phase = null;
  function tick() {
    var p = istParts(), t = state.today, w = t.window, hm = p.hour + ':' + p.minute;
    $('att-clock').textContent = p.hour + ':' + p.minute + ':' + p.second;
    if (t.running_since) {
      $('att-today-hours').textContent = fmtSeconds(Math.max(0, Math.floor(nowIst().getTime() / 1000) - t.running_since));
    }
    if (p.year + '-' + p.month + '-' + p.day !== t.date && !busy) { busy = true; load([]).then(function () { busy = false; }); return; }

    var canIn = t.can_check_in, canOut = t.can_check_out, note = t.message, now;
    if (w && !t.out) {
      if (!t.in) {
        now = hm < w.in_opens ? 'before_in' : (hm <= w.in_closes ? 'in' : 'in_closed');
        canIn = now === 'in';
        note = now === 'before_in' ? 'Check-in opens at ' + w.in_opens + ' (class ' + t.class.start + '–' + t.class.end + ').'
          : (now === 'in' ? 'Check in by ' + w.in_closes + '.' : 'Check-in closed at ' + w.in_closes + '. If you attended, use Fix a day.');
      } else {
        now = hm < w.out_opens ? 'before_out' : (hm <= w.out_closes ? 'out' : 'out_closed');
        canOut = now === 'out' || (now === 'before_out' && t.early_checkout);
        note = now === 'before_out' ? (t.early_checkout ? 'Class ends at ' + t.class.end + '. Checking out now marks you as leaving early.' : 'Check-out opens at ' + w.out_opens + '.')
          : (now === 'out' ? 'Check out by ' + w.out_closes + '.' : 'Check-out closed at ' + w.out_closes + '. Use Fix a day to add your check-out time.');
      }
      // A window just closed: refresh so the day shows as absent / missing check-out.
      if (phase && phase !== now && (now === 'in_closed' || now === 'out_closed') && !busy) { phase = now; load([]); }
      phase = now;
    }
    $('att-check-in').disabled = busy || !canIn;
    $('att-check-out').disabled = busy || !canOut;
    $('att-today-note').textContent = note || '';

    // Online class requests close a set number of minutes before class.
    var r = t.online_request;
    if (r && r.visible) {
      var closed = r.closes_at && hm >= r.closes_at;
      $('att-open-online').disabled = busy || !r.allowed || closed;
      $('att-online-note').textContent = r.allowed && closed ? 'Online class requests closed at ' + r.closes_at + ' (' + r.cutoff + ' minutes before class).' : (r.message || '');
    }
  }

  function renderSchedule() {
    var on = {};
    state.schedule.days.forEach(function (d) { on[d.day] = d; });
    $('att-days').innerHTML = DAY_NAMES.map(function (n, i) {
      var d = on[i + 1];
      return '<span class="' + (d ? 'on' : '') + '" title="' + (d ? esc(n + ' ' + d.start + '–' + d.end) : 'No class') + '">' + n.charAt(0) + '</span>';
    }).join('');
    var c = state.course, html = '';
    html += '<p>' + (state.schedule.text ? esc(state.schedule.text) : 'Class days not set yet.') + '</p>';
    $('att-schedule-text').innerHTML = html;

    // Course period and progress.
    html = '';
    if (c.start) {
      html += '<h4>My course</h4>';
      if (c.name) { html += '<p><strong>' + esc(c.name) + '</strong></p>'; }
      html += '<p><small>' + esc(fmtDate(c.start)) + ' – ' + esc(fmtDate(c.end)) + (c.months ? ' · ' + plural(c.months, 'month') : '') + '</small></p>';
      if (c.state === 'not_started') { html += '<p><small>Starts on ' + esc(fmtDate(c.start)) + '.</small></p>'; }
      else if (c.state === 'ended') { html += '<p><small>Course ended on ' + esc(fmtDate(c.end)) + '.</small></p>'; }
      if (c.progress !== null) {
        html += '<div class="att-progress" role="progressbar" aria-label="Course progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + c.progress + '"><i style="width:' + c.progress + '%"></i></div>';
      }
      if (c.classes_total) {
        html += '<p><small>' + c.classes_done + ' of ' + plural(c.classes_total, 'class') + ' done · ' + (c.classes_total - c.classes_done) + ' to go</small></p>';
      }
    }
    $('att-course').hidden = !html;
    $('att-course').innerHTML = html;

    var s = state.history.summary;
    $('att-overall').innerHTML = '<h4>Overall</h4>' +
      '<p><strong style="font-size:22px;color:#571d1f">' + (s.percent === null ? '—' : s.percent + '%') + '</strong> attendance' +
      (s.held ? ' <small>(' + plural(s.held, 'class') + ' held)</small>' : '') + '</p>' +
      '<p><small>' + plural(s.present, 'day') + ' present · ' + s.absent + ' absent · ' + s.leave + ' on leave</small></p>';

    var r = state.reviewed;
    $('att-reviewed').hidden = !r.length;
    $('att-reviewed').innerHTML = '<h4>Recent decisions</h4>' + r.map(function (x) {
      return '<div class="att-decision"><div>' + (TYPE_LABEL[x.type] || x.type) + ' · ' + esc(x.from) + (x.to ? ' – ' + esc(x.to) : '') +
        (x.note ? '<small>' + esc(x.note) + '</small>' : '') + '</div><span class="att-badge ' + (x.status === 'approved' ? 'present' : 'absent') + '">' + (x.status === 'approved' ? 'Approved' : 'Rejected') + '</span></div>';
    }).join('');
  }

  function fmtDate(d) {
    if (!d) { return '…'; }
    var m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return d.substr(8, 2) + ' ' + m[parseInt(d.substr(5, 2), 10) - 1] + ' ' + d.substr(0, 4);
  }

  function renderMonth() {
    var m = state.month, s = m.summary;
    $('att-month-label').textContent = m.label;
    $('att-prev').disabled = false;
    $('att-next').disabled = !m.next;
    $('att-month-sub').textContent = s.held ? plural(s.held, 'class') + ' held so far' : 'No classes held yet this month';
    $('att-month-stats').innerHTML = [
      ['Attendance', s.percent === null ? '—' : s.percent + '%'], ['Present', s.present], ['Absent', s.absent],
      ['Late or early', s.late_or_early], ['On leave', s.leave], ['Hours', s.hours]
    ].map(function (x) { return '<div><span>' + x[0] + '</span><strong>' + esc(x[1]) + '</strong></div>'; }).join('');

    var html = DAY_NAMES.map(function (n) { return '<div class="att-cal-dow">' + n + '</div>'; }).join('');
    for (var i = 0; i < m.offset; i++) { html += '<div></div>'; }
    m.days.forEach(function (d) {
      var cls = d.status === 'present' && d.late ? 'late' : (d.status === 'today' ? 'today_class' : d.status);
      var tip = fmtDate(d.date) + ' · ';
      if (d.status === 'present') { tip += 'Present ' + d.in + ' – ' + (d.out || '…'); }
      else if (d.status === 'holiday') { tip += 'Holiday: ' + d.holiday; }
      else if (d.status === 'no_class') { tip += 'No class'; }
      else if (d.status === 'upcoming' || d.status === 'today') { tip += 'Class ' + d.class.start + '–' + d.class.end; }
      else { tip += STATUS[d.status]; }
      html += '<div class="att-cal-day ' + cls + (d.today ? ' today' : '') + '" title="' + esc(tip) + '">' + d.day + '</div>';
    });
    $('att-cal').innerHTML = html;
  }

  function renderHistory() {
    var h = state.history, s = h.summary;
    $('att-history-sub').textContent = plural(h.total, 'day') + ' · ' + s.late_or_early + ' late or early · ' + s.absent + ' absent';
    if (!h.rows.length) {
      $('att-history').innerHTML = '<tr><td colspan="7" class="att-empty">No attendance yet. Use <strong>Check in</strong> on your class day.</td></tr>';
      $('att-history-foot').innerHTML = '';
      return;
    }
    $('att-history').innerHTML = h.rows.map(function (d) {
      var flags = [];
      if (d.late) { flags.push('came ' + mins(d.late) + ' late'); }
      if (d.early) { flags.push('left ' + mins(d.early) + ' early'); }
      var flagHtml = d.missed_checkout ? '<span class="att-flag danger">no check-out</span>' + (flags.length ? '<span class="att-flag"> · ' + flags.join(' · ') + '</span>' : '')
        : (flags.length ? '<span class="att-flag">' + flags.join(' · ') + '</span>' : '<span class="att-dash">—</span>');
      var cls = d.class ? d.class.start + '–' + d.class.end : (d.extra ? '<small>Extra day</small>' : (d.holiday ? '<small>' + esc(d.holiday) + '</small>' : '<span class="att-dash">—</span>'));
      var action = d.fix_pending ? '<span class="att-fix-pending">Fix pending</span>'
        : (d.can_fix ? '<button type="button" class="att-fix" data-fix="' + d.date + '">Fix</button>' : '');
      return '<tr>' +
        '<td>' + esc(d.label) + '<small>' + esc(d.weekday) + '</small></td>' +
        '<td class="att-times">' + cls + '</td>' +
        '<td class="att-times">' + (d.in ? esc(d.in) + ' – ' + esc(d.out || '…') : '<span class="att-dash">—</span>') + '</td>' +
        '<td class="att-hours">' + (d.hours ? esc(d.hours) : '<span class="att-dash">—</span>') + '</td>' +
        '<td><span class="att-badge ' + d.status + '">' + STATUS[d.status] + '</span></td>' +
        '<td>' + flagHtml + '</td>' +
        '<td class="text-right">' + action + '</td></tr>';
    }).join('');

    var foot = '<div>Showing <strong>' + h.from + '–' + h.to + '</strong> of <strong>' + h.total + '</strong></div>';
    if (h.last_page > 1) {
      var start = Math.max(1, Math.min(h.page - 2, h.last_page - 4)), end = Math.min(h.last_page, start + 4);
      foot += '<div class="att-pager"><button type="button" data-page="' + (h.page - 1) + '"' + (h.page === 1 ? ' disabled' : '') + ' aria-label="Previous page"><i class="fa fa-angle-left"></i></button>';
      for (var p = start; p <= end; p++) {
        foot += '<button type="button" data-page="' + p + '"' + (p === h.page ? ' class="active" disabled' : '') + '>' + p + '</button>';
      }
      foot += '<button type="button" data-page="' + (h.page + 1) + '"' + (h.page === h.last_page ? ' disabled' : '') + ' aria-label="Next page"><i class="fa fa-angle-right"></i></button></div>';
    }
    $('att-history-foot').innerHTML = foot;
  }

  function renderPending() {
    var list = state.pending, box = $('att-pending');
    box.hidden = !list.length;
    box.innerHTML = '<h4>Waiting for approval</h4>' + list.map(function (r) {
      return '<div class="att-pending-item"><div><strong>' + (TYPE_LABEL[r.type] || r.type) + '</strong> · ' + esc(r.from) + (r.to ? ' – ' + esc(r.to) : '') + (r.times ? ' · ' + esc(r.times) : '') +
        '</div><button type="button" class="att-link" data-cancel="' + r.id + '">Withdraw</button></div>';
    }).join('');
  }

  // ---------- Actions ----------
  // Check in / out. At centers with a location check the browser's position goes with the
  // check-in (the server measures the distance); an approved online class skips this.
  // Check-out works from anywhere, so it never asks for the location.
  function action(button, url, confirmText, checkIn) {
    if (busy || (confirmText && !confirm(confirmText))) { return; }
    busy = true; tick();
    var label = button.innerHTML, needLocation = checkIn && state.today.location && state.today.location.required;
    if (needLocation) { button.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Finding you…'; }
    (needLocation ? getPosition() : Promise.resolve(null))
      .then(function (c) {
        var body = new URLSearchParams();
        if (c) {
          body.append('lat', c.latitude);
          body.append('lng', c.longitude);
          body.append('accuracy', Math.round(c.accuracy));
        }
        return request('POST', url, body);
      })
      .then(function (res) { toast(res.message); }, function (e) { toast(e.message, true); })
      .then(function () { button.innerHTML = label; busy = false; tick(); });
  }
  $('att-check-in').addEventListener('click', function () { action(this, URLS.checkIn, null, true); });
  $('att-check-out').addEventListener('click', function () { action(this, URLS.checkOut, 'Check out for today? You can check in only once a day.'); });

  $('att-prev').addEventListener('click', function () { view.month = state.month.prev; load(['att-month-card']); });
  $('att-next').addEventListener('click', function () { if (state.month.next) { view.month = state.month.next; load(['att-month-card']); } });
  $('att-history-foot').addEventListener('click', function (e) {
    var b = e.target.closest('[data-page]');
    if (b && !b.disabled) { view.page = parseInt(b.getAttribute('data-page'), 10); load(['att-history-card']); }
  });
  $('att-pending').addEventListener('click', function (e) {
    var b = e.target.closest('[data-cancel]');
    if (!b || !confirm('Withdraw this request?')) { return; }
    b.disabled = true;
    request('POST', URLS.cancel.replace('__ID__', b.getAttribute('data-cancel')), new URLSearchParams())
      .then(function (res) { toast(res.message); }, function (e) { toast(e.message, true); b.disabled = false; });
  });

  // ---------- Modals ----------
  function openModal(id) {
    var m = $(id);
    m.querySelector('.att-form-error').style.display = 'none';
    m.classList.add('open');
    var first = m.querySelector('input:not([readonly]), textarea');
    if (first) { setTimeout(function () { first.focus(); }, 50); }
  }
  function closeModals() { document.querySelectorAll('.att-modal.open').forEach(function (m) { m.classList.remove('open'); }); }
  document.querySelectorAll('.att-modal').forEach(function (m) {
    m.addEventListener('click', function (e) { if (e.target === m || e.target.hasAttribute('data-close')) { closeModals(); } });
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeModals(); } });

  function dayInfo(date) {
    var all = state.history.rows.concat(state.month.days);
    for (var i = 0; i < all.length; i++) { if (all[i].date === date) { return all[i]; } }
    return null;
  }
  function openFix(date) {
    var f = $('att-fix-form'), d = date ? dayInfo(date) : null;
    f.reset();
    f.date.min = state.limits.fix_from;
    f.date.max = state.limits.today;
    f.date.value = date || '';
    f.date.readOnly = !!date;
    f.check_in_time.value = d && d.in ? d.in : (d && d.class ? d.class.start : '');
    f.checkout_time.value = d && d.out ? d.out : (d && d.class && d.status === 'absent' ? d.class.end : '');
    updateFixInfo();
    openModal('att-fix-modal');
  }
  function updateFixInfo() {
    var d = dayInfo($('fix-date').value);
    $('fix-day-info').textContent = !d ? '' : (d.class ? 'Class that day: ' + d.class.start + '–' + d.class.end : 'No class was scheduled that day.') +
      (d.in ? ' · Recorded ' + d.in + ' – ' + (d.out || '…') : (d.status === 'absent' ? ' · Marked absent' : ''));
  }
  $('fix-date').addEventListener('change', updateFixInfo);
  $('att-open-fix').addEventListener('click', function () { openFix(''); });
  $('att-history').addEventListener('click', function (e) {
    var b = e.target.closest('[data-fix]');
    if (b) { openFix(b.getAttribute('data-fix')); }
  });

  $('att-open-leave').addEventListener('click', function () {
    var f = $('att-leave-form');
    f.reset();
    f.from_date.min = f.to_date.min = state.limits.fix_from;
    $('leave-info').textContent = '';
    openModal('att-leave-modal');
  });
  function updateLeaveInfo() {
    var f = $('att-leave-form');
    if (f.from_date.value && (!f.to_date.value || f.to_date.value < f.from_date.value)) { f.to_date.value = f.from_date.value; }
    f.to_date.min = f.from_date.value || state.limits.fix_from;
    if (!state.today.has_schedule || !f.from_date.value) { $('leave-info').textContent = ''; return; }
    var on = {}, count = 0;
    state.schedule.days.forEach(function (d) { on[d.day] = true; });
    var d = new Date(f.from_date.value + 'T00:00:00'), end = new Date(f.to_date.value + 'T00:00:00');
    for (var i = 0; d <= end && i < 62; i++, d.setDate(d.getDate() + 1)) { if (on[(d.getDay() + 6) % 7 + 1]) { count++; } }
    $('leave-info').textContent = count ? 'Covers ' + plural(count, 'class day') + '.' : 'You have no classes on these days.';
  }
  $('leave-from').addEventListener('change', updateLeaveInfo);
  $('leave-to').addEventListener('change', updateLeaveInfo);

  function submitForm(form, url) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var err = form.querySelector('.att-form-error'), btn = form.querySelector('[type=submit]');
      err.style.display = 'none';
      btn.disabled = true;
      request('POST', url, new URLSearchParams(new FormData(form)))
        .then(function (res) { closeModals(); toast(res.message); }, function (e) {
          var msgs = e.errors ? Object.keys(e.errors).map(function (k) { return e.errors[k][0]; }) : [e.message];
          err.innerHTML = msgs.map(esc).join('<br>');
          err.style.display = 'block';
        })
        .then(function () { btn.disabled = false; });
    });
  }
  submitForm($('att-fix-form'), URLS.fix);
  submitForm($('att-leave-form'), URLS.leave);
  submitForm($('att-online-form'), URLS.online);

  $('att-open-online').addEventListener('click', function () {
    var t = state.today, r = t.online_request;
    $('att-online-form').reset();
    $('online-info').textContent = t.class ? 'Today’s class: ' + t.class.start + '–' + t.class.end + '. Requests close at ' + r.closes_at + '.' : '';
    openModal('att-online-modal');
  });

  apply(state);
  setInterval(tick, 1000);
})();
</script>
@endsection
