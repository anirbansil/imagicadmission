{{-- How a paid instalment was paid: by the student online, or recorded by the center / admin. --}}
@php $icons = ['Online' => 'fa-globe', 'Cash' => 'fa-money', 'Cheque' => 'fa-file-text-o']; @endphp
@if($r->channel === 'portal')
  <span class="pay-via portal"><i class="fa fa-user" aria-hidden="true"></i> Self · Student Portal</span>
  @if(!empty($detail))<small><i class="fa {{ $icons['Online'] }}" aria-hidden="true"></i> Online{{ $r->online->method ? ' ('.strtoupper($r->online->method).')' : '' }}</small>@endif
@else
  <span class="pay-via center"><i class="fa fa-building-o" aria-hidden="true"></i> Center</span>
  <small><i class="fa {{ $icons[$r->mode] ?? 'fa-circle-o' }}" aria-hidden="true"></i> {{ $r->mode }}@if(!empty($detail) && $r->collected_by) · {{ $r->collected_by }}@endif</small>
@endif
