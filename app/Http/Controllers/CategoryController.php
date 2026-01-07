<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CategoryController extends Controller
{
    /**
     * عرض جميع الأقسام
     */
    public function index()
    {
        try {
            $categories = Category::getMainCategories();

            return view('categories.index', compact('categories'));
        } catch (Exception $e) {
            Log::error('Error in CategoryController@index: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('home')->with('error', 'حدث خطأ أثناء تحميل الأقسام');
        }
    }

    /**
     * عرض قسم معين
     */
    public function show($slug, Request $request)
    {
        try {
            $category = Category::where('slug', $slug)
                ->where('is_active', true)
                ->with(['children', 'subCategories', 'services.user', 'fields' => function ($query) {
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
                $query->where(function ($q) use ($searchTerm) {
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('categories.index')->with('error', 'القسم غير موجود');
        } catch (Exception $e) {
            Log::error('Error in CategoryController@show: ' . $e->getMessage(), [
                'exception' => $e,
                'slug' => $slug
            ]);
            return redirect()->route('categories.index')->with('error', 'حدث خطأ أثناء تحميل القسم');
        }
    }

    /**
     * عرض الأقسام الفرعية
     */
    public function subcategories($parentSlug)
    {
        try {
            $parentCategory = Category::where('slug', $parentSlug)
                ->where('is_active', true)
                ->firstOrFail();

            $subcategories = $parentCategory->children()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            return view('categories.subcategories', compact('parentCategory', 'subcategories'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('categories.index')->with('error', 'القسم غير موجود');
        } catch (Exception $e) {
            Log::error('Error in CategoryController@subcategories: ' . $e->getMessage(), [
                'exception' => $e,
                'parent_slug' => $parentSlug
            ]);
            return redirect()->route('categories.index')->with('error', 'حدث خطأ أثناء تحميل الأقسام الفرعية');
        }
    }
}
