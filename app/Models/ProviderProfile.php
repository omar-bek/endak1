<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'phone',
        'address',
        'is_verified',
        'is_active',
        'max_categories',
        'max_cities',
        'working_hours',
        'rating',
        'completed_services'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'working_hours' => 'array',
        'rating' => 'decimal:2'
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع الأقسام
    public function categories()
    {
        return $this->hasMany(ProviderCategory::class, 'user_id', 'user_id');
    }

    // العلاقة مع المدن
    public function cities()
    {
        return $this->hasMany(ProviderCity::class, 'user_id', 'user_id');
    }

    // الحصول على الأقسام النشطة
    public function activeCategories()
    {
        return $this->categories()->where('is_active', true);
    }

    // الحصول على المدن النشطة
    public function activeCities()
    {
        return $this->cities()->where('is_active', true);
    }

    // التحقق من إمكانية إضافة قسم جديد
    public function canAddCategory()
    {
        return $this->activeCategories()->count() < $this->max_categories;
    }

    // التحقق من إمكانية إضافة مدينة جديدة
    public function canAddCity()
    {
        return $this->activeCities()->count() < $this->max_cities;
    }

    // الحصول على عدد الأقسام المتبقية
    public function getRemainingCategoriesAttribute()
    {
        return $this->max_categories - $this->activeCategories()->count();
    }

    // الحصول على جميع التقييمات للمزود
    public function getRatings()
    {
        return \App\Models\ServiceOffer::where('provider_id', $this->user_id)
            ->whereNotNull('rating')
            ->where('status', 'delivered')
            ->with(['service.user', 'service.category'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // حساب متوسط التقييمات
    public function calculateAverageRating()
    {
        $ratings = \App\Models\ServiceOffer::where('provider_id', $this->user_id)
            ->whereNotNull('rating')
            ->where('status', 'delivered')
            ->pluck('rating');

        if ($ratings->count() > 0) {
            return round($ratings->avg(), 2);
        }

        return 0;
    }

    // تحديث التقييم تلقائياً
    public function updateRating()
    {
        $averageRating = $this->calculateAverageRating();
        $completedServices = \App\Models\ServiceOffer::where('provider_id', $this->user_id)
            ->whereNotNull('rating')
            ->where('status', 'delivered')
            ->count();

        $this->update([
            'rating' => $averageRating,
            'completed_services' => $completedServices
        ]);
    }

    // الحصول على عدد المدن المتبقية
    public function getRemainingCitiesAttribute()
    {
        return $this->max_cities - $this->activeCities()->count();
    }

    // التحقق من اكتمال الملف الشخصي
    public function isProfileComplete()
    {
        return $this->bio &&
               $this->phone &&
               $this->address &&
               $this->activeCategories()->count() > 0 &&
               $this->activeCities()->count() > 0;
    }
}
