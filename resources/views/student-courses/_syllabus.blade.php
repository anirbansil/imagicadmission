{{-- Module-wise syllabus of one course. Expects $course (CourseMaster with courseModule.subModules). --}}
@if($course->courseModule->isEmpty())
  <p class="crs-empty-syllabus">The syllabus for this course has not been added yet. Please ask your center for details.</p>
@else
  <ol class="crs-modules">
    @foreach($course->courseModule as $i => $module)
    <li>
      <span class="crs-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
      <div>
        <strong>{{ $module->module_name }}</strong>
        @if($module->subModules->isNotEmpty())
        <ul class="crs-topics">
          @foreach($module->subModules as $topic)
          <li>{{ $topic->sub_module_name }}</li>
          @endforeach
        </ul>
        @endif
      </div>
    </li>
    @endforeach
  </ol>
@endif
