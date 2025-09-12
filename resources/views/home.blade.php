@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">منصة الخدمات الأولى</h1>
                <p class="lead mb-4">اكتشف أفضل الخدمات واحصل على ما تحتاجه بسهولة وسرعة</p>
                <div class="d-flex gap-3">
                    @auth
                        @if(auth()->user()->isProvider())
                            <a href="{{ route('services.index') }}" class="btn btn-light btn-lg">استكشف الخدمات</a>
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg">تصفح الأقسام</a>
                        @else
                            <a href="{{ route('categories.index') }}" class="btn btn-light btn-lg">تصفح الأقسام</a>
                            <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg">خدماتي المطلوبة</a>
                        @endif
                    @else
                        <a href="{{ route('categories.index') }}" class="btn btn-light btn-lg">تصفح الأقسام</a>
                        <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg">استكشف الخدمات</a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-tools" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">الأقسام الرئيسية</h2>
            <p class="text-muted">اختر من بين مجموعة واسعة من الأقسام</p>
        </div>

        <div class="row">
            @forelse($categories as $category)
            <div class="col-6 col-md-4 col-lg-2 mb-4">
                <div class="card category-card h-100">
                    <div class="category-image-container">
                        <img src="{{ $category->image_url }}" class="category-image" alt="{{ $category->name }}">
                        <div class="category-overlay">
                            <h6 class="category-title">{{ $category->name }}</h6>
                        </div>
                    </div>
                    <div class="card-body text-center p-2">
                        @if($category->hasChildren())
                            <a href="{{ route('categories.subcategories', $category->slug) }}" class="btn btn-primary btn-sm">
                                الأقسام الفرعية
                            </a>
                        @else
                            <a href="{{ route('services.request', $category->id) }}" class="btn btn-primary btn-sm">
                                اطلب خدمة
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">لا توجد أقسام متاحة حالياً</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Services Section -->
@if($featuredServices->count() > 0)
@auth
    @if(auth()->user()->isProvider())
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">الخدمات المميزة</h2>
                <p class="text-muted">أفضل الخدمات المختارة لك</p>
            </div>

            <div class="row">
                @foreach($featuredServices as $service)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ $service->image_url }}" class="card-img-top" alt="{{ $service->title }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $service->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($service->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">{{ $service->formatted_price }}</span>
                                <small class="text-muted">{{ $service->category->name }}</small>
                            </div>
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
        </div>
    </section>
    @endif
@else
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">الخدمات المميزة</h2>
            <p class="text-muted">أفضل الخدمات المختارة لك</p>
        </div>

        <div class="row">
            @foreach($featuredServices as $service)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $service->image_url }}" class="card-img-top" alt="{{ $service->title }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($service->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold">{{ $service->formatted_price }}</span>
                            <small class="text-muted">{{ $service->category->name }}</small>
                        </div>
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
    </div>
</section>
@endauth
@endif

<!-- Latest Services Section -->
@if($latestServices->count() > 0)
@auth
    @if(auth()->user()->isProvider())
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">أحدث الخدمات</h2>
                <p class="text-muted">اكتشف أحدث الخدمات المضافة</p>
            </div>

            <div class="row">
                @foreach($latestServices as $service)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ $service->image_url }}" class="card-img-top" alt="{{ $service->title }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $service->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($service->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold">{{ $service->formatted_price }}</span>
                            <small class="text-muted">{{ $service->category->name }}</small>
                        </div>

                        @php
                            $userOffer = $service->offers->where('provider_id', auth()->id())->first();
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

            <div class="text-center mt-4">
                <a href="{{ route('services.index') }}" class="btn btn-primary btn-lg">عرض جميع الخدمات</a>
            </div>
        </div>
    </section>
    @endif
@else
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">أحدث الخدمات</h2>
            <p class="text-muted">اكتشف أحدث الخدمات المضافة</p>
        </div>

        <div class="row">
            @foreach($latestServices as $service)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $service->image_url }}" class="card-img-top" alt="{{ $service->title }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($service->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold">{{ $service->formatted_price }}</span>
                            <small class="text-muted">{{ $service->category->name }}</small>
                        </div>

                        @auth
                            @if(auth()->user()->isProvider())
                                @php
                                    $userOffer = $service->offers->where('provider_id', auth()->id())->first();
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
                                @endif
                            @endif
                        @endauth
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

        <div class="text-center mt-4">
            <a href="{{ route('services.index') }}" class="btn btn-primary btn-lg">عرض جميع الخدمات</a>
        </div>
    </div>
</section>
@endauth
@endif

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-shield-alt text-primary" style="font-size: 3rem;"></i>
                </div>
                <h4>خدمات آمنة</h4>
                <p class="text-muted">جميع الخدمات مضمونة وآمنة 100%</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-clock text-primary" style="font-size: 3rem;"></i>
                </div>
                <h4>خدمة سريعة</h4>
                <p class="text-muted">احصل على الخدمة في أسرع وقت ممكن</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-star text-primary" style="font-size: 3rem;"></i>
                </div>
                <h4>جودة عالية</h4>
                <p class="text-muted">نختار لك أفضل مزودي الخدمات</p>
            </div>
        </div>
    </div>
</section>
@endsection
