<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Each centre (branch) keeps its own address and check-in location.
 * Students of a centre with a location must be within geo_radius metres of it to check in.
 */
class AddLocationToCenterMaster extends Migration
{
    public function up()
    {
        Schema::table('center_master', function (Blueprint $table) {
            $table->text('address')->nullable()->after('name');
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedSmallInteger('geo_radius')->nullable()->after('longitude');
            $table->unsignedInteger('location_updated_by')->nullable()->after('geo_radius');
            $table->timestamp('location_updated_at')->nullable()->after('location_updated_by');
        });
    }

    public function down()
    {
        Schema::table('center_master', function (Blueprint $table) {
            $table->dropColumn(['address', 'latitude', 'longitude', 'geo_radius', 'location_updated_by', 'location_updated_at']);
        });
    }
}
