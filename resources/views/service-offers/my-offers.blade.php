@extends('layouts.app')

@section('title', 'عروضي')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- العنوان -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary">
                    <i class="fas fa-handshake"></i> عروضي المقدمة
                </h2>
                <a href="{{ route('services.index') }}" class="btn btn-success">
                    <i class="fas fa-search"></i> البحث عن خدمات
                </a>
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

            <!-- قائمة العروض -->
            @if($offers->count() > 0)
                <div class="row">
                    @foreach($offers as $offer)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm border-{{ $offer->status_color }}">
                                <!-- صورة الخدمة -->
                                @if($offer->service && $offer->service->image)
                                    <img src="{{ asset('storage/' . $offer->service->image) }}"
                                         alt="{{ $offer->service->title }}"
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
                                    <h5 class="card-title text-truncate">
                                        {{ $offer->service ? $offer->service->title : 'خدمة محذوفة' }}
                                    </h5>

                                    <!-- القسم -->
                                    @if($offer->service && $offer->service->category)
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-folder text-primary"></i>
                                            {{ $offer->service->category->name }}
                                        </p>
                                    @endif

                                    <!-- صاحب الخدمة -->
                                    @if($offer->service && $offer->service->user)
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-user text-info"></i>
                                            {{ $offer->service->user->name }}
                                        </p>
                                    @endif

                                    <!-- السعر -->
                                    <div class="mb-3">
                                        <h4 class="text-success mb-0">{{ $offer->formatted_price }}</h4>
                                    </div>

                                    <!-- حالة العرض -->
                                    <div class="mb-3">
                                        <span class="badge bg-{{ $offer->status_color }}">
                                            {{ $offer->status_label }}
                                        </span>
                                    </div>

                                    <!-- تاريخ التقديم -->
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-calendar text-info"></i>
                                        {{ $offer->created_at }}
                                    </p>


                                    <!-- الملاحظات -->
                                    @if($offer->notes)
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($offer->notes, 100) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if($offer->service)
                                            <a href="{{ route('services.show', $offer->service->slug) }}"
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> عرض الخدمة
                                            </a>
                                        @else
                                            <span class="btn btn-outline-secondary btn-sm disabled">
                                                <i class="fas fa-ban"></i> خدمة محذوفة
                                            </span>
                                        @endif

                                        @if($offer->status === 'pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> في الانتظار
                                            </span>
                                        @elseif($offer->status === 'accepted')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> مقبول
                                            </span>
                                        @elseif($offer->status === 'rejected')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> مرفوض
                                            </span>
                                        @endif
                                    </div>

                                    <!-- أزرار الإجراءات -->
                                    <div class="mt-2 text-center">
                                        <div class="btn-group" role="group">
                                            @if($offer->status === 'pending')
                                                <a href="{{ route('service-offers.edit', $offer->id) }}"
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                            @endif
                                            <a href="{{ route('messages.offer-conversation', $offer->id) }}"
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-comments"></i> رسالة
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- الترقيم -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $offers->links() }}
                </div>
            @else
                <!-- لا توجد عروض -->
                <div class="text-center py-5">
                    <i class="fas fa-handshake fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">لا توجد عروض مقدمة</h4>
                    <p class="text-muted mb-4">لم تقدم أي عروض بعد. ابدأ بالبحث عن خدمات لتقدم عليها!</p>
                    <a href="{{ route('services.index') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-search"></i> البحث عن خدمات
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
