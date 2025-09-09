<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

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
}
