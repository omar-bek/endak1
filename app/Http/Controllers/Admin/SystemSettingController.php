<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    /**
     * عرض إعدادات النظام
     */
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');
        return view('admin.system-settings.index', compact('settings'));
    }

    /**
     * تحديث إعدادات النظام
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
        ]);

        foreach ($request->settings as $setting) {
            $systemSetting = SystemSetting::where('key', $setting['key'])->first();

            if ($systemSetting) {
                $systemSetting->update([
                    'value' => is_array($setting['value']) ? json_encode($setting['value']) : (string) $setting['value']
                ]);
            }
        }

        return redirect()->route('admin.system-settings.index')
                         ->with('success', 'تم تحديث إعدادات النظام بنجاح');
    }

    /**
     * تحديث إعدادات مزود الخدمة
     */
    public function updateProviderSettings(Request $request)
    {
        $request->validate([
            'provider_max_categories' => 'required|integer|min:1|max:10',
            'provider_max_cities' => 'required|integer|min:1|max:20',
            'provider_verification_required' => 'boolean',
            'provider_auto_approve' => 'boolean',
        ]);

        SystemSetting::set('provider_max_categories', $request->provider_max_categories, 'integer', 'provider');
        SystemSetting::set('provider_max_cities', $request->provider_max_cities, 'integer', 'provider');
        SystemSetting::set('provider_verification_required', $request->provider_verification_required, 'boolean', 'provider');
        SystemSetting::set('provider_auto_approve', $request->provider_auto_approve, 'boolean', 'provider');

        return redirect()->route('admin.system-settings.index')
                         ->with('success', 'تم تحديث إعدادات مزود الخدمة بنجاح');
    }

    /**
     * تحديث الصورة الافتراضية للخدمات
     */
    public function updateDefaultServiceImage(Request $request)
    {
        $request->validate([
            'default_service_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'default_service_image_enabled' => 'boolean',
            'remove_image' => 'boolean'
        ]);

        // إذا تم طلب حذف الصورة
        if ($request->has('remove_image') && $request->remove_image) {
            $currentImage = SystemSetting::get('default_service_image');
            if ($currentImage && Storage::disk('public')->exists($currentImage)) {
                Storage::disk('public')->delete($currentImage);
            }
            SystemSetting::setDefaultServiceImage('services/default-service.jpg');
        }

        // إذا تم رفع صورة جديدة
        if ($request->hasFile('default_service_image')) {
            $file = $request->file('default_service_image');

            // حذف الصورة القديمة
            $currentImage = SystemSetting::get('default_service_image');
            if ($currentImage && Storage::disk('public')->exists($currentImage)) {
                Storage::disk('public')->delete($currentImage);
            }

            // حفظ الصورة الجديدة
            $path = $file->store('services', 'public');
            SystemSetting::setDefaultServiceImage($path);
        }

        // تحديث حالة التفعيل
        SystemSetting::setDefaultServiceImageEnabled($request->has('default_service_image_enabled'));

        return redirect()->route('admin.system-settings.index')
                         ->with('success', 'تم تحديث الصورة الافتراضية للخدمات بنجاح');
    }
}
