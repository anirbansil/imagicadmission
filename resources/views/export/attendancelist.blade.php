<table class="listingtable">
<tr>
    <th>Sl No</th>
    <th>teacher Name </th>
    <th>Course Name</th>
    <th>Module Name</th>
    <th>Date</th>
    <th>Checkin Time</th>
    <th>Checkout Time</th>
    <th>Total Time</th>
</tr>
@php $a= 1; @endphp
    @forelse($checkin_list as $list)
<tr>
<td>{{$a}}</td>
<td>{{@$list->user->name}}</td>
<td>{{@$list->courseName->name}}</td>
<td>{{@$list->module}}</td>
<td>{{\Carbon\Carbon::parse(@$list->date)->format('d-M-Y')}}</td>
<td>{{\Carbon\Carbon::parse(@$list->check_in_time)->format('h:i A')}}</td>
<td>{{@$list->checkout_time != null ? \Carbon\Carbon::parse(@$list->checkout_time)->format('h:i A') : "Didn't Checkout"}}</td>
<td>{{@$list->checkout_time != null ? (new \Carbon\Carbon(@$list->check_in_time))->diff(new \Carbon\Carbon(@$list->checkout_time))->format('%h:%I:%s') : '00:00'}}</td>
</tr>
@php $a++; @endphp
@empty
<tr><td colspan="6">No Data </td></tr>
@endforelse
</table>