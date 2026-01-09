<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Service;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CategoryController extends BaseApiController
{
    public function index()
    {
        return $this->executeApiWithTryCatch(function () {
            $categories = Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with([
                    'children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
                ])
                ->withCount('services')
                ->orderBy('sort_order')
                ->get();

            return $this->success($categories);
        }, 'حدث خطأ أثناء جلب الأقسام');
    }

    public function show(string $slug, Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($slug, $request) {
            $category = Category::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->with(['subCategories' => fn ($query) => $query->where('status', true)])
                ->firstOrFail();

            $servicesQuery = Service::query()
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->with(['user:id,name,avatar', 'city:id,name_ar,name_en']);

            if ($request->filled('sub_category_id')) {
                $servicesQuery->where('sub_category_id', $request->integer('sub_category_id'));
            }

            if ($request->filled('search')) {
                $term = $request->get('search');
                $servicesQuery->where(function ($query) use ($term) {
                    $query->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            }

            if ($request->filled('city_id')) {
                $servicesQuery->where('city_id', $request->integer('city_id'));
            }

            $services = $servicesQuery->paginate($request->get('per_page', 12));

            return $this->success([
                'category' => $category,
                'services' => $services,
            ]);
        }, 'حدث خطأ أثناء جلب القسم');
    }

    public function subcategories($category)
    {
        return $this->executeApiWithTryCatch(function () use ($category) {
            // يمكن أن يكون category ID أو slug
            $categoryModel = is_numeric($category) 
                ? Category::query()->where('id', $category)->where('is_active', true)->firstOrFail()
                : Category::query()->where('slug', $category)->where('is_active', true)->firstOrFail();

            $subcategories = SubCategory::query()
                ->where('category_id', $categoryModel->id)
                ->where('status', true)
                ->orderBy('name_ar')
                ->get(['id', 'name_ar', 'name_en', 'description_ar', 'description_en', 'category_id', 'image', 'slug']);

            return $this->success([
                'category' => [
                    'id' => $categoryModel->id,
                    'name_ar' => $categoryModel->name_ar ?? $categoryModel->name,
                    'name_en' => $categoryModel->name_en,
                    'slug' => $categoryModel->slug,
                ],
                'subcategories' => $subcategories
            ]);
        }, 'حدث خطأ أثناء جلب الأقسام الفرعية');
    }
}

