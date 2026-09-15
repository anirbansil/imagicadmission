<table class="listingtable">
    <tr>
    <th>Sl No</th>
    <th>Student Name</th>
    <th>Course Name</th>
    <th>Module Descriotion</th>
    </tr>
    @php $a=1; @endphp
    @forelse($marks_list as $marks)
<tr>
    <td>{{$a}}</td>
    <td>{{$marks->student->regDetails->first_name}} &nbsp; {{$marks->student->regDetails->mid_name}} &nbsp; {{$marks->student->regDetails->last_name}}</td>
    <td>{{$marks->courseName->name}}</td>
    <td>
        @php
        $modules = explode(',',$marks->courseName->module)
        @endphp
        @foreach($modules as $module)
        {{$module}} : {{@App\Helpers\Helper::marksBymodule($marks->stdnt_id,$module)->marks}}
        @endforeach
    </td>
</tr>
@php $a++; @endphp
@empty
<tr><td colspan="6">No Data </td></tr>
@endforelse
</table>