@extends('layouts.app')

@section('title', 'تفاصيل العرض')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}">الخدمات</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.show', $offer->service->slug) }}">{{ $offer->service->title }}</a></li>
                    <li class="breadcrumb-item active">تفاصيل العرض</li>
                </ol>
            </nav>

            <!-- تفاصيل العرض -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-contract"></i>
                        تفاصيل العرض المقدم
                    </h4>
                </div>
                <div class="card-body">
                    <!-- حالة العرض -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-2">حالة العرض:</span>
                                <span class="badge bg-{{ $offer->status === 'accepted' ? 'success' : ($offer->status === 'rejected' ? 'danger' : 'warning') }} fs-6">
                                    {{ $offer->status_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                تم التقديم: {{ $offer->created_at->format('Y-m-d H:i') }}
                            </small>
                        </div>
                    </div>

                    <!-- معلومات الخدمة -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 text-primary">
                                <i class="fas fa-concierge-bell"></i>
                                تفاصيل الخدمة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="fw-bold">{{ $offer->service->title }}</h6>
                                    <p class="text-muted mb-2">{{ $offer->service->description }}</p>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-folder text-primary me-2"></i>
                                        <span>{{ app()->getLocale() == 'ar' ? $offer->service->category->name : $offer->service->category->name_en }}</span>
                                    </div>
                                    @if($offer->service->city)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            <span>{{ app()->getLocale() == 'ar' ? $offer->service->city->name_ar : $offer->service->city->name_en }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تفاصيل العرض -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 text-success">
                                <i class="fas fa-handshake"></i>
                                عرضك
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="fw-bold">السعر المقترح:</label>
                                        <div class="h4 text-success">{{ $offer->formatted_price }}</div>
                                    </div>
                                    @if($offer->notes)
                                        <div class="mb-3">
                                            <label class="fw-bold">ملاحظاتك:</label>
                                            <p class="text-muted">{{ $offer->notes }}</p>
                                        </div>
                                    @endif
                                    @if($offer->expires_at)
                                        <div class="mb-3">
                                            <label class="fw-bold">تاريخ انتهاء الصلاحية:</label>
                                            <div class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $offer->expires_at->format('Y-m-d H:i') }}
                                                @if($offer->expires_at->isPast())
                                                    <span class="badge bg-danger ms-2">منتهي الصلاحية</span>
                                                @else
                                                    <span class="badge bg-success ms-2">صالح</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="d-flex flex-column gap-2">
                                        @if($offer->status === 'pending')
                                            <span class="badge bg-warning fs-6">في انتظار الرد</span>
                                        @elseif($offer->status === 'accepted')
                                            <span class="badge bg-success fs-6">تم القبول</span>
                                        @elseif($offer->status === 'rejected')
                                            <span class="badge bg-danger fs-6">تم الرفض</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- أزرار الإجراءات -->
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            العودة للخدمات
                        </a>

                        @if(Auth::id() == $offer->provider_id)
                            <!-- مزود الخدمة - يرى عروضه -->
                            <a href="{{ route('service-offers.my-offers') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i>
                                عروضي
                            </a>
                        @elseif(Auth::id() == $offer->service->user_id)
                            <!-- صاحب الخدمة - يرى عروض خدمته -->
                            <a href="{{ route('services.show', $offer->service->slug) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i>
                                عرض عروض الخدمة
                            </a>

                            <!-- أزرار قبول/رفض للعرض -->
                            @if($offer->status === 'pending')
                                <form method="POST" action="{{ route('service-offers.accept', $offer->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟')">
                                        <i class="fas fa-check"></i>
                                        قبول العرض
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('service-offers.reject', $offer->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من رفض هذا العرض؟')">
                                        <i class="fas fa-times"></i>
                                        رفض العرض
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
