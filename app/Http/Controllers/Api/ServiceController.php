<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\CategoryField;
use App\Models\City;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ServiceController extends BaseApiController
{
    /**
     * عرض قائمة الخدمات
     */
    public function index(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            $services = Service::query()
                ->where('is_active', true)
                ->with(['category:id,name,slug', 'subCategory:id,name_ar,name_en', 'city:id,name_ar,name_en', 'user:id,name,avatar'])
                ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->integer('category_id')))
                ->when($request->filled('sub_category_id'), fn($q) => $q->where('sub_category_id', $request->integer('sub_category_id')))
                ->when($request->filled('city_id'), fn($q) => $q->where('city_id', $request->integer('city_id')))
                ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->integer('user_id')))
                ->when($request->filled('search'), function ($q) use ($request) {
                    $term = $request->get('search');
                    $q->where(
                        fn($inner) => $inner
                            ->where('title', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%")
                    );
                })
                ->latest()
                ->paginate($request->get('per_page', 12));

            return $this->success($services);
        }, 'حدث خطأ أثناء جلب الخدمات');
    }

    /**
     * عرض خدمة محددة
     */
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

    /**
     * إنشاء خدمة جديدة
     */
    public function store(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            // التحقق من الحقول في الجذر (root) - رفض الحقول غير المسموح بها
            $this->validateRootFields($request);

            $data = $this->validateServiceData($request);

            // التحقق الإضافي من الصور
            $this->validateImages($request);

            $data['user_id'] = $request->user()->id;
            $data['is_active'] = true;
            $data['price'] = $request->input('price', 0.00);

            // التحقق من أن المدينة متاحة للقسم المحدد
            $this->validateCityForCategory($data['city_id'], $data['category_id']);

            // التحقق من الحقول المخصصة (يجب أن يكون قبل المعالجة)
            $this->validateCustomFields($request, $data['category_id'], $data['sub_category_id'] ?? null);

            // معالجة وفلترة الحقول المخصصة (بعد التحقق)
            $processedFields = $this->processCustomFields($request);
            $data['custom_fields'] = $this->filterCustomFields(
                $processedFields,
                $data['category_id'],
                $data['sub_category_id'] ?? null
            );

            // إنشاء الخدمة
            $data['slug'] = $this->generateSlug($data['title']);
            $service = Service::create($data);

            Log::info('API Service created', [
                'service_id' => $service->id,
                'user_id' => $request->user()->id,
                'custom_fields' => $data['custom_fields'],
            ]);

            return $this->success($service->fresh(), 'تم إنشاء الخدمة بنجاح', 201);
        }, 'حدث خطأ أثناء إنشاء الخدمة');
    }

    /**
     * تحديث خدمة موجودة
     */
    public function update(Request $request, Service $service)
    {
        return $this->executeApiWithTryCatch(function () use ($request, $service) {
            $this->authorizeServiceOwner($request->user()->id, $service);

            $rules = [
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'string'],
                'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
                'category_id' => ['sometimes', 'exists:categories,id'],
                'sub_category_id' => ['sometimes', 'nullable', 'exists:sub_categories,id'],
                'city_id' => ['sometimes', 'exists:cities,id'],
                'custom_fields' => ['sometimes', 'array'],
                'is_active' => ['sometimes', 'boolean'],
            ];

            // إضافة قواعد validation للصور
            $imageRules = $this->getImageValidationRules($request, true);
            $rules = array_merge($rules, $imageRules);

            $data = $request->validate($rules);

            // التحقق الإضافي من الصور
            $this->validateImages($request);

            $categoryId = $data['category_id'] ?? $service->category_id;
            $subCategoryId = $data['sub_category_id'] ?? $service->sub_category_id;
            $cityId = $data['city_id'] ?? $service->city_id;

            // التحقق من أن المدينة متاحة للقسم المحدد (إذا تم تغيير القسم أو المدينة)
            if (isset($data['category_id']) || isset($data['city_id'])) {
                $this->validateCityForCategory($cityId, $categoryId);
            }

            // التحقق من الحقول المخصصة إذا كانت موجودة
            if ($request->has('custom_fields')) {
                $this->validateCustomFields($request, $categoryId, $subCategoryId);
                $data['custom_fields'] = $this->filterCustomFields(
                    $this->processCustomFields($request),
                    $categoryId,
                    $subCategoryId
                );
            }

            $service->update($data);

            Log::info('API Service updated', [
                'service_id' => $service->id,
                'user_id' => $request->user()->id,
            ]);

            return $this->success($service->fresh(), 'تم تحديث الخدمة بنجاح');
        }, 'حدث خطأ أثناء تحديث الخدمة');
    }

    /**
     * حذف خدمة
     */
    public function destroy(Request $request, Service $service)
    {
        return $this->executeApiWithTryCatch(function () use ($request, $service) {
            $this->authorizeServiceOwner($request->user()->id, $service);
            $service->delete();

            Log::info('API Service deleted', [
                'service_id' => $service->id,
                'user_id' => $request->user()->id,
            ]);

            return $this->success(null, 'تم حذف الخدمة بنجاح');
        }, 'حدث خطأ أثناء حذف الخدمة');
    }

    /**
     * عرض خدمات المستخدم
     */
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

    // ==================== Private Methods ====================

    /**
     * التحقق من بيانات الخدمة الأساسية
     */
    private function validateServiceData(Request $request): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*' => ['nullable'],
        ];

        // إضافة قواعد validation للصور
        $imageRules = $this->getImageValidationRules($request);
        $rules = array_merge($rules, $imageRules);

        return $request->validate($rules);
    }

    /**
     * معالجة الحقول المخصصة من الطلب
     */
    private function processCustomFields(Request $request): ?array
    {
        // جلب custom_fields مباشرة من الطلب
        $customFields = $request->input('custom_fields');

        Log::info('Processing custom fields', [
            'custom_fields_raw' => $customFields,
            'custom_fields_type' => gettype($customFields),
            'request_all' => $request->all(),
        ]);

        // إذا كانت null أو غير موجودة، حاول البحث في جميع المدخلات
        if ($customFields === null) {
            $customFields = $this->extractCustomFieldsFromRequest($request);
        }

        // تحويل JSON string إلى array
        if (is_string($customFields)) {
            $decoded = json_decode($customFields, true);
            $customFields = is_array($decoded) ? $decoded : [];
        }

        // التأكد من أن customFields هي array
        if (!is_array($customFields)) {
            $customFields = [];
        }

        $cleaned = $this->cleanCustomFields($customFields);

        Log::info('Processed custom fields result', [
            'cleaned' => $cleaned,
        ]);

        return $cleaned;
    }

    /**
     * استخراج الحقول المخصصة من الطلب
     */
    private function extractCustomFieldsFromRequest(Request $request): array
    {
        $customFields = [];

        foreach ($request->all() as $key => $value) {
            if (!str_starts_with($key, 'custom_fields[')) {
                continue;
            }

            if (!preg_match('/custom_fields\[([^\]]+)\](?:\[(\d+)\])?/', $key, $matches)) {
                continue;
            }

            $fieldName = $matches[1];
            $index = $matches[2] ?? null;

            if (!isset($customFields[$fieldName])) {
                $customFields[$fieldName] = [];
            }

            if ($value !== null && $value !== '') {
                $cleanValue = is_string($value) ? trim($value, '"\'') : $value;
                $customFields[$fieldName][$index ?? 'single'] = $cleanValue;
            }
        }

        // تحويل المصفوفات التي تحتوي على 'single' فقط
        foreach ($customFields as $key => $value) {
            if (isset($value['single'])) {
                $customFields[$key] = $value['single'];
            }
        }

        return $customFields;
    }

    /**
     * تنظيف الحقول المخصصة
     */
    private function cleanCustomFields(array $customFields): ?array
    {
        foreach ($customFields as $fieldName => &$values) {
            if (is_array($values)) {
                $values = array_values(array_filter($values, fn($v) => $v !== null && $v !== ''));
                if (count($values) === 1) {
                    $values = $values[0];
                } elseif (empty($values)) {
                    unset($customFields[$fieldName]);
                }
            } elseif (is_string($values)) {
                $values = trim($values, '"\'');
            }
        }
        unset($values);

        return empty($customFields) ? null : $customFields;
    }

    /**
     * فلترة الحقول المخصصة - فقط الحقول الموجودة في القسم
     */
    private function filterCustomFields(?array $customFields, int $categoryId, ?int $subCategoryId = null): ?array
    {
        if (empty($customFields) || !is_array($customFields)) {
            return null;
        }

        $allFields = $this->getCategoryFields($categoryId, $subCategoryId);

        if ($allFields->isEmpty()) {
            return null;
        }

        $filteredFields = [];
        foreach ($customFields as $fieldKey => $fieldValue) {
            $field = $this->findFieldByKey($allFields, $fieldKey);
            if ($field) {
                $filteredFields[$field->name] = $fieldValue;
            } else {
                Log::warning('Filtered out unknown field', [
                    'field_key' => $fieldKey,
                    'category_id' => $categoryId,
                ]);
            }
        }

        return !empty($filteredFields) ? $filteredFields : null;
    }

    /**
     * التحقق من الحقول المخصصة بناءً على الفئة
     */
    private function validateCustomFields(Request $request, int $categoryId, ?int $subCategoryId = null): void
    {
        // جلب الحقول مباشرة من الطلب
        $customFields = $request->input('custom_fields');

        // إذا كانت null، جرب json_decode للـ request body
        if ($customFields === null) {
            $jsonBody = $request->getContent();
            if (!empty($jsonBody)) {
                $decoded = json_decode($jsonBody, true);
                if (is_array($decoded) && isset($decoded['custom_fields'])) {
                    $customFields = $decoded['custom_fields'];
                }
            }
        }

        // تحويل JSON string إلى array إذا لزم الأمر
        if (is_string($customFields)) {
            $decoded = json_decode($customFields, true);
            $customFields = is_array($decoded) ? $decoded : [];
        }

        // التأكد من أن customFields هي array
        if (!is_array($customFields)) {
            $customFields = [];
        }

        $allFields = $this->getCategoryFields($categoryId, $subCategoryId);
        $requiredFields = $this->getRequiredFields($categoryId, $subCategoryId);

        $errors = [];

        // إذا لم توجد حقول معرفة للقسم، رفض جميع الحقول المرسلة
        if ($allFields->isEmpty()) {
            if (!empty($customFields)) {
                foreach ($customFields as $fieldKey => $fieldValue) {
                    $errors["custom_fields.{$fieldKey}"] = "حقل '{$fieldKey}' غير موجود في القسم (ID: {$categoryId}). لا توجد حقول معرفة لهذا القسم";
                }
                Log::warning('Fields sent but no fields defined for category', [
                    'category_id' => $categoryId,
                    'custom_fields_keys' => array_keys($customFields),
                ]);
            }
        } else {
            // إذا كان للقسم حقول معرفة، يجب إرسال custom_fields
            if (empty($customFields)) {
                $availableFieldsList = $allFields->pluck('name')->implode(', ');
                $errors['custom_fields'] = "يجب إدخال الحقول المخصصة للقسم (ID: {$categoryId}). الحقول المتاحة: {$availableFieldsList}";
            } else {
                // التحقق من أن جميع الحقول المتاحة موجودة في custom_fields
                foreach ($allFields as $field) {
                    // إذا كان الحقل من نوع image، تحقق من الملفات المرفوعة أولاً
                    if ($field->type === 'image' && $request->hasFile("custom_fields.{$field->name}")) {
                        $fieldValue = $request->file("custom_fields.{$field->name}");
                    } else {
                        $fieldValue = $this->getFieldValue($customFields, $field);
                    }

                    if ($this->isEmpty($fieldValue)) {
                        if ($field->is_required) {
                            $errors["custom_fields.{$field->name}"] = "حقل '{$field->name_ar}' مطلوب للقسم (ID: {$categoryId})";
                        }
                    } else {
                        $this->validateFieldType($field, $fieldValue, $errors);
                    }
                }

                // رفض الحقول غير الموجودة في القسم
                foreach ($customFields as $fieldKey => $fieldValue) {
                    $field = $this->findFieldByKey($allFields, $fieldKey);
                    if (!$field) {
                        // حقل غير موجود - استخدام الاسم المرسل في مفتاح الخطأ
                        $errors["custom_fields.{$fieldKey}"] = $this->getUnknownFieldErrorMessage($fieldKey, $categoryId, $allFields);
                    }
                    // ملاحظة: إذا كان الحقل موجوداً، فإن التحقق الأساسي أعلاه سيتعامل معه
                    // باستخدام الاسم الإنجليزي ($field->name) في مفتاح الخطأ
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * جلب جميع الحقول النشطة للفئة
     */
    private function getCategoryFields(int $categoryId, ?int $subCategoryId = null): Collection
    {
        return CategoryField::where('category_id', $categoryId)
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('sub_category_id')->orWhere('sub_category_id', $subCategoryId))
            ->get();
    }

    /**
     * جلب الحقول المطلوبة للفئة
     */
    private function getRequiredFields(int $categoryId, ?int $subCategoryId = null): Collection
    {
        return CategoryField::where('category_id', $categoryId)
            ->where('is_active', true)
            ->where('is_required', true)
            ->where(fn($q) => $q->whereNull('sub_category_id')->orWhere('sub_category_id', $subCategoryId))
            ->get();
    }

    /**
     * البحث عن حقل باستخدام المفتاح
     */
    private function findFieldByKey(Collection $fields, string $fieldKey): ?CategoryField
    {
        return $fields->first(
            fn($field) =>
            $field->name === $fieldKey
                || $field->name_ar === $fieldKey
                || $field->name_en === $fieldKey
        );
    }

    /**
     * الحصول على قيمة الحقل من custom_fields
     */
    private function getFieldValue(array $customFields, CategoryField $field)
    {
        return $customFields[$field->name]
            ?? $customFields[$field->name_ar]
            ?? $customFields[$field->name_en]
            ?? null;
    }

    /**
     * التحقق من الحقل المطلوب
     */
    private function validateRequiredField(CategoryField $field, $fieldValue, int $categoryId, array &$errors): void
    {
        if ($this->isEmpty($fieldValue)) {
            $errors["custom_fields.{$field->name}"] = "حقل '{$field->name_ar}' مطلوب للقسم (ID: {$categoryId})";
        }
    }

    /**
     * التحقق من نوع الحقل
     */
    private function validateFieldType(CategoryField $field, $fieldValue, array &$errors): void
    {
        if ($this->isEmpty($fieldValue)) {
            return;
        }

        $error = $this->validateFieldValue($field, $fieldValue);
        if ($error) {
            $errors["custom_fields.{$field->name}"] = $error;
        }
    }

    /**
     * التحقق من قيمة حقل بناءً على نوعه
     */
    private function validateFieldValue(CategoryField $field, $value): ?string
    {
        if ($field->is_repeatable && is_array($value)) {
            foreach ($value as $index => $singleValue) {
                $error = $this->validateSingleFieldValue($field, $singleValue);
                if ($error) {
                    return "{$field->name_ar} (المجموعة " . ($index + 1) . "): {$error}";
                }
            }
            return null;
        }

        return $this->validateSingleFieldValue($field, $value);
    }

    /**
     * التحقق من قيمة حقل واحدة
     */
    private function validateSingleFieldValue(CategoryField $field, $value): ?string
    {
        if ($this->isEmpty($value)) {
            return null;
        }

        return match ($field->type) {
            'number' => !is_numeric($value) ? "حقل {$field->name_ar} يجب أن يكون رقماً" : null,
            'select' => $this->validateSelectField($field, $value),
            'checkbox' => $this->validateCheckboxField($field, $value),
            'date' => $this->validateDateField($field, $value),
            'time' => $this->validateTimeField($field, $value),
            'image' => $this->validateImageFieldValue($field, $value),
            'text', 'textarea', 'title' => !is_string($value) ? "حقل {$field->name_ar} يجب أن يكون نصاً" : null,
            default => null,
        };
    }

    /**
     * التحقق من حقل select
     */
    private function validateSelectField(CategoryField $field, $value): ?string
    {
        if ($field->options && is_array($field->options) && !in_array($value, $field->options)) {
            return "قيمة حقل {$field->name_ar} غير صحيحة. يجب أن تكون واحدة من: " . implode(', ', $field->options);
        }
        return null;
    }

    /**
     * التحقق من حقل checkbox
     */
    private function validateCheckboxField(CategoryField $field, $value): ?string
    {
        $validValues = [true, false, 0, 1, '0', '1', 'true', 'false', 'on', 'off'];
        if (!in_array($value, $validValues, true)) {
            return "حقل {$field->name_ar} يجب أن يكون true أو false";
        }
        return null;
    }

    /**
     * التحقق من حقل date
     */
    private function validateDateField(CategoryField $field, $value): ?string
    {
        try {
            $date = \Carbon\Carbon::parse($value);
            if (!$date || $date->format('Y-m-d') !== $value) {
                return "حقل {$field->name_ar} يجب أن يكون تاريخاً بصيغة Y-m-d";
            }
        } catch (\Exception $e) {
            return "حقل {$field->name_ar} يجب أن يكون تاريخاً بصيغة Y-m-d";
        }
        return null;
    }

    /**
     * التحقق من حقل time
     */
    private function validateTimeField(CategoryField $field, $value): ?string
    {
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:([0-5][0-9]))?$/', $value)) {
            return "حقل {$field->name_ar} يجب أن يكون وقتاً بصيغة H:i أو H:i:s";
        }
        return null;
    }

    /**
     * التحقق من قيمة حقل الصورة في custom_fields
     */
    private function validateImageFieldValue(CategoryField $field, $value): ?string
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // إذا كانت القيمة ملف مرفوع
        if (is_object($value) && method_exists($value, 'isValid')) {
            if (!$value->isValid()) {
                return "حقل {$field->name_ar}: الصورة المرفوعة غير صالحة";
            }

            // التحقق من نوع الملف
            $mimeType = $value->getMimeType();
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                return "حقل {$field->name_ar}: نوع الصورة غير مدعوم. يجب أن يكون: " . implode(', ', $allowedExtensions);
            }

            // التحقق من حجم الملف
            if ($value->getSize() > $maxSize) {
                return "حقل {$field->name_ar}: حجم الصورة كبير جداً. الحد الأقصى هو 5MB";
            }

            return null;
        }

        // إذا كانت القيمة string (اسم ملف، URL، أو base64)
        if (!is_string($value)) {
            return "حقل {$field->name_ar} يجب أن يكون صورة (ملف أو اسم ملف أو URL)";
        }

        // التحقق من base64
        if (preg_match('/^data:image\/(jpeg|jpg|png|gif|webp);base64,/', $value)) {
            // استخراج البيانات من base64
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $value);
            $decoded = base64_decode($base64Data, true);

            if ($decoded === false) {
                return "حقل {$field->name_ar}: بيانات base64 غير صالحة";
            }

            // التحقق من حجم البيانات
            if (strlen($decoded) > $maxSize) {
                return "حقل {$field->name_ar}: حجم الصورة كبير جداً. الحد الأقصى هو 5MB";
            }

            // التحقق من نوع الصورة من البيانات
            $imageInfo = @getimagesizefromstring($decoded);
            if ($imageInfo === false) {
                return "حقل {$field->name_ar}: البيانات المرسلة ليست صورة صالحة";
            }

            $mimeType = $imageInfo['mime'];
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                return "حقل {$field->name_ar}: نوع الصورة غير مدعوم. يجب أن يكون: " . implode(', ', $allowedExtensions);
            }

            return null;
        }

        // التحقق من URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            // التحقق من أن URL ينتهي بامتداد صورة صالح
            $urlPath = parse_url($value, PHP_URL_PATH);
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions)) {
                return "حقل {$field->name_ar}: رابط الصورة يجب أن ينتهي بامتداد صورة صالح: " . implode(', ', $allowedExtensions);
            }

            return null;
        }

        // التحقق من اسم الملف (امتداد فقط)
        $extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return "حقل {$field->name_ar}: اسم الملف يجب أن ينتهي بامتداد صورة صالح: " . implode(', ', $allowedExtensions);
        }

        return null;
    }

    /**
     * التحقق من أن القيمة فارغة
     */
    private function isEmpty($value): bool
    {
        return $value === null
            || $value === ''
            || (is_array($value) && empty($value))
            || (is_string($value) && trim($value) === '');
    }

    /**
     * الحصول على رسالة خطأ للحقل غير الموجود
     */
    private function getUnknownFieldErrorMessage(string $fieldKey, int $categoryId, Collection $allFields): string
    {
        // استخدام الأسماء الإنجليزية في قائمة الحقول المتاحة
        $availableFieldsList = $allFields->pluck('name')->implode(', ');
        $availableFieldsCount = $allFields->count();

        if ($availableFieldsCount > 0) {
            return "حقل '{$fieldKey}' غير موجود في القسم (ID: {$categoryId}). الحقول المتاحة لهذا القسم ({$availableFieldsCount}): {$availableFieldsList}";
        }

        return "حقل '{$fieldKey}' غير موجود في القسم (ID: {$categoryId}). لا توجد حقول معرفة لهذا القسم";
    }

    /**
     * إنشاء slug فريد
     */
    private function generateSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug . '-' . time() . '-' . rand(1000, 9999);

        while (Service::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . time() . '-' . rand(1000, 9999);
        }

        return $slug;
    }

    /**
     * التحقق من الحقول في الجذر (root) - رفض الحقول غير المسموح بها
     */
    private function validateRootFields(Request $request): void
    {
        // الحقول المسموح بها في الجذر
        $allowedFields = [
            'title',
            'description',
            'price',
            'category_id',
            'sub_category_id',
            'city_id',
            'custom_fields',
            'voice_note',
            'image',
            'location',
            'contact_phone',
            'contact_email',
        ];

        $requestData = $request->all();
        $errors = [];

        // التحقق من كل حقل في الطلب
        foreach ($requestData as $fieldKey => $fieldValue) {
            // تجاهل الحقول المسموح بها
            if (in_array($fieldKey, $allowedFields)) {
                continue;
            }

            // رفض أي حقل غير مسموح به
            $errors[$fieldKey] = "حقل '{$fieldKey}' غير مسموح به. الحقول المسموح بها: " . implode(', ', $allowedFields);
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
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

    /**
     * التحقق من أن المدينة متاحة للقسم المحدد
     */
    private function validateCityForCategory(int $cityId, int $categoryId): void
    {
        // التحقق من وجود القسم والمدينة
        $category = Category::find($categoryId);
        $city = City::find($cityId);

        if (!$category) {
            throw ValidationException::withMessages([
                'category_id' => "القسم المحدد (ID: {$categoryId}) غير موجود"
            ]);
        }

        // التحقق من أن القسم نشط
        if (!$category->is_active) {
            throw ValidationException::withMessages([
                'category_id' => "القسم '{$category->name}' غير نشط حالياً"
            ]);
        }

        if (!$city) {
            throw ValidationException::withMessages([
                'city_id' => "المدينة المحددة (ID: {$cityId}) غير موجودة"
            ]);
        }

        // التحقق من أن المدينة نشطة
        if (!$city->is_active) {
            throw ValidationException::withMessages([
                'city_id' => "المدينة '{$city->name_ar}' غير نشطة حالياً"
            ]);
        }

        // التحقق من وجود العلاقة في جدول category_cities
        $categoryCity = DB::table('category_cities')
            ->where('category_id', $categoryId)
            ->where('city_id', $cityId)
            ->first();

        if (!$categoryCity) {
            // جلب المدن المتاحة للقسم لعرضها في رسالة الخطأ
            $availableCities = Category::find($categoryId)
                ->activeCities()
                ->pluck('name_ar')
                ->toArray();

            $availableCitiesList = !empty($availableCities)
                ? implode('، ', $availableCities)
                : 'لا توجد مدن متاحة لهذا القسم';

            throw ValidationException::withMessages([
                'city_id' => "المدينة '{$city->name_ar}' غير متاحة للقسم '{$category->name}'. المدن المتاحة لهذا القسم: {$availableCitiesList}"
            ]);
        }

        // التحقق من أن العلاقة نشطة
        if (!$categoryCity->is_active) {
            throw ValidationException::withMessages([
                'city_id' => "المدينة '{$city->name_ar}' معطلة للقسم '{$category->name}' حالياً"
            ]);
        }
    }

    /**
     * الحصول على قواعد validation للصور
     *
     * @param Request $request
     * @param bool $isUpdate
     * @return array
     */
    private function getImageValidationRules(Request $request, bool $isUpdate = false): array
    {
        $rules = [];
        $prefix = $isUpdate ? 'sometimes|' : '';

        // التحقق من الصورة الرئيسية (image)
        if ($request->hasFile('image')) {
            $rules['image'] = $prefix . 'image|mimes:jpeg,jpg,png,gif,webp|max:5120';
        } elseif ($request->has('image')) {
            // إذا كانت الصورة كـ base64 أو URL
            $rules['image'] = $prefix . 'string|max:10000';
        }

        // التحقق من الصور المتعددة (images)
        if ($request->hasFile('images')) {
            $rules['images'] = 'array|max:10';
            $rules['images.*'] = 'image|mimes:jpeg,jpg,png,gif,webp|max:5120';
        } elseif ($request->has('images') && is_array($request->input('images'))) {
            $rules['images'] = 'array|max:10';
            $rules['images.*'] = 'string|max:10000';
        }

        return $rules;
    }

    /**
     * التحقق من الصور يدوياً (للتحقق الإضافي)
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    private function validateImages(Request $request): void
    {
        $errors = [];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // التحقق من الصورة الرئيسية
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if (!$image->isValid()) {
                $errors['image'] = 'الصورة الرئيسية غير صالحة';
            } else {
                // التحقق من نوع الملف
                $mimeType = $image->getMimeType();
                if (!in_array($mimeType, $allowedMimes)) {
                    $errors['image'] = 'نوع الصورة الرئيسية غير مدعوم. يجب أن يكون: ' . implode(', ', $allowedExtensions);
                }

                // التحقق من حجم الملف
                if ($image->getSize() > $maxSize) {
                    $errors['image'] = 'حجم الصورة الرئيسية كبير جداً. الحد الأقصى هو 5MB';
                }
            }
        }

        // التحقق من الصور المتعددة
        if ($request->hasFile('images')) {
            $images = $request->file('images');

            if (count($images) > 10) {
                $errors['images'] = 'يمكن رفع 10 صور كحد أقصى';
            }

            foreach ($images as $index => $image) {
                if (!$image->isValid()) {
                    $errors["images.{$index}"] = "الصورة رقم " . ($index + 1) . " غير صالحة";
                    continue;
                }

                // التحقق من نوع الملف
                $mimeType = $image->getMimeType();
                if (!in_array($mimeType, $allowedMimes)) {
                    $errors["images.{$index}"] = "نوع الصورة رقم " . ($index + 1) . " غير مدعوم. يجب أن يكون: " . implode(', ', $allowedExtensions);
                }

                // التحقق من حجم الملف
                if ($image->getSize() > $maxSize) {
                    $errors["images.{$index}"] = "حجم الصورة رقم " . ($index + 1) . " كبير جداً. الحد الأقصى هو 5MB";
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
