@extends('layouts.app')

@section('title', $category->name)

@section('content')
<!-- Flash Messages -->
@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="container mt-3">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="container mt-3">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<!-- Category Header -->
<section class="py-5 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">الأقسام</a></li>
                @if($category->parent)
                <li class="breadcrumb-item"><a href="{{ route('categories.show', $category->parent->slug) }}">{{ $category->parent->name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold">{{ $category->name }}</h1>
                <p class="lead text-muted">{{ $category->description }}</p>
                            <div class="d-flex gap-2">
                <span class="badge bg-primary">{{ $services->total() }} خدمة</span>
                @if($category->subCategories && $category->subCategories->count() > 0)
                <span class="badge bg-secondary">{{ $category->subCategories->count() }} قسم فرعي</span>
                @endif
                @if($category->fields && $category->fields->count() > 0)
                <span class="badge bg-info">{{ $category->fields->count() }} حقل مخصص</span>
                @endif
                    @auth
                        @if(!auth()->user()->isProvider())
                            @if($category->subCategories && $category->subCategories->count() > 0)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>يرجى اختيار قسم فرعي لطلب الخدمة</strong>
                                    <br><small>هذا القسم يحتوي على أقسام فرعية، يرجى اختيار القسم الفرعي المناسب من القائمة أدناه</small>
                                </div>
                            @else
                                <a href="{{ route('services.request', $category->id) }}" class="btn btn-success">
                                    <i class="fas fa-concierge-bell"></i> طلب خدمة من هذا القسم
                                </a>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-success">
                            <i class="fas fa-sign-in-alt"></i> تسجيل دخول لطلب الخدمة
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="category-header-image">
                    <img src="{{ $category->image_url }}" class="img-fluid rounded" alt="{{ $category->name }}" style="max-height: 200px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Subcategories Section -->
@if($category->subCategories && $category->subCategories->count() > 0)
<section class="py-4">
    <div class="container">
        <h3 class="mb-4 text-center" style="color:#1976d2; font-weight:bold;">
            <i class="fas fa-layer-group"></i> الأقسام الفرعية
        </h3>
        <div class="row">
            @foreach($category->subCategories as $subCategory)
                @if($subCategory->status)
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card text-center h-100 sub-category-card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                        <div class="subcategory-image-container">
                            @if($subCategory->image)
                                <img src="{{ asset('storage/' . $subCategory->image) }}" class="subcategory-image" alt="{{ $subCategory->name_ar ?? $subCategory->name_en }}">
                            @else
                                <div class="subcategory-image-placeholder">
                                    <i class="fas fa-folder" style="font-size: 3rem; color: #6c757d;"></i>
                                </div>
                            @endif
                            <div class="subcategory-overlay">
                                <h6 class="subcategory-title">{{ app()->getLocale() == 'ar' ? $subCategory->name_ar : $subCategory->name_en }}</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($subCategory->description_ar || $subCategory->description_en)
                                <p class="card-text text-muted" style="font-size: 0.9rem; margin-bottom: 1rem;">
                                    {{ app()->getLocale() == 'ar' ? $subCategory->description_ar : $subCategory->description_en }}
                                </p>
                            @endif

                            <!-- Services Count -->
                            @php
                                $servicesCount = \App\Models\Service::where('category_id', $category->id)
                                                                   ->where('sub_category_id', $subCategory->id)
                                                                   ->where('is_active', true)
                                                                   ->count();
                            @endphp
                            <div class="text-center mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-tasks"></i> {{ $servicesCount }} خدمة
                                </small>
                            </div>

                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('categories.show', $category->slug) }}?sub_category_id={{ $subCategory->id }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> استكشف
                                </a>
                                @auth
                                    @if(!auth()->user()->isProvider())
                                        <a href="{{ route('services.request', $category->id) }}?sub_category_id={{ $subCategory->id }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-concierge-bell"></i> طلب خدمة
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>
                    الخدمات في {{ $category->name }}
                    @if($selectedSubCategory)
                        <small class="text-muted">- {{ app()->getLocale() == 'ar' ? $selectedSubCategory->name_ar : $selectedSubCategory->name_en }}</small>
                    @endif
                    <span class="badge bg-primary ms-2">{{ $services->total() }} خدمة</span>
                </h3>
                @if($selectedSubCategory)
                    <div class="alert alert-info mt-2 mb-0">
                        <i class="fas fa-filter"></i>
                        <strong>القسم الفرعي المحدد:</strong>
                        {{ app()->getLocale() == 'ar' ? $selectedSubCategory->name_ar : $selectedSubCategory->name_en }}
                        <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i> إلغاء التصفية
                        </a>
                    </div>
                @endif
            </div>
            <div class="d-flex gap-2">
                <form class="d-flex" method="GET">
                    @if(request('sub_category_id'))
                        <input type="hidden" name="sub_category_id" value="{{ request('sub_category_id') }}">
                    @endif
                    <input type="text" name="search" class="form-control me-2" placeholder="البحث في الخدمات..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">بحث</button>
                </form>
            </div>
        </div>

        @if($services->count() > 0)
        <div class="row">
            @foreach($services as $service)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $service->image_url }}" class="card-img-top" alt="{{ $service->title }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($service->description, 100) }}</p>

                        <!-- Category and Subcategory Info -->
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $service->category->name }}</span>
                            @if($service->subCategory)
                                <span class="badge bg-secondary">{{ app()->getLocale() == 'ar' ? $service->subCategory->name_ar : $service->subCategory->name_en }}</span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-primary fw-bold">{{ $service->formatted_price }}</span>
                            <small class="text-muted">{{ $service->user->name }}</small>
                        </div>

                        @if($service->location)
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $service->location }}
                            </small>
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
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('services.show', $service->slug) }}" class="btn btn-primary w-100">
                            عرض التفاصيل
                        </a>
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
                @if($selectedSubCategory)
                    لا توجد خدمات في القسم الفرعي "{{ app()->getLocale() == 'ar' ? $selectedSubCategory->name_ar : $selectedSubCategory->name_en }}"
                @else
                    لا توجد خدمات في هذا القسم
                @endif
            </h4>
            <p class="text-muted">
                @if($selectedSubCategory)
                    يمكنك طلب خدمة من هذا القسم الفرعي أو استكشاف الأقسام الفرعية الأخرى
                @else
                    سيتم إضافة خدمات قريباً
                @endif
            </p>
            @if($selectedSubCategory)
                <div class="mt-3">
                    <a href="{{ route('services.request', $category->id) }}?sub_category_id={{ $selectedSubCategory->id }}" class="btn btn-primary me-2">
                        <i class="fas fa-concierge-bell"></i> طلب خدمة من هذا القسم الفرعي
                    </a>
                    <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eye"></i> عرض جميع الخدمات
                    </a>
                </div>
            @endif
        </div>
        @endif
    </div>
</section>
@endsection

@section('styles')
<style>
    /* تنسيق رسائل Flash للهاتف المحمول */
    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
        padding: 1rem 1.25rem;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .alert-warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    .alert-info {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        opacity: 0.7;
        padding: 0.5rem;
    }

    .btn-close:hover {
        opacity: 1;
    }

    /* تحسين للهاتف المحمول */
    @media (max-width: 768px) {
        .alert {
            margin: 0.5rem;
            padding: 0.875rem 1rem;
            font-size: 0.9rem;
            border-radius: 8px;
        }

        .alert i {
            font-size: 1rem;
        }

        .btn-close {
            font-size: 1rem;
            padding: 0.25rem;
        }
    }

    @media (max-width: 576px) {
        .alert {
            margin: 0.25rem;
            padding: 0.75rem 0.875rem;
            font-size: 0.85rem;
        }

        .alert i {
            font-size: 0.9rem;
        }
    }

    /* تنسيق الأقسام الفرعية */
    .sub-category-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e3e3;
    }

    .sub-category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #1976d2;
    }

    .subcategory-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 8px 8px 0 0;
        height: 150px;
        background: #f8f9fa;
    }

    .subcategory-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .subcategory-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }

    .subcategory-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: white;
        padding: 20px 15px 15px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .sub-category-card:hover .subcategory-overlay {
        transform: translateY(0);
    }

    .sub-category-card:hover .subcategory-image {
        transform: scale(1.1);
    }

    .subcategory-title {
        margin: 0;
        font-weight: bold;
        font-size: 1rem;
    }

    /* تحسين الأزرار */
    .btn-outline-primary {
        border-color: #1976d2;
        color: #1976d2;
    }

    .btn-outline-primary:hover {
        background-color: #1976d2;
        border-color: #1976d2;
        color: white;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    /* تحسين العنوان */
    .section h3 {
        position: relative;
        padding-bottom: 10px;
    }

    .section h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #1976d2, #43e97b);
        border-radius: 2px;
    }
</style>
@endsection

@section('scripts')
<script>
    // تحسين رسائل Flash للهاتف المحمول
    document.addEventListener('DOMContentLoaded', function() {
        // إخفاء الرسائل تلقائياً بعد 5 ثوان
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert && alert.parentNode) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 500);
                }
            }, 5000);
        });

        // تحسين إغلاق الرسائل على الهاتف المحمول
        const closeButtons = document.querySelectorAll('.btn-close');
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const alert = this.closest('.alert');
                if (alert) {
                    alert.style.transition = 'opacity 0.3s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 300);
                }
            });
        });

        // إضافة تأثير النقر على الرسائل
        alerts.forEach(function(alert) {
            alert.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });
    });
</script>
@endsection
