<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Route model binding للأقسام
        \Illuminate\Support\Facades\Route::bind('category', function ($value, $route) {
            // في لوحة الإدارة، نستخدم id دائماً
            if ($route->named('admin.*') || request()->is('admin/*')) {
                // محاولة البحث باستخدام id أولاً
                $category = \App\Models\Category::find($value);
                if ($category) {
                    return $category;
                }
                // إذا لم نجد باستخدام id، نحاول slug
                return \App\Models\Category::where('slug', $value)->firstOrFail();
            }

            // في API routes، نستخدم id إذا كان رقم، وإلا slug
            if (request()->is('api/*')) {
                if (is_numeric($value)) {
                    return \App\Models\Category::findOrFail($value);
                }
                return \App\Models\Category::where('slug', $value)->firstOrFail();
            }

            // في الصفحات العامة، نستخدم slug (السلوك الافتراضي)
            return \App\Models\Category::where('slug', $value)
                ->orWhere('id', $value)
                ->firstOrFail();
        });
    }
}
