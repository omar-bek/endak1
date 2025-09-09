@extends('layouts.app')

@section('title', 'الخدمات')

<style>
.voice-note-mini {
    display: flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    background: #f8f9fa;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.voice-note-mini i {
    font-size: 0.8rem;
}

.voice-note-mini small {
    font-size: 0.75rem;
}
</style>

@section('content')
<!-- Header Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center">
            <h1 class="fw-bold">
                @auth
                    @if(auth()->user()->isProvider())
                        جميع الخدمات المتاحة
                    @else
                        خدماتي المطلوبة
                    @endif
                @else
                    الخدمات
                @endauth
            </h1>
            <p class="text-muted">
                @auth
                    @if(auth()->user()->isProvider())
                        اكتشف جميع الخدمات المتاحة وقدم عروضك
                    @else
                        عرض جميع الخدمات التي طلبتها
                    @endif
                @else
                    اكتشف مجموعة واسعة من الخدمات المميزة
                @endauth
            </p>
        </div>
    </div>
</section>

<!-- Search and Filter Section -->
<section class="py-4">
    <div class="container">
        @auth
            @if(!auth()->user()->isProvider())
                <div class="alert alert-info text-center mb-4">
                    <i class="fas fa-info-circle"></i>
                    <strong>مرحباً!</strong> أنت تتصفح خدماتك المطلوبة فقط.
                    <div class="mt-2">
                        <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-th-large"></i> تصفح الأقسام
                        </a>
                        <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list"></i> جميع خدماتي
                        </a>
                    </div>
                </div>
            @else
                <div class="alert alert-success text-center mb-4">
                    <i class="fas fa-handshake"></i>
                    <strong>مرحباً مزود الخدمة!</strong> يمكنك تصفح جميع الخدمات المتاحة وتقديم عروضك.
                </div>
            @endif
        @endauth
        <form method="GET" class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control"
                       placeholder="ابحث في الخدمات..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="category" id="category" class="form-select">
                    <option value="">جميع الأقسام</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ app()->getLocale() == 'ar' ? $category->name : $category->name_en }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="sub_category" id="sub_category" class="form-select" {{ empty(request('category')) ? 'disabled' : '' }}>
                    <option value="">جميع الأقسام الفرعية</option>
                    @if(isset($subCategories) && $subCategories->count() > 0)
                        @foreach($subCategories as $subCategory)
                        <option value="{{ $subCategory->id }}"
                                {{ request('sub_category') == $subCategory->id ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'ar' ? $subCategory->name_ar : $subCategory->name_en }}
                        </option>
                        @endforeach
                    @endif
                </select>
            </div>
            @auth
                @if(auth()->user()->isProvider())
                <div class="col-md-2">
                    <select name="city" class="form-select">
                        <option value="">جميع المدن</option>
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}"
                                {{ request('city') == $city->id ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'ar' ? $city->name : $city->name_en }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
                @else
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث في خدماتي
                    </button>
                </div>
                @endif
            @else
            <div class="col-md-2">
                <select name="city" class="form-select">
                    <option value="">جميع المدن</option>
                    @foreach($cities as $city)
                    <option value="{{ $city->id }}"
                            {{ request('city') == $city->id ? 'selected' : '' }}>
                        {{ app()->getLocale() =='ar' ? $city->name : $city->name_en }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> بحث
                </button>
            </div>
            @endauth
        </form>
    </div>
</section>

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        @if($services->count() > 0)
        <div class="row">
            @foreach($services as $service)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $service->image_url }}" class="card-img-top" alt="{{ $service->title }}"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($service->description, 100) }}</p>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-primary fw-bold">{{ $service->formatted_price }}</span>
                            <div class="text-end">
                                <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? $service->category->name : $service->category->name_en }}</small>
                                @if($service->subCategory)
                                    <small class="text-info d-block">
                                        <i class="fas fa-layer-group me-1"></i>
                                        {{ app()->getLocale() == 'ar' ? $service->subCategory->name_ar : $service->subCategory->name_en }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        @if($service->city)
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ app()->getLocale() == 'ar' ? $service->city->name : $service->city->name_en }}
                            </small>
                        </div>
                        @endif

                        <!-- عرض تسجيل صوتي مصغر -->
                        @if($service->voice_note)
                        <div class="mb-3">
                            <div class="voice-note-mini">
                                <i class="fas fa-microphone text-primary me-1"></i>
                                <small class="text-muted">تسجيل صوتي متاح</small>
                            </div>
                        </div>
                        @endif

                        @if($service->average_rating > 0)
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $service->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted ms-2">({{ $service->ratings_count }})</small>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            @auth
                                @if(auth()->user()->isProvider())
                                    <small class="text-muted">بواسطة: {{ $service->user->name }}</small>
                                @else
                                    <small class="text-muted">تاريخ الطلب: {{ $service->created_at->format('Y-m-d') }}</small>
                                @endif
                            @else
                                <small class="text-muted">بواسطة: {{ $service->user->name }}</small>
                            @endauth
                            @auth
                                @if(auth()->user()->isProvider())
                                    @if($service->is_featured)
                                    <span class="badge bg-warning">مميز</span>
                                    @endif
                                @endif
                            @else
                                @if($service->is_featured)
                                <span class="badge bg-warning">مميز</span>
                                @endif
                            @endauth
                        </div>

                        <!-- عرض حالة العرض للمزود -->
                        @auth
                            @if(auth()->user()->isProvider())
                                @php
                                    $userOffer = $service->offers->first();

                                    // التحقق من إمكانية مزود الخدمة تقديم عرض
                                    $canOffer = true;
                                    $profile = auth()->user()->providerProfile;
                                    $offerReason = '';

                                    if ($profile) {
                                        // التحقق من أن القسم متطابق مع اختيارات مزود الخدمة
                                        $providerCategoryIds = $profile->activeCategories()->pluck('category_id')->toArray();
                                        if (!in_array($service->category_id, $providerCategoryIds)) {
                                            $canOffer = false;
                                            $offerReason = 'القسم غير متطابق مع اختياراتك';
                                        }

                                        // التحقق من أن المدن متطابقة مع اختيارات مزود الخدمة
                                        if ($canOffer && $service->city_id) {
                                            $providerCityIds = $profile->activeCities()->pluck('city_id')->toArray();
                                            if (!in_array($service->city_id, $providerCityIds)) {
                                                $canOffer = false;
                                                $offerReason = 'المدينة غير متطابقة مع اختياراتك';
                                            }
                                        }
                                    } else {
                                        $canOffer = false;
                                        $offerReason = 'يجب إكمال الملف الشخصي أولاً';
                                    }
                                @endphp
                                @if($userOffer)
                                    <div class="mt-3 p-2 bg-success bg-opacity-10 border border-success rounded">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <span class="text-success fw-bold">تم تقديم العرض</span>
                                            </div>
                                            <span class="badge bg-success">{{ $userOffer->formatted_price }}</span>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                تم التقديم: {{ $userOffer->created_at ? $userOffer->created_at->diffForHumans() : 'غير محدد' }}
                                            </small>
                                        </div>
                                        @if($userOffer->status !== 'pending')
                                            <div class="mt-1">
                                                <span class="badge bg-{{ $userOffer->status === 'accepted' ? 'success' : ($userOffer->status === 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ $userOffer->status_label }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @elseif(!$canOffer)
                                    <div class="mt-3 p-2 bg-warning bg-opacity-10 border border-warning rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            <div>
                                                <small class="text-warning fw-bold">لا يمكن تقديم عرض</small>
                                                <div class="mt-1">
                                                    <small class="text-muted">{{ $offerReason }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endauth
                    </div>
                    <div class="card-footer bg-transparent">
                        @auth
                            @if(auth()->user()->isProvider())
                                <div class="d-flex gap-2">
                                    <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline-primary flex-fill">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                    @if($userOffer)
                                        <a href="{{ route('service-offers.show', $userOffer) }}" class="btn btn-info flex-fill">
                                            <i class="fas fa-eye"></i> عرض العرض
                                        </a>
                                    @elseif($canOffer)
                                        <a href="{{ route('service-offers.create', $service) }}" class="btn btn-success flex-fill">
                                            <i class="fas fa-plus"></i> قدم عرض
                                        </a>
                                    @else
                                        <button class="btn btn-secondary flex-fill" disabled title="لا يمكنك تقديم عرض على هذه الخدمة - {{ $offerReason }}">
                                            <i class="fas fa-ban"></i> لا يمكن التقديم
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="d-flex gap-2">
                                    <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline-primary flex-fill">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                    @if(auth()->id() === $service->user_id)
                                        <a href="{{ route('services.edit', $service->id) }}" class="btn btn-outline-warning flex-fill">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @else
                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-primary w-100">
                                عرض التفاصيل
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $services->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3 text-muted">
                @auth
                    @if(auth()->user()->isProvider())
                        لا توجد خدمات متاحة
                    @else
                        لا توجد خدمات مطلوبة
                    @endif
                @else
                    لا توجد خدمات متاحة
                @endauth
            </h4>
            <p class="text-muted">
                @auth
                    @if(auth()->user()->isProvider())
                        @if(request('search') || request('category'))
                        جرب البحث بكلمات مختلفة أو اختر قسم آخر
                        @else
                        سيتم إضافة خدمات قريباً
                        @endif
                    @else
                        @if(request('search') || request('category'))
                        جرب البحث بكلمات مختلفة أو اختر قسم آخر
                        @else
                        لم تقم بطلب أي خدمات بعد
                        @endif
                    @endif
                @else
                    @if(request('search') || request('category'))
                    جرب البحث بكلمات مختلفة أو اختر قسم آخر
                    @else
                    سيتم إضافة خدمات قريباً
                    @endif
                @endauth
            </p>
            @auth
                @if(!auth()->user()->isProvider())
                    <div class="mt-3">
                        <a href="{{ route('categories.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-th-large"></i> تصفح الأقسام
                        </a>
                        <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> جميع خدماتي
                        </a>
                    </div>
                @endif
            @else
                @if(request('search') || request('category'))
                <a href="{{ route('services.index') }}" class="btn btn-primary">
                    عرض جميع الخدمات
                </a>
                @endif
            @endauth
        </div>
        @endif
    </div>
</section>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const subCategorySelect = document.getElementById('sub_category');

    if (categorySelect && subCategorySelect) {
        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;

            if (selectedCategory) {
                // تفعيل حقل القسم الفرعي
                subCategorySelect.disabled = false;

                // جلب الأقسام الفرعية للقسم المختار
                fetch(`/api/categories/${selectedCategory}/subcategories`)
                    .then(response => response.json())
                    .then(data => {
                        subCategorySelect.innerHTML = '<option value="">جميع الأقسام الفرعية</option>';

                        data.forEach(subCategory => {
                            const option = document.createElement('option');
                            option.value = subCategory.id;
                            option.textContent = subCategory.name_ar || subCategory.name_en;
                            subCategorySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching subcategories:', error);
                    });
            } else {
                // تعطيل حقل القسم الفرعي وإفراغه
                subCategorySelect.disabled = true;
                subCategorySelect.innerHTML = '<option value="">جميع الأقسام الفرعية</option>';
            }
        });
    }
});
</script>
@endsection

