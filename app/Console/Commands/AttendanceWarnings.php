<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\StudentClassSchedule;
use App\Models\User;
use App\Services\Notifier;
use App\Services\StudentAttendance;
use Illuminate\Console\Command;

/**
 * Warn students who were absent or late for several classes in a row (3 by default),
 * and alert their centre with admin in CC. Runs every evening from the scheduler.
 *
 * Only the student's own class days count; holidays, leave and days without a class
 * are skipped, and approved leave or an on-time day ends the run of bad days.
 * Each run of bad days is warned about once, however long it gets.
 */
class AttendanceWarnings extends Command
{
    protected $signature = 'attendance:warnings
                            {--dry-run : List who would be warned without sending anything}
                            {--user= : Check one student (user id) only}';

    protected $description = 'Email students absent or late for 3 classes in a row, and alert their centre (admin in CC)';

    public function handle()
    {
        $streak = (int) config('notifications.warning_streak', 3);
        $dry = (bool) $this->option('dry-run');

        $ids = StudentClassSchedule::query()->distinct()->pluck('user_id');
        if ($this->option('user')) {
            $ids = $ids->intersect([(int) $this->option('user')]);
        }

        $warned = 0;
        foreach (User::whereIn('id', $ids)->where('status', 1)->get() as $user) {
            try {
                $days = self::badRun(new StudentAttendance($user));
            } catch (\Throwable $e) {
                $this->warn("User {$user->id}: ".$e->getMessage());
                continue;
            }
            if (count($days) < $streak) {
                continue;
            }
            // Already warned about this run of bad days?
            $alreadyWarned = NotificationLog::where('type', 'attendance_warning')->where('user_id', $user->id)
                ->where('status', 'sent')->where('ref', '>=', $days[0]['date'])->exists();
            if ($alreadyWarned) {
                continue;
            }

            $last = array_slice($days, -$streak);
            $line = "{$user->name} (#{$user->id}): ".implode(', ', array_map(function ($d) {
                return $d['date'].' '.($d['status'] == 'absent' ? 'absent' : 'late '.$d['late'].'m');
            }, $last));

            if ($dry) {
                $this->line('[dry run] would warn '.$line);
            } else {
                Notifier::attendanceWarning($user, $last);
                $this->line('Warned '.$line);
            }
            $warned++;
        }

        $this->info(($dry ? 'Would warn ' : 'Warned ').$warned.' student(s).');
        return 0;
    }

    /**
     * The student's latest run of bad classes (absent, or checked in late), oldest first.
     * Empty when their most recent finished class was fine.
     */
    public static function badRun(StudentAttendance $attendance)
    {
        $today = $attendance->now->copy()->startOfDay();
        $days = $attendance->days($today->copy()->subDays(60), $today)->reverse();

        $run = [];
        foreach ($days as $day) {
            // Only finished class days count; skip holidays, days without a class,
            // today's class while its check-in is still open, and future classes.
            if (in_array($day['status'], ['no_class', 'holiday', 'today', 'upcoming']) || !$day['class']) {
                continue;
            }
            $bad = $day['status'] == 'absent' || ($day['status'] == 'present' && $day['late'] > 0);
            if (!$bad) {
                break; // on time, or on leave: the run ends here
            }
            array_unshift($run, $day);
        }
        return $run;
    }
}
