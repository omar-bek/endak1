<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Http\Request;

class CategoryFieldController extends BaseApiController
{
    /**
     * جلب جميع الحقول النشطة لقسم معين
     * GET /api/v1/categories/{category}/fields
     */
    public function index($categoryId, Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($categoryId, $request) {
            $category = Category::where('id', $categoryId)
                ->where('is_active', true)
                ->firstOrFail();

            $query = CategoryField::where('category_id', $category->id)
                ->where('is_active', true);

            // إذا كان هناك قسم فرعي محدد
            if ($request->filled('sub_category_id')) {
                $query->where(function ($q) use ($request) {
                    $q->where('sub_category_id', $request->integer('sub_category_id'))
                        ->orWhereNull('sub_category_id');
                });
            }

            $fields = $query->orderBy('sort_order')->get();

            return $this->success([
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'name_ar' => $category->name,
                    'name_en' => $category->name_en,
                    'slug' => $category->slug,
                ],
                'fields' => $fields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'name' => $field->name,
                        'name_ar' => $field->name_ar,
                        'name_en' => $field->name_en,
                        'type' => $field->type,
                        'value' => $field->value,
                        'options' => $field->options,
                        'input_group' => $field->input_group,
                        'is_required' => $field->is_required,
                        'is_repeatable' => $field->is_repeatable,
                        'description' => $field->description,
                        'sort_order' => $field->sort_order,
                        'sub_category_id' => $field->sub_category_id,
                    ];
                }),
            ]);
        }, 'حدث خطأ أثناء جلب الحقول');
    }

    /**
     * جلب حقل معين
     * GET /api/v1/categories/{category}/fields/{field}
     */
    public function show($categoryId, $fieldId)
    {
        return $this->executeApiWithTryCatch(function () use ($categoryId, $fieldId) {
            $category = Category::where('id', $categoryId)
                ->where('is_active', true)
                ->firstOrFail();

            $field = CategoryField::where('id', $fieldId)
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->firstOrFail();

            return $this->success([
                'field' => [
                    'id' => $field->id,
                    'name' => $field->name,
                    'name_ar' => $field->name_ar,
                    'name_en' => $field->name_en,
                    'type' => $field->type,
                    'value' => $field->value,
                    'options' => $field->options,
                    'input_group' => $field->input_group,
                    'is_required' => $field->is_required,
                    'is_repeatable' => $field->is_repeatable,
                    'description' => $field->description,
                    'sort_order' => $field->sort_order,
                    'sub_category_id' => $field->sub_category_id,
                ],
            ]);
        }, 'حدث خطأ أثناء جلب الحقل');
    }

    /**
     * جلب الحقول المجمعة حسب input_group
     * GET /api/v1/categories/{category}/fields/grouped
     */
    public function grouped($categoryId, Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($categoryId, $request) {
            $category = Category::where('id', $categoryId)
                ->where('is_active', true)
                ->firstOrFail();

            $query = CategoryField::where('category_id', $category->id)
                ->where('is_active', true);

            // إذا كان هناك قسم فرعي محدد
            if ($request->filled('sub_category_id')) {
                $query->where(function ($q) use ($request) {
                    $q->where('sub_category_id', $request->integer('sub_category_id'))
                        ->orWhereNull('sub_category_id');
                });
            }

            $fields = $query->orderBy('sort_order')->get();

            // تجميع الحقول حسب input_group
            $grouped = [];
            foreach ($fields as $field) {
                $group = $field->input_group ?: 'default';
                if (!isset($grouped[$group])) {
                    $grouped[$group] = [];
                }
                $grouped[$group][] = [
                    'id' => $field->id,
                    'name' => $field->name,
                    'name_ar' => $field->name_ar,
                    'name_en' => $field->name_en,
                    'type' => $field->type,
                    'value' => $field->value,
                    'options' => $field->options,
                    'is_required' => $field->is_required,
                    'is_repeatable' => $field->is_repeatable,
                    'description' => $field->description,
                    'sort_order' => $field->sort_order,
                    'sub_category_id' => $field->sub_category_id,
                ];
            }

            return $this->success([
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'name_ar' => $category->name,
                    'name_en' => $category->name_en,
                    'slug' => $category->slug,
                ],
                'grouped_fields' => $grouped,
            ]);
        }, 'حدث خطأ أثناء جلب الحقول المجمعة');
    }
}

