<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Module results
    |--------------------------------------------------------------------------
    |
    | Each module exam has a Theory and a Practical section. The percentage is
    | (theory + practical obtained) / (theory + practical full marks), for a
    | module and for the whole course (all evaluated modules together).
    |
    */

    // Grade bands, highest first: the first band whose 'min' the percentage reaches.
    'grades' => [
        ['min' => 90, 'grade' => 'A+', 'remark' => 'Outstanding'],
        ['min' => 80, 'grade' => 'A',  'remark' => 'Excellent'],
        ['min' => 70, 'grade' => 'B+', 'remark' => 'Very Good'],
        ['min' => 60, 'grade' => 'B',  'remark' => 'Good'],
        ['min' => 50, 'grade' => 'C',  'remark' => 'Average'],
        ['min' => 40, 'grade' => 'D',  'remark' => 'Pass'],
        ['min' => 0,  'grade' => 'F',  'remark' => 'Fail'],
    ],

    // Below this percentage a module (or the course) is a fail.
    'pass_percentage' => 40,

    // Suggested full marks when a module has none set yet (staff can change them per exam).
    'default_full_marks' => [
        'theory' => 50,
        'practical' => 50,
    ],

];
