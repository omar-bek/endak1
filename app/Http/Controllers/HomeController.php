<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;

class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية
     */
    public function index()
    {
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
    }

    /**
     * صفحة اتصل بنا
     */
    public function contact()
    {
        return view('contact');
    }
}
