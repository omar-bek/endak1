@extends('layouts.app')

@section('title', 'عروض الخدمة - ' . $service->title)

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- معلومات الخدمة -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-start mb-3">
                        <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> العودة للخدمة
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="img-fluid rounded">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $service->title }}</h4>
                            <p class="text-muted">{{ $service->description }}</p>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-primary me-2"></i>
                                <span>{{ $service->user->name }}</span>
                            </div>
                            @if($service->location)
                                <div class="d-flex align-items-center mt-2">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <span>{{ $service->location }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- العروض -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-handshake"></i> العروض المقدمة ({{ $offers->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($offers->count() > 0)
                        @foreach($offers as $offer)
                            <div class="card mb-3 border-{{ $offer->status_color }}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="{{ $offer->provider->avatar_url }}" alt="{{ $offer->provider->name }}"
                                                     class="rounded-circle me-3" width="50" height="50">
                                                <div>
                                                    <h6 class="mb-0">{{ $offer->provider->name }}</h6>
                                                    <small class="text-muted">{{ $offer->created_at }}</small>
                                                </div>
                                            </div>

                                            @if($offer->notes)
                                                <p class="text-muted mb-2">{{ $offer->notes }}</p>
                                            @endif

                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-{{ $offer->status_color }} me-2">{{ $offer->status_label }}</span>
                                                @if($offer->expires_at)
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> ينتهي في {{ $offer->expires_at->format('Y-m-d H:i') }}
                                                    </small>
                                                @endif
                                            </div>

                                            <!-- معلومات إضافية للعرض -->
                                            @if($offer->accepted_at)
                                                <div class="mt-2">
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle"></i> تم القبول في {{ $offer->accepted_at->format('Y-m-d H:i') }}
                                                    </small>
                                                </div>
                                            @endif

                                            @if($offer->delivered_at)
                                                <div class="mt-2">
                                                    <small class="text-info">
                                                        <i class="fas fa-check-double"></i> تم التسليم في {{ $offer->delivered_at->format('Y-m-d H:i') }}
                                                    </small>
                                                </div>
                                            @endif

                                            @if($offer->rating)
                                                <div class="mt-2">
                                                    <div class="d-flex align-items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $offer->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 0.8rem;"></i>
                                                        @endfor
                                                        <small class="text-muted ms-2">تم التقييم</small>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <div class="h4 text-success mb-2">{{ $offer->formatted_price }}</div>

                                            @if($offer->status === 'pending' && auth()->id() === $service->user_id)
                                                <div class="btn-group" role="group">
                                                    <form method="POST" action="{{ route('service-offers.accept', $offer) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                                onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟')">
                                                            <i class="fas fa-check"></i> قبول
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('service-offers.reject', $offer) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('هل أنت متأكد من رفض هذا العرض؟')">
                                                            <i class="fas fa-times"></i> رفض
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif

                                            <!-- أزرار العميل بعد قبول العرض -->
                                            @if($offer->status === 'accepted' && auth()->id() === $service->user_id)
                                                <div class="mb-2">
                                                    @if(!$offer->delivered_at)
                                                        <form method="POST" action="{{ route('service-offers.deliver', $offer) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-sm"
                                                                    onclick="return confirm('هل أنت متأكد من تسليم الخدمة؟')">
                                                                <i class="fas fa-check-double"></i> تم تسليم الخدمة
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-success mb-2">
                                                            <i class="fas fa-check-circle"></i> تم تسليم الخدمة
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- زر التقييم بعد تسليم الخدمة -->
                                            @if($offer->status === 'delivered' && auth()->id() === $service->user_id && !$offer->rating)
                                                <div class="mb-2">
                                                    <button type="button" class="btn btn-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reviewModal{{ $offer->id }}">
                                                        <i class="fas fa-star"></i> تقييم المزود
                                                    </button>
                                                </div>
                                            @elseif($offer->rating && auth()->id() === $service->user_id)
                                                <div class="mb-2">
                                                    <div class="text-center">
                                                        <div class="mb-1">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $offer->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <small class="text-muted">تم التقييم</small>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- زر الرسائل -->
                                            <div class="mt-2">
                                                <a href="{{ route('messages.offer-conversation', $offer->id) }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-comments"></i> إرسال رسالة
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد عروض بعد</h5>
                            <p class="text-muted">لم يتم تقديم أي عروض لهذه الخدمة حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="col-lg-4">
            <!-- إحصائيات العروض -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">إحصائيات العروض</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="h4 text-warning">{{ $offers->where('status', 'pending')->count() }}</div>
                            <small class="text-muted">في الانتظار</small>
                        </div>
                        <div class="col-3">
                            <div class="h4 text-success">{{ $offers->where('status', 'accepted')->count() }}</div>
                            <small class="text-muted">مقبول</small>
                        </div>
                        <div class="col-3">
                            <div class="h4 text-info">{{ $offers->where('status', 'delivered')->count() }}</div>
                            <small class="text-muted">تم التسليم</small>
                        </div>
                        <div class="col-3">
                            <div class="h4 text-danger">{{ $offers->where('status', 'rejected')->count() }}</div>
                            <small class="text-muted">مرفوض</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">معلومات إضافية</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <i class="fas fa-info-circle"></i> يمكنك قبول عرض واحد فقط. عند القبول، سيتم رفض باقي العروض تلقائياً.
                    </p>
                    <p class="small text-muted">
                        <i class="fas fa-check-double"></i> بعد قبول العرض، يمكنك تأكيد تسليم الخدمة ثم تقييم المزود.
                    </p>
                    <p class="small text-muted">
                        <i class="fas fa-clock"></i> العروض التي تنتهي صلاحيتها ستظهر كـ "منتهي الصلاحية".
                    </p>
                </div>
            </div>

            <!-- مراحل العمل -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">مراحل العمل</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">تقديم العرض</h6>
                                <small class="text-muted">مزود الخدمة يقدم عرضه</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">قبول العرض</h6>
                                <small class="text-muted">العميل يقبل العرض</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">تسليم الخدمة</h6>
                                <small class="text-muted">العميل يؤكد تسليم الخدمة</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">التقييم</h6>
                                <small class="text-muted">العميل يقيم مزود الخدمة</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals التقييم -->
@foreach($offers as $offer)
    @if($offer->status === 'delivered' && auth()->id() === $service->user_id && !$offer->rating)
        <div class="modal fade" id="reviewModal{{ $offer->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $offer->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewModalLabel{{ $offer->id }}">
                            <i class="fas fa-star"></i> تقييم مزود الخدمة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('service-offers.review', $offer) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">التقييم <span class="text-danger">*</span></label>
                                <div class="rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}_{{ $offer->id }}" class="rating-radio">
                                        <label for="rating{{ $i }}_{{ $offer->id }}" class="rating-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                <small class="text-muted">اضغط على النجوم لإعطاء التقييم</small>
                            </div>

                            <div class="mb-3">
                                <label for="review{{ $offer->id }}" class="form-label">تعليق (اختياري)</label>
                                <textarea class="form-control" id="review{{ $offer->id }}" name="review" rows="3"
                                          placeholder="اكتب تعليقك عن الخدمة المقدمة..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> إرسال التقييم
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@push('styles')
<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
    gap: 5px;
}

.rating-radio {
    display: none;
}

.rating-label {
    cursor: pointer;
    font-size: 2rem;
    color: #ddd;
    transition: color 0.2s ease;
}

.rating-label:hover,
.rating-label:hover ~ .rating-label,
.rating-radio:checked ~ .rating-label {
    color: #ffc107;
}

.rating-radio:checked ~ .rating-label {
    color: #ffc107;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: -19px;
    top: 17px;
    width: 2px;
    height: 20px;
    background-color: #dee2e6;
}

.timeline-content h6 {
    font-size: 0.9rem;
    margin-bottom: 2px;
}

.timeline-content small {
    font-size: 0.8rem;
}
</style>
@endpush
