<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * عرض جميع الأقسام
     */
    public function index()
    {
        $categories = Category::getMainCategories();

        return view('categories.index', compact('categories'));
    }

    /**
     * عرض قسم معين
     */
    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)
                           ->where('is_active', true)
                           ->with(['children', 'subCategories', 'services.user', 'fields' => function($query) {
                               $query->where('is_active', true)->orderBy('sort_order', 'asc');
                           }])
                           ->firstOrFail();

        // جلب الخدمات حسب القسم والقسم الفرعي إذا كان محدداً
        $query = Service::where('category_id', $category->id)
                       ->where('is_active', true)
                       ->with(['user', 'category', 'subCategory', 'city'])
                       ->orderBy('created_at', 'desc');

        // إذا كان هناك قسم فرعي محدد، فلنعرض فقط الخدمات التابعة له
        if ($request->has('sub_category_id') && $request->sub_category_id) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // البحث في الخدمات إذا كان موجوداً
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $services = $query->latest()->paginate(12);

        // جلب القسم الفرعي المحدد إذا كان موجوداً
        $selectedSubCategory = null;
        if ($request->has('sub_category_id') && $request->sub_category_id) {
            $selectedSubCategory = $category->subCategories()
                                          ->where('id', $request->sub_category_id)
                                          ->where('status', true)
                                          ->first();
        }

        return view('categories.show', compact('category', 'services', 'selectedSubCategory'));
    }

    /**
     * عرض الأقسام الفرعية
     */
    public function subcategories($parentSlug)
    {
        $parentCategory = Category::where('slug', $parentSlug)
                                 ->where('is_active', true)
                                 ->firstOrFail();

        $subcategories = $parentCategory->children()
                                       ->where('is_active', true)
                                       ->orderBy('sort_order')
                                       ->get();

        return view('categories.subcategories', compact('parentCategory', 'subcategories'));
    }
}
