<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\ProviderCategory;
use App\Models\ProviderCity;
use App\Models\Category;
use App\Models\City;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProviderProfileController extends Controller
{
    /**
     * عرض صفحة إكمال الملف الشخصي
     */
    public function completeProfile()
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return redirect()->route('home')->with('error', 'هذه الصفحة متاحة لمزودي الخدمة فقط');
        }

        $user = Auth::user();
        $profile = $user->providerProfile;

        // إذا كان الملف الشخصي مكتمل، توجيه إلى صفحة العرض
        if ($profile && $profile->isProfileComplete()) {
            return redirect()->route('provider.profile')->with('info', 'الملف الشخصي مكتمل بالفعل');
        }

        $categories = Category::where('is_active', true)->get();
        $cities = City::getActiveCities();
        $maxCategories = SystemSetting::get('provider_max_categories', 3);
        $maxCities = SystemSetting::get('provider_max_cities', 10);

        // تحميل العلاقات للتأكد من عملها
        if ($profile) {
            $profile->load(['activeCategories', 'activeCities']);
        }

        return view('provider.complete-profile', compact('profile', 'categories', 'cities', 'maxCategories', 'maxCities'));
    }

    /**
     * عرض صفحة تعديل الملف الشخصي
     */
    public function edit()
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return redirect()->route('home')->with('error', 'هذه الصفحة متاحة لمزودي الخدمة فقط');
        }

        $user = Auth::user();
        $profile = $user->providerProfile;

        // إذا لم يكن لديه ملف شخصي، توجيه إلى صفحة الإكمال
        if (!$profile) {
            return redirect()->route('provider.complete-profile')->with('error', 'يجب إنشاء ملف شخصي أولاً');
        }

        // تحميل العلاقات للتأكد من عملها
        $profile->load(['activeCategories', 'activeCities']);

        $categories = Category::where('is_active', true)->get();
        $cities = City::getActiveCities();
        $maxCategories = SystemSetting::get('provider_max_categories', 3);
        $maxCities = SystemSetting::get('provider_max_cities', 10);

        return view('provider.edit-profile', compact('profile', 'categories', 'cities', 'maxCategories', 'maxCities'));
    }

    /**
     * تحديث الملف الشخصي
     */
    public function update(Request $request)
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return redirect()->route('home')->with('error', 'هذه الصفحة متاحة لمزودي الخدمة فقط');
        }

        $request->validate([
            'bio' => 'required|string|max:1000',
            'address' => 'required|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'cities' => 'required|array|min:1',
            'cities.*' => 'exists:cities,id',
        ]);

        $user = Auth::user();
        $profile = $user->providerProfile;
        $maxCategories = SystemSetting::get('provider_max_categories', 3);
        $maxCities = SystemSetting::get('provider_max_cities', 10);

        // معالجة رفع الصورة الشخصية
        $imagePath = null;
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('users', 'public');
        }

        // التحقق من عدد الأقسام
        if (count($request->categories) > $maxCategories) {
            return back()->withErrors(['categories' => "يمكنك اختيار حد أقصى {$maxCategories} أقسام"]);
        }

        // التحقق من عدد المدن
        if (count($request->cities) > $maxCities) {
            return back()->withErrors(['cities' => "يمكنك اختيار حد أقصى {$maxCities} مدن"]);
        }

        // تحديث صورة المستخدم إذا تم رفع صورة جديدة
        if ($imagePath) {
            $user->update(['image' => $imagePath]);
        }

        // تحديث الملف الشخصي
        $profile->update([
            'bio' => $request->bio,
            'phone' => $user->phone, // استخدام رقم الهاتف من المستخدم
            'address' => $request->address,
        ]);

        // حذف الأقسام والمدن القديمة
        $user->providerCategories()->delete();
        $user->providerCities()->delete();

        // إضافة الأقسام الجديدة
        foreach ($request->categories as $categoryId) {
            ProviderCategory::create([
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'is_active' => true,
            ]);
        }

        // إضافة المدن الجديدة
        foreach ($request->cities as $cityId) {
            ProviderCity::create([
                'user_id' => $user->id,
                'city_id' => $cityId,
                'is_active' => true,
            ]);
        }

        return redirect()->route('provider.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * حفظ الملف الشخصي
     */
    public function store(Request $request)
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return redirect()->route('home')->with('error', 'هذه الصفحة متاحة لمزودي الخدمة فقط');
        }

        $request->validate([
            'bio' => 'required|string|max:1000',
            'address' => 'required|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'cities' => 'required|array|min:1',
            'cities.*' => 'exists:cities,id',
        ]);

        $user = Auth::user();
        $maxCategories = SystemSetting::get('provider_max_categories', 3);
        $maxCities = SystemSetting::get('provider_max_cities', 10);

        // معالجة رفع الصورة الشخصية
        $imagePath = null;
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->image) {
                \Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('users', 'public');
        }

        // التحقق من عدد الأقسام
        if (count($request->categories) > $maxCategories) {
            return back()->withErrors(['categories' => "يمكنك اختيار حد أقصى {$maxCategories} أقسام"]);
        }

        // التحقق من عدد المدن
        if (count($request->cities) > $maxCities) {
            return back()->withErrors(['cities' => "يمكنك اختيار حد أقصى {$maxCities} مدن"]);
        }

        // تحديث صورة المستخدم إذا تم رفع صورة جديدة
        if ($imagePath) {
            $user->update(['image' => $imagePath]);
        }

        // إنشاء أو تحديث الملف الشخصي
        $profile = $user->providerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'bio' => $request->bio,
                'phone' => $user->phone, // استخدام رقم الهاتف من المستخدم
                'address' => $request->address,
                'max_categories' => $maxCategories,
                'max_cities' => $maxCities,
                'is_verified' => SystemSetting::get('provider_auto_approve', false),
                'is_active' => SystemSetting::get('provider_auto_approve', false),
            ]
        );

        // حذف الأقسام والمدن القديمة
        $user->providerCategories()->delete();
        $user->providerCities()->delete();

        // إضافة الأقسام الجديدة
        foreach ($request->categories as $categoryId) {
            ProviderCategory::create([
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'is_active' => true,
            ]);
        }

        // إضافة المدن الجديدة
        foreach ($request->cities as $cityId) {
            ProviderCity::create([
                'user_id' => $user->id,
                'city_id' => $cityId,
                'is_active' => true,
            ]);
        }

        return redirect()->route('provider.profile')->with('success', 'تم حفظ الملف الشخصي بنجاح');
    }

    /**
     * عرض الملف الشخصي
     */
    public function show()
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return redirect()->route('home')->with('error', 'هذه الصفحة متاحة لمزودي الخدمة فقط');
        }

        $user = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return redirect()->route('provider.complete-profile')->with('error', 'يجب إكمال الملف الشخصي أولاً');
        }

        return view('provider.profile', compact('profile'));
    }



    /**
     * إضافة قسم جديد
     */
    public function addCategory(Request $request)
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:500',
            'hourly_rate' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0|max:50',
        ]);

        $user = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return response()->json(['error' => 'يجب إكمال الملف الشخصي أولاً'], 400);
        }

        if (!$profile->canAddCategory()) {
            return response()->json(['error' => 'لا يمكن إضافة المزيد من الأقسام'], 400);
        }

        // التحقق من عدم وجود القسم مسبقاً
        if ($user->providerCategories()->where('category_id', $request->category_id)->exists()) {
            return response()->json(['error' => 'هذا القسم مضاف مسبقاً'], 400);
        }

        ProviderCategory::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'hourly_rate' => $request->hourly_rate,
            'experience_years' => $request->experience_years,
            'is_active' => true,
        ]);

        return response()->json(['success' => 'تم إضافة القسم بنجاح']);
    }

    /**
     * إضافة مدينة جديدة
     */
    public function addCity(Request $request)
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return response()->json(['error' => 'يجب إكمال الملف الشخصي أولاً'], 400);
        }

        if (!$profile->canAddCity()) {
            return response()->json(['error' => 'لا يمكن إضافة المزيد من المدن'], 400);
        }

        // التحقق من عدم وجود المدينة مسبقاً
        if ($user->providerCities()->where('city_id', $request->city_id)->exists()) {
            return response()->json(['error' => 'هذه المدينة مضافة مسبقاً'], 400);
        }

        ProviderCity::create([
            'user_id' => $user->id,
            'city_id' => $request->city_id,
            'notes' => $request->notes,
            'is_active' => true,
        ]);

        return response()->json(['success' => 'تم إضافة المدينة بنجاح']);
    }

    /**
     * حذف قسم
     */
    public function removeCategory($id)
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $category = ProviderCategory::where('id', $id)
                                   ->where('user_id', Auth::id())
                                   ->first();

        if (!$category) {
            return response()->json(['error' => 'القسم غير موجود'], 404);
        }

        $category->delete();

        return response()->json(['success' => 'تم حذف القسم بنجاح']);
    }

    /**
     * حذف مدينة
     */
    public function removeCity($id)
    {
        // التأكد من أن المستخدم مزود خدمة
        if (!Auth::user()->isProvider()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $city = ProviderCity::where('id', $id)
                           ->where('user_id', Auth::id())
                           ->first();

        if (!$city) {
            return response()->json(['error' => 'المدينة غير موجودة'], 404);
        }

        $city->delete();

        return response()->json(['success' => 'تم حذف المدينة بنجاح']);
    }
}
