<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('name');
            }

            if (!Schema::hasColumn('cities', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });

        $coordinates = [
            'TP. Ho Chi Minh' => [10.7769, 106.7009],
            'Ha Noi' => [21.0278, 105.8342],
            'Da Nang' => [16.0544, 108.2022],
            'Can Tho' => [10.0452, 105.7469],
            'Hai Phong' => [20.8449, 106.6881],
        ];

        foreach ($coordinates as $name => [$latitude, $longitude]) {
            DB::table('cities')
                ->where('name', $name)
                ->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'longitude')) {
                $table->dropColumn('longitude');
            }

            if (Schema::hasColumn('cities', 'latitude')) {
                $table->dropColumn('latitude');
            }
        });
    }
};
