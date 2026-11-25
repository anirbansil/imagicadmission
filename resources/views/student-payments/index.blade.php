@extends('layout')
@section('title', $title)
@section('content')
@php
  // Indian digit grouping: ₹1,18,220
  $money = function ($n) {
    $n = (string) abs((int) $n);
    $grouped = strlen($n) > 3 ? preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($n, 0, -3)).','.substr($n, -3) : $n;
    return '₹'.$grouped;
  };
  $fmt = function ($d) { return $d ? $d->format('d M Y') : '—'; };
  $all = $accounts->flatMap(function ($a) { return $a->emis; });
  $nextDue = $accounts->pluck('next')->filter()->sortBy(function ($r) { return optional($r->due)->timestamp ?? PHP_INT_MAX; })->first();
  $justPaid = session('paid');
@endphp
<div id="main" class="admin_main">
<style>
  .pay-page { --pay-ink: #2B2022; --pay-muted: #8a7f80; --pay-line: #ece6e3; --pay-soft: #faf7f5; --pay-primary: #571d1f; --pay-primary-dark: #3a1214;
    --pay-accent: #eca107; --pay-yellow-bg: #fff6dc; --pay-green: #2f8a4c; --pay-green-bg: #e8f5ec; --pay-red: #a12620; --pay-red-bg: #fbe3e1; color: var(--pay-ink); }
  .pay-page.dashboard-materials { margin-top: 10px; }
  .pay-page * { box-sizing: border-box; }
  .pay-page .pay-section { margin: 0 0 22px; padding: 0; } /* site CSS gives every <section> 70px padding */
  .pay-intro { color: var(--pay-muted); margin: 0 0 14px; }
  .pay-alert { border-radius: 8px; padding: 11px 14px; margin-bottom: 14px; font-size: 14px; display: flex; gap: 10px; align-items: flex-start; }
  .pay-alert i { margin-top: 3px; }
  .pay-alert.ok { background: var(--pay-green-bg); color: #1e6b34; border: 1px solid #c9e6d2; }
  .pay-alert.bad { background: #fdf0ef; color: #8b1e17; border: 1px solid #f2c9c5; }
  .pay-alert.info { background: var(--pay-soft); color: var(--pay-ink); border: 1px solid var(--pay-line); }

  .pay-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
  .pay-stat { background: #fff; border: 1px solid var(--pay-line); border-radius: 10px; padding: 14px 16px; }
  .pay-stat span { display: block; font-size: 11.5px; letter-spacing: .4px; text-transform: uppercase; color: var(--pay-muted); font-weight: 600; margin-bottom: 4px; }
  .pay-stat strong { font-size: 21px; font-variant-numeric: tabular-nums; }
  .pay-stat small { display: block; color: var(--pay-muted); font-size: 12.5px; margin-top: 2px; }
  .pay-stat.due { background: var(--pay-primary); border-color: var(--pay-primary); color: #fff; }
  .pay-stat.due span, .pay-stat.due small { color: rgba(255, 255, 255, .75); }
  .pay-stat.due.overdue { background: var(--pay-red); border-color: var(--pay-red); }

  .pay-section-head { display: flex; align-items: center; gap: 10px; margin: 0 0 10px; }
  .pay-section-head h2 { font-size: 19px; font-weight: 700; margin: 0; }
  .pay-section-icon { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; background: #f3e9e9; color: var(--pay-primary); }

  .pay-card { background: #fff; border: 1px solid var(--pay-line); border-radius: 10px; box-shadow: 0 1px 3px rgba(43, 32, 34, .05); overflow: hidden; margin-bottom: 16px; }
  .pay-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 18px; border-bottom: 1px solid var(--pay-line); background: var(--pay-yellow-bg); }
  .pay-head.completed { background: var(--pay-green-bg); }
  .pay-head h3 { font-size: 17px; font-weight: 600; margin: 0 0 3px; }
  .pay-head p { margin: 0; color: var(--pay-muted); font-size: 13px; }
  .pay-summary { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 1px solid var(--pay-line); }
  .pay-summary div { padding: 10px 18px; border-right: 1px solid var(--pay-line); }
  .pay-summary div:last-child { border-right: 0; }
  .pay-summary span { display: block; font-size: 11px; letter-spacing: .4px; text-transform: uppercase; color: var(--pay-muted); font-weight: 600; margin-bottom: 2px; }
  .pay-summary strong { font-size: 15px; font-variant-numeric: tabular-nums; }
  .pay-summary small { color: var(--pay-muted); font-size: 12px; }

  .pay-next { display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap; padding: 14px 18px; border-bottom: 1px solid var(--pay-line); background: #fffdf7; }
  .pay-next.overdue { background: #fff7f6; }
  .pay-next-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--pay-muted); }
  .pay-next-amount { font-size: 24px; font-weight: 700; font-variant-numeric: tabular-nums; line-height: 1.2; }
  .pay-next-meta { font-size: 13px; color: var(--pay-muted); }
  .pay-next-meta b { color: var(--pay-red); }

  .pay-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; border: 1px solid var(--pay-primary); background: var(--pay-primary); color: #fff; border-radius: 7px; padding: 10px 20px; font-size: 14.5px; font-weight: 700; cursor: pointer; white-space: nowrap; line-height: 1.2; }
  .pay-btn:hover { background: var(--pay-primary-dark); }
  .pay-btn[disabled] { opacity: .65; cursor: wait; }
  .pay-btn.small { padding: 5px 12px; font-size: 13px; }
  .pay-secure { font-size: 12px; color: var(--pay-muted); margin-top: 5px; text-align: right; }
  .pay-link { color: var(--pay-primary); font-weight: 600; font-size: 13px; white-space: nowrap; }
  .pay-center-note { font-size: 13px; color: var(--pay-muted); max-width: 320px; text-align: right; }

  .pay-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
  .pay-table th { background: var(--pay-soft); color: var(--pay-muted); font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; padding: 9px 12px; border-bottom: 1px solid var(--pay-line); text-align: left; white-space: nowrap; }
  .pay-table td { padding: 9px 12px; border-bottom: 1px solid var(--pay-line); vertical-align: middle; }
  .pay-table tr:last-child td { border-bottom: 0; }
  .pay-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
  .pay-table small { display: block; color: var(--pay-muted); font-size: 12px; }
  .pay-table tr.just-paid td { background: var(--pay-green-bg); }
  .pay-status { display: inline-block; font-size: 12px; font-weight: 700; border-radius: 12px; padding: 2px 10px; white-space: nowrap; }
  .pay-status.paid { background: var(--pay-green-bg); color: #1e6b34; }
  .pay-status.pending { background: #fff3d6; color: #8a5a00; }
  .pay-status.overdue { background: var(--pay-red-bg); color: var(--pay-red); }
  .pay-empty { padding: 22px 18px; text-align: center; color: var(--pay-muted); }
  .pay-mode { white-space: nowrap; }
  .pay-mode small i { color: var(--pay-muted); width: 14px; }
  .pay-via { display: inline-block; font-size: 12px; font-weight: 700; border-radius: 6px; padding: 2px 8px; white-space: nowrap; }
  .pay-via.center { background: #efe9f7; color: #4b2f7a; }
  .pay-via.portal { background: #e3f0fb; color: #1f4f8a; }
  .pay-filter { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 12px 18px; border-bottom: 1px solid var(--pay-line); }
  .pay-chip { border: 1px solid var(--pay-line); background: #fff; color: var(--pay-ink); border-radius: 18px; padding: 5px 12px; font-size: 13px; font-weight: 600; cursor: pointer; }
  .pay-chip b { margin-left: 3px; color: var(--pay-muted); font-variant-numeric: tabular-nums; }
  .pay-chip:hover { border-color: var(--pay-primary); }
  .pay-chip.active { background: var(--pay-primary); border-color: var(--pay-primary); color: #fff; }
  .pay-chip.active b { color: rgba(255, 255, 255, .8); }
  .pay-filter-total { margin-left: auto; font-size: 13px; font-weight: 700; font-variant-numeric: tabular-nums; }

  @media (max-width: 991px) { .pay-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 767px) {
    .pay-summary { grid-template-columns: 1fr 1fr; }
    .pay-summary div:nth-child(2) { border-right: 0; }
    .pay-summary div:nth-child(-n+2) { border-bottom: 1px solid var(--pay-line); }
    .pay-secure, .pay-center-note { text-align: left; }
  }
  @media (max-width: 575px) {
    .pay-stats { grid-template-columns: 1fr; }
    .pay-head, .pay-next, .pay-summary div { padding-left: 14px; padding-right: 14px; }
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
      <div class="dashboard-materials pay-page">
        <p class="pay-intro">Your course fee instalments (EMIs). The admission payment is collected by your center; you can pay the instalments after it here, online.</p>

        @if(session('msg'))
          <div class="pay-alert ok" role="status"><i class="fa fa-check-circle" aria-hidden="true"></i><div>{{ session('msg') }}</div></div>
        @endif
        @if(session('error'))
          <div class="pay-alert bad" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><div>{{ session('error') }}</div></div>
        @endif
        <div class="pay-alert bad" role="alert" id="pay-error" hidden><i class="fa fa-exclamation-circle" aria-hidden="true"></i><div></div></div>
        @foreach($refunds as $r)
          <div class="pay-alert info"><i class="fa fa-info-circle" aria-hidden="true"></i><div>Your online payment of {{ $money($r->amount) }} on {{ $r->paid_at->format('d M Y') }} (ref. {{ $r->payment_id }}) was for an instalment that had already been paid. It will be refunded; please contact your center if you have questions.</div></div>
        @endforeach

        @if($accounts->isEmpty())
          <div class="pay-card"><div class="pay-empty">You are not enrolled in any course yet, so there are no fees to show.</div></div>
        @else

        <div class="pay-stats">
          <div class="pay-stat"><span>Total course fees</span><strong>{{ $money($accounts->sum('total')) }}</strong><small>{{ $accounts->count() }} {{ $accounts->count() === 1 ? 'course' : 'courses' }}</small></div>
          <div class="pay-stat"><span>Paid so far</span><strong>{{ $money($accounts->sum('admission') + $accounts->sum('paid')) }}</strong><small>{{ $money($accounts->sum('admission') + $accounts->sum('paid_center')) }} at center · {{ $money($accounts->sum('paid_portal')) }} online by you</small></div>
          <div class="pay-stat"><span>Balance</span><strong>{{ $money($accounts->sum('balance')) }}</strong><small>{{ $all->where('status', '!=', 'paid')->count() }} instalments left</small></div>
          @if($nextDue)
          <div class="pay-stat due {{ $nextDue->status === 'overdue' ? 'overdue' : '' }}">
            <span>{{ $nextDue->status === 'overdue' ? 'Overdue' : 'Next due' }}</span>
            <strong>{{ $money($nextDue->payable_amount) }}</strong>
            <small>{{ $nextDue->status === 'overdue' ? 'was due '.$fmt($nextDue->due) : ($nextDue->due_today ? 'due today' : 'due '.$fmt($nextDue->due)) }}</small>
          </div>
          @else
          <div class="pay-stat"><span>Next due</span><strong style="color: var(--pay-green)">All paid</strong><small>No instalments due</small></div>
          @endif
        </div>

        <section class="pay-section">
          <div class="pay-section-head">
            <span class="pay-section-icon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
            <h2>Instalments</h2>
          </div>

          @foreach($accounts as $a)
          @php $e = $a->enrolment; $n = $a->next; @endphp
          <div class="pay-card">
            <div class="pay-head {{ $e->state === 'completed' ? 'completed' : '' }}">
              <div>
                <h3>{{ $e->course->name }}</h3>
                <p>Application {{ $a->application->application_id }}@if($e->center) · {{ $e->center }}@endif</p>
              </div>
              @if($a->emis->isEmpty() || !$n)
                <span class="pay-status paid"><i class="fa fa-check" aria-hidden="true"></i> Fully paid</span>
              @elseif($a->overdue)
                <span class="pay-status overdue">{{ $a->overdue }} overdue</span>
              @endif
            </div>
            <div class="pay-summary">
              <div><span>Course fee</span><strong>{{ $money($a->total) }}</strong>@if((int) $a->application->gst_amount > 0) <small>incl. GST</small>@endif</div>
              <div><span>Paid at admission</span><strong>{{ $money($a->admission) }}</strong> <small>at center</small></div>
              <div><span>EMIs paid</span><strong>{{ $money($a->paid) }}</strong> <small>{{ $a->emis->where('status', 'paid')->count() }}/{{ $a->emis->count() }}</small></div>
              <div><span>Balance</span><strong>{{ $money($a->balance) }}</strong></div>
            </div>

            @if($n)
            <div class="pay-next {{ $n->status }}">
              <div>
                <div class="pay-next-label">{{ $n->status === 'overdue' ? 'Overdue instalment' : 'Next instalment' }} · EMI {{ $n->number }}</div>
                <div class="pay-next-amount">{{ $money($n->payable_amount) }}</div>
                <div class="pay-next-meta">
                  @if($n->status === 'overdue') Was due {{ $fmt($n->due) }} · <b>{{ $n->days_overdue }} {{ $n->days_overdue === 1 ? 'day' : 'days' }} late</b>
                  @elseif($n->due_today) Due <b style="color: inherit">today</b>
                  @else Due {{ $fmt($n->due) }}
                  @endif
                  @if($n->late_fee) · includes {{ $money($n->late_fee) }} late fee @endif
                </div>
              </div>
              <div>
                @if($n->payable)
                  <button type="button" class="pay-btn js-pay" data-emi="{{ $n->emi->id }}" data-amount="{{ $n->payable_amount }}"><i class="fa fa-lock" aria-hidden="true"></i> Pay {{ $money($n->payable_amount) }}</button>
                  <div class="pay-secure">UPI, cards, net banking · secured by Razorpay</div>
                @else
                  <div class="pay-center-note"><i class="fa fa-info-circle" aria-hidden="true"></i> Online payment is not available for your center yet. Please pay this instalment at your center.</div>
                @endif
              </div>
            </div>
            @endif

            @if($a->emis->isEmpty())
              <div class="pay-empty">No instalments for this course: the fee was settled at admission.</div>
            @else
            <div class="table-responsive">
              <table class="pay-table">
                <thead>
                  <tr>
                    <th>EMI</th>
                    <th>Due date</th>
                    <th class="num">Amount</th>
                    <th class="num">Late fee</th>
                    <th>Status</th>
                    <th>Paid on</th>
                    <th>Mode</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($a->emis as $r)
                  <tr class="{{ $justPaid == $r->emi->id ? 'just-paid' : '' }}">
                    <td><strong>{{ $r->number }}</strong></td>
                    <td style="white-space: nowrap">{{ $fmt($r->due) }}</td>
                    <td class="num">{{ $money($r->amount) }}</td>
                    <td class="num">{{ $r->late_fee ? $money($r->late_fee) : '—' }}</td>
                    <td>
                      <span class="pay-status {{ $r->status }}">{{ ucfirst($r->status) }}</span>
                      @if($r->status === 'overdue')<small>{{ $r->days_overdue }} {{ $r->days_overdue === 1 ? 'day' : 'days' }} late</small>@elseif($r->due_today)<small>Due today</small>@endif
                    </td>
                    <td style="white-space: nowrap">{{ $r->paid_on ? $fmt($r->paid_on) : '—' }}</td>
                    <td class="pay-mode">@if($r->mode)@include('student-payments._via', ['r' => $r])@else — @endif</td>
                    <td class="num">
                      @if($r->status === 'paid')
                        <a class="pay-link" href="{{ route('student.payments.receipt', $r->emi->id) }}" target="_blank" rel="noopener"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Receipt</a>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
          @endforeach
        </section>

        <section class="pay-section">
          <div class="pay-section-head">
            <span class="pay-section-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
            <h2>Payment History</h2>
          </div>
          <div class="pay-card">
            @if($history->isEmpty())
              <div class="pay-empty">No instalments paid yet.</div>
            @else
            @php
              $viaCount = $history->groupBy(function ($h) { return $h->row->channel; })->map->count();
              $viaSum = $history->groupBy(function ($h) { return $h->row->channel; })->map(function ($g) { return $g->sum(function ($h) { return $h->row->paid_amount; }); });
            @endphp
            <div class="pay-filter" role="group" aria-label="Filter payment history">
              <button type="button" class="pay-chip active" data-via="all" aria-pressed="true">All <b>{{ $history->count() }}</b></button>
              <button type="button" class="pay-chip" data-via="center" aria-pressed="false"><i class="fa fa-building-o" aria-hidden="true"></i> Paid at center <b>{{ $viaCount['center'] ?? 0 }}</b></button>
              <button type="button" class="pay-chip" data-via="portal" aria-pressed="false"><i class="fa fa-user" aria-hidden="true"></i> Paid online by you <b>{{ $viaCount['portal'] ?? 0 }}</b></button>
              <span class="pay-filter-total" data-for="all">Total {{ $money($history->sum(function ($h) { return $h->row->paid_amount; })) }}</span>
              <span class="pay-filter-total" data-for="center" hidden>Total {{ $money($viaSum['center'] ?? 0) }}</span>
              <span class="pay-filter-total" data-for="portal" hidden>Total {{ $money($viaSum['portal'] ?? 0) }}</span>
            </div>
            <div class="table-responsive">
              <table class="pay-table" id="pay-history">
                <thead>
                  <tr>
                    <th>Paid on</th>
                    <th>Receipt no.</th>
                    <th>Course</th>
                    <th>EMI</th>
                    <th class="num">Amount paid</th>
                    <th>Paid via</th>
                    <th>Reference</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($history as $h)
                  @php $r = $h->row; @endphp
                  <tr class="{{ $justPaid == $r->emi->id ? 'just-paid' : '' }}" data-via="{{ $r->channel }}">
                    <td style="white-space: nowrap">{{ $fmt($r->paid_on) }}</td>
                    <td style="white-space: nowrap">{{ $r->receipt_no }}</td>
                    <td>{{ $h->account->enrolment->course->name }}<small>{{ $h->account->application->application_id }}</small></td>
                    <td>{{ $r->number }}</td>
                    <td class="num"><strong>{{ $money($r->paid_amount) }}</strong>@if($r->late_fee)<small>incl. {{ $money($r->late_fee) }} late fee</small>@endif</td>
                    <td class="pay-mode">@include('student-payments._via', ['r' => $r, 'detail' => true])</td>
                    <td style="word-break: break-all">{{ $r->reference ?: '—' }}</td>
                    <td class="num"><a class="pay-link" href="{{ route('student.payments.receipt', $r->emi->id) }}" target="_blank" rel="noopener"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Receipt</a></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
        </section>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Razorpay Checkout calls this form back with the signed payment details. --}}
<form method="post" action="{{ route('student.payments.verify') }}" id="pay-verify" hidden>
  @csrf
  <input type="hidden" name="razorpay_order_id">
  <input type="hidden" name="razorpay_payment_id">
  <input type="hidden" name="razorpay_signature">
</form>
<script>
  // Payment history filter: all / paid at center / paid online by the student.
  document.querySelectorAll('.pay-chip').forEach(function (chip) {
    chip.addEventListener('click', function () {
      var via = chip.dataset.via;
      document.querySelectorAll('.pay-chip').forEach(function (c) {
        c.classList.toggle('active', c === chip);
        c.setAttribute('aria-pressed', c === chip ? 'true' : 'false');
      });
      document.querySelectorAll('#pay-history tbody tr').forEach(function (tr) {
        tr.hidden = via !== 'all' && tr.dataset.via !== via;
      });
      document.querySelectorAll('.pay-filter-total').forEach(function (t) { t.hidden = t.dataset.for !== via; });
    });
  });
</script>
@if($accounts->contains(function ($a) { return $a->next && $a->next->payable; }))
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
  var token = @json(csrf_token());
  var orderUrl = @json(route('student.payments.order', '__EMI__'));
  var failedUrl = @json(route('student.payments.failed'));
  var errorBox = document.getElementById('pay-error');

  function showError(message) {
    errorBox.querySelector('div').textContent = message;
    errorBox.hidden = false;
    errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
  function post(url, data) {
    return fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(data || {})
    });
  }

  document.querySelectorAll('.js-pay').forEach(function (btn) {
    var label = btn.innerHTML;
    function reset() { btn.disabled = false; btn.innerHTML = label; }

    btn.addEventListener('click', function () {
      errorBox.hidden = true;
      btn.disabled = true;
      btn.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Opening payment…';

      post(orderUrl.replace('__EMI__', btn.dataset.emi)).then(function (res) {
        return res.json().catch(function () { return {}; }).then(function (body) {
          if (!res.ok) { throw new Error(body.message || 'The payment could not be started. Please try again.'); }
          return body;
        });
      }).then(function (o) {
        if (typeof Razorpay === 'undefined') { throw new Error('The payment window could not load. Check your internet connection and try again.'); }
        var checkout = new Razorpay({
          key: o.key, order_id: o.order_id, amount: o.amount, currency: o.currency,
          name: o.name, description: o.description, prefill: o.prefill, notes: o.notes, theme: o.theme,
          handler: function (r) {
            btn.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Confirming payment…';
            var form = document.getElementById('pay-verify');
            form.razorpay_order_id.value = r.razorpay_order_id;
            form.razorpay_payment_id.value = r.razorpay_payment_id;
            form.razorpay_signature.value = r.razorpay_signature;
            form.submit();
          },
          modal: { ondismiss: reset }
        });
        checkout.on('payment.failed', function (r) {
          var reason = (r.error && r.error.description) || 'Payment failed.';
          post(failedUrl, { order_id: o.order_id, reason: reason });
          showError(reason + ' You can try again. If money was debited for this failed attempt, your bank will return it.');
        });
        checkout.open();
      }).catch(function (err) {
        reset();
        showError(err.message);
      });
    });
  });
})();
</script>
@endif
@endsection
