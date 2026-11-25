<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One email or SMS sent (or attempted) by App\Services\Notifier.
 */
class NotificationLog extends Model
{
    protected $table = 'notification_logs';

    protected $fillable = ['type', 'channel', 'user_id', 'ref', 'to', 'cc', 'subject', 'status', 'error'];
}
