<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            $services = Service::query()
                ->where('is_active', true)
                ->with(['category:id,name,slug', 'subCategory:id,name_ar,name_en', 'city:id,name_ar,name_en', 'user:id,name,avatar'])
                ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
                ->when($request->filled('sub_category_id'), fn ($query) => $query->where('sub_category_id', $request->integer('sub_category_id')))
                ->when($request->filled('city_id'), fn ($query) => $query->where('city_id', $request->integer('city_id')))
                ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
                ->when($request->filled('search'), function ($query) use ($request) {
                    $term = $request->get('search');
                    $query->where(function ($inner) use ($term) {
                        $inner->where('title', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%");
                    });
                })
                ->latest()
                ->paginate($request->get('per_page', 12));

            return $this->success($services);
        }, 'حدث خطأ أثناء جلب الخدمات');
    }

    public function show(Service $service)
    {
        return $this->executeApiWithTryCatch(function () use ($service) {
            $service->load([
                'category:id,name,slug',
                'subCategory:id,name_ar,name_en',
                'city:id,name_ar,name_en',
                'user:id,name,avatar',
                'offers.provider:id,name,avatar',
            ]);

            return $this->success($service);
        }, 'حدث خطأ أثناء جلب الخدمة');
    }

    public function store(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            // التحقق من البيانات
            $data = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'price' => ['nullable', 'numeric', 'min:0'],
                'category_id' => ['required', 'exists:categories,id'],
                'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
                'city_id' => ['required', 'exists:cities,id'],
                'custom_fields' => ['nullable', 'array'],
                'custom_fields.*' => ['nullable'],
            ]);

            // إضافة البيانات الأساسية
            $data['user_id'] = $request->user()->id;
            $data['is_active'] = true;

            // إضافة price إذا لم يكن موجوداً
            $data['price'] = $request->has('price') && $request->input('price') !== null
                ? (float) $request->input('price')
                : 0.00;

            // معالجة custom_fields
            $data['custom_fields'] = $this->processCustomFields($request);

            // إضافة slug
            $data['slug'] = $this->generateSlug($data['title']);

            // إنشاء الخدمة
            $service = Service::create($data);

            Log::info('API Service created successfully', [
                'service_id' => $service->id,
                'user_id' => $request->user()->id,
            ]);

            return $this->success($service->fresh(), 'تم إنشاء الخدمة بنجاح', 201);
        }, 'حدث خطأ أثناء إنشاء الخدمة');
    }

    public function update(Request $request, Service $service)
    {
        return $this->executeApiWithTryCatch(function () use ($request, $service) {
            $this->authorizeServiceOwner($request->user()->id, $service);

            $data = $request->validate([
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'string'],
                'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
                'category_id' => ['sometimes', 'exists:categories,id'],
                'sub_category_id' => ['sometimes', 'nullable', 'exists:sub_categories,id'],
                'city_id' => ['sometimes', 'exists:cities,id'],
                'custom_fields' => ['sometimes', 'array'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            // معالجة custom_fields إذا كانت موجودة
            if ($request->has('custom_fields')) {
                $data['custom_fields'] = $this->processCustomFields($request);
            }

            $service->update($data);

            Log::info('API Service updated', [
                'service_id' => $service->id,
                'user_id' => $request->user()->id
            ]);

            return $this->success($service->fresh(), 'تم تحديث الخدمة بنجاح');
        }, 'حدث خطأ أثناء تحديث الخدمة');
    }

    public function destroy(Request $request, Service $service)
    {
        return $this->executeApiWithTryCatch(function () use ($request, $service) {
            $this->authorizeServiceOwner($request->user()->id, $service);
            $service->delete();

            Log::info('API Service deleted', [
                'service_id' => $service->id,
                'user_id' => $request->user()->id
            ]);

            return $this->success(null, 'تم حذف الخدمة بنجاح');
        }, 'حدث خطأ أثناء حذف الخدمة');
    }

    public function myServices(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            $services = $request->user()
                ->services()
                ->with(['category:id,name', 'city:id,name_ar'])
                ->latest()
                ->paginate($request->get('per_page', 12));

            return $this->success($services);
        }, 'حدث خطأ أثناء جلب الخدمات');
    }

    /**
     * معالجة custom_fields من الطلب
     */
    private function processCustomFields(Request $request): ?array
    {
        $customFields = $request->input('custom_fields', []);

        // إذا كانت JSON string، قم بتحويلها
        if (is_string($customFields)) {
            $decoded = json_decode($customFields, true);
            $customFields = is_array($decoded) ? $decoded : [];
        }

        // إذا كانت فارغة، حاول البحث في جميع المدخلات
        if (empty($customFields) || !is_array($customFields)) {
            $customFields = [];
            $allInput = $request->all();

            foreach ($allInput as $key => $value) {
                if (strpos($key, 'custom_fields[') === 0) {
                    if (preg_match('/custom_fields\[([^\]]+)\](?:\[(\d+)\])?/', $key, $matches)) {
                        $fieldName = $matches[1];
                        $index = isset($matches[2]) ? (int)$matches[2] : null;

                        if (!isset($customFields[$fieldName])) {
                            $customFields[$fieldName] = [];
                        }

                        if ($value !== null && $value !== '') {
                            $cleanValue = is_string($value) ? trim($value, '"\'') : $value;

                            if ($index !== null) {
                                $customFields[$fieldName][$index] = $cleanValue;
                            } else {
                                $customFields[$fieldName] = $cleanValue;
                            }
                        }
                    }
                }
            }
        }

        // تنظيف القيم
        foreach ($customFields as $fieldName => &$values) {
            if (is_array($values)) {
                // إزالة القيم الفارغة
                $values = array_values(array_filter($values, function($v) {
                    return $v !== null && $v !== '';
                }));

                // إذا كانت المصفوفة تحتوي على عنصر واحد فقط، قم بتحويلها إلى قيمة واحدة
                if (count($values) === 1) {
                    $values = $values[0];
                } elseif (empty($values)) {
                    unset($customFields[$fieldName]);
                }
            } elseif (is_string($values)) {
                // إزالة علامات الاقتباس الزائدة
                $values = trim($values, '"\'');
            }
        }
        unset($values);

        return empty($customFields) ? null : $customFields;
    }

    /**
     * إنشاء slug فريد
     */
    private function generateSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug . '-' . time() . '-' . rand(1000, 9999);

        // التأكد من أن slug فريد
        while (Service::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . time() . '-' . rand(1000, 9999);
        }

        return $slug;
    }

    /**
     * التحقق من أن المستخدم هو مالك الخدمة
     */
    private function authorizeServiceOwner(int $userId, Service $service): void
    {
        if ($service->user_id !== $userId) {
            abort(403, 'لا يمكنك تعديل هذه الخدمة');
        }
    }
}
