<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة إعداد الصورة الافتراضية للخدمات
        DB::table('system_settings')->insert([
            [
                'key' => 'default_service_image',
                'value' => 'services/default-service.jpg',
                'type' => 'string',
                'group' => 'general',
                'description' => 'الصورة الافتراضية للخدمات التي لا تحتوي على صورة',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_service_image_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'تفعيل استخدام الصورة الافتراضية للخدمات',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', [
            'default_service_image',
            'default_service_image_enabled'
        ])->delete();
    }
};
