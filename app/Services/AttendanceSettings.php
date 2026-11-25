<?php

namespace App\Services;

use App\Models\AttendanceSetting;
use Illuminate\Support\Facades\Schema;

/**
 * Attendance rules, resolved for a student's center and course.
 *
 * Order (last wins): config/attendance.php defaults, global row, center row,
 * course row, center + course row. The admin screen that edits these rows can
 * be added later; it only needs set() / forget() below.
 */
class AttendanceSettings
{
    protected static $rows;

    /**
     * Rules for one center / course, typed like the defaults.
     */
    public static function for($centerId = null, $courseId = null)
    {
        $defaults = config('attendance.defaults');
        $values = $defaults;

        $rows = self::rows()->filter(function ($r) use ($centerId, $courseId) {
            return ($r->center_id === null || $r->center_id == $centerId)
                && ($r->course_id === null || $r->course_id == $courseId);
        })->sortBy(function ($r) {
            return ($r->center_id !== null ? 1 : 0) + ($r->course_id !== null ? 2 : 0);
        });

        foreach ($rows as $row) {
            if (array_key_exists($row->key, $defaults) && $row->value !== null) {
                $values[$row->key] = self::cast($row->value, $defaults[$row->key]);
            }
        }
        return $values;
    }

    /**
     * Store a rule (global when both center and course are null).
     */
    public static function set($key, $value, $centerId = null, $courseId = null, $userId = null)
    {
        $defaults = config('attendance.defaults');
        if (!array_key_exists($key, $defaults)) {
            throw new \InvalidArgumentException('Unknown attendance setting: '.$key);
        }
        $value = is_bool($defaults[$key]) ? ($value ? '1' : '0') : (string) $value;

        AttendanceSetting::updateOrCreate(
            ['key' => $key, 'center_id' => $centerId, 'course_id' => $courseId],
            ['value' => $value, 'updated_by' => $userId]
        );
        self::$rows = null;
    }

    /**
     * Remove an override so the wider value applies again.
     */
    public static function forget($key, $centerId = null, $courseId = null)
    {
        AttendanceSetting::where(['key' => $key, 'center_id' => $centerId, 'course_id' => $courseId])->delete();
        self::$rows = null;
    }

    protected static function rows()
    {
        if (self::$rows === null) {
            // Before the migration has run, fall back to the config defaults.
            self::$rows = Schema::hasTable('attendance_settings') ? AttendanceSetting::all() : collect();
        }
        return self::$rows;
    }

    protected static function cast($value, $default)
    {
        if (is_bool($default)) {
            return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
        }
        if (is_int($default)) {
            return (int) $value;
        }
        return $value;
    }
}
