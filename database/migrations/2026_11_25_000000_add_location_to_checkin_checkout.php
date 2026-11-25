<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where a student was when they checked in and out, so centers can see that
 * attendance was marked at the institute (or under an approved online class).
 */
class AddLocationToCheckinCheckout extends Migration
{
    public function up()
    {
        Schema::table('checkin_checkout', function (Blueprint $table) {
            // campus = location checked at the center; online = approved online class; null = not checked
            $table->string('mode', 10)->nullable()->after('checkout_time');
            $table->decimal('check_in_lat', 10, 7)->nullable()->after('mode');
            $table->decimal('check_in_lng', 10, 7)->nullable()->after('check_in_lat');
            $table->unsignedInteger('check_in_accuracy')->nullable()->after('check_in_lng');
            $table->unsignedInteger('check_in_distance')->nullable()->after('check_in_accuracy');
            $table->decimal('checkout_lat', 10, 7)->nullable()->after('check_in_distance');
            $table->decimal('checkout_lng', 10, 7)->nullable()->after('checkout_lat');
            $table->unsignedInteger('checkout_accuracy')->nullable()->after('checkout_lng');
            $table->unsignedInteger('checkout_distance')->nullable()->after('checkout_accuracy');
        });
    }

    public function down()
    {
        Schema::table('checkin_checkout', function (Blueprint $table) {
            $table->dropColumn(['mode', 'check_in_lat', 'check_in_lng', 'check_in_accuracy', 'check_in_distance',
                'checkout_lat', 'checkout_lng', 'checkout_accuracy', 'checkout_distance']);
        });
    }
}
