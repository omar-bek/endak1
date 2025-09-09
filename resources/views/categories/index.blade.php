@extends('layouts.app')

@section('title', 'الأقسام')

@section('content')
<!-- Header Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center">
            <h1 class="fw-bold">الأقسام</h1>
            <p class="text-muted">اختر من بين مجموعة واسعة من الأقسام والخدمات</p>
            <p class="text-muted">اختر من بين مجموعة واسعة من الأقسام والخدمات</p>
            <p class="text-muted small">اضغط على "استكشف القسم" لطلب خدمة من القسم المطلوب</p>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
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
                    <div class="card-body text-center">
                        {{--  <p class="card-text text-muted">{{ $category->description }}</p>  --}}

                        {{--  @if($category->hasChildren())
                        <div class="mb-3">
                            <small class="text-muted">الأقسام الفرعية:</small>
                            <div class="mt-2">
                                @foreach($category->children->take(3) as $child)
                                <span class="badge bg-light text-dark me-1">{{ $child->name }}</span>
                                @endforeach
                                @if($category->children->count() > 3)
                                <span class="badge bg-primary">+{{ $category->children->count() - 3 }} أكثر</span>
                                @endif
                            </div>
                        </div>
                        @endif  --}}

                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-primary">
                                استكشف القسم
                            </a>
                            @if($category->hasChildren())
                            <a href="{{ route('categories.subcategories', $category->slug) }}" class="btn btn-outline-primary">
                                الأقسام الفرعية
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="py-5">
                    <i class="fas fa-folder-open text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">لا توجد أقسام متاحة</h4>
                    <p class="text-muted">سيتم إضافة الأقسام قريباً</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
