@extends('layouts.app')

@section('title', 'خدماتي')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- العنوان -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary">
                    <i class="fas fa-list"></i>
                    @if(auth()->user()->isProvider())
                        جميع الخدمات المتاحة
                    @else
                        خدماتي المطلوبة
                    @endif
                </h2>
                @if(!auth()->user()->isProvider())
                    <a href="{{ route('categories.index') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> طلب خدمة جديدة
                    </a>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- قائمة الخدمات -->
            @if($services->count() > 0)
                <div class="row">
                    @foreach($services as $service)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <!-- صورة الخدمة -->
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}"
                                         alt="{{ $service->title }}"
                                         class="card-img-top"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                         style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif

                                <div class="card-body">
                                    <!-- عنوان الخدمة -->
                                    <h5 class="card-title text-truncate">{{ $service->title }}</h5>

                                    <!-- القسم -->
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-folder text-primary"></i>
                                        {{ $service->category->name }}
                                    </p>

                                    <!-- المدينة -->
                                    @if($service->from_city)
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-map-marker-alt text-success"></i>
                                            {{ $service->from_city }}
                                        </p>
                                    @endif

                                    <!-- صاحب الخدمة (لمزود الخدمة فقط) -->
                                    @if(auth()->user()->isProvider())
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-user text-primary"></i>
                                            {{ $service->user->name }}
                                        </p>
                                    @endif

                                    <!-- تاريخ الإنشاء -->
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-calendar text-info"></i>
                                        {{ $service->created_at->format('Y-m-d') }}
                                    </p>

                                    <!-- حالة الخدمة -->
                                    <div class="mb-3">
                                        @if($service->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </div>

                                    <!-- عدد العروض -->
                                    <div class="mb-3">
                                        @php
                                            $pendingOffers = $service->offers->where('status', 'pending')->count();
                                            $acceptedOffers = $service->offers->where('status', 'accepted')->count();
                                            $myOffers = $service->offers->where('provider_id', auth()->id());
                                            $myPendingOffers = $myOffers->where('status', 'pending')->count();
                                            $myAcceptedOffers = $myOffers->where('status', 'accepted')->count();
                                        @endphp

                                        @if(auth()->user()->isProvider())
                                            <!-- عرض عروضي على هذه الخدمة -->
                                            @if($myPendingOffers > 0)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> عرضي في الانتظار
                                                </span>
                                            @endif

                                            @if($myAcceptedOffers > 0)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> عرضي مقبول
                                                </span>
                                            @endif

                                            @if($myOffers->count() == 0)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-plus"></i> يمكنك تقديم عرض
                                                </span>
                                            @endif
                                        @else
                                            <!-- عرض عروض الخدمة للمستخدم العادي -->
                                            @if($pendingOffers > 0)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> {{ $pendingOffers }} عرض في الانتظار
                                                </span>
                                            @endif

                                            @if($acceptedOffers > 0)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> عرض مقبول
                                                </span>
                                            @endif

                                            @if($service->offers->count() == 0)
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-inbox"></i> لا توجد عروض
                                                </span>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- الوصف -->
                                    @if($service->description)
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($service->description, 100) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('services.show', $service->slug) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>

                                        @if(auth()->user()->isProvider())
                                            <!-- أزرار مزود الخدمة -->
                                            @if($myOffers->count() > 0)
                                                <a href="{{ route('service-offers.index', $service) }}"
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-handshake"></i> عروضي
                                                    @if($myPendingOffers > 0)
                                                        <span class="badge bg-warning ms-1">{{ $myPendingOffers }}</span>
                                                    @endif
                                                </a>
                                            @else
                                                <a href="{{ route('service-offers.create', $service) }}"
                                                   class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-plus"></i> قدم عرض
                                                </a>
                                            @endif
                                        @else
                                            <!-- أزرار المستخدم العادي -->
                                            @if($service->offers->count() > 0)
                                                <a href="{{ route('service-offers.index', $service) }}"
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-handshake"></i> العروض
                                                    @if($pendingOffers > 0)
                                                        <span class="badge bg-warning ms-1">{{ $pendingOffers }}</span>
                                                    @endif
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- الترقيم -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $services->links() }}
                </div>
            @else
                <!-- لا توجد خدمات -->
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                    @if(auth()->user()->isProvider())
                        <h4 class="text-muted">لا توجد خدمات متاحة</h4>
                        <p class="text-muted mb-4">لا توجد خدمات متاحة حالياً. تحقق لاحقاً!</p>
                    @else
                        <h4 class="text-muted">لا توجد خدمات مطلوبة</h4>
                        <p class="text-muted mb-4">لم تطلب أي خدمات بعد. ابدأ بطلب خدمة جديدة!</p>
                        <a href="{{ route('categories.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus"></i> طلب خدمة جديدة
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
