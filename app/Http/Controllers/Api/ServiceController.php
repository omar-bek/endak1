<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

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
            $data = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'price' => ['nullable', 'numeric', 'min:0'],
                'category_id' => ['required', 'exists:categories,id'],
                'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
                'city_id' => ['required', 'exists:cities,id'],
                'notes' => ['required', 'string', 'max:1000'],
                'voice_note' => ['nullable', 'string', 'max:16777215'],
                'custom_fields' => ['required', 'array'],
                'custom_fields.*' => ['required'],
            ]);

            // التحقق من الحقول المخصصة المطلوبة
            $category = \App\Models\Category::findOrFail($data['category_id']);
            $validationResult = $this->validateCustomFields($request, $category);
            if ($validationResult !== true) {
                return $validationResult;
            }

            $data['user_id'] = $request->user()->id;
            $data['is_active'] = true;
            $data['custom_fields'] = $data['custom_fields'] ?? [];

            $service = Service::create($data);

            Log::info('API Service created', [
                'service_id' => $service->id,
                'user_id' => $request->user()->id
            ]);

            return $this->success($service->fresh(), 'تم إنشاء الخدمة بنجاح', 201);
        }, 'حدث خطأ أثناء إنشاء الخدمة');
    }

    /**
     * التحقق من الحقول المخصصة المطلوبة
     */
    private function validateCustomFields(Request $request, \App\Models\Category $category)
    {
        // جلب جميع الحقول المخصصة النشطة (الآن جميع الحقول مطلوبة)
        $requiredFields = \App\Models\CategoryField::where('category_id', $category->id)
            ->where('is_active', true)
            ->get();

        $errors = [];
        $customFields = $request->custom_fields ?? [];

        foreach ($requiredFields as $field) {
            $fieldName = $field->name;
            $fieldValue = $customFields[$fieldName] ?? null;

            // التحقق من وجود القيمة
            if ($fieldValue === null || $fieldValue === '') {
                $fieldLabel = app()->getLocale() == 'ar' ? $field->name_ar : $field->name_en;
                $errors["custom_fields.{$fieldName}"] = "حقل '{$fieldLabel}' مطلوب";
                continue;
            }

            // التحقق حسب نوع الحقل
            switch ($field->type) {
                case 'image':
                    // للصور، يجب التحقق من وجود ملفات
                    if (is_array($fieldValue)) {
                        $hasFiles = false;
                        foreach ($fieldValue as $value) {
                            if (is_array($value)) {
                                // ملفات متعددة في repeatable groups
                                foreach ($value as $file) {
                                    if ($file && (is_object($file) && method_exists($file, 'isValid') && $file->isValid())) {
                                        $hasFiles = true;
                                        break 2;
                                    }
                                }
                            } elseif ($value !== null && $value !== '') {
                                $hasFiles = true;
                                break;
                            }
                        }
                        if (!$hasFiles) {
                            $fieldLabel = app()->getLocale() == 'ar' ? $field->name_ar : $field->name_en;
                            $errors["custom_fields.{$fieldName}"] = "يجب رفع صورة واحدة على الأقل لحقل '{$fieldLabel}'";
                        }
                    } else {
                        $fieldLabel = app()->getLocale() == 'ar' ? $field->name_ar : $field->name_en;
                        $errors["custom_fields.{$fieldName}"] = "حقل '{$fieldLabel}' مطلوب (يجب رفع صورة)";
                    }
                    break;

                case 'select':
                    // للقوائم المنسدلة، يجب التحقق من أن القيمة موجودة في الخيارات
                    if (is_array($fieldValue)) {
                        foreach ($fieldValue as $value) {
                            if ($value === null || $value === '') {
                                $fieldLabel = app()->getLocale() == 'ar' ? $field->name_ar : $field->name_en;
                                $errors["custom_fields.{$fieldName}"] = "حقل '{$fieldLabel}' مطلوب";
                                break;
                            }
                            if ($field->options && is_array($field->options) && !in_array($value, $field->options)) {
                                $fieldLabel = app()->getLocale() == 'ar' ? $field->name_ar : $field->name_en;
                                $errors["custom_fields.{$fieldName}"] = "القيمة المحددة لحقل '{$fieldLabel}' غير صحيحة";
                                break;
                            }
                        }
                    } else {
                        if ($field->options && is_array($field->options) && !in_array($fieldValue, $field->options)) {
                            $fieldLabel = app()->getLocale() == 'ar' ? $field->name_ar : $field->name_en;
                            $errors["custom_fields.{$fieldName}"] = "القيمة المحددة لحقل '{$fieldLabel}' غير صحيحة";
                        }
                    }
                    break;

                case 'number':
                    if (is_array($fieldValue)) {
                        foreach ($fieldValue as $value) {
                            if ($value !== null && $value !== '' && !is_numeric($value)) {
                                $fieldLabel = app()->getLocale() == 'ar' ? $field->name_ar : $field->name_en;
                                $errors["custom_fields.{$fieldName}"] = "حقل '{$fieldLabel}' يجب أن يكون رقماً";
                                break;
                            }
                        }
                    } elseif (!is_numeric($fieldValue) && $fieldValue !== null && $fieldValue !== '') {
                        $fieldLabel = app()->getLocale() == 'ar' ? $field->name_ar : $field->name_en;
                        $errors["custom_fields.{$fieldName}"] = "حقل '{$fieldLabel}' يجب أن يكون رقماً";
                    }
                    break;
            }
        }

        if (!empty($errors)) {
            return $this->error('خطأ في التحقق من البيانات', 422, $errors);
        }

        return true;
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

    private function authorizeServiceOwner(int $userId, Service $service): void
    {
        if ($service->user_id !== $userId) {
            abort(403, 'لا يمكنك تعديل هذه الخدمة');
        }
    }
}

