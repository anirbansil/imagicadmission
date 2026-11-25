{{-- Grade badge. Expects $grade from App\Services\StudentResults::grade() (or null). --}}
@if($grade)
<span class="mk-grade mk-g-{{ strtolower(str_replace('+', 'p', $grade['grade'])) }}" title="{{ $grade['remark'] }}">{{ $grade['grade'] }}</span>
@else
<span class="mk-muted">—</span>
@endif
