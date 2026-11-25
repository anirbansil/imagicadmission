@extends('layout')
@section('title', $title)
@section('content')
<div id="main" class="admin_main">
<style>
  .cs-page { --cs-primary: #571d1f; --cs-line: #ece6e3; --cs-muted: #8a7f80; }
  .cs-days { display: inline-flex; gap: 3px; }
  .cs-days span { width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; border-radius: 5px; font-size: 11px; background: #f4f1f0; color: #c2b8b8; }
  .cs-days span.on { background: var(--cs-primary); color: #fff; font-weight: 600; }
  .cs-sub { display: block; font-size: 12px; color: var(--cs-muted); }
  .cs-none { color: #b3261e; font-size: 13px; }
  .cs-bulk { display: none; align-items: center; gap: 10px; background: #fdf6e3; border: 1px solid #f3e0b0; border-radius: 6px; padding: 8px 12px; margin-bottom: 12px; font-size: 14px; }
  .cs-bulk.show { display: flex; }
  .cs-grid { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 20px; align-items: start; }
  .cs-card { background: #fff; border: 1px solid var(--cs-line); border-radius: 8px; padding: 14px 16px; }
  .cs-card h3 { font-size: 16px; font-weight: 600; margin: 0 0 4px; }
  .cs-card p.cs-help { font-size: 12.5px; color: var(--cs-muted); margin: 0 0 12px; }
  .cs-card .form-control { margin-bottom: 8px; height: 34px; font-size: 13px; }
  .cs-hol { display: flex; justify-content: space-between; gap: 8px; padding: 8px 0; border-top: 1px dashed var(--cs-line); font-size: 13px; }
  .cs-hol.past { opacity: .55; }
  .cs-side { display: flex; flex-direction: column; gap: 20px; }
  .cs-card h3 .fa { color: var(--cs-primary); }
  .cs-loc { display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 8px 0; border-top: 1px dashed var(--cs-line); font-size: 13px; }
  .cs-loc-state { font-size: 11.5px; font-weight: 700; border-radius: 10px; padding: 1px 8px; white-space: nowrap; }
  .cs-loc-state.on { background: #e3f4e8; color: #1e6b34; }
  .cs-loc-state.off { background: #f4f1f0; color: var(--cs-muted); }
  .cs-loc-actions { display: flex; gap: 8px; white-space: nowrap; }
  .cs-loc-actions a, .cs-loc-actions button { background: none; border: 0; padding: 0; font-size: 12px; cursor: pointer; color: var(--cs-primary); }
  .cs-loc-actions button.off { color: #b3261e; }
  .cs-hol button { background: none; border: 0; color: #b3261e; cursor: pointer; font-size: 12px; padding: 0; }
  .cs-btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--cs-line); background: #fff; color: #2B2022; border-radius: 6px; padding: 5px 12px; font-size: 13px; cursor: pointer; }
  .cs-btn:hover { border-color: var(--cs-primary); color: var(--cs-primary); }
  .cs-btn.primary { background: var(--cs-primary); border-color: var(--cs-primary); color: #fff; }
  .cs-btn:disabled { opacity: .6; cursor: wait; }
  .cs-modal { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(43, 32, 34, .55); align-items: center; justify-content: center; padding: 16px; }
  .cs-modal.open { display: flex; }
  .cs-modal-box { background: #fff; border-radius: 10px; width: 100%; max-width: 520px; max-height: 100%; overflow: auto; box-shadow: 0 20px 50px rgba(0,0,0,.25); }
  .cs-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid var(--cs-line); }
  .cs-modal-head h4 { margin: 0; font-size: 16px; font-weight: 600; }
  .cs-modal-head small { display: block; color: var(--cs-muted); font-weight: 400; }
  .cs-modal-close { background: none; border: 0; font-size: 22px; line-height: 1; color: var(--cs-muted); cursor: pointer; }
  .cs-modal-body { padding: 16px 18px; }
  .cs-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid var(--cs-line); }
  .cs-day-row { display: grid; grid-template-columns: 110px 1fr 1fr; gap: 10px; align-items: center; padding: 5px 0; }
  .cs-day-row label { margin: 0; font-weight: 500; display: flex; align-items: center; gap: 8px; cursor: pointer; }
  .cs-day-row input[type=time] { height: 32px; font-size: 13px; }
  .cs-day-row input[type=time]:disabled { opacity: .35; }
  .cs-same { display: flex; gap: 8px; align-items: center; background: #faf7f5; border-radius: 6px; padding: 8px 10px; margin-bottom: 10px; font-size: 13px; flex-wrap: wrap; }
  .cs-same input { width: 110px; height: 30px; font-size: 13px; }
  .cs-error { background: #fdecea; color: #8c1d18; border: 1px solid #f5c6c2; border-radius: 6px; padding: 8px 12px; font-size: 13px; margin-bottom: 12px; display: none; }
  .cs-toast { position: fixed; right: 20px; bottom: 20px; z-index: 2100; max-width: 360px; padding: 12px 16px; border-radius: 8px; font-size: 14px; color: #fff; background: #571d1f; box-shadow: 0 10px 30px rgba(0,0,0,.18); opacity: 0; transition: opacity .2s; pointer-events: none; }
  .cs-toast.show { opacity: 1; }
  .cs-toast.err { background: #8c1d18; }
  @media (max-width: 1199px) { .cs-grid { grid-template-columns: 1fr; } }
  @media (max-width: 575px) { .cs-day-row { grid-template-columns: 1fr 1fr; } .cs-day-row label { grid-column: 1 / -1; } }
</style>
  <div class="admin-inn-contents">
    <div class="container-fluid cs-page">
      <h1 class="main-title">
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('checkin-checkout.list')}}">Attendance List</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
          </ul>
        </nav>
      </h1>

      <div class="relg-new-struct-add">
        <form action="{{route('class-schedules.index')}}" method="get">
          <div class="filter_row">
            <div class="filter_div row">
              <div class="col-xs-12 col-md-4 col-sm-6">
                <div class="form-group"><input type="text" name="q" class="form-control" placeholder="Name, email, roll no or mobile" value="{{$request->q}}"></div>
              </div>
              @if($centers->isNotEmpty())
              <div class="col-xs-12 col-md-3 col-sm-6">
                <div class="form-group">
                  <select name="center" class="form-control">
                    <option value="">All centers</option>
                    @foreach($centers as $center)
                      <option value="{{$center->id}}" {{$request->center == $center->id ? 'selected' : ''}}>{{$center->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              @endif
              <div class="col-xs-12 col-md-2 col-sm-6">
                <div class="form-group">
                  <select name="schedule" class="form-control">
                    <option value="">All students</option>
                    <option value="not_set" {{$request->schedule == 'not_set' ? 'selected' : ''}}>Class days not set</option>
                    <option value="set" {{$request->schedule == 'set' ? 'selected' : ''}}>Class days set</option>
                  </select>
                </div>
              </div>
              <div class="col-xs-12 col-md-3 col-sm-6">
                <button type="submit" class="btn btn-info">Search</button>
                <a href="{{route('class-schedules.index')}}" class="reset">Reset</a>
              </div>
            </div>
          </div>
        </form>

        <div class="cs-grid">
          <div>
            <div class="cs-bulk" id="cs-bulk">
              <strong id="cs-bulk-count"></strong>
              <button type="button" class="cs-btn primary" id="cs-bulk-edit"><i class="fa fa-calendar"></i> Set class days</button>
              <button type="button" class="cs-btn" id="cs-bulk-clear">Clear selection</button>
            </div>
            <div class="relg-list">
              <div class="table-responsive">
                <table class="listingtable">
                  <tr>
                    <th><input type="checkbox" id="cs-all" aria-label="Select all"></th>
                    <th>Student</th>
                    <th>Center / Course</th>
                    <th>Batch</th>
                    <th>Class Days</th>
                    <th></th>
                  </tr>
                  @forelse($rows as $row)
                  <tr>
                    <td><input type="checkbox" class="cs-pick" value="{{$row['id']}}" aria-label="Select {{$row['name']}}"></td>
                    <td>{{$row['name']}}<span class="cs-sub">{{$row['roll_no'] ?: $row['email']}}</span></td>
                    <td>{{$row['center'] ?: '—'}}<span class="cs-sub">{{$row['course']}}</span></td>
                    <td>
                      {{$row['batch_time'] ?: '—'}}
                      @if($row['batch_start'])<span class="cs-sub">{{\Carbon\Carbon::parse($row['batch_start'])->format('d M Y')}} – {{$row['batch_end'] ? \Carbon\Carbon::parse($row['batch_end'])->format('d M Y') : '…'}}</span>@endif
                    </td>
                    <td>
                      @if($row['days']->isEmpty())
                        <span class="cs-none">Not set</span>
                      @else
                        @php $on = $row['days']->pluck('day')->all(); @endphp
                        <span class="cs-days">@foreach(App\Services\StudentAttendance::DAY_NAMES as $n => $d)<span class="{{in_array($n, $on) ? 'on' : ''}}">{{substr($d, 0, 1)}}</span>@endforeach</span>
                        <span class="cs-sub">{{$row['schedule_text']}}@if($row['starts_later']) (changes from {{\Carbon\Carbon::parse($row['days']->max('from'))->format('d M')}})@endif</span>
                      @endif
                    </td>
                    <td><button type="button" class="cs-btn" data-edit="{{$row['id']}}" data-name="{{$row['name']}}">Edit</button></td>
                  </tr>
                  @empty
                  <tr><td colspan="6">No students found.</td></tr>
                  @endforelse
                </table>
              </div>
            </div>
            <div class="pagination-sec">{{$students->links()}}</div>
          </div>

          <div class="cs-side">
          <div class="cs-card" id="cs-loc-card">
            <h3><i class="fa fa-map-marker" aria-hidden="true"></i> Check-in location</h3>
            <p class="cs-help">Students can check in only inside their centre, unless you approve an online class for them. Check-out works from anywhere. Each centre's address and location are set on its profile.</p>
            @foreach($locations as $loc)
            <div class="cs-loc">
              <div><strong>{{$loc['name']}}</strong><span class="cs-sub">{{$loc['set'] ? 'Within '.$loc['radius'].' m of the centre' : 'Not set: students can check in from anywhere'}}</span></div>
              <div class="cs-loc-actions">
                <span class="cs-loc-state {{$loc['set'] ? 'on' : 'off'}}">{{$loc['set'] ? 'On' : 'Off'}}</span>
                @if($loc['map'])<a href="{{$loc['map']}}" target="_blank" rel="noopener">Map</a>@endif
                <a href="{{$loc['edit']}}">{{$loc['set'] ? 'Edit' : 'Set'}}</a>
              </div>
            </div>
            @endforeach
          </div>

          <div class="cs-card">
            <h3>Holidays</h3>
            <p class="cs-help">Classes on these days are not counted as absent.</p>
            <form id="cs-hol-form" novalidate>
              <div class="cs-error"></div>
              <input type="text" name="title" class="form-control" placeholder="Holiday name, e.g. Durga Puja" maxlength="191" required>
              <div class="row">
                <div class="col-6"><input type="date" name="from_date" class="form-control" required aria-label="From"></div>
                <div class="col-6"><input type="date" name="to_date" class="form-control" aria-label="To (optional)" title="To (optional)"></div>
              </div>
              @if($centers->isNotEmpty())
              <select name="center_id" class="form-control">
                <option value="">All centers</option>
                @foreach($centers as $center)<option value="{{$center->id}}">{{$center->name}}</option>@endforeach
              </select>
              @endif
              <button type="submit" class="cs-btn primary"><i class="fa fa-plus"></i> Add holiday</button>
            </form>
            <div id="cs-hol-list" style="margin-top:12px"></div>
          </div>
          </div>
        </div>
      </div>

      {{-- Schedule editor --}}
      <div class="cs-modal" id="cs-modal" role="dialog" aria-modal="true" aria-labelledby="cs-modal-title">
        <form class="cs-modal-box" id="cs-form" novalidate>
          <div class="cs-modal-head">
            <h4 id="cs-modal-title">Class days<small id="cs-modal-sub"></small></h4>
            <button type="button" class="cs-modal-close" data-close aria-label="Close">&times;</button>
          </div>
          <div class="cs-modal-body">
            <div class="cs-error"></div>
            <div class="cs-same">
              Same time for all days:
              <input type="time" id="cs-same-start" class="form-control" aria-label="Start">
              <input type="time" id="cs-same-end" class="form-control" aria-label="End">
              <button type="button" class="cs-btn" id="cs-same-apply">Apply</button>
            </div>
            @foreach(App\Services\StudentAttendance::DAY_NAMES as $n => $d)
            <div class="cs-day-row">
              <label><input type="checkbox" name="days[{{$n}}][on]" value="1" data-day="{{$n}}"> {{\Carbon\Carbon::now()->startOfWeek()->addDays($n - 1)->format('l')}}</label>
              <input type="time" name="days[{{$n}}][start]" class="form-control" aria-label="{{$d}} start" disabled>
              <input type="time" name="days[{{$n}}][end]" class="form-control" aria-label="{{$d}} end" disabled>
            </div>
            @endforeach
            <p class="cs-help" id="cs-days-hint" style="font-size:12px;color:#8a7f80;margin-top:8px"></p>
            <label for="cs-from" style="margin-top:12px;display:block;font-weight:500">Applies from</label>
            <input type="date" class="form-control" id="cs-from" name="effective_from" required>
            <p class="cs-help" style="font-size:12px;color:#8a7f80;margin-top:6px">Attendance before this date keeps the old class days. Choose the batch start date to count absences for the whole batch.</p>
          </div>
          <div class="cs-modal-foot">
            <button type="button" class="cs-btn" data-close>Cancel</button>
            <button type="submit" class="cs-btn primary">Save class days</button>
          </div>
        </form>
      </div>

      <div class="cs-toast" id="cs-toast" role="status" aria-live="polite"></div>
    </div>
  </div>
</div>
<script>
(function () {
  var CSRF = @json(csrf_token());
  var URLS = {
    show: @json(route('class-schedules.show', ['id' => '__ID__'])),
    save: @json(route('class-schedules.save')),
    addHoliday: @json(route('class-schedules.holidays.add')),
    deleteHoliday: @json(route('class-schedules.holidays.delete', ['id' => '__ID__']))
  };
  var TODAY = @json($today);
  var RULES = @json($rules);
  var rules = RULES, period = null;
  var holidays = @json($holidays);
  var editing = [];

  function $(id) { return document.getElementById(id); }
  function esc(v) { return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }

  function send(method, url, body) {
    var opts = { method: method, credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF } };
    if (body) { opts.headers['Content-Type'] = 'application/x-www-form-urlencoded'; opts.body = body.toString(); }
    return fetch(url, opts).then(function (res) {
      if (res.status === 419) { throw { message: 'Your session has expired. Please refresh the page.' }; }
      return res.json().catch(function () { return {}; }).then(function (json) {
        if (!res.ok) { throw { message: json.message || 'Something went wrong.', errors: json.errors }; }
        return json;
      });
    }, function () { throw { message: 'Could not reach the server.' }; });
  }
  var toastTimer;
  function toast(msg, err) {
    var t = $('cs-toast'); t.textContent = msg; t.className = 'cs-toast show' + (err ? ' err' : '');
    clearTimeout(toastTimer); toastTimer = setTimeout(function () { t.className = 'cs-toast'; }, 4000);
  }
  function showError(form, e) {
    var box = form.querySelector('.cs-error');
    var msgs = e.errors ? Object.keys(e.errors).map(function (k) { return e.errors[k][0]; }) : [e.message];
    box.innerHTML = msgs.map(esc).join('<br>'); box.style.display = 'block';
  }

  // ---------- Selection ----------
  var picks = document.querySelectorAll('.cs-pick');
  function selected() { return Array.prototype.filter.call(picks, function (c) { return c.checked; }).map(function (c) { return c.value; }); }
  function updateBulk() {
    var n = selected().length;
    $('cs-bulk').classList.toggle('show', n > 0);
    $('cs-bulk-count').textContent = n + ' selected';
    $('cs-all').checked = n > 0 && n === picks.length;
  }
  picks.forEach(function (c) { c.addEventListener('change', updateBulk); });
  $('cs-all').addEventListener('change', function () { var on = this.checked; picks.forEach(function (c) { c.checked = on; }); updateBulk(); });
  $('cs-bulk-clear').addEventListener('click', function () { picks.forEach(function (c) { c.checked = false; }); updateBulk(); });

  // ---------- Editor ----------
  var form = $('cs-form');
  function dayInputs(n) { return { on: form.querySelector('[name="days[' + n + '][on]"]'), start: form.querySelector('[name="days[' + n + '][start]"]'), end: form.querySelector('[name="days[' + n + '][end]"]') }; }
  function setDay(n, on, start, end) {
    var d = dayInputs(n);
    d.on.checked = on; d.start.disabled = d.end.disabled = !on;
    if (start !== undefined) { d.start.value = start || ''; }
    if (end !== undefined) { d.end.value = end || ''; }
    updateHint();
  }
  function hm(m) { m = Math.min(1439, Math.max(0, m)); return ('0' + Math.floor(m / 60)).slice(-2) + ':' + ('0' + m % 60).slice(-2); }
  function minutes(t) { var p = t.split(':'); return parseInt(p[0], 10) * 60 + parseInt(p[1], 10); }
  function fmtDate(d) { var m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; return d.substr(8, 2) + ' ' + m[parseInt(d.substr(5, 2), 10) - 1] + ' ' + d.substr(0, 4); }
  // Days chosen against the usual number a week, the check-in / check-out windows, and the course period.
  function updateHint() {
    var count = 0, first = null, parts = [];
    for (var n = 1; n <= 7; n++) { var d = dayInputs(n); if (d.on.checked) { count++; if (!first && d.start.value && d.end.value) { first = d; } } }
    parts.push(count + (count === 1 ? ' day' : ' days') + ' a week' + (count && count !== rules.classes_per_week ? ' (usually ' + rules.classes_per_week + ')' : ''));
    if (first) {
      var s = minutes(first.start.value), e = minutes(first.end.value);
      parts.push('Check-in ' + hm(s - rules.checkin_before) + '–' + hm(s + rules.checkin_after) + ', check-out ' + hm(e - rules.checkout_before) + '–' + hm(e + rules.checkout_after));
    }
    if (period && period.start) { parts.push('Course ' + fmtDate(period.start) + (period.end ? ' – ' + fmtDate(period.end) : '') + (period.months ? ' (' + period.months + ' months)' : '')); }
    $('cs-days-hint').textContent = parts.join(' · ');
  }
  form.addEventListener('input', function (e) { if (e.target.type === 'time') { updateHint(); } });
  form.querySelectorAll('[data-day]').forEach(function (c) {
    c.addEventListener('change', function () {
      var n = this.getAttribute('data-day'), d = dayInputs(n);
      setDay(n, this.checked);
      if (this.checked && !d.start.value) { d.start.value = $('cs-same-start').value; d.end.value = $('cs-same-end').value; }
    });
  });
  $('cs-same-apply').addEventListener('click', function () {
    for (var n = 1; n <= 7; n++) { var d = dayInputs(n); if (d.on.checked) { d.start.value = $('cs-same-start').value; d.end.value = $('cs-same-end').value; } }
  });

  function openEditor(ids, title, data) {
    editing = ids;
    form.reset();
    form.querySelector('.cs-error').style.display = 'none';
    for (var n = 1; n <= 7; n++) { setDay(n, false, '', ''); }
    $('cs-modal-sub').textContent = title;
    $('cs-from').value = TODAY;
    rules = data && data.rules ? data.rules : RULES;
    period = data ? { start: data.batch_start, end: data.batch_end, months: data.months } : null;
    if (data) {
      data.days.forEach(function (d) { setDay(d.day, true, d.start, d.end); });
      var first = data.days[0] || data.suggested;
      if (first) { $('cs-same-start').value = first.start; $('cs-same-end').value = first.end; }
      // A batch that has not started yet: start the schedule with it.
      if (data.batch_start && data.batch_start > TODAY) { $('cs-from').value = data.batch_start; }
    }
    updateHint();
    $('cs-modal').classList.add('open');
  }
  document.querySelectorAll('[data-edit]').forEach(function (b) {
    b.addEventListener('click', function () {
      var id = b.getAttribute('data-edit');
      b.disabled = true;
      send('GET', URLS.show.replace('__ID__', id)).then(function (data) { openEditor([id], b.getAttribute('data-name'), data); }, function (e) { toast(e.message, true); })
        .then(function () { b.disabled = false; });
    });
  });
  $('cs-bulk-edit').addEventListener('click', function () {
    var ids = selected();
    openEditor(ids, ids.length + ' students — replaces their current class days', null);
  });
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var btn = form.querySelector('[type=submit]'), body = new URLSearchParams(new FormData(form));
    editing.forEach(function (id) { body.append('user_ids[]', id); });
    form.querySelector('.cs-error').style.display = 'none';
    btn.disabled = true;
    send('POST', URLS.save, body).then(function (res) {
      toast(res.message);
      setTimeout(function () { window.location.reload(); }, 700);
    }, function (err) { showError(form, err); btn.disabled = false; });
  });
  document.querySelectorAll('.cs-modal').forEach(function (m) {
    m.addEventListener('click', function (e) { if (e.target === m || e.target.hasAttribute('data-close')) { m.classList.remove('open'); } });
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { $('cs-modal').classList.remove('open'); } });

  // ---------- Holidays ----------
  function renderHolidays() {
    $('cs-hol-list').innerHTML = holidays.length ? holidays.map(function (h) {
      return '<div class="cs-hol' + (h.date < TODAY ? ' past' : '') + '"><div><strong>' + esc(h.label) + '</strong><span class="cs-sub">' + esc(h.title) + ' · ' + esc(h.center) + '</span></div>' +
        (h.can_delete ? '<button type="button" data-del="' + h.id + '">Remove</button>' : '') + '</div>';
    }).join('') : '<p class="cs-help">No holidays added.</p>';
  }
  $('cs-hol-form').addEventListener('submit', function (e) {
    e.preventDefault();
    var f = this, btn = f.querySelector('[type=submit]');
    f.querySelector('.cs-error').style.display = 'none';
    btn.disabled = true;
    send('POST', URLS.addHoliday, new URLSearchParams(new FormData(f))).then(function (res) {
      holidays = res.holidays; renderHolidays(); f.reset(); toast(res.message);
    }, function (err) { showError(f, err); }).then(function () { btn.disabled = false; });
  });
  $('cs-hol-list').addEventListener('click', function (e) {
    var b = e.target.closest('[data-del]');
    if (!b || !confirm('Remove this holiday?')) { return; }
    b.disabled = true;
    send('DELETE', URLS.deleteHoliday.replace('__ID__', b.getAttribute('data-del'))).then(function (res) {
      holidays = res.holidays; renderHolidays(); toast(res.message);
    }, function (err) { toast(err.message, true); b.disabled = false; });
  });
  renderHolidays();

})();
</script>
@endsection
