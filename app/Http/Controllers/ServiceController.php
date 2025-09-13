<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use App\Models\City;
use App\Models\ServiceOffer;
use App\Models\CategoryField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * عرض الخدمات
     */
    public function index(Request $request)
    {
        // إذا كان المستخدم مسجل دخول
        if (auth()->check()) {
            // إذا كان مزود خدمة، اعرض جميع الخدمات
                        if (auth()->user()->isProvider()) {
                $query = Service::where('is_active', true)
                           ->with(['category', 'subCategory', 'user', 'city', 'offers' => function($q) {
                               $q->where('provider_id', auth()->id());
                           }]);
            } else {
                // إذا كان مستخدم عادي، اعرض خدماته فقط
                $query = Service::where('user_id', auth()->id())
                               ->with(['category', 'subCategory', 'user', 'city']);
            }
        } else {
            // إذا لم يكن مسجل دخول، اعرض جميع الخدمات النشطة
            $query = Service::where('is_active', true)
                           ->with(['category', 'subCategory', 'user', 'city']);
        }

        // البحث
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%");
            });
        }

        // تصفية حسب القسم
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // تصفية حسب القسم الفرعي
        if ($request->has('sub_category') && $request->sub_category) {
            $query->where('sub_category_id', $request->sub_category);
        }

        // تصفية حسب المدينة
        if ($request->has('city') && $request->city) {
            $query->where('city_id', $request->city);
        }

        $services = $query->latest()->paginate(12);
        $categories = Category::where('is_active', true)->get();
        $cities = City::getActiveCities();

        // جلب الأقسام الفرعية إذا تم اختيار قسم رئيسي
        $subCategories = collect();
        if ($request->has('category') && $request->category) {
            $subCategories = \App\Models\SubCategory::where('category_id', $request->category)
                                                   ->where('status', true)
                                                   ->get();
        }

        return view('services.index', compact('services', 'categories', 'subCategories', 'cities'));
    }

    /**
     * عرض خدمة معينة
     */
    public function show($slug)
    {
        $service = Service::where('slug', $slug)
                         ->where('is_active', true)
                         ->with(['category', 'subCategory', 'user', 'city'])
                         ->firstOrFail();

        // التحقق من إمكانية مزود الخدمة تقديم عرض
        $canProviderOffer = false;
        $userOffer = null;

        if (auth()->check() && auth()->user()->isProvider() && auth()->id() !== $service->user_id) {
            $canProviderOffer = $this->canProviderOfferService(auth()->user(), $service);

            // جلب العرض المقدم من المستخدم الحالي لهذه الخدمة
            $userOffer = ServiceOffer::where('service_id', $service->id)
                                   ->where('provider_id', auth()->id())
                                   ->first();
        }

        // الخدمات المشابهة
        $relatedServices = Service::where('category_id', $service->category_id)
                                 ->where('id', '!=', $service->id)
                                 ->where('is_active', true)
                                 ->with(['category', 'subCategory', 'user', 'city'])
                                 ->limit(6)
                                 ->get();

        return view('services.show', compact('service', 'relatedServices', 'canProviderOffer', 'userOffer'));
    }

    /**
     * البحث في الخدمات
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return redirect()->route('services.index');
        }

        // إذا كان المستخدم مسجل دخول
        if (auth()->check()) {
                    // إذا كان مزود خدمة، ابحث في جميع الخدمات
        if (auth()->user()->isProvider()) {
            $services = Service::where('is_active', true)
                              ->where(function($q) use ($query) {
                                  $q->where('title', 'like', "%{$query}%")
                                    ->orWhere('description', 'like', "%{$query}%");
                              })
                              ->with(['category', 'user', 'city'])
                              ->latest()
                              ->paginate(12);
        } else {
            // إذا كان مستخدم عادي، ابحث في خدماته فقط
            $services = Service::where('user_id', auth()->id())
                              ->where(function($q) use ($query) {
                                  $q->where('title', 'like', "%{$query}%")
                                    ->orWhere('description', 'like', "%{$query}%");
                              })
                              ->with(['category', 'user', 'city'])
                              ->latest()
                              ->paginate(12);
        }
        } else {
                    // إذا لم يكن مسجل دخول، ابحث في جميع الخدمات النشطة
        $services = Service::where('is_active', true)
                          ->where(function($q) use ($query) {
                              $q->where('title', 'like', "%{$query}%")
                                ->orWhere('description', 'like', "%{$query}%");
                          })
                          ->with(['category', 'user', 'city'])
                          ->latest()
                          ->paginate(12);
        }

        $categories = Category::where('is_active', true)->get();
        $cities = City::getActiveCities();

        return view('services.search', compact('services', 'categories', 'cities', 'query'));
    }

    /**
     * عرض صفحة طلب الخدمة
     */
    public function request(Category $category)
    {
        // التأكد من أن المستخدم مسجل دخول
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول لطلب الخدمة');
        }

        // تحميل الحقول المخصصة مع القسم مرتبة حسب الترتيب
        $category->load(['fields' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order', 'asc');
        }, 'subCategories']);

        // التحقق من وجود أقسام فرعية
        $hasSubCategories = $category->subCategories && $category->subCategories->count() > 0;
        $selectedSubCategoryId = request('sub_category_id');
        $selectedSubCategory = null;

        // إذا كان القسم يحتوي على أقسام فرعية، يجب اختيار قسم فرعي
        if ($hasSubCategories && !$selectedSubCategoryId) {
            return redirect()->route('categories.show', $category->slug)
                           ->with('error', 'يرجى اختيار قسم فرعي لطلب الخدمة');
        }

        // جلب القسم الفرعي المحدد إذا كان موجوداً
        if ($selectedSubCategoryId) {
            $selectedSubCategory = $category->subCategories()
                                          ->where('id', $selectedSubCategoryId)
                                          ->where('status', true)
                                          ->first();

            // التحقق من صحة القسم الفرعي
            if (!$selectedSubCategory) {
                return redirect()->route('categories.show', $category->slug)
                               ->with('error', 'القسم الفرعي المحدد غير صحيح');
            }
        }

        // جلب المدن المرتبطة بهذا القسم فقط
        $cities = $category->activeCities()->orderBy('sort_order')->orderBy('name_ar')->get();

        return view('services.request', compact('category', 'cities', 'selectedSubCategory', 'selectedSubCategoryId', 'hasSubCategories'));
    }

    /**
     * حفظ طلب الخدمة
     */
    public function store(Request $request)
    {
        // التأكد من أن المستخدم مسجل دخول
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول لطلب الخدمة');
        }

        // التأكد من أن المستخدم موجود في قاعدة البيانات
        $user = auth()->user();
        if (!$user || !$user->id) {
            return redirect()->route('login')->with('error', 'خطأ في بيانات المستخدم، يرجى تسجيل الدخول مرة أخرى');
        }

        // Debug: Log user information
        \Log::info('Service creation - User info:', [
            'user_id' => $user->id,
            'user_phone' => $user->phone,
            'user_name' => $user->name
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'city_id' => 'required|exists:cities,id',
            'notes' => 'nullable|string|max:1000',
            'voice_note' => 'nullable|string|max:16777215', // longText max size
            'custom_fields.*' => 'nullable',
        ]);

        // التحقق من أن المدينة متاحة في هذا القسم
        $category = Category::find($request->category_id);
        $availableCityIds = $category->activeCities()->pluck('cities.id')->toArray();

        if (!in_array($request->city_id, $availableCityIds)) {
            return back()->withErrors(['city_id' => 'المدينة المختارة غير متاحة لهذا القسم'])->withInput();
        }

        // التحقق من وجود أقسام فرعية
        $hasSubCategories = $category->subCategories && $category->subCategories->count() > 0;

        // إذا كان القسم يحتوي على أقسام فرعية، يجب اختيار قسم فرعي
        if ($hasSubCategories && !$request->sub_category_id) {
            return back()->withErrors(['sub_category_id' => 'يرجى اختيار قسم فرعي لطلب الخدمة'])->withInput();
        }

        // التحقق من صحة القسم الفرعي إذا كان محدداً
        if ($request->sub_category_id) {
            $subCategory = $category->subCategories()
                                  ->where('id', $request->sub_category_id)
                                  ->where('status', true)
                                  ->first();

            if (!$subCategory) {
                return back()->withErrors(['sub_category_id' => 'القسم الفرعي المحدد غير صحيح'])->withInput();
            }
        }

        $city = City::find($request->city_id);

        // معالجة الحقول المخصصة المتكررة
        $customFields = $request->custom_fields ?? [];
        $processedFields = [];

        foreach ($customFields as $fieldName => $fieldValues) {
            if (is_array($fieldValues)) {
                // حقل متكرر
                $processedValues = [];
                foreach ($fieldValues as $index => $value) {
                    if (is_array($value)) {
                        // معالجة الصور المتعددة
                        $imagePaths = [];
                        foreach ($value as $file) {
                            if ($file && $file->isValid()) {
                                $path = $file->store('custom_fields/' . $fieldName, 'public');
                                $imagePaths[] = $path;
                                Log::info('Service creation - uploaded image:', ['path' => $path]);
                            }
                        }
                        if (!empty($imagePaths)) {
                            $processedValues[] = $imagePaths;
                        }
                    } elseif ($value !== null && $value !== '') {
                        $processedValues[] = $value;
                    }
                }
                if (!empty($processedValues)) {
                    $processedFields[$fieldName] = $processedValues;
                }
            } else {
                // حقل عادي
                if ($fieldValues !== null && $fieldValues !== '') {
                    $processedFields[$fieldName] = $fieldValues;
                }
            }
        }

        // Debug: Log the processed fields
        Log::info('Service creation - processed fields:', ['processed_fields' => $processedFields]);

        // Debug: Log the sub_category_id
        Log::info('Service creation - sub_category_id:', [
            'sub_category_id' => $request->sub_category_id,
            'request_all' => $request->all()
        ]);

        // Debug: Log the custom fields from request
        Log::info('Service creation - custom fields from request:', ['custom_fields' => $request->custom_fields]);

        // إنشاء slug فريد
        $title = 'طلب خدمة - ' . $category->name . ' - ' . $city->name_ar;
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        // التأكد من أن slug فريد
        while (Service::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $data = [
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'city_id' => $request->city_id,
            'user_id' => $user->id,
            'title' => $title,
            'description' => $request->notes ?? '',
            'price' => 0, // سيتم تحديده لاحقاً
            'is_active' => true,
            'custom_fields' => $processedFields,
            'voice_note' => $request->voice_note,
            'slug' => $slug,
        ];

        // Debug: Log the data array
        Log::info('Service creation - data array:', ['data' => $data]);

        // Debug: Log the custom fields in data
        Log::info('Service creation - custom fields in data:', ['custom_fields' => $data['custom_fields'] ?? []]);

        // إنشاء الخدمة
        $service = Service::create($data);

        // Debug: Log the created service
        Log::info('Service created successfully:', [
            'service_id' => $service->id,
            'custom_fields' => $service->custom_fields
        ]);

        // Debug: Log the custom fields from the created service
        Log::info('Service creation - custom fields from created service:', ['custom_fields' => $service->custom_fields]);

        return redirect()->route('services.show', $service->slug)
                         ->with('success', 'تم إرسال طلب الخدمة بنجاح');
    }

    /**
     * عرض صفحة تعديل الخدمة
     */
    public function edit(Service $service)
    {
        // التحقق من أن المستخدم هو صاحب الخدمة
        if (auth()->id() !== $service->user_id) {
            abort(403, 'غير مصرح لك بتعديل هذه الخدمة');
        }

        $categories = Category::where('is_active', true)->get();
        $cities = City::getActiveCities();

        // جلب الأقسام الفرعية للقسم المختار
        $subCategories = collect();
        if ($service->category_id) {
            $subCategories = \App\Models\SubCategory::where('category_id', $service->category_id)
                                                   ->where('status', true)
                                                   ->get();
        }

        // جلب الحقول المخصصة للقسم
        $categoryFields = \App\Models\CategoryField::where('category_id', $service->category_id)
                                                   ->where('is_active', true)
                                                   ->orderBy('sort_order')
                                                   ->get();

        return view('services.edit', compact('service', 'categories', 'subCategories', 'cities', 'categoryFields'));
    }

    /**
     * تحديث الخدمة
     */
    public function update(Request $request, Service $service)
    {
        // التحقق من أن المستخدم هو صاحب الخدمة
        if (auth()->id() !== $service->user_id) {
            abort(403, 'غير مصرح لك بتعديل هذه الخدمة');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'city_id' => 'required|exists:cities,id',
            'notes' => 'nullable|string|max:1000',
            'voice_note' => 'nullable|string|max:16777215', // longText max size
            'custom_fields.*' => 'nullable',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'city_id' => $request->city_id,
        ];

        // معالجة الحقول المخصصة المتكررة
        $customFields = $request->custom_fields ?? [];
        $processedFields = [];

        // البدء بالحقول الموجودة
        $existingCustomFields = $service->custom_fields ?? [];

        foreach ($customFields as $fieldName => $fieldValues) {
            if (is_array($fieldValues)) {
                // حقل متكرر
                $processedValues = [];
                $existingValues = $existingCustomFields[$fieldName] ?? [];

                foreach ($fieldValues as $index => $value) {
                    if (is_array($value)) {
                        // معالجة الصور المتعددة
                        $imagePaths = [];
                        foreach ($value as $file) {
                            if ($file && $file->isValid()) {
                                $path = $file->store('custom_fields/' . $fieldName, 'public');
                                $imagePaths[] = $path;
                                Log::info('Service update - uploaded new image:', ['path' => $path]);
                            }
                        }
                        if (!empty($imagePaths)) {
                            $processedValues[] = $imagePaths;
                        }
                    } elseif ($value !== null && $value !== '') {
                        $processedValues[] = $value;
                    }
                }

                // دمج القيم الموجودة مع الجديدة
                if (!empty($processedValues)) {
                    $processedFields[$fieldName] = $processedValues;
                } elseif (isset($existingValues)) {
                    $processedFields[$fieldName] = $existingValues;
                }
            } else {
                // حقل عادي
                if ($fieldValues !== null && $fieldValues !== '') {
                    $processedFields[$fieldName] = $fieldValues;
                } elseif (isset($existingCustomFields[$fieldName])) {
                    $processedFields[$fieldName] = $existingCustomFields[$fieldName];
                }
            }
        }

        // Debug: Log the processed fields
        Log::info('Service update - processed fields:', ['processed_fields' => $processedFields]);

        // معالجة حذف الصور الموجودة
        if ($request->has('delete_images')) {
            $deleteImages = $request->delete_images;
            foreach ($deleteImages as $fieldName => $imagePaths) {
                foreach ($imagePaths as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                        Log::info('Service update - deleted image:', ['path' => $imagePath]);
                    }
                }

                // إزالة الصور المحذوفة من الحقول المخصصة الموجودة
                if (isset($existingCustomFields[$fieldName]) && is_array($existingCustomFields[$fieldName])) {
                    $existingCustomFields[$fieldName] = array_filter($existingCustomFields[$fieldName], function($value) use ($imagePaths) {
                        if (is_array($value)) {
                            return !array_intersect($value, $imagePaths);
                        }
                        return !in_array($value, $imagePaths);
                    });
                }

                // إزالة الصور المحذوفة من الحقول المخصصة الجديدة
                if (isset($processedFields[$fieldName]) && is_array($processedFields[$fieldName])) {
                    $processedFields[$fieldName] = array_filter($processedFields[$fieldName], function($value) use ($imagePaths) {
                        if (is_array($value)) {
                            return !array_intersect($value, $imagePaths);
                        }
                        return !in_array($value, $imagePaths);
                    });
                }
            }
        }

        // دمج الحقول المخصصة الموجودة مع الجديدة
        $finalCustomFields = array_merge($existingCustomFields, $processedFields);

        // تنظيف الحقول الفارغة
        $finalCustomFields = array_filter($finalCustomFields, function($value) {
            if (is_array($value)) {
                return !empty(array_filter($value, function($item) {
                    if (is_array($item)) {
                        return !empty($item);
                    }
                    return $item !== null && $item !== '';
                }));
            }
            return $value !== null && $value !== '';
        });

        $data['custom_fields'] = $finalCustomFields;

        // Debug: Log the data being saved
        Log::info('Service update - data being saved:', ['data' => $data]);

        // Debug: Log the custom fields in data
        Log::info('Service update - custom fields in data:', ['custom_fields' => $data['custom_fields'] ?? []]);

        // Debug: Log the data array
        Log::info('Service update - data array:', ['data' => $data]);

        // Debug: Log the request data
        Log::info('Service update - request data:', ['request_data' => $request->all()]);

        // Debug: Log the request files
        Log::info('Service update - request files:', ['request_files' => $request->allFiles()]);

        // Debug: Log the request custom fields
        Log::info('Service update - request custom fields:', ['custom_fields' => $request->custom_fields]);

        // Debug: Log the request delete images
        Log::info('Service update - request delete images:', ['delete_images' => $request->delete_images ?? []]);

        // Debug: Log the request has delete images
        Log::info('Service update - request has delete images:', ['has_delete_images' => $request->has('delete_images')]);

        // Debug: Log the request has custom fields
        Log::info('Service update - request has custom fields:', ['has_custom_fields' => $request->has('custom_fields')]);

        // Debug: Log the final custom fields
        Log::info('Service update - final custom fields:', ['final_custom_fields' => $finalCustomFields]);
        Log::info('Service update - existing custom fields:', $existingCustomFields);
        Log::info('Service update - processed fields:', ['processed_fields' => $processedFields]);
        Log::info('Service update - request custom fields:', ['custom_fields' => $request->custom_fields]);

        // Debug: Log the existing custom fields
        Log::info('Service update - existing custom fields:', $existingCustomFields);

        // Debug: Log the processed fields
        Log::info('Service update - processed fields:', ['processed_fields' => $processedFields]);

        // Debug: Log the final custom fields
        Log::info('Service update - final custom fields:', ['final_custom_fields' => $finalCustomFields]);

        // معالجة التسجيل الصوتي
        if ($request->has('voice_note') && $request->voice_note) {
            $data['voice_note'] = $request->voice_note;
        }

        // معالجة الملاحظات
        if ($request->has('notes')) {
            $data['notes'] = $request->notes;
        }


        $service->update($data);

        // Debug: Log the updated service
        Log::info('Service updated successfully:', [
            'service_id' => $service->id,
            'custom_fields' => $service->custom_fields
        ]);

        // Debug: Log the custom fields from the updated service
        Log::info('Service update - custom fields from updated service:', $service->custom_fields);

        return redirect()->route('services.show', $service->slug)
                         ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * حذف الخدمة
     */
    public function destroy(Service $service)
    {
        // التحقق من أن المستخدم هو صاحب الخدمة
        if (auth()->id() !== $service->user_id) {
            abort(403, 'غير مصرح لك بحذف هذه الخدمة');
        }

        // حذف الصورة إذا كانت موجودة
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        // حذف الخدمة (سيتم حذف العروض المرتبطة تلقائياً بسبب foreign key constraints)
        $service->delete();

        return redirect()->route('services.index')
                         ->with('success', 'تم حذف الخدمة بنجاح');
    }

    /**
     * عرض خدمات المستخدم
     */
    public function myServices()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول');
        }

        // إذا كان مزود خدمة، اعرض جميع الخدمات
        if (auth()->user()->isProvider()) {
            $services = Service::where('is_active', true)
                              ->with(['category', 'offers', 'user', 'city'])
                              ->latest()
                              ->paginate(10);
        } else {
            // إذا كان مستخدم عادي، اعرض خدماته فقط
            $services = Service::where('user_id', auth()->id())
                              ->with(['category', 'offers', 'city'])
                              ->latest()
                              ->paginate(10);
        }

        return view('services.my-services', compact('services'));
    }

    /**
     * التحقق من إمكانية مزود الخدمة تقديم عرض لخدمة معينة
     */
    private function canProviderOfferService($provider, $service)
    {
        // التحقق من أن مزود الخدمة لديه ملف شخصي
        $profile = $provider->providerProfile;
        if (!$profile) {
            return false;
        }

        // التحقق من أن القسم متطابق مع اختيارات مزود الخدمة
        $providerCategoryIds = $profile->activeCategories()->pluck('category_id')->toArray();
        if (!in_array($service->category_id, $providerCategoryIds)) {
            return false;
        }

        // التحقق من أن المدن متطابقة مع اختيارات مزود الخدمة
        $providerCityIds = $profile->activeCities()->pluck('city_id')->toArray();

        // إذا كانت الخدمة لها مدينة محددة
        if ($service->city_id) {
            if (!in_array($service->city_id, $providerCityIds)) {
                return false;
            }
        } else {
            // إذا كانت الخدمة متاحة في جميع المدن، يجب أن يكون مزود الخدمة متاح في نفس المدن
            // أو يمكن تعديل هذا المنطق حسب احتياجاتك
            return true;
        }

        return true;
    }
}
