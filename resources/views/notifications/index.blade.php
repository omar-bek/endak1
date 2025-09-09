@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- العنوان -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary">
                    <i class="fas fa-bell"></i> الإشعارات
                    @if($notifications->where('read_at', null)->count() > 0)
                        <span class="badge bg-danger ms-2">{{ $notifications->where('read_at', null)->count() }}</span>
                    @endif
                </h2>
                @if($notifications->where('read_at', null)->count() > 0)
                    <button class="btn btn-success" onclick="markAllAsRead()" id="markAllReadBtn">
                        <i class="fas fa-check-double"></i> تحديد الكل كمقروء
                    </button>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- قائمة الإشعارات -->
            @if($notifications->count() > 0)
                <div class="row">
                    @foreach($notifications as $notification)
                        <div class="col-12 mb-3">
                            <div class="card {{ $notification->isRead() ? '' : 'border-primary' }} notification-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="{{ $notification->icon }}" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1 {{ $notification->isRead() ? 'text-muted' : 'fw-bold' }}">
                                                    {{ $notification->title }}
                                                    @if(!$notification->isRead())
                                                        <span class="badge bg-primary ms-2">جديد</span>
                                                    @endif
                                                </h6>
                                                <p class="card-text text-muted mb-2">{{ $notification->message }}</p>

                                                <!-- أزرار العمل -->
                                                <div class="notification-actions mb-2">
                                                    @if($notification->data && isset($notification->data['offer_id']))
                                                        @php
                                                            $offer = \App\Models\ServiceOffer::find($notification->data['offer_id']);
                                                        @endphp
                                                        @if($offer && $offer->service)
                                                            @if(auth()->id() == $offer->provider_id)
                                                                <!-- مزود الخدمة - يرى عرضه -->
                                                                <a href="{{ route('service-offers.show', $offer->id) }}"
                                                                   class="btn btn-sm btn-outline-primary me-2">
                                                                    <i class="fas fa-handshake"></i> عرض عرضي
                                                                </a>
                                                            @elseif(auth()->id() == $offer->service->user_id)
                                                                <!-- صاحب الخدمة - يرى عروض خدمته -->
                                                                <a href="{{ route('services.show', $offer->service->slug) }}"
                                                                   class="btn btn-sm btn-outline-success me-2">
                                                                    <i class="fas fa-eye"></i> عرض عروض الخدمة
                                                                </a>
                                                            @endif
                                                        @elseif($offer && !$offer->service)
                                                            <!-- الخدمة محذوفة -->
                                                            <span class="btn btn-sm btn-outline-secondary disabled">
                                                                <i class="fas fa-ban"></i> خدمة محذوفة
                                                            </span>
                                                        @endif
                                                    @endif

                                                    @if($notification->data && isset($notification->data['service_id']))
                                                        @php
                                                            $service = \App\Models\Service::find($notification->data['service_id']);
                                                        @endphp
                                                        @if($service)
                                                            <a href="{{ route('services.show', $service->slug) }}"
                                                               class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye"></i> عرض الخدمة
                                                            </a>
                                                        @else
                                                            <!-- الخدمة محذوفة -->
                                                            <span class="btn btn-sm btn-outline-secondary disabled">
                                                                <i class="fas fa-ban"></i> خدمة محذوفة
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>

                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            @if(!$notification->isRead())
                                                <button class="btn btn-sm btn-outline-success" onclick="markAsRead({{ $notification->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- الترقيم -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <!-- لا توجد إشعارات -->
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">لا توجد إشعارات</h4>
                    <p class="text-muted">ستظهر هنا الإشعارات الجديدة عند وصولها</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.notification-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    border-left: 4px solid transparent;
}

.notification-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.notification-card:not(.border-primary) {
    opacity: 0.8;
}

.notification-card.border-primary {
    border-left-color: var(--primary-color);
}

.notification-actions .btn {
    border-radius: 20px;
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    transition: all 0.3s ease;
}

.notification-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

.notification-card .card-body {
    padding: 1.25rem;
}

.notification-card .card-title {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.notification-card .card-text {
    font-size: 0.9rem;
    line-height: 1.4;
}

/* تحسين الأيقونات */
.notification-card i[class*="fas fa-"] {
    width: 24px;
    text-align: center;
}

/* تحسين البادج */
.notification-card .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* تحسين الأزرار */
.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, #20c997, #28a745);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
}


/* تحسين spinner */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
function markAsRead(notificationId) {
    // إظهار loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // إخفاء الزر بعد النجاح
            button.style.display = 'none';
            // إعادة تحميل الصفحة
            location.reload();
        } else {
            throw new Error('Failed to mark as read');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // إعادة تعيين الزر
        button.innerHTML = originalText;
        button.disabled = false;
        alert('حدث خطأ أثناء تحديث الإشعار. يرجى المحاولة مرة أخرى.');
    });
}

function markAllAsRead() {
    // إظهار loading state
    const button = document.getElementById('markAllReadBtn');
    if (!button) {
        console.error('Button not found');
        return;
    }

    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
    button.disabled = true;

    // الحصول على CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        button.innerHTML = originalText;
        button.disabled = false;
        alert('خطأ في الأمان. يرجى إعادة تحميل الصفحة.');
        return;
    }

    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // إعادة تحميل الصفحة
            location.reload();
        } else {
            throw new Error('Server returned success: false');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // إعادة تعيين الزر
        button.innerHTML = originalText;
        button.disabled = false;
        alert('حدث خطأ أثناء تحديث الإشعارات: ' + error.message);
    });
}

</script>
@endsection
