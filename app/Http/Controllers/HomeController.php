<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Exception;

class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية
     */
    public function index()
    {
        try {
            // الأقسام الرئيسية
            $categories = Category::getMainCategories();

            // الخدمات المميزة
            $featuredServices = Service::getFeaturedServices(6);

            // أحدث الخدمات
            $latestServices = Service::where('is_active', true)
                                    ->with(['category', 'user'])
                                    ->latest()
                                    ->limit(8)
                                    ->get();

            return view('home', compact('categories', 'featuredServices', 'latestServices'));
        } catch (Exception $e) {
            Log::error('Error in HomeController@index: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return view('home', [
                'categories' => collect(),
                'featuredServices' => collect(),
                'latestServices' => collect()
            ])->with('error', 'حدث خطأ أثناء تحميل الصفحة الرئيسية');
        }
    }

    /**
     * صفحة اتصل بنا
     */
    public function contact()
    {
        try {
            return view('contact');
        } catch (Exception $e) {
            Log::error('Error in HomeController@contact: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('home')->with('error', 'حدث خطأ أثناء تحميل الصفحة');
        }
    }
}
